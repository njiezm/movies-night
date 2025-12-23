@extends('layouts.admin')
@section('title','Ajouter/Modifier Film')
@section('content')
<h1>{{ isset($film) ? 'Modifier' : 'Ajouter' }} un film</h1>
<form action="{{ isset($film) ? route('admin.films.update',$film) : route('admin.films.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <label>Titre :</label><input type="text" name="title" value="{{ $film->title ?? '' }}" required><br>
    <label>Description :</label><textarea name="description">{{ $film->description ?? '' }}</textarea><br>
    <label>Vignette :</label><input type="file" name="vignette"><br>
    <button type="submit" class="btn">{{ isset($film) ? 'Mettre à jour' : 'Ajouter' }}</button>
</form>
@endsection
