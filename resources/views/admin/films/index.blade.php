@extends('layouts.admin')
@section('title', 'Films')
@section('content')

<div class="page-header">
    <h1><i class="fas fa-video"></i> Gestion des Films</h1>
    <div class="header-actions">
        <div class="search-box">
            <div class="input-group">
                <div class="input-icon">
                    <i class="fas fa-search"></i>
                </div>
                <input type="text" id="searchFilms" class="form-control" placeholder="Rechercher un film...">
            </div>
        </div>
        <button class="btn btn-primary" id="addFilmBtn">
            <i class="fas fa-plus"></i> Ajouter un film
        </button>
    </div>
</div>

<!-- Filtres -->
<div class="filter-section">
    <div class="filter-group">
        <label for="filterBy">Trier par</label>
        <select id="filterBy" class="form-control">
            <option value="title">Titre</option>
            <option value="participants">Participants</option>
            <option value="date">Date d'ajout</option>
            <option value="period">Période</option>
        </select>
    </div>
    <div class="filter-group">
        <label for="filterOrder">Ordre</label>
        <select id="filterOrder" class="form-control">
            <option value="asc">Croissant</option>
            <option value="desc">Décroissant</option>
        </select>
    </div>
    <button class="btn-filter" id="applyFiltersBtn">
        <i class="fas fa-filter"></i> Appliquer
    </button>
</div>

