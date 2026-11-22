@if (session('succes'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('succes') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
@endif

@if (session('erreur'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('erreur') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
@endif