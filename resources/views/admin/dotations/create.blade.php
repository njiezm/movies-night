@extends('layouts.admin')
@section('title','Ajouter/Modifier Dotation')
@section('content')
<h1>{{ isset($dotation) ? 'Modifier' : 'Ajouter' }} Dotation</h1>
<form action="{{ isset($dotation) ? route('admin.dotations.update',$dotation) : route('admin.dotations.store') }}" method="POST">
    @csrf
    <label>Title :</label><input type="text" name="title" value="{{ $dotation->title ?? '' }}" required><br>
    <label>Date :</label><input type="date" name="dotationdate" value="{{ $dotation->dotationdate ?? '' }}" required><br>
    <button type="submit" class="btn">{{ isset($dotation) ? 'Mettre à jour' : 'Ajouter' }}</button>
</form>
@endsection
