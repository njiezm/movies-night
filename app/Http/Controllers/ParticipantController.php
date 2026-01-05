<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\Film;
use App\Models\Base\Genesys;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function index()
    {
        $participants = Participant::with('films')->get();
        
        // Déchiffrement des données pour l'affichage
        $decryptedParticipants = $participants->map(function($participant) {
            $participant->firstname = Genesys::Decrypt($participant->firstname);
            $participant->lastname = Genesys::Decrypt($participant->lastname);
            $participant->telephone = Genesys::Decrypt($participant->telephone);
            if ($participant->email) {
                $participant->email = Genesys::Decrypt($participant->email);
            }
            return $participant;
        });
        
        return view('admin.participants.index', ['participants' => $decryptedParticipants]);
    }

    public function create()
    {
        $films = Film::all();
        return view('admin.participants.create', compact('films'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lastname' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'email' => 'nullable|email|unique:participants,email',
            'telephone' => 'nullable|unique:participants,telephone',
            'zipcode' => 'nullable|string|max:10',
            'optin' => 'boolean',
            'bysms' => 'boolean',
            'byemail' => 'boolean',
        ]);

        // Chiffrement des données sensibles avant stockage
        $data = $request->all();
        $data['firstname'] = Genesys::Crypt($request->firstname);
        $data['lastname'] = Genesys::Crypt($request->lastname);
        if ($request->telephone) {
            $data['telephone'] = Genesys::Crypt($request->telephone);
        }
        if ($request->email) {
            $data['email'] = Genesys::Crypt($request->email);
        }
        $data['slug'] = \Str::slug($request->firstname.'-'.$request->lastname.'-'.uniqid());

        $participant = Participant::create($data);

        if ($request->films) {
            $participant->films()->attach($request->films);
        }

        return redirect()->route('participants.index')->with('success','Participant ajouté');
    }

    public function edit(Participant $participant)
    {
        // Déchiffrement des données pour l'édition
        $participant->firstname = Genesys::Decrypt($participant->firstname);
        $participant->lastname = Genesys::Decrypt($participant->lastname);
        $participant->telephone = Genesys::Decrypt($participant->telephone);
        if ($participant->email) {
            $participant->email = Genesys::Decrypt($participant->email);
        }
        
        $films = Film::all();
        return view('admin.participants.edit', compact('participant','films'));
    }

    public function update(Request $request, Participant $participant)
    {
        $request->validate([
            'lastname' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'email' => 'nullable|email|unique:participants,email,'.$participant->id,
            'telephone' => 'nullable|unique:participants,telephone,'.$participant->id,
            'zipcode' => 'nullable|string|max:10',
            'optin' => 'boolean',
            'bysms' => 'boolean',
            'byemail' => 'boolean',
        ]);

        // Chiffrement des données sensibles avant mise à jour
        $data = $request->all();
        $data['firstname'] = Genesys::Crypt($request->firstname);
        $data['lastname'] = Genesys::Crypt($request->lastname);
        if ($request->telephone) {
            $data['telephone'] = Genesys::Crypt($request->telephone);
        }
        if ($request->email) {
            $data['email'] = Genesys::Crypt($request->email);
        }

        $participant->update($data);

        if ($request->films) {
            $participant->films()->sync($request->films);
        } else {
            $participant->films()->detach();
        }

        return redirect()->route('participants.index')->with('success','Participant mis à jour');
    }

    public function destroy(Participant $participant)
    {
        $participant->delete();
        return redirect()->route('participants.index')->with('success','Participant supprimé');
    }
}