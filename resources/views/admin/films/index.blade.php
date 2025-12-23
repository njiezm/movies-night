@extends('layouts.admin')
@section('title','Films')
@section('content')
<h3 class="text-white text-center mb-4"><i class="fas fa-video"></i> Gestion des Films</h3>

<div class="text-center mb-4">
    <button class="btn btn-danger" onclick="showAddFilmModal()">
        <i class="fas fa-plus"></i> Ajouter un film
    </button>
</div>

<div class="film-grid">
    @foreach($films as $film)
        <div class="film-card">
            @if($film->vignette)
                <img src="{{ asset('storage/'.$film->vignette) }}" alt="{{ $film->title }}">
            @else
                <img src="https://via.placeholder.com/300x180/333/fff?text=No+Image" alt="{{ $film->title }}">
            @endif
            <div class="film-card-content">
                <h4 class="film-card-title">{{ $film->title }}</h4>
                <p class="film-card-description">{{ Str::limit($film->description, 80) }}</p>
                
                @if($film->qrcode)
                    <div class="qr-code-container">
                        <img src="{{ asset($film->qrcode) }}" alt="QR Code" onclick="showQrModal('{{ asset($film->qrcode) }}', '{{ route('scan', $film->slug) }}')">
                        <div class="qr-link">{{ route('scan', $film->slug) }}</div>
                    </div>
                @endif
                
                <div class="film-card-footer">
                    <span><i class="fas fa-users"></i> {{ $film->participants_count }}</span>
                    <div>
                        <button class="btn btn-primary btn-sm" onclick="showEditFilmModal({{ $film->id }})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteFilm({{ $film->id }})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Modal Ajout/Modification Film -->
<div id="filmModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4 id="filmModalTitle">Ajouter un film</h4>
            <span class="close">&times;</span>
        </div>
        <form id="filmForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="film_id" name="id">
            <div class="form-group">
                <label for="title">Titre</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description"></textarea>
            </div>
            <div class="form-group">
                <label for="vignette">Vignette</label>
                <input type="file" class="form-control" id="vignette" name="vignette">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-danger">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('modals')
<!-- Modal Suppression Film -->
<div id="deleteFilmModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Supprimer un film</h4>
            <span class="close">&times;</span>
        </div>
        <p>Êtes-vous sûr de vouloir supprimer ce film ?</p>
        <form id="deleteFilmForm">
            @csrf
            @method('DELETE')
            <input type="hidden" id="delete_film_id" name="id">
            <button type="submit" class="btn btn-danger">Supprimer</button>
            <button type="button" class="btn btn-secondary" onclick="$('#deleteFilmModal').hide()">Annuler</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Afficher le modal d'ajout de film
    function showAddFilmModal() {
        $('#filmModalTitle').text('Ajouter un film');
        $('#film_id').val('');
        $('#title').val('');
        $('#description').val('');
        $('#vignette').val('');
        $('#filmForm').attr('action', '{{ route("admin.films.store") }}');
        $('#filmModal').show();
    }

    // Afficher le modal de modification de film
    function showEditFilmModal(id) {
        $('#filmModalTitle').text('Modifier un film');
        $('#film_id').val(id);
        
        // Récupérer les données du film via AJAX
        $.get('/admin/films/' + id + '/data', function(data) {
            $('#title').val(data.title);
            $('#description').val(data.description);
            $('#filmForm').attr('action', '/admin/films/' + id);
            $('#filmModal').show();
        });
    }

    // Supprimer un film
    function deleteFilm(id) {
        $('#delete_film_id').val(id);
        $('#deleteFilmForm').attr('action', '/admin/films/' + id);
        $('#deleteFilmModal').show();
    }

    // Soumission du formulaire de film
    $('#filmForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var id = $('#film_id').val();
        var url = id ? '/admin/films/' + id : '/admin/films';
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
    $('#deleteFilmForm').on('submit', function(e) {
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