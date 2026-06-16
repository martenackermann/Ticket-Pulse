<?php

namespace App\Models;

use App\Enums\CardStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable = ['board_id', 'title', 'description', 'status', 'position'];

    protected $casts = [
        'status' => CardStatus::class,
    ];

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }
}
