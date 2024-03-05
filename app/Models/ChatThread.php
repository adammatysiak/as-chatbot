<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatThread extends Model
{
    protected $table = 'chat_threads';

    protected $fillable = [
        'conversation_uuid',
        'thread_id',
    ];

}