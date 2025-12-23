@extends('layouts.admin')
@section('title','Tirages au sort')
@section('content')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-gift"></i> Tirages au sort</h3>
        <button class="btn btn-success" onclick="showAddTirageModal()">
            <i class="fas fa-plus"></i> Ajouter un tirage
        </button>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Dotation</th>
                    <th>Date</th>
                    <th>Gagnant</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tirages as $tirage)
                    <tr>
                        <td>{{ $tirage->title }}</td>
                        <td>{{ $tirage->dotation->title }}</td>
                        <td>{{ $tirage->date }}</td>
                        <td>
                            @if($tirage->winner)
                                {{ $tirage->winner->firstname }} {{ $tirage->winner->lastname }}
                            @else
                                <span class="text-muted">Non défini</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-primary btn-sm" onclick="showEditTirageModal({{ $tirage->id }})">
                                <i class="fas fa-edit"></i>
                            </button>
                            @if(!$tirage->winner_id)
                                <button class="btn btn-warning btn-sm" onclick="drawTirage({{ $tirage->id }})">
                                    <i class="fas fa-dice"></i> Tirer
                                </button>
                            @endif
                            <button class="btn btn-danger btn-sm" onclick="deleteTirage({{ $tirage->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ajout/Modification Tirage -->
<div id="tirageModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4 id="tirageModalTitle">Ajouter un tirage au sort</h4>
            <span class="close">&times;</span>
        </div>
        <form id="tirageForm">
            @csrf
            <input type="hidden" id="tirage_id" name="id">
            <div class="form-group">
                <label for="title">Titre</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="dotation_id">Dotation</label>
                <select class="form-control" id="dotation_id" name="dotation_id" required>
                    <option value="">Sélectionner une dotation</option>
                    @foreach($dotations as $dotation)
                        <option value="{{ $dotation->id }}">{{ $dotation->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('modals')
<!-- Modal Suppression Tirage -->
<div id="deleteTirageModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Supprimer un tirage au sort</h4>
            <span class="close">&times;</span>
        </div>
        <p>Êtes-vous sûr de vouloir supprimer ce tirage au sort ?</p>
        <form id="deleteTirageForm">
            @csrf
            @method('DELETE')
            <input type="hidden" id="delete_tirage_id" name="id">
            <button type="submit" class="btn btn-danger">Supprimer</button>
            <button type="button" class="btn btn-secondary" onclick="$('#deleteTirageModal').hide()">Annuler</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Afficher le modal d'ajout de tirage
    function showAddTirageModal() {
        $('#tirageModalTitle').text('Ajouter un tirage au sort');
        $('#tirage_id').val('');
        $('#title').val('');
        $('#dotation_id').val('');
        $('#date').val('');
        $('#tirageForm').attr('action', '{{ route("admin.tirages.store") }}');
        $('#tirageModal').show();
    }

    // Afficher le modal de modification de tirage
    function showEditTirageModal(id) {
        $('#tirageModalTitle').text('Modifier un tirage au sort');
        $('#tirage_id').val(id);
        
        // Récupérer les données du tirage via AJAX
        $.get('/admin/tirages/' + id + '/data', function(data) {
            $('#title').val(data.title);
            $('#dotation_id').val(data.dotation_id);
            $('#date').val(data.date);
            $('#tirageForm').attr('action', '/admin/tirages/' + id);
            $('#tirageModal').show();
        });
    }

    // Supprimer un tirage
    function deleteTirage(id) {
        $('#delete_tirage_id').val(id);
        $('#deleteTirageForm').attr('action', '/admin/tirages/' + id);
        $('#deleteTirageModal').show();
    }

    // Tirer au sort
    function drawTirage(id) {
        if (confirm('Êtes-vous sûr de vouloir procéder au tirage au sort ?')) {
            $.post('/admin/tirages/' + id + '/draw', {
                _token: '{{ csrf_token() }}'
            }, function(response) {
                location.reload();
            }).fail(function(xhr) {
                alert('Erreur: ' + xhr.responseText);
            });
        }
    }

    // Soumission du formulaire de tirage
    $('#tirageForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var id = $('#tirage_id').val();
        var url = id ? '/admin/tirages/' + id : '/admin/tirages';
        var method = id ? 'POST' : 'POST';
        
        if (id) {
            formData.append('_method', 'POST');
        }
        
        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                alert('Erreur: ' + xhr.responseText);
            }
        });
    });

    // Soumission du formulaire de suppression
    $('#deleteTirageForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                alert('Erreur: ' + xhr.responseText);
            }
        });
    });
</script>
@endpush