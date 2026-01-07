<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    protected $fillable = [
        'title', 'description', 'slug', 'vignette', 'qrcode', 'start_date', 'end_date'
    ];

    public function participants()
    {
        return $this->belongsToMany(Participant::class, 'participant_film');
    }
    
    public function tirage()
    {
        return $this->hasOne(Tirage::class);
    }
}