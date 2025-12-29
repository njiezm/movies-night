@extends('layouts.admin')
@section('title', 'Gestion des dotations')
@section('content')

<div class="page-header">
    <h1><i class="fas fa-trophy"></i> Gestion des dotations</h1>
    <a href="{{ route('admin.dotations.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Ajouter une dotation
    </a>
</div>

<section class="content-section">
    <div class="table-container">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dotations as $dotation)
                        <tr>
                            <td>{{ $dotation->title }}</td>
                            <td>{{ \Carbon\Carbon::parse($dotation->dotationdate)->format('d/m/Y') }}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.dotations.edit', $dotation) }}" class="btn btn-sm btn-outline-primary" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.dotations.delete', $dotation) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette dotation ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Aucune dotation trouvée</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

@endsection