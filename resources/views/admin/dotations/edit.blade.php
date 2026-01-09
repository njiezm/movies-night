@extends('layouts.admin')
@section('title', "Modifier une dotation")
@section('content')

<div class="page-header">
    <h1><i class="fas fa-trophy"></i> Modifier une dotation</h1>
</div>

<section class="content-section">
    <div class="form-container">
        <form action="{{ route('admin.dotations.update', $dotation) }}" method="POST" class="admin-form">
            @csrf
            @method('PUT') 
            
            <div class="form-group">
                <label for="title" class="form-label text-white">Titre de la dotation</label>
                <div class="input-group">
                    <div class="input-icon">
                        <i class="fas fa-heading" style="color: white"></i>
                    </div>
                    <input type="text" 
                        class="form-control text-white bg-dark border-light" 
                        id="title" 
                        name="title" 
                        value="{{ $dotation->title }}" 
                        required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="dotationdate" class="form-label text-white">Date de la dotation</label>
                <div class="input-group">
                    <div class="input-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <input type="date" class="form-control text-white bg-dark border-light"  id="dotationdate" name="dotationdate" value="{{ $dotation->dotationdate }}" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="quantity" class="form-label text-white">Quantité</label>
                <div class="input-group">
                    <div class="input-icon">
                        <i class="fas fa-sort-numeric-up"></i>
                    </div>
                    <input type="number" class="form-control text-white bg-dark border-light"  id="quantity" name="quantity" value="{{ $dotation->quantity }}" min="{{ $dotation->attributed_count ?? 1 }}" required>
                    @if($dotation->attributed_count > 0)
                        <small class="form-text text-muted">Note: {{ $dotation->attributed_count }} dotation(s) déjà attribuée(s)</small>
                    @endif
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label text-white">Type de dotation</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="is_big_tas" id="monthly" value="0" {{ !$dotation->is_big_tas ? 'checked' : '' }}>
                    <label class="form-check-label text-white" for="monthly">
                        Dotation mensuelle (pour les tirages mensuels)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="is_big_tas" id="bigtas" value="1" {{ $dotation->is_big_tas ? 'checked' : '' }}>
                    <label class="form-check-label text-white" for="bigtas">
                        Dotation BIG TAS (pour le grand tirage annuel)
                    </label>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="{{ route('admin.dotations') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Mettre à jour
                </button>
            </div>
        </form>
    </div>
</section>
<style>
    .input-group {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none; 
    transition: color 0.3s;
    color: #ccc; 
}

.input-group input:focus + .input-icon i,
.input-group input:focus ~ .input-icon i {
    color: white; 
}

.input-group input {
    padding-left: 35px; 
}

</style>
@endsection