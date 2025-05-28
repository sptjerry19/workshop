<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MomoPaymentController extends Controller
{
    protected $partnerCode;
    protected $accessKey;
    protected $secretKey;
    protected $endpoint;

    public function __construct()
    {
        $this->partnerCode = config('services.momo.partner_code');
        $this->accessKey = config('services.momo.access_key');
        $this->secretKey = config('services.momo.secret_key');
        $this->endpoint = config('services.momo.endpoint');
    }

    public function createPayment(Request $request)
    {
        $requestId = time() . "";
        $orderId = $request->get('order_id');
        $requestType = "captureWallet";
        $extraData = "";

        // Tạo chuỗi raw hash theo thứ tự yêu cầu của Momo
        $rawHash = "accessKey=" . $this->accessKey;
        $rawHash .= "&amount=" . $request->amount;
        $rawHash .= "&extraData=" . $extraData;
        $rawHash .= "&ipnUrl=" . route('momo.payment.ipn');
        $rawHash .= "&orderId=" . $orderId;
        $rawHash .= "&orderInfo=" . $request->description;
        $rawHash .= "&partnerCode=" . $this->partnerCode;
        $rawHash .= "&redirectUrl=" . route('momo.payment.return');
        $rawHash .= "&requestId=" . $requestId;
        $rawHash .= "&requestType=" . $requestType;

        // Tạo chữ ký
        $signature = hash_hmac('sha256', $rawHash, $this->secretKey);

        // Log để debug
        Log::info('Momo Payment Request:', [
            'rawHash' => $rawHash,
            'signature' => $signature
        ]);

        $data = [
            'partnerCode' => $this->partnerCode,
            'requestId' => $requestId,
            'amount' => $request->amount,
            'orderId' => $orderId,
            'orderInfo' => $request->description,
            'redirectUrl' => route('momo.payment.return'),
            'ipnUrl' => route('momo.payment.ipn'),
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Content-Length' => strlen(json_encode($data))
            ])->timeout(5)->post($this->endpoint . '/v2/gateway/api/create', $data);

            $result = $response->json();

            // Log response để debug
            Log::info('Momo Payment Response:', $result);

            if ($result['resultCode'] == 0) {
                // Lưu thông tin payment
                $payment = Payment::create([
                    'order_id' => $orderId,
                    'payment_method' => 'momo',
                    'amount' => $request->amount,
                    'transaction_id' => $requestId,
                    'status' => 'pending',
                    'payment_url' => $result['payUrl'],
                    'payment_details' => [
                        'orderId' => $orderId,
                        'requestId' => $requestId,
                        'description' => $request->description
                    ]
                ]);

                return response()->json([
                    'success' => true,
                    'payment' => [
                        'id' => $payment->id,
                        'payUrl' => $result['payUrl']
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Có lỗi xảy ra khi tạo thanh toán'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Momo Payment Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo thanh toán: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getQRCode($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);

        if ($payment->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Payment already completed'
            ], 400);
        }

        // Gọi API Momo để lấy QR code
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->get($this->endpoint . '/v2/gateway/api/qr', [
            'partnerCode' => $this->partnerCode,
            'requestId' => $payment->transaction_id,
            'amount' => $payment->amount,
            'orderId' => $payment->payment_details['orderId'],
            'orderInfo' => $payment->payment_details['description']
        ]);

        $result = $response->json();

        if ($result['resultCode'] == 0) {
            return response()->json([
                'success' => true,
                'qrCodeUrl' => $result['qrCodeUrl']
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 400);
    }

    public function checkStatus($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);

        if ($payment->status === 'completed') {
            return response()->json([
                'success' => true,
                'status' => 'completed'
            ]);
        }

        // Gọi API Momo để kiểm tra trạng thái
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->get($this->endpoint . '/v2/gateway/api/status', [
            'partnerCode' => $this->partnerCode,
            'requestId' => $payment->transaction_id,
            'orderId' => $payment->payment_details['orderId']
        ]);

        $result = $response->json();

        if ($result['resultCode'] == 0) {
            $payment->update([
                'status' => 'completed',
                'payment_details' => array_merge($payment->payment_details, [
                    'transId' => $result['transId'],
                    'responseTime' => $result['responseTime']
                ])
            ]);

            return response()->json([
                'success' => true,
                'status' => 'completed'
            ]);
        }

        return response()->json([
            'success' => true,
            'status' => 'pending'
        ]);
    }

    public function return(Request $request)
    {
        $payment = Payment::where('transaction_id', $request->requestId)->first();

        if (!$payment) {
            return redirect('/cart')->with('error', 'Không tìm thấy thông tin thanh toán');
        }

        if ($request->resultCode == 0) {
            $payment->update([
                'status' => 'completed',
                'payment_details' => array_merge($payment->payment_details, [
                    'transId' => $request->transId,
                    'responseTime' => $request->responseTime
                ])
            ]);

            return redirect('/cart')
                ->with('success', 'Thanh toán thành công');
        }

        return redirect('/cart')
            ->with('error', 'Thanh toán thất bại: ' . $request->message);
    }

    public function ipn(Request $request)
    {
        Log::info('Momo IPN:', $request->all());

        $payment = Payment::where('transaction_id', $request->requestId)->first();

        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        if ($request->resultCode == 0) {
            $payment->update([
                'status' => 'completed',
                'payment_details' => array_merge($payment->payment_details, [
                    'transId' => $request->transId,
                    'responseTime' => $request->responseTime
                ])
            ]);
        }

        return response()->json(['success' => true]);
    }
}
