@extends('layouts.admin')
@section('title', 'Participants du film')
@section('content')

<div class="page-header">
    <h1><i class="fas fa-users"></i> Participants pour : {{ $film->title }}</h1>
    <a href="{{ route('admin.films') }}" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Retour à la liste des films
    </a>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>#ID</th>
            <th>Prénom</th>
            <th>Nom</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>Âge</th>
            <th>Inscrit le</th>
        </tr>
    </thead>
    <tbody>
        @forelse($participants as $participant)
            <tr>
                <td>{{ $participant['id'] }}</td>
                <td>{{ $participant['firstname'] }}</td>
                <td>{{ $participant['lastname'] }}</td>
                <td>{{ $participant['email'] ?? '-' }}</td>
                <td>{{ $participant['telephone'] }}</td>
                <td>{{ $participant['age'] }}</td>
                <td>{{ \Carbon\Carbon::parse($participant['created_at'])->format('d/m/Y H:i') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">Aucun participant pour ce film.</td>
            </tr>
        @endforelse
    </tbody>
</table>

@endsection