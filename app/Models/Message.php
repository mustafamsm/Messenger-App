<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['conversation_id', 'user_id', 'body', 'type'];
    protected $casts=[
        'body'=>'json'
    ];


    //the users who have recipt the message
    //many to many relationship
     public function recipients(){
        return $this->belongsToMany(User::class, 'recipients')
        ->withPivot([
            'read_at','deleted_at'
        ]);
     }

    //one to many relationship
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }


    //the user whon sent the message
    public function user(){
        return $this->belongsTo(User::class)->withDefault(
            [
                'name' => __('User')
            ]
        );
    }
}
