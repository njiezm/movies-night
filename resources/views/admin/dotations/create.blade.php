@extends('layouts.admin')
@section('title', "Ajouter une dotation")
@section('content')

<div class="page-header">
    <h1><i class="fas fa-trophy"></i> Ajouter une dotation</h1>
</div>

<section class="content-section">
    <div class="form-container">
        <form action="{{ route('admin.dotations.store') }}" method="POST" class="admin-form">
            @csrf
            
            <div class="form-group">
                <label for="title" class="form-label text-white">Titre de la dotation</label>
                <div class="input-group">
                    <div class="input-icon">
                        <i class="fas fa-heading"></i>
                    </div>
                    <input type="text" class="form-control text-white bg-dark border-light"  id="title" name="title" value="{{ old('title') }}" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="dotationdate" class="form-label text-white">Date de la dotation</label>
                <div class="input-group">
                    <div class="input-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <input type="date" class="form-control text-white bg-dark border-light"  id="dotationdate" name="dotationdate" value="{{ old('dotationdate') }}" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="quantity" class="form-label text-white">Quantité</label>
                <div class="input-group">
                    <div class="input-icon">
                        <i class="fas fa-sort-numeric-up"></i>
                    </div>
                    <input type="number" class="form-control text-white bg-dark border-light"  id="quantity" name="quantity" value="{{ old('quantity', 1) }}" min="1" required>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="{{ route('admin.dotations') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Ajouter
                </button>
            </div>
        </form>
    </div>
</section>

@endsection
