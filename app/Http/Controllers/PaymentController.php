<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function createPayment(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'description' => 'required|string|max:255'
        ]);

        $partnerCode = config('services.momo.partner_code');
        $accessKey = config('services.momo.access_key');
        $secretKey = config('services.momo.secret_key');
        $endpoint = config('services.momo.endpoint');

        // Validate required configs
        if (!$partnerCode || !$accessKey || !$secretKey) {
            Log::error('Missing Momo configuration', [
                'partnerCode' => $partnerCode,
                'accessKey' => $accessKey,
                'secretKey' => $secretKey
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Payment gateway configuration is missing'
            ], 500);
        }

        $orderId = Str::random(10);
        $orderInfo = $validated['description'];
        $amount = $validated['amount'];
        $ipnUrl = route('payment.ipn');
        $redirectUrl = route('payment.redirect');
        $extraData = '';

        $requestId = time() . '';
        $requestType = 'captureWallet';

        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac('sha256', $rawHash, $secretKey);

        $data = [
            'partnerCode' => $partnerCode,
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature,
        ];

        try {
            // Log request data for debugging
            Log::info('Momo API Request:', [
                'endpoint' => $endpoint,
                'data' => $data
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($endpoint, $data);

            // Log the raw response for debugging
            Log::info('Momo API Raw Response:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to connect to payment gateway',
                    'error' => $response->body()
                ], 500);
            }

            $responseData = $response->json();

            // Log the parsed response for debugging
            Log::info('Momo API Parsed Response:', $responseData);

            // Check if the response has the required fields
            if (isset($responseData['resultCode']) && $responseData['resultCode'] == 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment created successfully',
                    'payment' => [
                        'id' => 'PAY-' . Str::random(12),
                        'amount' => $amount,
                        'currency' => $validated['currency'],
                        'description' => $orderInfo,
                        'status' => 'pending',
                        'payUrl' => $responseData['payUrl'] ?? null
                    ]
                ]);
            }

            // If we get here, something went wrong
            return response()->json([
                'success' => false,
                'message' => $responseData['message'] ?? 'Failed to create payment',
                'error' => $responseData
            ], 400);
        } catch (\Exception $e) {
            Log::error('Momo API Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error connecting to payment gateway',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getQRCode($paymentId)
    {
        // In a real application, you would fetch the payment details from your database
        // and generate a QR code URL based on the payment information
        $qrCodeUrl = "https://example.com/qr/" . $paymentId; // Replace with actual QR code generation

        return response()->json([
            'success' => true,
            'qrCodeUrl' => $qrCodeUrl
        ]);
    }

    public function getPaymentStatus($paymentId)
    {
        // In a real application, you would check the payment status from your database
        // or from the payment provider's API
        $status = 'pending'; // Replace with actual status check

        return response()->json([
            'success' => true,
            'status' => $status
        ]);
    }

    public function handleIPN(Request $request)
    {
        $partnerCode = $request->partnerCode;
        $orderId = $request->orderId;
        $requestId = $request->requestId;
        $amount = $request->amount;
        $orderInfo = $request->orderInfo;
        $orderType = $request->orderType;
        $transId = $request->transId;
        $resultCode = $request->resultCode;
        $message = $request->message;
        $payType = $request->payType;
        $signature = $request->signature;

        $accessKey = config('services.momo.access_key');
        $secretKey = config('services.momo.secret_key');

        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $request->extraData . "&message=" . $message . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&orderType=" . $orderType . "&partnerCode=" . $partnerCode . "&payType=" . $payType . "&requestId=" . $requestId . "&resultCode=" . $resultCode . "&transId=" . $transId;
        $expectedSignature = hash_hmac('sha256', $rawHash, $secretKey);

        if ($signature === $expectedSignature) {
            if ($resultCode == 0) {
                // Payment successful
                // Update your database here
                return response()->json(['RspCode' => 0, 'Message' => 'OK']);
            }
        }

        return response()->json(['RspCode' => 99, 'Message' => 'Unknown error']);
    }

    public function handleRedirect(Request $request)
    {
        // Handle redirect after payment
        return response()->json($request->all());
    }
}