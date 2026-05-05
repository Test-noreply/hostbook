<?php
// Démarrer la session si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function parametres($titre)
{
    echo ('
    <head>
        <title>' . htmlspecialchars($titre) . '</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" sizes="32x32" href="https://img.icons8.com/?size=100&id=kfRfIRUL7jMk&format=png&color=000000">
        <!-- Utilisation de Bootstrap 5 -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    ');
}

function entete()
{
    echo ('<header class="p-3 bg-dark text-white mb-0">');
    echo ('<div class="container">');
    echo ('<div class="row align-items-center">');

    // Titre
    echo ('<div class="col">');
    echo ('<h2 class="m-0">Intranet Entreprise</h2>');
    echo ('</div>');

    // Zone utilisateur
    echo ('<div class="col-auto text-end">');
    if (isset($_SESSION['pseudo'])) {
        $groupe = $_SESSION['role'] ?? 'user';
        echo '<p class="m-0">Bonjour <strong>' . htmlspecialchars($_SESSION['pseudo']) . '</strong> <span class="badge bg-secondary">' . htmlspecialchars($groupe) . '</span></p>';
        echo '<a href="deconnexion.php" class="btn btn-outline-light btn-sm mt-1">Se déconnecter</a>';
    } else {
        echo '<p class="m-0">Non connecté</p>';
        echo '<a href="connexion.php" class="btn btn-outline-light btn-sm mt-1">S\'identifier</a>';
    }
    echo ('</div>');

    echo ('</div>'); // fin row
    echo ('</div>'); // fin container
    echo ('</header>');
}

function navigation()
{
    // On n'affiche la navigation complète que si l'utilisateur est connecté
    $page_active = basename($_SERVER['PHP_SELF']);
    
    echo ('<nav class="navbar navbar-expand-lg navbar-dark bg-secondary mb-4">');
    echo ('<div class="container">');
    echo ('<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">');
    echo ('<span class="navbar-toggler-icon"></span>');
    echo ('</button>');
    echo ('<div class="collapse navbar-collapse" id="navbarNav">');
    echo ('<ul class="navbar-nav me-auto">');
    
    if (isset($_SESSION['pseudo'])) {
        echo '<li class="nav-item"><a class="nav-link ' . ($page_active == 'accueil_intranet.php' ? 'active fw-bold' : '') . '" href="accueil_intranet.php">Accueil</a></li>';
        echo '<li class="nav-item"><a class="nav-link ' . ($page_active == 'annuaire_employe.php' ? 'active fw-bold' : '') . '" href="annuaire_employe.php">Employés</a></li>';
        echo '<li class="nav-item"><a class="nav-link ' . ($page_active == 'annuaire_fournisseurs.php' ? 'active fw-bold' : '') . '" href="annuaire_fournisseurs.php">Fournisseurs</a></li>';
        echo '<li class="nav-item"><a class="nav-link ' . ($page_active == 'annuaire_client.php' ? 'active fw-bold' : '') . '" href="annuaire_client.php">Clients</a></li>';
        echo '<li class="nav-item"><a class="nav-link ' . ($page_active == 'partage.php' ? 'active fw-bold' : '') . '" href="partage.php">Partage de fichiers</a></li>';
        
        // Seuls certains groupes ont accès à la gestion des utilisateurs (ex: admin, direction)
        $role = $_SESSION['role'] ?? '';
        if (in_array($role, ['admin', 'direction'])) {
            echo '<li class="nav-item"><a class="nav-link ' . ($page_active == 'gestion_utilisateurs.php' ? 'active fw-bold' : '') . '" href="gestion_utilisateurs.php">Utilisateurs</a></li>';
        }
    }
    
    echo '<li class="nav-item"><a class="nav-link ' . ($page_active == 'wiki.php' ? 'active fw-bold' : '') . '" href="wiki.php">Wiki</a></li>';
    
    echo ('</ul>');
    echo ('</div>'); // collapse
    echo ('</div>'); // container
    echo ('</nav>');
}

function pieddepage()
{
    $annee = date('Y');
    echo ('
    <footer class="p-4 bg-light text-center border-top mt-5">
        <div class="container">
            <p>SAÉ 203 - Développement d\'un portail web</p>
            <p>&copy; ' . $annee . ' - Intranet d\'Entreprise</p>
        </div>
    </footer>
    ');
}
?>