<section class="content-section">
    <div class="section-header">
        <h2>Liste des films</h2>
        <div class="view-toggle">
            <button class="btn btn-sm btn-outline-secondary active" id="gridView" title="Vue grille">
                <i class="fas fa-th"></i>
            </button>
            <button class="btn btn-sm btn-outline-secondary" id="listView" title="Vue liste">
                <i class="fas fa-list"></i>
            </button>
        </div>
    </div>
    
   <!-- Vue grille -->
    <div class="film-grid" id="filmsGridView">
        @forelse($films as $film)
            <div class="film-card" data-id="{{ $film->id }}" data-title="{{ strtolower($film->title) }}" data-participants="{{ $film->participants_count }}" data-start-date="{{ $film->start_date ?? '' }}" data-end-date="{{ $film->end_date ?? '' }}" data-description="{{ $film->description }}">
                <div class="film-poster">
                    @if($film->vignette)
                        <img src="{{ asset('storage/'.$film->vignette) }}" alt="{{ $film->title }}">
                    @else
                        <img src="https://via.placeholder.com/300x180/333/fff?text=No+Image" alt="{{ $film->title }}">
                    @endif
                    <div class="film-overlay">
                        <div class="film-stats">
                            <span><i class="fas fa-users"></i> {{ $film->participants_count }}</span>
                            <span><i class="fas fa-qrcode"></i> QR</span>
                            @if($film->start_date && $film->end_date)
                                <span><i class="fas fa-calendar"></i> {{ $film->start_date }} au {{ $film->end_date }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="film-card-content">
                    <h4 class="film-card-title">{{ $film->title }}</h4>
                    <p class="film-card-description">{{ Str::limit($film->description, 80) }}</p>
                    
                    @if($film->start_date && $film->end_date)
                        <div class="film-period mb-2">
                            <small class="text-white">
                                <i class="fas fa-calendar-alt"></i> 
                                Du {{ \Carbon\Carbon::parse($film->start_date)->format('d/m/Y') }} 
                                au {{ \Carbon\Carbon::parse($film->end_date)->format('d/m/Y') }}
                            </small>
                        </div>
                    @endif
                    
                    <div class="film-card-footer">
                        <div class="film-actions">
                            <button class="btn btn-sm btn-outline-primary edit-film-btn" data-id="{{ $film->id }}" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-info show-qr-btn" data-qr="{{ asset($film->qrcode ?? '') }}" data-url="{{ route('scan', $film->slug) }}" title="Voir le QR Code">
                                <i class="fas fa-qrcode"></i>
                            </button>
                            @if($film->qrcode)
                            <button class="btn btn-sm btn-outline-success download-qr-btn" data-qr="{{ asset($film->qrcode) }}" data-title="{{ $film->title }}" title="Télécharger le QR Code">
                                <i class="fas fa-download"></i>
                            </button>
                            @endif
                            <button class="btn btn-sm btn-outline-danger delete-film-btn" data-id="{{ $film->id }}" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state col-12">
                <i class="fas fa-film fa-4x mb-3"></i>
                <h3>Aucun film trouvé</h3>
                <p>Commencez par ajouter votre premier film.</p>
                <button class="btn btn-primary" id="addFilmEmptyBtn">
                    <i class="fas fa-plus"></i> Ajouter un film
                </button>
            </div>
        @endforelse
    </div>
    
    <!-- Vue liste (cachée par défaut) -->
    <div class="table-container" id="filmsListView" style="display: none;">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Affiche</th>
                        <th>Titre</th>
                        <th>Description</th>
                        <th>Période</th>
                        <th>Participants</th>
                        <th>QR Code</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($films as $film)
                        <tr data-id="{{ $film->id }}" data-title="{{ strtolower($film->title) }}" data-participants="{{ $film->participants_count }}" data-start-date="{{ $film->start_date ?? '' }}" data-end-date="{{ $film->end_date ?? '' }}" data-description="{{ $film->description }}">
                            <td>
                                @if($film->vignette)
                                    <img src="{{ asset('storage/'.$film->vignette) }}" alt="{{ $film->title }}" class="film-thumbnail">
                                @else
                                    <img src="https://via.placeholder.com/60x60/333/fff?text=No+Image" alt="{{ $film->title }}" class="film-thumbnail">
                                @endif
                            </td>
                            <td>{{ $film->title }}</td>
                            <td>{{ Str::limit($film->description, 50) }}</td>
                            <td>
                                @if($film->start_date && $film->end_date)
                                    <small>
                                        <i class="fas fa-calendar-alt text-white"></i> 
                                        Du {{ \Carbon\Carbon::parse($film->start_date)->format('d/m/Y') }} 
                                        au {{ \Carbon\Carbon::parse($film->end_date)->format('d/m/Y') }}
                                    </small>
                                @else
                                    <span class="text-muted">Non définie</span>
                                @endif
                            </td>
                            <td>{{ $film->participants_count }}</td>
                            <td>
                                @if($film->qrcode)
                                    <button class="btn btn-sm btn-outline-info show-qr-btn" data-qr="{{ asset($film->qrcode) }}" data-url="{{ route('scan', $film->slug) }}">
                                        <i class="fas fa-qrcode"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success download-qr-btn" data-qr="{{ asset($film->qrcode) }}" data-title="{{ $film->title }}" title="Télécharger le QR Code">
                                        <i class="fas fa-download"></i>
                                    </button>
                                @else
                                    <span class="text-muted">Non généré</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-outline-primary edit-film-btn" data-id="{{ $film->id }}" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-film-btn" data-id="{{ $film->id }}" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                <div class="empty-state">
                                    <i class="fas fa-film fa-3x mb-3"></i>
                                    <p>Aucun film trouvé</p>
                                    <button class="btn btn-primary" id="addFilmTableBtn">
                                        <i class="fas fa-plus"></i> Ajouter un film
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

{{-- MODAL AJOUT / MODIF --}}
<div class="modal fade" id="filmModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="filmForm" class="modal-content admin-form" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="filmModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="id" id="film_id">

                <div class="form-group">
                    <label for="title" class="form-label">Titre du film</label>
                    <div class="input-group">
                        <div class="input-icon">
                            <i class="fas fa-heading"></i>
                        </div>
                        <input style="color: black !important" type="text-dark" class="form-control" name="title" id="title" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <div class="input-group">
                        <div class="input-icon" style="align-items: flex-start; padding-top: 0.75rem;">
                            <i class="fas fa-align-left"></i>
                        </div>
                        <textarea style="color: black !important" class="form-control" name="description" id="description" rows="4"></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="start_date" class="form-label">Date de début</label>
                            <div class="input-group">
                                <div class="input-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <input style="color: black !important" type="date" class="form-control" name="start_date" id="start_date">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="end_date" class="form-label">Date de fin</label>
                            <div class="input-group">
                                <div class="input-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <input style="color: black !important" type="date" class="form-control" name="end_date" id="end_date">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="vignette" class="form-label">Affiche</label>
                    <div class="input-group">
                        <div class="input-icon">
                            <i class="fas fa-image"></i>
                        </div>
                        <input style="color: black !important" type="file" class="form-control" name="vignette" id="vignette" accept="image/*">
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
<div class="modal fade" id="deleteFilmModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form method="POST" id="deleteFilmForm" class="modal-content admin-form">
            @csrf
            @method('DELETE')
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Supprimer le film
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

{{-- MODAL QR CODE --}}
<div class="modal fade" id="qrModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-qrcode"></i> QR Code du film
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qrCodeContainer">
                    <!-- Le QR code sera affiché ici -->
                </div>
                <div class="mt-3">
                    <p id="qrCodeUrl" class="text-muted small"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Fermer
                </button>
                <button type="button" class="btn btn-success" id="downloadQrBtn">
                    <i class="fas fa-download"></i> Télécharger
                </button>
            </div>
        </div>
    </div>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {!! session('error') !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@endsection

@push('scripts')
<script>
 $(function () {
    const baseUrl = '{{ url("/admin/films") }}';
    
    // Fonction utilitaire pour afficher une modale Bootstrap
    function showModal(modalId) {
        const modal = new bootstrap.Modal(document.getElementById(modalId));
        modal.show();
    }

    // --- GESTION DES VUES (GRILLE/LISTE) ---
    $('#gridView').click(function() {
        $('#filmsGridView').show();
        $('#filmsListView').hide();
        $(this).addClass('active');
        $('#listView').removeClass('active');
    });
    
    $('#listView').click(function() {
        $('#filmsGridView').hide();
        $('#filmsListView').show();
        $(this).addClass('active');
        $('#gridView').removeClass('active');
    });

    // --- RECHERCHE ---
    $('#searchFilms').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        
        $('.film-card').each(function() {
            const title = $(this).data('title');
            $(this).toggle(title.includes(searchTerm));
        });
        
        $('#filmsListView tbody tr').each(function() {
            const title = $(this).data('title');
            $(this).toggle(title.includes(searchTerm));
        });
    });

    // --- FILTRES ---
    $('#applyFiltersBtn').click(function() {
        const sortBy = $('#filterBy').val();
        const sortOrder = $('#filterOrder').val();
        
        // Trier la vue grille
        let sortedFilms = $('.film-card').toArray().sort(function(a, b) {
            return compareElements(a, b, sortBy, sortOrder);
        });
        $('.film-grid').empty().append(sortedFilms);
        
        // Trier la vue liste
        let sortedRows = $('#filmsListView tbody tr').toArray().sort(function(a, b) {
            return compareElements(a, b, sortBy, sortOrder);
        });
        $('#filmsListView tbody').empty().append(sortedRows);
    });

    // Fonction utilitaire pour la comparaison lors du tri
    function compareElements(a, b, sortBy, sortOrder) {
        let aVal, bVal;
        const $a = $(a), $b = $(b);
        
        if (sortBy === 'title') {
            aVal = $a.data('title') || $a.find('td:eq(1)').text().toLowerCase();
            bVal = $b.data('title') || $b.find('td:eq(1)').text().toLowerCase();
        } else if (sortBy === 'participants') {
            aVal = parseInt($a.data('participants') || $a.find('td:eq(4)').text());
            bVal = parseInt($b.data('participants') || $b.find('td:eq(4)').text());
        } else if (sortBy === 'period') {
            aVal = $a.data('start-date') || '9999-12-31';
            bVal = $b.data('start-date') || '9999-12-31';
        } else { // Par défaut, trier par ID
            aVal = parseInt($a.data('id'));
            bVal = parseInt($b.data('id'));
        }
        
        if (aVal < bVal) return sortOrder === 'asc' ? -1 : 1;
        if (aVal > bVal) return sortOrder === 'asc' ? 1 : -1;
        return 0;
    }

    // --- GESTION DES FICHIERS ---
    $('#vignette').change(function() {
        const fileName = $(this).val().split('\\').pop();
        $('#file-name').text(fileName || 'Choisir une image');
    });
    
    // --- AJOUTER UN FILM ---
    $('#addFilmBtn, #addFilmEmptyBtn, #addFilmTableBtn').on('click', function () {
        $('#filmForm')[0].reset();
        $('#filmForm').attr('action', '{{ route("admin.films.store") }}');
        $('#filmForm').find('input[name="_method"]').remove();
        $('#filmModalLabel').text('Ajouter un film');
        $('#file-name').text('Choisir une image');
        showModal('filmModal');
    });

    // --- MODIFIER UN FILM ---
    // Utiliser la délégation d'événements pour les éléments dynamiques
    $(document).on('click', '.edit-film-btn', function () {
        const filmId = $(this).data('id');
        const $card = $('.film-card[data-id="' + filmId + '"]');
        
        $('#filmForm').attr('action', baseUrl + '/' + filmId);
        $('#filmForm').find('input[name="_method"]').remove();
        $('#filmForm').append('<input type="hidden" name="_method" value="PUT">');
        
        $('#film_id').val(filmId);
        $('#title').val($card.find('.film-card-title').text());
        $('#description').val($card.data('description'));
        $('#start_date').val($card.data('start-date'));
        $('#end_date').val($card.data('end-date'));
        
        $('#filmModalLabel').text('Modifier un film');
        showModal('filmModal');
    });

    // --- SUPPRIMER UN FILM ---
    $(document).on('click', '.delete-film-btn', function () {
        const filmId = $(this).data('id');
        $('#deleteFilmForm').attr('action', baseUrl + '/' + filmId);
        showModal('deleteFilmModal');
    });
    
    // --- AFFICHER LE QR CODE ---
    $(document).on('click', '.show-qr-btn', function () {
        const qrCodePath = $(this).data('qr');
        const scanUrl = $(this).data('url');
        
        if (!qrCodePath) {
            alert('QR Code non disponible pour ce film');
            return;
        }
        
        $('#qrCodeContainer').html('<img src="' + qrCodePath + '" alt="QR Code" class="img-fluid">');
        $('#qrCodeUrl').text(scanUrl);
        showModal('qrModal');
    });

    // --- TÉLÉCHARGER LE QR CODE ---
    // Bouton dans la modal QR
    $('#downloadQrBtn').on('click', function() {
        const qrSrc = $('#qrCodeContainer img').attr('src');
        if(qrSrc) {
            downloadFile(qrSrc, 'QRCode');
        }
    });
    
    // Boutons directs dans la liste/grille
    $(document).on('click', '.download-qr-btn', function () {
        const qrCodePath = $(this).data('qr');
        const filmTitle = $(this).data('title');
        
        if (!qrCodePath) {
            alert('QR Code non disponible pour ce film');
            return;
        }
        downloadFile(qrCodePath, 'QRCode_' + filmTitle.replace(/\s+/g, '_'));
    });

    // Fonction utilitaire pour télécharger un fichier
    function downloadFile(path, filename) {
        const link = document.createElement('a');
        link.href = path;
        link.download = filename + '.png';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
});
</script>
@endpush

@push('styles')
<style>

.text-muted {
    --bs-text-opacity: 1;
    color: white !important;
}
</style>
@endpush