<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;


//Pivot model
class Participant extends Pivot
{
    use HasFactory;

    protected $table = 'participants';
    public $timestamps = false;

    protected $casts=[
        'joined_at'=>'datetime'
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
