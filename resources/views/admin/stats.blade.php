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
            <div class="stat-label">Nombre d'inscrits</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $totalOptinParticipants }}</div>
            <div class="stat-label">Nombre d'opt-in</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $totalFilms }}</div>
            <div class="stat-label">Nombre de films intégrés</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $films->sum('participants_count') }}</div>
            <div class="stat-label">Scans totaux</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ count($films) }}</div>
            <div class="stat-label">Tirages au sort</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $bigTasEligible }}</div>
            <div class="stat-label">Éligibles BIG TAS</div>
        </div>
    </div>
</section>



<!-- Section d'éligibilité par films -->
<section class="content-section">
    <div class="section-header">
        <h2><i class="fas fa-trophy"></i> Nombre de personnes éligbiles aux TAS Mensuel</h2>
    </div>
    <div class="table-container">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Titre du film</th>
                        <th>Nombre d'éligibles (TAS mensuel)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($filmsEligibility as $item)
                        <tr>
                            <td>{{ $item['film']->title }}</td>
                            <td>{{ $item['eligible_count'] }} personne(s)</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="alert alert-info mt-3 d-flex flex-column">
    <i class="fas fa-info-circle"></i>
    <strong>Éligible TAS mensuel:</strong> Tous ceux qui ont vu le film du mois
    <strong>Éligible BIG TAS:</strong> Uniquement ceux qui ont vu tous les films
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
                    </tr>
                </thead>
                <tbody>
                    @foreach($films->sortByDesc('participants_count') as $film)
                        <tr>
                            <td>{{ $film->title }}</td>
                            <td>{{ $film->participants_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

@endsection