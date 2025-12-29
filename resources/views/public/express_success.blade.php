@extends('layouts.app')
@section('title','Connexion Express')
@section('content')
<h1>Bienvenue {{ $participant->firstname }}</h1>
<p>Vous êtes connecté(e) en express.</p>
<a href="{{ route('scan',$participant->slug) }}">Voir votre progression</a>
@endsection
