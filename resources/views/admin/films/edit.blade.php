@extends('layouts.admin')
@section('title','{{ isset($film) ? 'Modifier' : 'Ajouter' }} un film')
@section('content')
<h3 class="text-white text-center mb-4"><i class="fas fa-video"></i> {{ isset($film) ? 'Modifier' : 'Ajouter' }} un film</h3>

<div class="row justify-content-center">
    <div class="col-md-8">
        <form action="{{ isset($film) ? route('admin.films.update',$film) : route('admin.films.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($film))
                @method('POST')
            @endif
            
            <div class="form-group">
                <label for="title">Titre</label>
                <input type="text" class="form-control" name="title" value="{{ $film->title ?? '' }}" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" name="description">{{ $film->description ?? '' }}</textarea>
            </div>
            
            <div class="form-group">
                <label for="vignette">Vignette</label>
                <input type="file" class="form-control" name="vignette">
                @if(isset($film) && $film->vignette)
                    <div class="mt-2">
                        <img src="{{ asset('storage/'.$film->vignette) }}" alt="Vignette actuelle" style="max-width: 200px;">
                    </div>
                @endif
            </div>
            
            <div class="form-group text-center">
                <button type="submit" class="btn btn-danger">
                    {{ isset($film) ? 'Mettre à jour' : 'Ajouter' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection