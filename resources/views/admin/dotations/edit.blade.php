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
                <label for="title" class="form-label">Titre de la dotation</label>
                <div class="input-group">
                    <div class="input-icon">
                        <i class="fas fa-heading"></i>
                    </div>
                    <input type="text" class="form-control" id="title" name="title" value="{{ $dotation->title }}" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="dotationdate" class="form-label">Date de la dotation</label>
                <div class="input-group">
                    <div class="input-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <input type="date" class="form-control" id="dotationdate" name="dotationdate" value="{{ $dotation->dotationdate }}" required>
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

@endsection