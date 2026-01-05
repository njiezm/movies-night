@extends('layouts.admin')
@section('title', 'Tirages au sort')
@section('content')

<div class="page-header">
    <h1><i class="fas fa-gift"></i> Gestion des Tirages</h1>
    <button class="btn btn-primary" onclick="showAddTirageModal()">
        <i class="fas fa-plus"></i> Ajouter un tirage
    </button>
</div>

<section class="content-section">
    <div class="table-container">
        <div class="table-responsive">
            <table class="table" id="tiragesTable">
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
                        <tr data-id="{{ $tirage->id }}">
                            <td>
                                <div class="tirage-title">
                                    {{ $tirage->title }}
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-dotation">
                                    {{ $tirage->dotation->title }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($tirage->date)->format('d/m/Y') }}</td>
                            <td>
                                @if($tirage->winner_id)
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle"></i> Terminé
                                    </span>
                                @else
                                    <span class="badge badge-warning">
                                        <i class="fas fa-clock"></i> En attente
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($tirage->winner)
                                    <div class="winner-info">
                                        <div class="winner-name">{{ $tirage->winner->firstname }} {{ $tirage->winner->lastname }}</div>
                                        <small class="text-muted">Tél: {{ $tirage->winner->telephone }}</small>
                                    </div>
                                @else
                                    <span class="text-muted">Non défini</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-outline-primary" onclick="showEditTirageModal({{ $tirage->id }})" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if(!$tirage->winner_id)
                                        <button class="btn btn-sm btn-outline-warning" onclick="drawTirage({{ $tirage->id }})" title="Tirer au sort">
                                            <i class="fas fa-dice"></i>
                                        </button>
                                    @else
                                        <!--button class="btn btn-sm btn-outline-info" onclick="showWinnerDetails({{ $tirage->id }})" title="Voir le gagnant">
                                            <i class="fas fa-user"></i>
                                        </!--button-->
                                    @endif
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteTirage({{ $tirage->id }})" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="empty-state">
                                    <i class="fas fa-gift fa-3x mb-3"></i>
                                    <p>Aucun tirage trouvé</p>
                                    <button class="btn btn-primary" onclick="showAddTirageModal()">
                                        <i class="fas fa-plus"></i> Ajouter un tirage
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Modal Ajout/Modification Tirage -->
<div id="tirageModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4 id="tirageModalTitle">Ajouter un tirage au sort</h4>
            <span class="close">&times;</span>
        </div>
        <form id="tirageForm" class="modal-form">
            @csrf
            <input type="hidden" id="tirage_id" name="id">
            <div class="form-group">
                <label for="title" class="form-label">Titre du tirage</label>
                <div class="input-group">
                    <div class="input-icon">
                        <i class="fas fa-heading"></i>
                    </div>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
            </div>
            <div class="form-group">
                <label for="dotation_id" class="form-label">Dotation associée</label>
                <div class="input-group">
                    <div class="input-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <select class="form-control" id="dotation_id" name="dotation_id" required>
                        <option value="">Sélectionner une dotation</option>
                        @foreach($dotations as $dotation)
                            <option value="{{ $dotation->id }}">{{ $dotation->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="date" class="form-label">Date du tirage</label>
                <div class="input-group">
                    <div class="input-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <input type="date" class="form-control" id="date" name="date" required>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="$('#tirageModal').hide()">
                    <i class="fas fa-times"></i> Annuler
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Suppression Tirage -->
<div id="deleteTirageModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Supprimer un tirage au sort</h4>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Êtes-vous sûr de vouloir supprimer ce tirage au sort ?</p>
                <p>Cette action est irréversible.</p>
            </div>
            <form id="deleteTirageForm">
                @csrf
                <input type="hidden" id="delete_tirage_id" name="id">
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="$('#deleteTirageModal').hide()">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tirage au sort -->
<div id="drawTirageModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Tirage au sort</h4>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body text-center">
            <div id="drawContent">
                <i class="fas fa-dice fa-3x mb-3 text-primary"></i>
                <p>Êtes-vous sûr de vouloir procéder au tirage au sort ?</p>
                <p>Un gagnant sera sélectionné au hasard parmi tous les participants.</p>
                <form id="drawTirageForm">
                    @csrf
                    <input type="hidden" id="draw_tirage_id" name="id">
                    <div class="form-actions justify-content-center">
                        <button type="button" class="btn btn-secondary" onclick="$('#drawTirageModal').hide()">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-dice"></i> Tirer au sort
                        </button>
                    </div>
                </form>
            </div>
            <div id="drawResult" style="display: none;">
                <i class="fas fa-trophy fa-3x mb-3 text-warning"></i>
                <h4>Le gagnant est :</h4>
                <div class="winner-card">
                    <div class="winner-avatar">
                        <i class="fas fa-user fa-2x"></i>
                    </div>
                    <div class="winner-info">
                        <h5 id="winnerName"></h5>
                        <p id="winnerEmail"></p>
                    </div>
                </div>
                <button type="button" class="btn btn-primary mt-3" onclick="location.reload()">
                    <i class="fas fa-check"></i> Terminer
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Définir les URLs de base pour les requêtes AJAX
    const tirageBaseUrl = '{{ url("/admin/tirages") }}';
    const tirageStoreUrl = '{{ route("admin.tirages.store") }}';
    
    // Afficher le modal d'ajout de tirage
    function showAddTirageModal() {
        $('#tirageModalTitle').text('Ajouter un tirage au sort');
        $('#tirage_id').val('');
        $('#title').val('');
        $('#dotation_id').val('');
        $('#date').val('');
        $('#tirageForm').attr('action', tirageStoreUrl);
        $('#tirageModal').show();
    }

    // Afficher le modal de modification de tirage
    function showEditTirageModal(id) {
        $('#tirageModalTitle').text('Modifier un tirage au sort');
        $('#tirage_id').val(id);
        
        // Afficher un indicateur de chargement
        const $row = $('tr[data-id="' + id + '"]');
        const originalButtons = $row.find('.action-buttons').html();
        $row.find('.action-buttons').html('<i class="fas fa-spinner fa-spin"></i>');
        
        // Récupérer les données du tirage via AJAX
        $.get(tirageBaseUrl + '/' + id + '/data', function(data) {
            $('#title').val(data.title);
            $('#dotation_id').val(data.dotation_id);
            $('#date').val(data.date);
            $('#tirageForm').attr('action', tirageBaseUrl + '/' + id);
            $('#tirageModal').show();
            
            // Restaurer les boutons d'action
            $row.find('.action-buttons').html(originalButtons);
        }).fail(function(xhr) {
            alert('Erreur: ' + xhr.responseText);
            $row.find('.action-buttons').html(originalButtons);
        });
    }

    // Supprimer un tirage
    function deleteTirage(id) {
        $('#delete_tirage_id').val(id);
        $('#deleteTirageForm').attr('action', tirageBaseUrl + '/' + id);
        $('#deleteTirageModal').show();
    }

    // Tirer au sort
    function drawTirage(id) {
        $('#draw_tirage_id').val(id);
        $('#drawTirageForm').attr('action', tirageBaseUrl + '/' + id + '/draw');
        $('#drawContent').show();
        $('#drawResult').hide();
        $('#drawTirageModal').show();
    }
    
    // Afficher les détails du gagnant
    function showWinnerDetails(tirageId) {
        // Récupérer les informations du tirage
        $.get(tirageBaseUrl + '/' + tirageId + '/data', function(data) {
            if (data.winner) {
                alert('Gagnant: ' + data.winner.firstname + ' ' + data.winner.lastname + '\nEmail: ' + data.winner.email);
            }
        }).fail(function(xhr) {
            alert('Erreur: ' + xhr.responseText);
        });
    }

    // Soumission du formulaire de tirage
    $('#tirageForm').on('submit', function(e) {
        e.preventDefault();
        
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.html();
        $submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Enregistrement...').prop('disabled', true);
        
        var formData = new FormData(this);
        var id = $('#tirage_id').val();
        
        // Ajouter la méthode PUT pour la modification
        if (id) {
            formData.append('_method', 'PUT');
        }
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#tirageModal').hide();
                location.reload();
            },
            error: function(xhr) {
                alert('Erreur: ' + xhr.responseText);
                $submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Soumission du formulaire de suppression
    $('#deleteTirageForm').on('submit', function(e) {
        e.preventDefault();
        
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.html();
        $submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Suppression...').prop('disabled', true);
        
        var formData = new FormData(this);
        formData.append('_method', 'DELETE');
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#deleteTirageModal').hide();
                location.reload();
            },
            error: function(xhr) {
                alert('Erreur: ' + xhr.responseText);
                $submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });
    
    // Soumission du formulaire de tirage au sort
 $('#drawTirageForm').on('submit', function(e) {
    e.preventDefault();
    
    const $submitBtn = $(this).find('button[type="submit"]');
    const originalText = $submitBtn.html();
    $submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Tirage en cours...').prop('disabled', true);
    
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',  // Spécifier que nous attendons du JSON
        success: function(response) {
            if (response.success) {
                // Afficher le résultat du tirage
                $('#drawContent').hide();
                $('#winnerName').text(response.winner_firstname + ' ' + response.winner_lastname);
                $('#winnerEmail').text(response.winner_email);
                $('#drawResult').show();
            } else {
                alert('Erreur: ' + response.error);
                $submitBtn.html(originalText).prop('disabled', false);
                $('#drawTirageModal').hide();
            }
        },
        error: function(xhr) {
            let errorMessage = 'Une erreur est survenue lors du tirage au sort.';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMessage = xhr.responseJSON.error;
            }
            alert('Erreur: ' + errorMessage);
            $submitBtn.html(originalText).prop('disabled', false);
            $('#drawTirageModal').hide();
        }
    });
});
</script>
@endpush