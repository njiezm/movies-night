<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Base\Genesys;

class Participant extends Model
{
    protected $fillable = [
        'lastname', 'firstname', 'email', 'telephone',
        'zipcode', 'optin', 'bysms', 'byemail', 'slug', 'source'
    ];

    // Relations
    public function films()
    {
        return $this->belongsToMany(Film::class, 'participant_film');
    }

    public function dotations()
    {
        return $this->belongsToMany(Dotation::class, 'participant_dotation');
    }
   
}
