<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Recipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return $user->conversations()->with(['lastMessage',
            'participants' => function ($q) use ($user) {
                $q->where('user_id', '<>', $user->id);
            }
        ])->withCount(['recipients as new_messages' => function ($q) use ($user) {
            $q->where('recipients.user_id', $user->id)
                ->whereNull('read_at');
        }
        ])
            ->paginate();
    }

    public function show($id): Conversation
    {
        $user = Auth::user();
        return $user->conversations()->with(['lastMessage',
            'participants' => function ($q) use ($user) {
                $q->where('user_id', '<>', $user->id);
            }
        ])->withCount(['recipients as new_messages' => function ($q) use ($user) {
            $q->where('recipients.user_id', $user->id)
                ->whereNull('read_at');
        }
        ])->firstOrFail($id);

    }

    function addParticipant(Request $request, Conversation $conversation): void
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);
        $conversation->participants()->attach($request->post('user_id'), [
            'joined_at' => now()
        ]);
    }

    public
    function removeParticipant(Request $request, Conversation $conversation): void
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);
        $conversation->participants()->detach($request->post('user_id'));
    }

    public function markAsRead($id)
    {
        Recipient::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->whereRaw('message_id IN (SELECT id FROM messages WHERE conversation_id = ?)', [$id])->update([
            'read_at' => now()
        ]);
        return ['status' => 'success'];
    }
    public function destroy($id)
    {

        Recipient::where('user_id', Auth::id())
            ->whereRaw('message_id IN (SELECT id FROM messages WHERE conversation_id = ?)', [$id])->delete();
        return ['status' => 'success'];
    }
}
