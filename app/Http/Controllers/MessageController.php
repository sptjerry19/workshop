<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Events\NewMessage;
use App\Helpers\APIResponse;
use Exception;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        try {
            $senderId = $request->get('sender_id');
            $messages = Message::orderBy('created_at', 'asc')
                ->where('from_sender_id', $senderId)
                ->orWhere('to_sender_id', $senderId)
                ->paginate(20);

            return response()->json($messages);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return APIResponse::error(__("get message failed"), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'content' => 'required|string|max:1000',
                'sender_id' => 'required|string',
            ]);

            $message = Message::create([
                'content' => $request->content,
                'from_sender_id' => $request->sender_id,
                'to_sender_id' => 'admin',
            ]);

            broadcast(new NewMessage($message))->toOthers();

            return response()->json($message);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return APIResponse::error(__("create message failed"), 500);
        }
    }

    public function adminReply(Request $request, Message $message)
    {
        try {
            $request->validate([
                'content' => 'required|string|max:1000',
                'sender_id' => 'required|string'
            ]);

            $reply = Message::create([
                'content' => $request->content,
                'from_sender_id' => 'admin',
                'to_sender_id' => $request->sender_id
            ]);

            broadcast(new NewMessage($reply))->toOthers();

            return response()->json($reply);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return APIResponse::error(__("create message failed"), 500);
        }
    }
}
