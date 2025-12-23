<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dotation extends Model
{
    protected $fillable = ['title', 'dotationdate'];

    public function participants()
    {
        return $this->belongsToMany(Participant::class);
    }
}
