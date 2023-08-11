<?php

namespace App\Http\Controllers;

use App\Events\MessageCreated;
use App\Models\Conversation;
use App\Models\Recipient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MessagesControlle extends Controller
{

    public function index($id)
    {
        $user = Auth::user();
        $conversations = $user->conversations()
            ->with([
                'participants' => function ($builder) use ($user) {
                    $builder->where('user_id', '!=', $user->id);
                }
            ])
            ->findOrFail($id);
        return [
            'conversation' => $conversations,
            'messages' => $conversations->messages()->with('user')->paginate()
        ];
    }


    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'conversation_id' => [
                Rule::requiredIf(function () use ($request) {
                    return !$request->has('user_id');
                }),
                'integer',
                'exists:conversations,id'
            ],
            'user_id' => [
                Rule::requiredIf(function () use ($request) {
                    return !$request->has('conversation_id');
                }),
                'integer',
                'exists:users,id',
            ]
        ]);
        $user = Auth::user();

        $conversations_id = $request->post('conversation_id');
        $user_id = $request->post('user_id');


        DB::beginTransaction();

        try {
            if ($conversations_id) {
                $conversation = $user->conversations()->findOrFail($conversations_id);
            } else {


                //if the converstion is peer 
                $conversation = Conversation::where('type', '=', 'peer')
                    ->whereHas('participants', function ($builder) use ($user_id, $user) {
                        $builder->join('participants as p', 'p.conversation_id', '=', 'participants.conversation_id')
                            ->where('p.user_id', '=', $user->id)
                            ->where('participants.user_id', '=', $user_id);
                    })->first();
            }

            if (!$conversation) {
                $conversation = Conversation::create([
                    'user_id' => $user->id, //who create the convertisaion
                    'type' => 'peer'
                ]);
                $conversation->participants()->attach([
                    $user->id => ['joined_at' => now()], //sender
                    $user_id => ['joined_at' => now()] //reciver
                ]);
            }

            $message = $conversation->messages()->create([
                'user_id' => $user->id,
                'body' => $request->post('message'),
            ]);

            //for every user in the conversation, create a recipient
            DB::statement(
                '
        INSERT INTO recipients (user_id, message_id) 
        SELECT user_id, ? FROM participants WHERE conversation_id = ?
        ',
                [$message->id, $conversation->id]
            );

            $conversation->update([
                'last_message_id' => $message->id,
            ]);
            Db::commit();
            $message->load('user');
            broadcast(new MessageCreated($message));
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

        return $message;
    }






    public function update(Request $request, $id)
    {
        //
    }



    public function destroy($id)
    {
        Recipient::where([
            'user_id' => Auth::id(),
            'message_id' => $id
        ])->delete();
        return [
            'message' => 'deleted'
        ];
    }
}
