@extends('layouts.admin')
@section('title', 'Réglages')
@section('content')

<div class="page-header">
    <h1><i class="fas fa-cog"></i> Réglages</h1>
</div>

<section class="content-section">
    <div class="form-container">
        <form action="{{ route('admin.settings.update') }}" method="POST" class="admin-form">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="full_access_code" class="form-label text-white">Code d'accès complet</label>
                <div class="input-group">
                    <div class="input-icon">
                        <i class="fas fa-key"></i>
                    </div>
                    <input type="text" class="form-control text-white bg-dark border-light" id="full_access_code" name="full_access_code" value="{{ $settings['full_access_code'] }}" maxlength="6" required>
                </div>
                <small class="form-text text-white">Code à 6 chiffres pour l'accès complet à toutes les fonctionnalités</small>
            </div>
            
            <div class="form-group">
                <label for="limited_access_code" class="form-label text-white">Code d'accès limité</label>
                <div class="input-group">
                    <div class="input-icon">
                        <i class="fas fa-key"></i>
                    </div>
                    <input type="text" class="form-control text-white bg-dark border-light" id="limited_access_code" name="limited_access_code" value="{{ $settings['limited_access_code'] }}" maxlength="6" required>
                </div>
                <small class="form-text text-white">Code à 6 chiffres pour l'accès limité (sans dotations)</small>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="opening_date" class="form-label text-white">Date d'ouverture</label>
                        <div class="input-group">
                            <div class="input-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <input type="date" class="form-control text-white bg-dark border-light" id="opening_date" name="opening_date" value="{{ $settings['opening_date'] }}" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="closing_date" class="form-label text-white">Date de fermeture</label>
                        <div class="input-group">
                            <div class="input-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <input type="date" class="form-control text-white bg-dark border-light" id="closing_date" name="closing_date" value="{{ $settings['closing_date'] }}" required>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="min_age" class="form-label text-white">Âge minimum requis</label>
                <div class="input-group">
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <input type="number" class="form-control text-white bg-dark border-light" id="min_age" name="min_age" value="{{ $settings['min_age'] }}" min="0" max="18" required>
                </div>
                <small class="form-text text-white">Âge minimum requis pour participer au marathon</small>
            </div>
            
            <div class="form-actions">
                <a href="{{ route('admin.stats') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</section>

@endsection