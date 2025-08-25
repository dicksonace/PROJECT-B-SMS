<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    protected $fillable = ['sender_id', 'receiver' ,'content', 'idempotency_key','delivered_at', 'processed', 'status'];
}
 