<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return $user->conversations()->with(['lastMessage',
        'participants'=>function($q) use ($user){
           $q->where('user_id','<>',$user->id);
        }
        ])->paginate();
    }

    public function show(Conversation $conversation): Conversation
    {
        return $conversation->load('participants');
    }
    public function  addParticipant(Request $request, Conversation $conversation): void
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);
        $conversation->participants()->attach($request->post('user_id'), [
            'joined_at' => now()
        ]);
    }

    public function  removeParticipant(Request $request, Conversation $conversation): void
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);
        $conversation->participants()->detach($request->post('user_id'));
    }
}
