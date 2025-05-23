<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MomoService
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

    public function createPayment($amount, $orderInfo, $returnUrl, $notifyUrl)
    {
        $requestId = time() . "";
        $orderId = time() . "";
        $requestType = "payWithATM";
        $extraData = "";

        // Tạo rawHash theo thứ tự như trong code mẫu
        $rawHash = "accessKey=" . $this->accessKey;
        $rawHash .= "&amount=" . $amount;
        $rawHash .= "&extraData=" . $extraData;
        $rawHash .= "&ipnUrl=" . $notifyUrl;
        $rawHash .= "&orderId=" . $orderId;
        $rawHash .= "&orderInfo=" . $orderInfo;
        $rawHash .= "&partnerCode=" . $this->partnerCode;
        $rawHash .= "&redirectUrl=" . $returnUrl;
        $rawHash .= "&requestId=" . $requestId;
        $rawHash .= "&requestType=" . $requestType;

        Log::debug("MOMO ▶ rawHash: " . $rawHash);

        $signature = hash_hmac("sha256", $rawHash, $this->secretKey);
        Log::debug("MOMO ▶ signature: " . $signature);

        $data = [
            'partnerCode' => $this->partnerCode,
            'partnerName' => "Test",
            'storeId' => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $returnUrl,
            'ipnUrl' => $notifyUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        ];

        Log::debug("MOMO ▶ request payload: " . json_encode($data));

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Content-Length' => strlen(json_encode($data))
        ])->timeout(5)->post($this->endpoint . '/v2/gateway/api/create', $data);

        Log::debug("MOMO ▶ response body: " . $response->body());

        return $response->json();
    }

    public function verifyPayment($requestId, $orderId, $amount, $orderInfo, $orderType, $transId, $resultCode, $message, $payType, $signature)
    {
        // Tạo rawHash theo thứ tự như trong code mẫu
        $rawHash = "accessKey=" . $this->accessKey;
        $rawHash .= "&amount=" . $amount;
        $rawHash .= "&extraData=";
        $rawHash .= "&message=" . $message;
        $rawHash .= "&orderId=" . $orderId;
        $rawHash .= "&orderInfo=" . $orderInfo;
        $rawHash .= "&orderType=" . $orderType;
        $rawHash .= "&partnerCode=" . $this->partnerCode;
        $rawHash .= "&payType=" . $payType;
        $rawHash .= "&requestId=" . $requestId;
        $rawHash .= "&resultCode=" . $resultCode;
        $rawHash .= "&transId=" . $transId;

        $expectedSignature = hash_hmac("sha256", $rawHash, $this->secretKey);

        return $signature === $expectedSignature;
    }
}
