<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function createPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'orderInfo' => 'required|string',
        ]);

        $partnerCode = config('services.momo.partner_code');
        $accessKey = config('services.momo.access_key');
        $secretKey = config('services.momo.secret_key');
        $endpoint = config('services.momo.endpoint');

        $orderId = Str::random(10);
        $orderInfo = $request->orderInfo;
        $amount = $request->amount;
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

        $response = Http::post($endpoint, $data);

        return response()->json($response->json());
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