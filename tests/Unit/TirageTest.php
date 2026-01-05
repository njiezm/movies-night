<?php

namespace Tests\Unit;

use App\Models\Tirage;
use App\Models\Dotation;
use App\Models\Participant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

class TirageTest extends TestCase
{

    public function le_modele_tirage_contient_les_bons_attributs_fillable()
    {
        $tirage = new Tirage();

        $this->assertEquals(
            ['title', 'dotation_id', 'date', 'winner_id'],
            $tirage->getFillable()
        );
    }

    public function un_tirage_appartient_a_une_dotation()
    {
        $tirage = new Tirage();

        $this->assertInstanceOf(
            BelongsTo::class,
            $tirage->dotation()
        );

        $this->assertInstanceOf(
            Dotation::class,
            $tirage->dotation()->getRelated()
        );
    }


    public function un_tirage_appartient_a_un_participant_gagnant()
    {
        $tirage = new Tirage();

        $this->assertInstanceOf(
            BelongsTo::class,
            $tirage->winner()
        );

        $this->assertInstanceOf(
            Participant::class,
            $tirage->winner()->getRelated()
        );
    }
}
