<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    use HasFactory;

    protected $fillable = ['workspace_id', 'name'];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function cards()
    {
        return $this->hasMany(Card::class)->orderBy('position');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class)->latest();
    }
}
