<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class whitelist extends Model
{
    use HasFactory;

    protected $fillable = ['sender_id'];
    
} 
