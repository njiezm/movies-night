@extends('layouts.admin')
@section('title', 'Tirages au sort')

@section('content')

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-gift"></i> Gestion des Tirages</h1>
    <button class="btn btn-primary" id="addTirageBtn">
        <i class="fas fa-plus"></i> Ajouter un tirage
    </button>
</div>

<section class="content-section">
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Dotation</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Gagnant</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($tirages as $tirage)
                    <tr>
                        <td>{{ $tirage->title }}</td>
                        <td><span class="badge bg-secondary">{{ $tirage->dotation->title }}</span></td>
                        <td>{{ \Carbon\Carbon::parse($tirage->date)->format('d/m/Y') }}</td>
                        <td>
                            @if($tirage->winner_id)
                                <span class="badge bg-success">Terminé</span>
                            @else
                                <span class="badge bg-warning text-dark">En attente</span>
                            @endif
                        </td>
                        <td>
                            @if($tirage->winner)
                                <strong>
                                    {{ App\Models\Base\Genesys::Decrypt($tirage->winner->firstname) }}
                                    {{ App\Models\Base\Genesys::Decrypt($tirage->winner->lastname) }}
                                </strong><br>
                                <small class="text-muted">
                                    {{ $tirage->winner->telephone }}
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
                                    data-date="{{ $tirage->date }}"
                                    title="Modifier"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>

                                @if(!$tirage->winner_id)
                                    <button
                                        class="btn btn-sm btn-outline-warning draw-tirage-btn"
                                        data-id="{{ $tirage->id }}"
                                        title="Tirer au sort"
                                    >
                                        <i class="fas fa-dice"></i>
                                    </button>
                                @endif

                                <button
                                    class="btn btn-sm btn-outline-danger delete-tirage-btn"
                                    data-id="{{ $tirage->id }}"
                                    title="Supprimer"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
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
                    <label for="dotation_id" class="form-label">Dotation</label>
                    <div class="input-group">
                        <div class="input-icon">
                            <i class="fas fa-gift"></i>
                        </div>
                        <select name="dotation_id" id="dotation_id" class="form-select" required>
                            <option value="">-- Choisir --</option>
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
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Annuler
                </button>
                <button class="btn btn-primary">
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
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Annuler
                </button>
                <button class="btn btn-danger">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL TIRAGE --}}
<div class="modal fade" id="drawTirageModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form method="POST" id="drawTirageForm" class="modal-content admin-form">
            @csrf
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-dice"></i> Tirage au sort
                </h5>
                <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-dice fa-3x text-warning mb-3"></i>
                <p>Confirmer le tirage au sort ?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Annuler
                </button>
                <button class="btn btn-warning">
                    <i class="fas fa-dice"></i> Tirer
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

    $('#addTirageBtn').on('click', function () {
        $('#tirageForm')[0].reset();
        $('#tirageForm').attr('action', '{{ route("admin.tirages.store") }}');
        $('#tirageForm').find('input[name="_method"]').remove();
        $('#tirageModalLabel').text('Ajouter un tirage');
        showModal('tirageModal');
    });

    $('.edit-tirage-btn').on('click', function () {
        const btn = $(this);

        $('#tirageForm').attr('action', baseUrl + '/' + btn.data('id'));
        $('#tirageForm').find('input[name="_method"]').remove();
        $('#tirageForm').append('<input type="hidden" name="_method" value="PUT">');

        $('#title').val(btn.data('title'));
        $('#dotation_id').val(btn.data('dotation'));
        $('#date').val(btn.data('date'));

        $('#tirageModalLabel').text('Modifier le tirage');
        showModal('tirageModal');
    });

    $('.delete-tirage-btn').on('click', function () {
        $('#deleteTirageForm').attr('action', baseUrl + '/' + $(this).data('id'));
        showModal('deleteTirageModal');
    });

    $('.draw-tirage-btn').on('click', function () {
        $('#drawTirageForm').attr('action', baseUrl + '/' + $(this).data('id') + '/draw');
        showModal('drawTirageModal');
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