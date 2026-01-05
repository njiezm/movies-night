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
        <button class="btn btn-primary" onclick="showAddFilmModal()">
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
    <button class="btn-filter" onclick="applyFilters()">
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
            <div class="film-card" data-id="{{ $film->id }}" data-title="{{ strtolower($film->title) }}" data-participants="{{ $film->participants_count }}" data-start-date="{{ $film->start_date ?? '' }}" data-end-date="{{ $film->end_date ?? '' }}">
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
                            <small class="text-muted">
                                <i class="fas fa-calendar-alt"></i> 
                                Du {{ \Carbon\Carbon::parse($film->start_date)->format('d/m/Y') }} 
                                au {{ \Carbon\Carbon::parse($film->end_date)->format('d/m/Y') }}
                            </small>
                        </div>
                    @endif
                    
                    <div class="film-card-footer">
                        <div class="film-actions">
                            <button class="btn btn-sm btn-outline-primary" onclick="showEditFilmModal({{ $film->id }})" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-info" onclick="showQrModal('{{ asset($film->qrcode ?? '') }}', '{{ route('scan', $film->slug) }}')" title="Voir le QR Code">
                                <i class="fas fa-qrcode"></i>
                            </button>
                            @if($film->qrcode)
                            <button class="btn btn-sm btn-outline-success" onclick="downloadQrCode('{{ asset($film->qrcode) }}', '{{ $film->title }}')" title="Télécharger le QR Code">
                                <i class="fas fa-download"></i>
                            </button>
                            @endif
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteFilm({{ $film->id }})" title="Supprimer">
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
                <button class="btn btn-primary" onclick="showAddFilmModal()">
                    <i class="fas fa-plus"></i> Ajouter un film
                </button>
            </div>
        @endforelse
    </div>
    
    <!-- Vue liste (cachée par défaut) -->
    <div class="table-container" id="filmsListView" style="display: none;">
        <div class="table-responsive">
            <table class="table">
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
                        <tr data-id="{{ $film->id }}" data-title="{{ strtolower($film->title) }}" data-participants="{{ $film->participants_count }}" data-start-date="{{ $film->start_date ?? '' }}" data-end-date="{{ $film->end_date ?? '' }}">
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
                                        <i class="fas fa-calendar-alt"></i> 
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
                                    <button class="btn btn-sm btn-outline-info" onclick="showQrModal('{{ asset($film->qrcode) }}', '{{ route('scan', $film->slug) }}')">
                                        <i class="fas fa-qrcode"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" onclick="downloadQrCode('{{ asset($film->qrcode) }}', '{{ $film->title }}')" title="Télécharger le QR Code">
                                        <i class="fas fa-download"></i>
                                    </button>
                                @else
                                    <span class="text-muted">Non généré</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-outline-primary" onclick="showEditFilmModal({{ $film->id }})" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteFilm({{ $film->id }})" title="Supprimer">
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
                                    <button class="btn btn-primary" onclick="showAddFilmModal()">
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

<!-- Modal Ajout/Modification Film -->
<div id="filmModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4 id="filmModalTitle">Ajouter un film</h4>
            <span class="close">&times;</span>
        </div>
        <form id="filmForm" class="modal-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="film_id" name="id">
            <div class="form-group">
                <label for="title" class="form-label">Titre du film</label>
                <div class="input-group">
                    <div class="input-icon">
                        <i class="fas fa-heading"></i>
                    </div>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
            </div>
            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <div class="input-group">
                    <div class="input-icon" style="top: 15px;">
                        <i class="fas fa-align-left"></i>
                    </div>
                    <textarea class="form-control" id="description" name="description" rows="4"></textarea>
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
                            <input type="date" class="form-control" id="start_date" name="start_date">
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
                            <input type="date" class="form-control" id="end_date" name="end_date">
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="vignette" class="form-label">Affiche</label>
                <div class="file-upload">
                    <input type="file" class="form-control" id="vignette" name="vignette" accept="image/*">
                    <label for="vignette" class="file-upload-label">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span id="file-name">Choisir une image</span>
                    </label>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="$('#filmModal').hide()">
                    <i class="fas fa-times"></i> Annuler
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Suppression Film -->
<div id="deleteFilmModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Supprimer un film</h4>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Êtes-vous sûr de vouloir supprimer ce film ?</p>
                <p>Cette action est irréversible et supprimera également toutes les données associées.</p>
            </div>
            <form id="deleteFilmForm">
                @csrf
                <input type="hidden" id="delete_film_id" name="id">
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="$('#deleteFilmModal').hide()">
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

