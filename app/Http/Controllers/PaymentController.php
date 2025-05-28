<?php

namespace App\Http\Controllers;

use App\Services\MomoService;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;

class PaymentController extends Controller
{
    protected $momoService;

    public function __construct(MomoService $momoService)
    {
        $this->momoService = $momoService;
    }

    public function create(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'orderInfo' => 'required|string'
        ]);

        $returnUrl = route('payment.return');
        $notifyUrl = route('payment.notify');

        $response = $this->momoService->createPayment(
            $request->amount,
            $request->orderInfo,
            $returnUrl,
            $notifyUrl
        );

        if ($response['resultCode'] === 0) {
            return response()->json([
                'success' => true,
                'paymentId' => $response['requestId'],
                'qrCodeUrl' => $response['qrCodeUrl'] ?? $response['payUrl']
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $response['message'] ?? 'Không thể tạo thanh toán'
        ], 400);
    }

    public function createPayment(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'description' => 'required|string|max:255'
        ]);

        // TODO: Implement actual payment gateway integration
        $payment = [
            'id' => uniqid('PAY-'),
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'description' => $validated['description'],
            'status' => 'pending'
        ];

        return response()->json([
            'message' => 'Payment created successfully',
            'payment' => $payment
        ]);
    }

    public function checkStatus($paymentId)
    {
        $payment = Payment::where('payment_id', $paymentId)->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thanh toán'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'status' => $payment->status
        ]);
    }

    public function return(Request $request)
    {
        $isValid = $this->momoService->verifyPayment(
            $request->requestId,
            $request->orderId,
            $request->amount,
            $request->orderInfo,
            $request->orderType,
            $request->transId,
            $request->resultCode,
            $request->message,
            $request->payType,
            $request->signature
        );

        if ($isValid && $request->resultCode === 0) {
            $payment = Payment::where('payment_id', $request->requestId)->first();
            if ($payment) {
                $payment->update([
                    'status' => 'completed',
                    'transaction_id' => $request->transId
                ]);

                if ($payment->order) {
                    $payment->order->update(['status' => 'paid']);
                }
            }

            return redirect()->route('payment.success');
        }

        return redirect()->route('payment.failed');
    }

    public function notify(Request $request)
    {
        $isValid = $this->momoService->verifyPayment(
            $request->requestId,
            $request->orderId,
            $request->amount,
            $request->orderInfo,
            $request->orderType,
            $request->transId,
            $request->resultCode,
            $request->message,
            $request->payType,
            $request->signature
        );

        if ($isValid && $request->resultCode === 0) {
            $payment = Payment::where('payment_id', $request->requestId)->first();
            if ($payment) {
                $payment->update([
                    'status' => 'completed',
                    'transaction_id' => $request->transId
                ]);

                if ($payment->order) {
                    $payment->order->update(['status' => 'paid']);
                }
            }

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 400);
    }
}
