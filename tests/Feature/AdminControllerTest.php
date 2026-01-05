<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Participant;
use App\Models\Dotation;
use App\Models\Tirage;
use App\Models\Film;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase; // Important pour réinitialiser la BDD entre chaque test

    /**
     * Fonction utilitaire pour créer un participant avec un slug généré automatiquemen
     */
    private function creerParticipant($prenom, $nom, $email)
    {
        // Génère un slug à partir du prénom et du nom (ex: "jean-dupont")
        $slug = strtolower($prenom . '-' . $nom);
        
        return Participant::create([
            'firstname' => $prenom,
            'lastname' => $nom,
            'email' => $email,
            'slug' => $slug
        ]);
    }

    /**
     * Test 1 : Vérifie qu'un administrateur peut créer un film avec succès
     * Scénario : Un admin se connecte, remplit le formulaire de création de film avec des données valides
     * Résultat attendu : Le film est créé en BDD et l'utilisateur est redirigé vers la liste des films
     */
    public function test_un_admin_peut_creer_un_film_avec_succes()
    {
        // 1. Simuler la connexion d'un administrateur avec le plein accès
        $response = $this->post('/admin/login', [
            'access_code' => '123456' // Code d'accès complet pour l'administrateur
        ]);
        $response->assertRedirect('/admin');

        // 2. Envoyer une requête POST pour créer un film avec des données valides et réalistes
        $donneesFilm = [
            'title' => 'Le Fabuleux Destin d\'Amélie Poulain',
            'description' => 'Une jeune femme décide de changer la vie des autres pour le meilleur, tout en cherchant l\'amour.',
            'start_date' => now()->addDay()->format('Y-m-d'),
            'end_date' => now()->addWeeks(2)->format('Y-m-d'),
        ];

        $response = $this->post('/admin/films', $donneesFilm);

        // 3. Vérifier que la réponse est une redirection vers la liste des films
        $response->assertRedirect('/admin/films');

        // 4. Vérifier que le film existe bien dans la base de données
        $this->assertDatabaseHas('films', [
            'title' => 'Le Fabuleux Destin d\'Amélie Poulain',
        ]);

        // 5. Vérifier qu'un message de succès est bien en session
        $response->assertSessionHas('success', 'Film ajouté avec succès !');
    }

    /**
     * Test 2 : Vérifie que la création d'un film échoue sans titre
     * Scénario : Un admin essaie de créer un film sans fournir de titre
     * Résultat attendu : Erreur de validation et aucun film n'est créé en BDD
     */
    public function test_la_creation_dun_film_echoue_sans_titre()
    {
        // 1. Connexion admin avec le code d'accès complet
        $this->post('/admin/login', ['access_code' => '123456']);

        // 2. Envoyer des données invalides (sans le titre obligatoire)
        $response = $this->post('/admin/films', [
            'description' => 'Un film passionnant mais sans titre',
        ]);

        // 3. Vérifier qu'il y a une erreur de validation pour le champ 'title'
        $response->assertSessionHasErrors('title');

        // 4. Vérifier qu'aucun film n'a été créé en base de données
        $this->assertDatabaseMissing('films', [
            'description' => 'Un film passionnant mais sans titre',
        ]);
    }

    /**
     * Test 3 : Vérifie qu'un utilisateur avec accès limité ne peut pas voir les dotations
     * Scénario : Un utilisateur se connecte avec un accès limité et essaie d'accéder à la page des dotations
     * Résultat attendu : Il est redirigé vers la page d'accueil admin avec un message d'erreur
     */
    public function test_un_utilisateur_acces_limite_ne_peut_pas_voir_les_dotations()
    {
        // 1. Simuler la connexion d'un utilisateur avec l'accès limité
        $response = $this->post('/admin/login', [
            'access_code' => '654321' // Code d'accès limité
        ]);
        $response->assertRedirect('/admin');

        // 2. Tenter d'accéder à la page des dotations
        $response = $this->get('/admin/dotations');

        // 3. Vérifier que l'utilisateur est redirigé vers la page d'accueil admin avec un message d'erreur
        $response->assertRedirect('/admin')
                 ->assertSessionHas('error', "Vous n'avez pas accès à cette section");
    }

    /**
     * Test 4 : Vérifie que le calcul de la quantité restante d'une dotation fonctionne correctement
     * Scénario : Une dotation avec 5 lots est créée, 2 gagnants sont tirés au sort
     * Résultat attendu : La quantité restante est de 3 (5 - 2)
     */
    public function test_le_calcul_de_la_quantite_restante_dune_dotation_fonctionne_correctement()
    {
        // 1. Créer une dotation avec une quantité de 5 lots
        $dotation = Dotation::create([
            'title' => 'Paniers gourmands de Noël',
            'dotationdate' => now()->addMonth(),
            'quantity' => 5
        ]);

        // 2. Créer 2 participants pour les tirages en utilisant notre fonction utilitaire
        $gagnant1 = $this->creerParticipant('Jean', 'Dupont', 'jean.dupont@example.com');
        $gagnant2 = $this->creerParticipant('Marie', 'Martin', 'marie.martin@example.com');

        // 3. Créer 2 tirages pour cette dotation et leur assigner un gagnant
        Tirage::create([
            'title' => 'Tirage des paniers gourmands - Première semaine',
            'dotation_id' => $dotation->id,
            'date' => now(),
            'winner_id' => $gagnant1->id
        ]);
        Tirage::create([
            'title' => 'Tirage des paniers gourmands - Deuxième semaine',
            'dotation_id' => $dotation->id,
            'date' => now(),
            'winner_id' => $gagnant2->id
        ]);

        // 4. Rafraîchir le modèle depuis la BDD pour s'assurer que les accesseurs sont bien appelés
        $dotation->refresh();

        // 5. Vérifier que la quantité restante est bien 3 (5 - 2)
        $this->assertEquals(3, $dotation->remaining_count);
    }

    /**
     * Test 5 : Vérifie que le tirage au sort sélectionne un gagnant et retourne les bonnes informations en JSON
     * Scénario : Un admin effectue un tirage au sort pour une dotation avec plusieurs participants
     * Résultat attendu : Un gagnant est sélectionné, enregistré en BDD et ses informations sont retournées en JSON
     */
    public function test_le_tirage_au_sort_selectionne_un_gagnant_et_retourne_les_bonnes_infos()
    {
        // 1. Connexion admin avec le code d'accès complet
        $this->post('/admin/login', ['access_code' => '123456']);

        // 2. Créer les données nécessaires : une dotation, un tirage, et des participants
        $dotation = Dotation::create([
            'title' => 'Bons de réduction pour le cinéma',
            'dotationdate' => now()->addMonth(),
            'quantity' => 2
        ]);
        
        $tirage = Tirage::create([
            'title' => 'Tirage mensuel des bons de réduction',
            'dotation_id' => $dotation->id,
            'date' => now()
        ]);
        
        // Créer 3 participants avec des informations françaises
        $participants = collect([
            $this->creerParticipant('Pierre', 'Lefebvre', 'pierre.lefebvre@example.com'),
            $this->creerParticipant('Sophie', 'Bernard', 'sophie.bernard@example.com'),
            $this->creerParticipant('Thomas', 'Petit', 'thomas.petit@example.com')
        ]);

        // 3. Envoyer la requête pour effectuer le tirage au sort
        $response = $this->postJson("/admin/tirages/{$tirage->id}/draw");

        // 4. Vérifier que la réponse est un JSON succès avec la structure attendue
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ])
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'winner_firstname',
                     'winner_lastname',
                     'winner_email'
                 ]);

        // 5. Vérifier que le gagnant a bien été enregistré dans le tirage en BDD
        $tirage->refresh();
        $this->assertNotNull($tirage->winner_id);
        $this->assertTrue($participants->contains('id', $tirage->winner_id));
    }
}