@extends('layouts.admin')
@section('title', 'Tirages au sort')

@section('content')

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-gift"></i> Gestion des Tirages</h1>
    <div>
       @if(!$bigTasExists)
            <!-- Formulaire caché pour créer le BIG TAS -->
            <form id="createBigTasForm" method="POST" action="{{ route('admin.tirages.createBigTas') }}" style="display: none;">
                @csrf
            </form>
            <button class="btn btn-success me-2" id="createBigTasBtn" type="button">
                <i class="fas fa-trophy"></i> Créer BIG TAS
            </button>
        @endif
        <!--button class="btn btn-primary" id="addTirageBtn">
            <i class="fas fa-plus"></i> Ajouter un tirage
        </!--button-->
    </div>
</div>

<!-- Affichage du message du gagnant si un tirage vient d'être fait -->
@if(session('winner_drawn'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <h4 class="alert-heading"><i class="fas fa-dice"></i> Tirage effectué !</h4>
        <p>Le gagnant est : <strong>{{ session('winner_firstname') }} {{ session('winner_lastname') }}</strong></p>
        <p class="mb-0">Contact : {{ session('winner_telephone') }}</p>
    </div>
@endif

<section class="content-section">
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Film</th>
                        <th>Dotation</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Condition de récupération</th>
                        <th>Gagnant</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($tirages as $tirage)
                    <tr>
                        <td>{{ $tirage->title }}</td>
                        <td>
                            @if($tirage->is_big_tas)
                                <span class="badge bg-warning text-dark">BIG TAS</span>
                            @elseif($tirage->film)
                                {{ $tirage->film->title }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($tirage->dotation)
                                <span class="badge bg-secondary">{{ $tirage->dotation->title }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($tirage->date)->format('d/m/Y') }}</td>
                        <td>
                            @if($tirage->winner_id)
                                @if($tirage->conf)
                                    <span class="badge bg-success">Confirmé</span>
                                @else
                                    <span class="badge bg-warning text-dark">En attente de confirmation</span>
                                @endif
                            @else
                                <span class="badge bg-info">En attente de tirage</span>
                            @endif
                        </td>
                        <td>
                            @if($tirage->condition_recuperation)
                                <small>{{ $tirage->condition_recuperation }}</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($tirage->winner)
                                <strong>
                                    {{ App\Models\Base\Genesys::Decrypt($tirage->winner->firstname) }}
                                    {{ App\Models\Base\Genesys::Decrypt($tirage->winner->lastname) }}
                                </strong><br>
                                <small class="text-muted">
                                    {{ App\Models\Base\Genesys::Decrypt($tirage->winner->telephone) }}
                                </small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button
                                    class="btn btn-sm btn-outline-primary edit-tirage-btn"
                                    data-id="{{ $tirage->id }}"
                                    data-title="{{ $tirage->title }}"
                                    data-dotation="{{ $tirage->dotation_id }}"
                                    data-film="{{ $tirage->film_id }}"
                                    data-date="{{ $tirage->date }}"
                                    data-condition="{{ $tirage->condition_recuperation }}"
                                    title="Modifier"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>

                                @if(!$tirage->winner_id)
                                    <!-- Formulaire pour tirer au sort -->
                                    <form method="POST" action="{{ route('admin.tirages.draw', $tirage->id) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="Tirer au sort">
                                            <i class="fas fa-dice"></i>
                                        </button>
                                    </form>
                                @elseif(!$tirage->conf)
                                    <!-- Formulaire pour confirmer le tirage -->
                                    <form method="POST" action="{{ route('admin.tirages.draw', $tirage->id) }}" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="confirm" value="1">
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Confirmer le tirage">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif

                               
                                <!--button
                                    class="btn btn-sm btn-outline-danger delete-tirage-btn"
                                    data-id="{{ $tirage->id }}"
                                    title="Supprimer"
                                >
                                    <i class="fas fa-trash"></i>
                                </!--button-->
                                
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-3 d-block"></i>
                            Aucun tirage enregistré
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- MODAL AJOUT / MODIF --}}
<div class="modal fade" id="tirageModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="tirageForm" class="modal-content admin-form">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="tirageModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="id" id="tirage_id">

                <div class="form-group">
                    <label for="title" class="form-label">Titre</label>
                    <div class="input-group">
                        <div class="input-icon">
                            <i class="fas fa-heading"></i>
                        </div>
                        <input type="text" class="form-control" name="title" id="title" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="film_id" class="form-label">Film</label>
                    <div class="input-group">
                        <div class="input-icon">
                            <i class="fas fa-film"></i>
                        </div>
                        <select name="film_id" id="film_id" class="form-select">
                            <option value="">-- Aucun --</option>
                            @foreach($films as $film)
                                <option value="{{ $film->id }}">{{ $film->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="dotation_id" class="form-label">Dotation</label>
                    <div class="input-group">
                        <div class="input-icon">
                            <i class="fas fa-gift"></i>
                        </div>
                        <select name="dotation_id" id="dotation_id" class="form-select">
                            <option value="">-- Aucune --</option>
                            @foreach($dotations as $dotation)
                                <option value="{{ $dotation->id }}">{{ $dotation->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="date" class="form-label">Date</label>
                    <div class="input-group">
                        <div class="input-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <input type="date" class="form-control" name="date" id="date" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="condition_recuperation" class="form-label">Condition de récupération</label>
                    <div class="input-group">
                        <div class="input-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <textarea class="form-control" name="condition_recuperation" id="condition_recuperation" rows="3"></textarea>
                    </div>
                </div>

                <!--div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_big_tas" id="is_big_tas">
                        <label class="form-check-label" for="is_big_tas">
                            BIG TAS (pour les participants ayant vu tous les films)
                        </label>
                    </div>
                </!--div-->
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Annuler
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL SUPPRESSION --}}
<div class="modal fade" id="deleteTirageModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form method="POST" id="deleteTirageForm" class="modal-content admin-form">
            @csrf
            @method('DELETE')
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Supprimer le tirage
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                <p class="mb-0">Cette action est définitive.</p>
                <p class="text-muted">Êtes-vous sûr de vouloir continuer ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Annuler
                </button>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
 $(function () {
    const baseUrl = '{{ url("/admin/tirages") }}';

    function showModal(id) {
        new bootstrap.Modal(document.getElementById(id)).show();
    }

    // Bouton pour créer un BIG TAS
    $('#createBigTasBtn').on('click', function() {
        if(confirm('Êtes-vous sûr de vouloir créer un BIG TAS ?')) {
            $('#createBigTasForm').submit();
        }
    });

    // Bouton pour ajouter un tirage
    $('#addTirageBtn').on('click', function () {
        $('#tirageForm')[0].reset();
        $('#tirageForm').attr('action', '{{ route("admin.tirages.store") }}');
        $('#tirageForm').find('input[name="_method"]').remove();
        $('#tirageModalLabel').text('Ajouter un tirage');
        showModal('tirageModal');
    });

    // Bouton pour éditer un tirage
    $('.edit-tirage-btn').on('click', function () {
        const btn = $(this);

        $('#tirageForm').attr('action', baseUrl + '/' + btn.data('id'));
        $('#tirageForm').find('input[name="_method"]').remove();
        $('#tirageForm').append('<input type="hidden" name="_method" value="PUT">');

        $('#title').val(btn.data('title'));
        $('#dotation_id').val(btn.data('dotation'));
        $('#film_id').val(btn.data('film'));
        $('#date').val(btn.data('date'));
        $('#condition_recuperation').val(btn.data('condition'));

        $('#tirageModalLabel').text('Modifier le tirage');
        showModal('tirageModal');
    });

    // Bouton pour supprimer un tirage
    $('.delete-tirage-btn').on('click', function () {
        $('#deleteTirageForm').attr('action', baseUrl + '/' + $(this).data('id'));
        showModal('deleteTirageModal');
    });

});
</script>
@endpush

@push('styles')
<style>
/* Styles pour les formulaires */
.content-section {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 20px;
    margin-bottom: 20px;
}

.table-container {
    border-radius: 6px;
    overflow: hidden;
}

.admin-form .form-group {
    margin-bottom: 20px;
}

.admin-form .form-label {
    font-weight: 600;
    margin-bottom: 8px;
    color: #495057;
}

.admin-form .input-group {
    position: relative;
    display: flex;
    flex-wrap: wrap;
    align-items: stretch;
    width: 100%;
}

.admin-form .input-icon {
    display: flex;
    align-items: center;
    padding: 0.375rem 0.75rem;
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
    border-right: none;
    border-radius: 0.25rem 0 0 0.25rem;
    color: #495057;
}

.admin-form .form-control,
.admin-form .form-select {
    border-left: none;
    border-radius: 0 0.25rem 0.25rem 0;
}

.admin-form .form-control:focus,
.admin-form .form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.admin-form .form-control:focus + .input-icon {
    border-color: #86b7fe;
}

.admin-form .form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 25px;
}

.action-buttons {
    display: flex;
    gap: 5px;
}

/* Amélioration des modals */
.modal-header {
    border-bottom: 1px solid #dee2e6;
    padding: 1rem 1.5rem;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    border-top: 1px solid #dee2e6;
    padding: 1rem 1.5rem;
}

.modal-sm {
    max-width: 400px;
}

/* Animation pour les boutons */
.btn {
    transition: all 0.2s ease-in-out;
}

.btn:hover {
    transform: translateY(-1px);
}

/* Style pour le tableau */
.table {
    margin-bottom: 0;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
    background-color: #f8f9fa;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

/* Message quand aucun résultat */
.text-center.text-muted.py-4 i {
    opacity: 0.5;
}
</style>
@endpush