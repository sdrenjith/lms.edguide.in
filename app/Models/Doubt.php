<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doubt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'message', 'reply', 'replied_at',
    ];

    protected $dates = ['replied_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 