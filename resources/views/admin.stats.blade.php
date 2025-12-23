@extends('layouts.app')
@section('content')
<h1>Statistiques</h1>
<p>Total participants : {{ $totalParticipants }}</p>
<p>Total films : {{ $totalFilms }}</p>
<h2>Participants par film</h2>
@foreach($films as $film)
<p>{{ $film->title }} : {{ $film->participants_count }} participants</p>
@endforeach
<h2>Classement participants</h2>
@foreach($ranking as $p)
<p>{{ $p->firstname }} {{ $p->lastname }} - {{ $p->films_count }} films vus</p>
@endforeach
@endsection
