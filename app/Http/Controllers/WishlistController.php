<?php

namespace App\Http\Controllers;

use App\Helpers\APIResponse;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        try {
            $wishlists = Wishlist::with('product')
                ->where('user_id', auth()->id())
                ->get()
                ->map(function ($wishlist) {
                    return [
                        'id' => $wishlist->id,
                        'product' => $wishlist->product->transform(),
                        'created_at' => $wishlist->created_at
                    ];
                });

            return APIResponse::success($wishlists, 'Wishlist fetched successfully');
        } catch (\Exception $e) {
            return APIResponse::error('Failed to fetch wishlist', 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id'
            ]);

            $wishlist = Wishlist::create([
                'user_id' => auth()->id(),
                'product_id' => $request->product_id
            ]);

            return APIResponse::success($wishlist, 'Product added to wishlist successfully');
        } catch (\Exception $e) {
            return APIResponse::error('Failed to add product to wishlist', 500);
        }
    }

    public function destroy($id)
    {
        try {
            $wishlist = Wishlist::where('user_id', auth()->id())
                ->where('id', $id)
                ->first();

            if (!$wishlist) {
                return APIResponse::error('Wishlist item not found', 404);
            }

            $wishlist->delete();
            return APIResponse::success(null, 'Product removed from wishlist successfully');
        } catch (\Exception $e) {
            return APIResponse::error('Failed to remove product from wishlist', 500);
        }
    }
}
