<?php

namespace App\Http\Controllers;

use App\Helpers\APIResponse;
use App\Http\Controllers\Controller;
use App\Models\News;
use App\Helpers\Common;
use App\Services\NewService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class NewController extends Controller
{
    protected $newService;
    public function __construct(NewService $newService)
    {
        $this->newService = $newService;
    }
    // News
    public function index(Request $request)
    {
        $params = $request->only([
            'q',
            'status',
            'sort_by',
            'sort_order',
            'per_page',
            'take',
        ]);

        $result = $this->newService->getNews($params);

        if (!$result) {
            return ApiResponse::success([], 'Failed to fetch news', 500);
        }

        return ApiResponse::success($result['items'], 'Fetched successfully', 200, $result['pagination']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'required|string',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
            'image' => 'required|string' // base64 image
        ]);

        try {
            // Handle base64 image using Common helper
            if (isset($validated['image'])) {
                $validated['image'] = Common::storeBase64Image($validated['image'], 'news');
            }

            $news = News::create($validated);

            Log::info('News created successfully', [
                'news_id' => $news->id,
                'title' => $news->title
            ]);

            return ApiResponse::success($news->transform(), 'News created successfully', 201);
        } catch (\Exception $e) {
            Log::error('Failed to create news', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return ApiResponse::error('Failed to create news', 500);
        }
    }

    public function show(string $id)
    {
        try {
            $news = $this->newService->getNew($id);
            return ApiResponse::success($news->transform(), 'News fetched successfully', 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return APIResponse::error(__('get data failed'), 500);
        }
    }

    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'required|string',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
            'image' => 'nullable|string' // base64 image
        ]);

        try {
            $data = [
                'title' => $validated['title'],
                'summary' => $validated['summary'],
                'content' => $validated['content'],
                'status' => $validated['status'],
            ];

            // Handle base64 image using Common helper
            if (isset($validated['image']) && !is_null($validated['image'])) {
                // Delete old image if exists
                if ($news->image) {
                    Storage::disk('public')->delete($news->image);
                }
                $data['image'] = Common::storeBase64Image($validated['image'], 'news');
            }

            $news->update($data);

            Log::info('News updated successfully', [
                'news_id' => $news->id,
                'title' => $news->title
            ]);

            return ApiResponse::success($news->transform(), 'News updated successfully', 200);
        } catch (\Exception $e) {
            Log::error('Failed to update news', [
                'error' => $e->getMessage(),
                'news_id' => $news->id,
                'trace' => $e->getTraceAsString()
            ]);

            return ApiResponse::error('Failed to update news', 500);
        }
    }

    public function destroy(News $news)
    {
        try {
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }

            $news->delete();

            Log::info('News deleted successfully', [
                'news_id' => $news->id,
                'title' => $news->title
            ]);

            return APIResponse::success([], 'News deleted successfully', 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete news', [
                'error' => $e->getMessage(),
                'news_id' => $news->id,
                'trace' => $e->getTraceAsString()
            ]);

            return ApiResponse::error('Failed to delete news', 500);
        }
    }
}
