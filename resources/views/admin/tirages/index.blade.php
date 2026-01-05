@extends('layouts.admin')
@section('title', 'Tirages au sort')

@section('content')

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-gift"></i> Gestion des Tirages</h1>
    <button class="btn btn-primary" id="addTirageBtn">
        <i class="fas fa-plus"></i> Ajouter un tirage
    </button>
</div>

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
                            {{ App\Models\Base\Genesys::Decrypt($tirage->winner->telephone) }}
                        </small>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    <button
                        class="btn btn-sm btn-outline-primary edit-tirage-btn"
                        data-id="{{ $tirage->id }}"
                        data-title="{{ $tirage->title }}"
                        data-dotation="{{ $tirage->dotation_id }}"
                        data-date="{{ $tirage->date }}"
                    >
                        <i class="fas fa-edit"></i>
                    </button>

                    @if(!$tirage->winner_id)
                        <button
                            class="btn btn-sm btn-outline-warning draw-tirage-btn"
                            data-id="{{ $tirage->id }}"
                        >
                            <i class="fas fa-dice"></i>
                        </button>
                    @endif

                    <button
                        class="btn btn-sm btn-outline-danger delete-tirage-btn"
                        data-id="{{ $tirage->id }}"
                    >
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    Aucun tirage enregistré
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- MODAL AJOUT / MODIF --}}
<div class="modal fade" id="tirageModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="tirageForm" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="tirageModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="id" id="tirage_id">

                <div class="mb-3">
                    <label class="form-label">Titre</label>
                    <input type="text" class="form-control" name="title" id="title" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Dotation</label>
                    <select name="dotation_id" id="dotation_id" class="form-select" required>
                        <option value="">-- Choisir --</option>
                        @foreach($dotations as $dotation)
                            <option value="{{ $dotation->id }}">{{ $dotation->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Date</label>
                    <input type="date" class="form-control" name="date" id="date" required>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL SUPPRESSION --}}
<div class="modal fade" id="deleteTirageModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="deleteTirageForm" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-header">
                <h5 class="modal-title">Supprimer le tirage</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-danger mb-0">Cette action est définitive.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button class="btn btn-danger">Supprimer</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL TIRAGE --}}
<div class="modal fade" id="drawTirageModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="drawTirageForm" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tirage au sort</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <p>Confirmer le tirage au sort ?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
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

