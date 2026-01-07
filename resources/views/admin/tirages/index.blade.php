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
                    <tr data-tirage-id="{{ $tirage->id }}">
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
                                    <!-- Bouton pour tirer au sort -->
                                    <button
                                        class="btn btn-sm btn-outline-warning draw-tirage-btn"
                                        data-id="{{ $tirage->id }}"
                                        title="Tirer au sort"
                                    >
                                        <i class="fas fa-dice"></i>
                                    </button>
                                @endif

                                @if($tirage->winner_id && !$tirage->conf)
                                    <!-- Bouton pour tirer à nouveau -->
                                    <button
                                        class="btn btn-sm btn-outline-warning redraw-tirage-btn"
                                        data-id="{{ $tirage->id }}"
                                        title="Tirer à nouveau"
                                    >
                                        <i class="fas fa-dice"></i>
                                    </button>
                                    <!-- Bouton pour confirmer le tirage -->
                                    <button
                                        class="btn btn-sm btn-outline-success confirm-tirage-btn"
                                        data-id="{{ $tirage->id }}"
                                        title="Confirmer le tirage"
                                    >
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
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
<button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal"></button>
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

{{-- MODAL GAGNANT --}}
<div class="modal fade" id="winnerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content admin-form">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-trophy"></i> Gagnant du tirage
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-trophy fa-3x text-warning mb-3"></i>
                <h4 id="winnerName" class="mb-3"></h4>
                <p id="winnerContact" class="mb-3"></p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <span id="winnerInfo"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Fermer
                </button>
                <button type="button" class="btn btn-success" id="confirmWinnerBtn">
                    <i class="fas fa-check"></i> Confirmer le gagnant
                </button>
                <button type="button" class="btn btn-warning" id="redrawBtn">
                    <i class="fas fa-dice"></i> Tirer à nouveau
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL CONFIRMATION --}}
<div class="modal fade" id="confirmationModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content admin-form">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle"></i> Confirmation
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <p>Le tirage a été confirmé avec succès !</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="fas fa-check"></i> OK
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL ERREUR TIRAGE --}}
<div class="modal fade" id="drawErrorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content admin-form">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Erreur de tirage
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <h4 id="drawErrorTitle" class="mb-3">Tirage impossible</h4>
                <p id="drawErrorMessage" class="mb-3"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Fermer
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
 $(function () {
    const baseUrl = '{{ url("/admin/tirages") }}';
    let currentTirageId = null;

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

    // Gestion du tirage au sort
 $('.draw-tirage-btn').on('click', function(e) {
    e.preventDefault();
    const tirageId = $(this).data('id');
    currentTirageId = tirageId;
    
    $.ajax({
        url: baseUrl + '/' + tirageId + '/draw',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                // Afficher les informations du gagnant dans le modal
                $('#winnerName').text(response.winner_firstname + ' ' + response.winner_lastname);
                $('#winnerContact').text(response.winner_telephone);
                $('#winnerInfo').text('Tirage effectué avec succès !');
                
                // Configurer les boutons du modal
                if (response.confirmed) {
                    $('#confirmWinnerBtn').hide();
                    $('#redrawBtn').hide();
                } else {
                    $('#confirmWinnerBtn').show();
                    $('#redrawBtn').show();
                }
                
                // Afficher le modal
                showModal('winnerModal');
            } else {
                // Afficher le modal d'erreur avec le message approprié
                $('#drawErrorTitle').text('Tirage impossible');
                $('#drawErrorMessage').text(response.error || 'Une erreur est survenue lors du tirage.');
                showModal('drawErrorModal');
            }
        },
        error: function(xhr) {
            // Gérer les erreurs HTTP (400, 500, etc.)
            let errorMessage = 'Une erreur est survenue lors du tirage.';
            
            if (xhr.status === 400) {
                // Erreur 400 - Pas de participants éligibles
                errorMessage = 'Aucun participant éligible disponible pour le tirage.';
            } else if (xhr.responseJSON && xhr.responseJSON.error) {
                // Utiliser le message d'erreur du serveur si disponible
                errorMessage = xhr.responseJSON.error;
            }
            
            $('#drawErrorTitle').text('Erreur de tirage');
            $('#drawErrorMessage').text(errorMessage);
            showModal('drawErrorModal');
        }
    });
});

    // Gestion du tirage à nouveau
    $('.redraw-tirage-btn').on('click', function(e) {
        e.preventDefault();
        const tirageId = $(this).data('id');
        currentTirageId = tirageId;
        
        // Récupérer les informations du gagnant depuis le tableau
        const row = $(this).closest('tr');
        const winnerName = row.find('td:nth-child(7) strong').text();
        const winnerContact = row.find('td:nth-child(7) small').text();
        
        // Afficher les informations du gagnant dans le modal
        $('#winnerName').text(winnerName);
        $('#winnerContact').text(winnerContact);
        $('#winnerInfo').text('Cliquez sur "Tirer à nouveau" pour choisir un autre gagnant');
        
        // Configurer les boutons du modal
        $('#confirmWinnerBtn').show();
        $('#redrawBtn').show();
        
        // Afficher le modal
        showModal('winnerModal');
    });

    // Gestion de la confirmation du tirage
    $('.confirm-tirage-btn').on('click', function(e) {
        e.preventDefault();
        const tirageId = $(this).data('id');
        currentTirageId = tirageId;
        
        // Récupérer les informations du gagnant depuis le tableau
        const row = $(this).closest('tr');
        const winnerName = row.find('td:nth-child(7) strong').text();
        const winnerContact = row.find('td:nth-child(7) small').text();
        
        // Afficher les informations du gagnant dans le modal
        $('#winnerName').text(winnerName);
        $('#winnerContact').text(winnerContact);
        $('#winnerInfo').text('En attente de confirmation');
        
        // Configurer les boutons du modal
        $('#confirmWinnerBtn').show();
        $('#redrawBtn').show();
        
        // Afficher le modal
        showModal('winnerModal');
    });

    // Bouton pour confirmer le gagnant
    $('#confirmWinnerBtn').on('click', function() {
        if (!currentTirageId) return;
        
        $.ajax({
            url: baseUrl + '/' + currentTirageId + '/draw',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                confirm: 1
            },
            success: function(response) {
                if (response.success) {
                    // Fermer le modal du gagnant
                    $('#winnerModal').modal('hide');
                    
                    // Afficher le modal de confirmation
                    showModal('confirmationModal');
                    
                    // Mettre à jour le statut dans le tableau après un court délai
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    alert(response.error || 'Une erreur est survenue lors de la confirmation.');
                }
            },
            error: function() {
                alert('Une erreur est survenue lors de la confirmation.');
            }
        });
    });

    // Bouton pour tirer à nouveau
    $('#redrawBtn').on('click', function() {
        if (!currentTirageId) return;
        
        // Fermer le modal actuel
        $('#winnerModal').modal('hide');
        
        // Effectuer un nouveau tirage après un court délai
        setTimeout(function() {
            $.ajax({
                url: baseUrl + '/' + currentTirageId + '/draw',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Afficher les informations du nouveau gagnant dans le modal
                        $('#winnerName').text(response.winner_firstname + ' ' + response.winner_lastname);
                        $('#winnerContact').text(response.winner_telephone);
                        $('#winnerInfo').text('Nouveau tirage effectué !');
                        
                        // Configurer les boutons du modal
                        $('#confirmWinnerBtn').show();
                        $('#redrawBtn').show();
                        
                        // Afficher le modal
                        showModal('winnerModal');
                    } else {
                        alert(response.error || 'Une erreur est survenue lors du tirage.');
                    }
                },
                error: function() {
                    alert('Une erreur est survenue lors du tirage.');
                }
            });
        }, 500);
    });

});
</script>
@endpush

@push('styles')
<style>


</style>
@endpush