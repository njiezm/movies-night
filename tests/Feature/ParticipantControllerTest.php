<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Participant;
use App\Models\Film;

class ParticipantControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Fonction utilitaire pour créer un participant avec un slug généré automatiquement
     */
    private function creerParticipant($prenom, $nom, $email = null, $telephone = null)
    {
        // Générer un slug unique
        $slug = strtolower($prenom . '-' . $nom . '-' . uniqid());
        
        return Participant::create([
            'firstname' => $prenom,
            'lastname' => $nom,
            'email' => $email ?? $prenom . '.' . $nom . '@example.com',
            'telephone' => $telephone ?? '06' . rand(10000000, 99999999),
            'slug' => $slug  // Ajout du champ slug
        ]);
    }

    /**
     * Test 1 : Vérifie que le modèle Participant fonctionne correctement
     */
    public function test_le_modele_participant_fonctionne_correctement()
    {
        // Créer des participants de test
        $participant1 = $this->creerParticipant('Jean', 'Dupont');
        $participant2 = $this->creerParticipant('Marie', 'Martin');
        
        // Créer des films et les associer aux participants
        $film1 = Film::create(['title' => 'Film A', 'slug' => 'film-a']);
        $film2 = Film::create(['title' => 'Film B', 'slug' => 'film-b']);
        
        $participant1->films()->attach($film1->id);
        $participant2->films()->attach([$film1->id, $film2->id]);
        
        // Vérifier que les relations fonctionnent correctement
        $this->assertEquals(1, $participant1->films()->count());
        $this->assertEquals(2, $participant2->films()->count());
        $this->assertTrue($participant1->films->contains($film1));
        $this->assertTrue($participant2->films->contains($film1));
        $this->assertTrue($participant2->films->contains($film2));
    }

    /**
     * Test 2 : Vérifie qu'un participant peut être créé avec les bonnes données
     */
    public function test_un_participant_peut_etre_cree_avec_les_bonnes_donnees()
    {
        // Créer des films pour l'association
        $film1 = Film::create(['title' => 'Film A', 'slug' => 'film-a']);
        $film2 = Film::create(['title' => 'Film B', 'slug' => 'film-b']);
        
        // Données du participant
        $donneesParticipant = [
            'lastname' => 'Durand',
            'firstname' => 'Pierre',
            'email' => 'pierre.durand@example.com',
            'telephone' => '0612345678',
            'zipcode' => '75001',
            'optin' => true,
            'bysms' => true,
            'byemail' => false,
        ];
        
        // Générer un slug unique pour le participant
        $slug = strtolower($donneesParticipant['firstname'] . '-' . $donneesParticipant['lastname'] . '-' . uniqid());
        $donneesParticipant['slug'] = $slug;
        
        // Créer le participant directement
        $participant = Participant::create($donneesParticipant);
        
        // Associer les films
        $participant->films()->attach([$film1->id, $film2->id]);
        
        // Vérifier que le participant a été créé en BDD
        $this->assertDatabaseHas('participants', [
            'lastname' => 'Durand',
            'firstname' => 'Pierre',
            'email' => 'pierre.durand@example.com',
            'telephone' => '0612345678',
            'slug' => $slug  // Vérifier que le slug a été enregistré
        ]);
        
        // Vérifier que les associations avec les films ont été créées
        $this->assertEquals(2, $participant->films()->count());
        $this->assertTrue($participant->films->contains($film1->id));
        $this->assertTrue($participant->films->contains($film2->id));
    }

    /**
     * Test 3 : Vérifie que la création échoue avec des données invalides
     */
    public function test_la_creation_echoue_avec_des_donnees_invalides()
    {
        // Tenter de créer un participant sans nom (qui devrait être requis selon la logique métier)
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Participant::create([
            'firstname' => 'Pierre',
            'email' => 'pierre.durand@example.com',
            'slug' => 'pierre-test'  // Ajout du slug pour éviter l'erreur de contrainte
        ]);
    }

    /**
     * Test 4 : Vérifie qu'un participant peut être modifié avec succès
     */
    public function test_un_participant_peut_etre_modifie_avec_succes()
    {
        // Créer un participant et des films
        $participant = $this->creerParticipant('Jean', 'Dupont', 'jean.dupont@example.com', '0612345678');
        $film1 = Film::create(['title' => 'Film A', 'slug' => 'film-a']);
        $film2 = Film::create(['title' => 'Film B', 'slug' => 'film-b']);
        
        // Associer initialement un seul film
        $participant->films()->attach($film1->id);
        
        // Données de modification
        $donneesModification = [
            'email' => 'jean.dupont.modifie@example.com',
            'zipcode' => '75002',
            'bysms' => false,
            'byemail' => true
        ];
        
        // Mettre à jour le participant
        $participant->update($donneesModification);
        
        // Changer les films associés
        $participant->films()->sync([$film2->id]);
        
        // Vérifier que les données ont été mises à jour
        $this->assertDatabaseHas('participants', [
            'id' => $participant->id,
            'email' => 'jean.dupont.modifie@example.com',
            'zipcode' => '75002',
            'bysms' => false,
            'byemail' => true
        ]);
        
        // Vérifier que l'association avec les films a été mise à jour
        $participant->refresh();
        $this->assertEquals(1, $participant->films()->count());
        $this->assertTrue($participant->films->contains($film2->id));
        $this->assertFalse($participant->films->contains($film1->id));
    }

    /**
     * Test 5 : Vérifie qu'un participant peut être supprimé avec succès
     */
    public function test_un_participant_peut_etre_supprime_avec_succes()
    {
        // Créer un participant
        $participant = $this->creerParticipant('Jean', 'Dupont');
        
        // Supprimer le participant
        $participant->delete();
        
        // Vérifier que le participant a été supprimé
        $this->assertDatabaseMissing('participants', [
            'id' => $participant->id
        ]);
    }
}