@extends('layouts.app')
@section('content')
<h1>Bonjour {{ $participant->firstname }}</h1>
<p>Vous avez vu {{ $filmsVus->count() }} / {{ $total }} films</p>
<ul>
@foreach($filmsVus as $f)
<li>{{ $f->title }}</li>
@endforeach
</ul>
<p>Bon film !</p>
@endsection
