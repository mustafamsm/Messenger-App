<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'label','last_message_id','type'];




    //many to many relationship
    public function participants()
    {
        return $this->belongsToMany(User::class, 'participants')
        ->withPivot(
            [
                'role','joined_at'
            ]
        );
    }


    //one to many relationship
    public function messages()
    {
        return $this->hasMany(Message::class, 'conversation_id', 'id');
         
    }

    //the user who created the conversation

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //the last message in the conversation
    public function lastMessage()
    {
        return $this->belongsTo(Message::class, 'last_message_id', 'id')
        ->withDefault();
             
    }
}
