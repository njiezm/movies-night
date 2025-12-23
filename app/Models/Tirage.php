<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tirage extends Model
{
    use HasFactory;
    
    protected $fillable = ['title', 'dotation_id', 'date', 'winner_id'];
    
    public function dotation()
    {
        return $this->belongsTo(Dotation::class);
    }
    
    public function winner()
    {
        return $this->belongsTo(Participant::class, 'winner_id');
    }
}