<!-- Modal QR Code -->
<div id="qrModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4>QR Code du film</h4>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body text-center">
            <div id="qrCodeContainer">
                <!-- Le QR code sera affiché ici -->
            </div>
            <div class="mt-3">
                <p id="qrCodeUrl" class="text-muted small"></p>
            </div>
            <div class="mt-3">
                <button id="downloadQrBtn" class="btn btn-success">
                    <i class="fas fa-download"></i> Télécharger le QR Code
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Définir les URLs de base pour les requêtes AJAX
    const filmBaseUrl = '{{ url("/admin/films") }}';
    const filmStoreUrl = '{{ route("admin.films.store") }}';
    
    // Gestion des vues (grille/liste)
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
    
    // Recherche de films
    $('#searchFilms').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        
        if (searchTerm === '') {
            $('.film-card').show();
            $('#filmsListView tbody tr').show();
        } else {
            $('.film-card').each(function() {
                const title = $(this).data('title');
                $(this).toggle(title.includes(searchTerm));
            });
            
            $('#filmsListView tbody tr').each(function() {
                const title = $(this).data('title');
                $(this).toggle(title.includes(searchTerm));
            });
        }
    });
    
    // Appliquer les filtres
    function applyFilters() {
        const sortBy = $('#filterBy').val();
        const sortOrder = $('#filterOrder').val();
        
        let sortedFilms = $('.film-card').toArray().sort(function(a, b) {
            let aVal, bVal;
            
            if (sortBy === 'title') {
                aVal = $(a).data('title');
                bVal = $(b).data('title');
            } else if (sortBy === 'participants') {
                aVal = parseInt($(a).data('participants'));
                bVal = parseInt($(b).data('participants'));
            } else if (sortBy === 'period') {
                aVal = $(a).data('start-date');
                bVal = $(b).data('start-date');
                if (aVal === '') aVal = '9999-12-31';
                if (bVal === '') bVal = '9999-12-31';
            } else {
                // Par défaut, trier par ID
                aVal = parseInt($(a).data('id'));
                bVal = parseInt($(b).data('id'));
            }
            
            if (sortOrder === 'asc') {
                return aVal > bVal ? 1 : -1;
            } else {
                return aVal < bVal ? 1 : -1;
            }
        });
        
        // Réorganiser les cartes
        $('.film-grid').empty().append(sortedFilms);
        
        // Appliquer le même tri à la vue liste
        let sortedRows = $('#filmsListView tbody tr').toArray().sort(function(a, b) {
            let aVal, bVal;
            
            if (sortBy === 'title') {
                aVal = $(a).find('td:eq(1)').text().toLowerCase();
                bVal = $(b).find('td:eq(1)').text().toLowerCase();
            } else if (sortBy === 'participants') {
                aVal = parseInt($(a).find('td:eq(4)').text());
                bVal = parseInt($(b).find('td:eq(4)').text());
            } else if (sortBy === 'period') {
                aVal = $(a).data('start-date');
                bVal = $(b).data('start-date');
                if (aVal === '') aVal = '9999-12-31';
                if (bVal === '') bVal = '9999-12-31';
            } else {
                // Par défaut, trier par ID
                aVal = parseInt($(a).data('id'));
                bVal = parseInt($(b).data('id'));
            }
            
            if (sortOrder === 'asc') {
                return aVal > bVal ? 1 : -1;
            } else {
                return aVal < bVal ? 1 : -1;
            }
        });
        
        $('#filmsListView tbody').empty().append(sortedRows);
    }
    
    // Gestion de l'upload de fichier
    $('#vignette').change(function() {
        const fileName = $(this).val().split('\\').pop();
        $('#file-name').text(fileName || 'Choisir une image');
    });
    
    // Afficher le modal d'ajout de film
    function showAddFilmModal() {
        $('#filmModalTitle').text('Ajouter un film');
        $('#film_id').val('');
        $('#title').val('');
        $('#description').val('');
        $('#start_date').val('');
        $('#end_date').val('');
        $('#vignette').val('');
        $('#file-name').text('Choisir une image');
        $('#filmForm').attr('action', filmStoreUrl);
        $('#filmModal').show();
    }

    // Afficher le modal de modification de film
    function showEditFilmModal(id) {
        $('#filmModalTitle').text('Modifier un film');
        $('#film_id').val(id);
        
        // Afficher un indicateur de chargement
        const $card = $('.film-card[data-id="' + id + '"]');
        const originalActions = $card.find('.film-actions').html();
        $card.find('.film-actions').html('<i class="fas fa-spinner fa-spin"></i>');
        
        // Récupérer les données du film via AJAX
        $.get(filmBaseUrl + '/' + id + '/data', function(data) {
            $('#title').val(data.title);
            $('#description').val(data.description);
            $('#start_date').val(data.start_date);
            $('#end_date').val(data.end_date);
            $('#filmForm').attr('action', filmBaseUrl + '/' + id);
            $('#filmModal').show();
            
            // Restaurer les boutons d'action
            $card.find('.film-actions').html(originalActions);
        }).fail(function(xhr) {
            alert('Erreur: ' + xhr.responseText);
            $card.find('.film-actions').html(originalActions);
        });
    }

    // Afficher le modal du QR Code
    function showQrModal(qrCodePath, scanUrl) {
        if (!qrCodePath) {
            alert('QR Code non disponible pour ce film');
            return;
        }
        
        $('#qrCodeContainer').html('<img src="' + qrCodePath + '" alt="QR Code" class="img-fluid">');
        $('#qrCodeUrl').text(scanUrl);
        
        // Configurer le bouton de téléchargement
        $('#downloadQrBtn').attr('onclick', 'downloadQrCode("' + qrCodePath + '", "QR Code")');
        
        $('#qrModal').show();
    }
    
    // Télécharger le QR Code
    function downloadQrCode(qrCodePath, filmTitle) {
        if (!qrCodePath) {
            alert('QR Code non disponible pour ce film');
            return;
        }
        
        // Créer un lien temporaire pour le téléchargement
        const link = document.createElement('a');
        link.href = qrCodePath;
        link.download = 'QRCode_' + filmTitle.replace(/\s+/g, '_') + '.png';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Supprimer un film
    function deleteFilm(id) {
        $('#delete_film_id').val(id);
        $('#deleteFilmForm').attr('action', filmBaseUrl + '/' + id);
        $('#deleteFilmModal').show();
    }

    // Soumission du formulaire de film
    $('#filmForm').on('submit', function(e) {
        e.preventDefault();
        
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.html();
        $submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Enregistrement...').prop('disabled', true);
        
        var formData = new FormData(this);
        var id = $('#film_id').val();
        
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
                $('#filmModal').hide();
                location.reload();
            },
            error: function(xhr) {
                alert('Erreur: ' + xhr.responseText);
                $submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Soumission du formulaire de suppression
    $('#deleteFilmForm').on('submit', function(e) {
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
                $('#deleteFilmModal').hide();
                location.reload();
            },
            error: function(xhr) {
                alert('Erreur: ' + xhr.responseText);
                $submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });
    
    // Gestion des modals
    $('.modal .close').on('click', function() {
        $(this).closest('.modal').hide();
    });
    
    // Fermer les modals en cliquant à l'extérieur
    $(window).on('click', function(event) {
        if ($(event.target).hasClass('modal')) {
            $(event.target).hide();
        }
    });
</script>
@endpush