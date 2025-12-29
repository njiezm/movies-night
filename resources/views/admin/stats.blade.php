@extends('layouts.admin')
@section('title','Statistiques')
@section('content')

<div class="page-header">
    <h1><i class="fas fa-chart-bar"></i> Tableau de bord</h1>
</div>

<!-- Section des cartes de statistiques -->
<section class="content-section">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $totalParticipants }}</div>
            <div class="stat-label">Participants</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $totalFilms }}</div>
            <div class="stat-label">Films</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $films->sum('participants_count') }}</div>
            <div class="stat-label">Scans totaux</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ count($films) }}</div>
            <div class="stat-label">Tirages au sort</div>
        </div>
    </div>
</section>

<!-- Section des films les plus populaires -->
<section class="content-section">
    <div class="section-header">
        <h2><i class="fas fa-video"></i> Films les plus populaires</h2>
    </div>
    <div class="table-container">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Participants</th>
                        <th>Progression</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($films->sortByDesc('participants_count') as $film)
                        <tr>
                            <td>{{ $film->title }}</td>
                            <td>{{ $film->participants_count }}</td>
                            <td>
                                @if($totalParticipants > 0)
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" style="width: {{ ($film->participants_count / $totalParticipants) * 100 }}%;" aria-valuenow="{{ $film->participants_count }}" aria-valuemin="0" aria-valuemax="{{ $totalParticipants }}">
                                            {{ round(($film->participants_count / $totalParticipants) * 100) }}%
                                        </div>
                                    </div>
                                @else
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="0">
                                            0%
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Section du classement des participants avec rechargement AJAX -->
<section class="content-section">
    <div class="section-header">
        <h2><i class="fas fa-trophy"></i> Classement des participants</h2>
        
    </div>
    <div class="table-container">
        <div class="table-responsive">
            <table class="table" id="rankingTable">
                <thead>
                    <tr>
                        <th>Position</th>
                        <th>Nom</th>
                        <th>Films vus</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ranking as $index => $participant)
                        <tr>
                            <td>
                                @if($index == 0)
                                    <i class="fas fa-medal text-warning"></i>
                                @elseif($index == 1)
                                    <i class="fas fa-medal text-secondary"></i>
                                @elseif($index == 2)
                                    <i class="fas fa-medal text-danger"></i>
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </td>
                            <td>{{ $participant->firstname }} {{ $participant->lastname }}</td>
                            <td>{{ $participant->films_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- Indicateur de chargement pour le rechargement AJAX -->
    <div class="text-center mt-3" id="rankingLoader" style="display: none;">
        <i class="fas fa-spinner fa-spin fa-2x"></i>
        <p class="mt-2">Chargement...</p>
    </div>
</section>



@endsection