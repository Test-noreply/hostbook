<?php
// Démarrer la session si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function parametres($titre)
{
    echo ('
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . htmlspecialchars($titre) . ' - Hostbook</title>
        <link rel="icon" type="image/png" sizes="32x32" href="https://img.icons8.com/?size=100&id=kfRfIRUL7jMk&format=png&color=000000">
        <!-- Bootstrap 5 CSS via CDN -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Fichier CSS personnalisé (surcharges de couleurs uniquement) -->
        <link href="styles.css" rel="stylesheet">
    </head>
    ');
}

function entete()
{
    // Ouvre le conteneur principal du nouveau design Hostbook
    echo ('<div class="min-vh-100 d-flex flex-column bg-light">');
}

function navigation()
{
    // On n\'affiche la navigation complète que si l\'utilisateur est connecté
    $page_active = basename($_SERVER['PHP_SELF']);

    echo ('<!-- BARRE DE NAVIGATION -->
    <nav class="navbar navbar-expand-lg border-bottom bg-white p-3 shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand hostbook-accent fw-bold fs-3" href="accueil_intranet.php">Hostbook</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHostbook"
                aria-controls="navbarHostbook" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarHostbook">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-4">');

    if (isset($_SESSION['pseudo'])) {
        echo '<li class="nav-item"><a class="nav-link ' . ($page_active == 'accueil_intranet.php' ? 'active hostbook-accent fw-bold' : 'text-dark fw-semibold') . ' px-3" href="accueil_intranet.php">Tableau de bord</a></li>';
        echo '<li class="nav-item"><a class="nav-link ' . ($page_active == 'partage.php' ? 'active hostbook-accent fw-bold' : 'text-dark fw-semibold') . ' px-3" href="partage.php">Fichiers Partagés</a></li>';
        echo '<li class="nav-item"><a class="nav-link ' . ($page_active == 'annuaire_employe.php' ? 'active hostbook-accent fw-bold' : 'text-dark fw-semibold') . ' px-3" href="annuaire_employe.php">Annuaire Employés</a></li>';
        echo '<li class="nav-item"><a class="nav-link ' . ($page_active == 'annuaire_client.php' ? 'active hostbook-accent fw-bold' : 'text-dark fw-semibold') . ' px-3" href="annuaire_client.php">Annuaire Clients</a></li>';
        echo '<li class="nav-item"><a class="nav-link ' . ($page_active == 'annuaire_fournisseurs.php' ? 'active hostbook-accent fw-bold' : 'text-dark fw-semibold') . ' px-3" href="annuaire_fournisseurs.php">Partenaires</a></li>';

        // Seuls certains groupes ont accès à la gestion des utilisateurs (ex: admin, direction)
        $role = $_SESSION['role'] ?? '';
        if (in_array($role, ['admin', 'direction'])) {
            echo '<li class="nav-item"><a class="nav-link ' . ($page_active == 'gestion_utilisateurs.php' ? 'active hostbook-accent fw-bold' : 'text-dark fw-semibold') . ' px-3" href="gestion_utilisateurs.php">Utilisateurs</a></li>';
        }
    }

    echo '<li class="nav-item"><a class="nav-link ' . ($page_active == 'wiki.php' ? 'active hostbook-accent fw-bold' : 'text-dark fw-semibold') . ' px-3" href="wiki.php">Wiki</a></li>';

    echo ('</ul>
                <div class="d-flex align-items-center">');

    if (isset($_SESSION['pseudo'])) {
        $groupe = $_SESSION['role'] ?? 'user';
        echo '<span class="me-3 text-secondary small">Connecté en tant que ' . htmlspecialchars($_SESSION['pseudo']) . ' (' . htmlspecialchars($groupe) . ')</span>';
        echo '<a href="deconnexion.php" class="btn btn-outline-danger btn-sm px-4 py-2 fw-bold">Déconnexion</a>';
    } else {
        echo '<span class="me-3 text-secondary small">Non connecté</span>';
        echo '<a href="connexion.php" class="btn btn-outline-primary btn-sm px-4 py-2 fw-bold">S\'identifier</a>';
    }

    echo ('     </div>
            </div>
        </div>
    </nav>
    <!-- ZONE DE CONTENU PRINCIPALE -->
    <main class="container-fluid flex-grow-1 p-4 p-md-5">');
}

function pieddepage()
{
    $annee = date('Y');
    echo ('
    </main>
    <footer class="p-4 bg-white text-center border-top mt-5">
        <div class="container text-secondary">
            <p>SAÉ 203 - Développement d\'un portail web</p>
            <p>&copy; ' . $annee . ' - Intranet Hostbook</p>
        </div>
    </footer>
    </div>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    ');
}
?>