@extends('layouts.admin')
@section('title', 'Participants du film')
@section('content')

<div class="page-header">
    <h1><i class="fas fa-users"></i> Participants totaux</h1>
</div>

<table class="table table-striped">
    <tbody>
        @forelse($participants as $participant)
            <tr>
                <td>{{ $participant['id'] }}</td>
                <td>{{ App\Models\Base\Genesys::Decrypt($participant['firstname']) }}</td>
                <td>{{ App\Models\Base\Genesys::Decrypt($participant['lastname']) }}</td>
                <td>{{ App\Models\Base\Genesys::Decrypt($participant['email'] ?? '-') }}</td>
                <td>{{ App\Models\Base\Genesys::Decrypt($participant['telephone']) }}</td>
                <td>{{ $participant['age'] }}</td>
                <td>{{ \Carbon\Carbon::parse($participant['created_at'])->format('d/m/Y H:i') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">Aucun participant inscrit.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- test -->

@endsection