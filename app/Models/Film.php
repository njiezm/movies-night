<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    protected $fillable = [
        'title', 'description', 'slug', 'vignette', 'qrcode'
    ];

    public function participants()
{
    return $this->belongsToMany(Participant::class, 'participant_film');
}

}
