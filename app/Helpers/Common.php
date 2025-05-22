<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Common
{

    public static function storeBase64Image(string $base64Image, string $folder = 'products'): ?string
    {
        // Tách phần "data:image/jpeg;base64,..."
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
            $extension = $matches[1]; // jpg, png, etc.
            $base64Str = substr($base64Image, strpos($base64Image, ',') + 1);
            $base64Str = str_replace(' ', '+', $base64Str); // fix spaces

            try {
                $imageData = base64_decode($base64Str);

                // Tạo tên file duy nhất
                $filename = $folder . '/' . Str::uuid() . '.' . $extension;

                // Lưu vào disk 'public'
                Storage::disk('public')->put($filename, $imageData);

                return $filename; // trả về path để lưu DB
            } catch (\Exception $e) {
                Log::error('Image decode/store failed: ' . $e->getMessage());
                return null;
            }
        }

        return null;
    }

    public static function deleteImage(string $path): bool
    {
        try {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                return true;
            }
        } catch (\Exception $e) {
            Log::error('Image delete failed: ' . $e->getMessage());
        }

        return false;
    }

    public static function getImageUrl(string $path): string
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::url($path);
        }

        return '';
    }

    public static function responseImage($path = ''): string
    {
        return env('RESPONE_IMAGE_URL') . $path;
    }

    public static function generateQr(?float $amount, ?string $descrition, ?string $accountName): string
    {
        if (empty($amount) || empty($descrition)) {
            return 'https://img.vietqr.io/image/MB-011911142003-compact2.png';
        }
        return 'https://img.vietqr.io/image/MB-011911142003-compact2.png' .
            '?amount=' . $amount . '&addInfo=' . $descrition . '&accountName=' . $accountName;
    }


    public static function getPagination($items, $perPage = 10)
    {
        $currentPage = $items->currentPage();
        $totalPages = $items->lastPage();
        $totalItems = $items->total();

        return [
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'total_items' => $totalItems,
            'per_page'    => $perPage,
        ];
    }
}
