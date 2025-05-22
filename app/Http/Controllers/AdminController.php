<?php

namespace App\Http\Controllers;

use App\Helpers\APIResponse;
use App\Helpers\Common;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\News;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    // Dashboard
    public function dashboard()
    {
        $stats = [
            'total_orders' => Order::count(),
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_revenue' => Order::where('order_status', 'completed')->sum('total_amount'),
            'recent_orders' => Order::with('orderItems')->latest()->take(5)->get(),
            'top_products' => Product::withCount('orderItems')
                ->orderBy('order_items_count', 'desc')
                ->take(5)
                ->get()->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'image' => Common::responseImage($product->image),
                        'total_sold' => $product->orderItems_count,
                    ];
                }),
        ];

        return APIResponse::success($stats, 'Dashboard stats fetched successfully');
    }

    // Orders
    public function orders(Request $request)
    {
        $query = Order::with('items');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', "%{$request->search}%")
                    ->orWhere('customer_name', 'like', "%{$request->search}%")
                    ->orWhere('customer_email', 'like', "%{$request->search}%");
            });
        }

        $orders = $query->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function showOrder(Order $order)
    {
        $order->load('items');
        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    public function updateOrder(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        $order->update($validated);

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    // Products
    public function products(Request $request)
    {
        $query = Product::with('category');

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        $products = $query->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function createProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:active,inactive',
            'image' => 'required|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    public function showProduct(Product $product)
    {
        $product->load('category');
        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    public function updateProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        $product->update($validated);

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    public function deleteProduct(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }

    // Categories
    public function categories(Request $request)
    {
        $query = Category::withCount('products');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        $categories = $query->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function createCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:active,inactive'
        ]);

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    public function showCategory(Category $category)
    {
        $category->load('products');
        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    public function updateCategory(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:active,inactive'
        ]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    public function deleteCategory(Category $category)
    {
        if ($category->products()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with associated products'
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }

    // News
    public function news(Request $request)
    {
        $query = News::query();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('content', 'like', "%{$request->search}%");
            });
        }

        $news = $query->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    public function createNews(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'required|string',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
            'image' => 'required|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('news', 'public');
            $validated['image'] = $path;
        }

        $news = News::create($validated);

        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    public function showNews(News $news)
    {
        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    public function updateNews(Request $request, News $news)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'required|string',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }
            $path = $request->file('image')->store('news', 'public');
            $validated['image'] = $path;
        }

        $news->update($validated);

        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    public function deleteNews(News $news)
    {
        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }
        $news->delete();

        return response()->json([
            'success' => true,
            'message' => 'News article deleted successfully'
        ]);
    }

    // Users
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->role) {
            $query->where('role', $request->role);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $users = $query->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,user',
            'status' => 'required|in:active,inactive'
        ]);

        $validated['password'] = bcrypt($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function showUser(User $user)
    {
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,user',
            'status' => 'required|in:active,inactive'
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete your own account'
            ], 422);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}
