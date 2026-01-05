<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dotation extends Model
{
    protected $fillable = ['title', 'dotationdate', 'quantity'];

    public function participants()
    {
        return $this->belongsToMany(Participant::class);
    }
    
    public function tirages()
    {
        return $this->hasMany(Tirage::class);
    }
    
    // Calculer le nombre de dotations attribuées
    public function getAttributedCountAttribute()
    {
        return $this->tirages()->whereNotNull('winner_id')->count();
    }
    
    // Calculer le nombre de dotations restantes
    public function getRemainingCountAttribute()
    {
        return $this->quantity - $this->attributed_count;
    }
}