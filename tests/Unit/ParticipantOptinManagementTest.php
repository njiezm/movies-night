<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OptinConfirmationNotification;
use Carbon\Carbon;

/**
 * Test unitaire complet pour la gestion des optins des participants
 * 
 * Ce fichier teste la logique métier liée aux préférences de communication
 * des participants, un aspect critique pour la conformité RGPD et le marketing.
 */
class ParticipantOptinManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Initialise les données communes à tous les tests
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Configuration initiale pour les tests
        config('app.optin_confirmation_required', true);
        config('app.optin_retention_days', 365);
    }

    /**
     * Crée un participant de test avec des options personnalisées
     */
    private function creerParticipant($options = [])
    {
        $defaultOptions = [
            'firstname' => 'Jean',
            'lastname' => 'Dupont',
            'email' => 'jean.dupont@example.com',
            'telephone' => '0612345678',
            'slug' => 'jean-dupont-' . uniqid(),
            'optin' => false,
            'bysms' => false,
            'byemail' => false,
            'zipcode' => '75001',
            'optin_confirmed_at' => null,
            'optin_confirmation_token' => null,
        ];
        
        return Participant::create(array_merge($defaultOptions, $options));
    }

    /**
     * Test 1 : Vérifie qu'un participant peut s'inscrire avec optin SMS uniquement
     * 
     * Scénario : Un participant s'inscrit et choisit de recevoir des SMS uniquement
     * Résultat attendu : Les champs optin et bysms sont à true, byemail à false
     */
    public function test_un_participant_peut_s_inscrire_avec_optin_sms_uniquement()
    {
        // Arrange & Act
        $participant = $this->creerParticipant([
            'optin' => true,
            'bysms' => true,
            'byemail' => false,
        ]);
        
        // Assert
        $this->assertTrue($participant->optin);
        $this->assertTrue($participant->bysms);
        $this->assertFalse($participant->byemail);
        $this->assertNull($participant->optin_confirmed_at);
        $this->assertNull($participant->optin_confirmation_token);
    }

    /**
     * Test 2 : Vérifie qu'un participant peut s'inscrire avec optin Email uniquement
     * 
     * Scénario : Un participant s'inscrit et choisit de recevoir des emails uniquement
     * Résultat attendu : Les champs optin et byemail sont à true, bysms à false
     */
    public function test_un_participant_peut_s_inscrire_avec_optin_email_uniquement()
    {
        // Arrange & Act
        $participant = $this->creerParticipant([
            'optin' => true,
            'bysms' => false,
            'byemail' => true,
        ]);
        
        // Assert
        $this->assertTrue($participant->optin);
        $this->assertFalse($participant->bysms);
        $this->assertTrue($participant->byemail);
        $this->assertNull($participant->optin_confirmed_at);
        $this->assertNull($participant->optin_confirmation_token);
    }

    /**
     * Test 3 : Vérifie qu'un participant peut s'inscrire avec optin SMS et Email
     * 
     * Scénario : Un participant s'inscrit et choisit de recevoir des SMS et des emails
     * Résultat attendu : Les champs optin, bysms et byemail sont tous à true
     */
    public function test_un_participant_peut_s_inscrire_avec_optin_sms_et_email()
    {
        // Arrange & Act
        $participant = $this->creerParticipant([
            'optin' => true,
            'bysms' => true,
            'byemail' => true,
        ]);
        
        // Assert
        $this->assertTrue($participant->optin);
        $this->assertTrue($participant->bysms);
        $this->assertTrue($participant->byemail);
        $this->assertNull($participant->optin_confirmed_at);
        $this->assertNull($participant->optin_confirmation_token);
    }

    /**
     * Test 4 : Vérifie qu'un participant peut s'inscrire sans optin
     * 
     * Scénario : Un participant s'inscrit mais ne souhaite recevoir aucune communication
     * Résultat attendu : Tous les champs optin, bysms et byemail sont à false
     */
    public function test_un_participant_peut_s_inscrire_sans_optin()
    {
        // Arrange & Act
        $participant = $this->creerParticipant([
            'optin' => false,
            'bysms' => false,
            'byemail' => false,
        ]);
        
        // Assert
        $this->assertFalse($participant->optin);
        $this->assertFalse($participant->bysms);
        $this->assertFalse($participant->byemail);
        $this->assertNull($participant->optin_confirmed_at);
        $this->assertNull($participant->optin_confirmation_token);
    }

   
   

    public static function optinProvider()
    {
        return [
            'optin_complet' => [true, true, true],
            'optin_sms_seulement' => [true, true, false],
            'optin_email_seulement' => [true, false, true],
            'pas_optin' => [false, false, false],
        ];
    }


}