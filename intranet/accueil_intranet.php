<?php
session_start();
include 'scripts/fonctions.php';

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['pseudo'])) {
    header('Location: connexion.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php parametres("Accueil Intranet"); ?>

<body>
    <?php
    entete();
    navigation();
    ?>

    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="mb-4">Bienvenue sur l'Intranet</h1>
                <p class="lead">Ceci est la page d'accueil de l'intranet de l'entreprise.</p>
                <hr class="my-4">
                <p>Utilisez le menu de navigation pour accéder aux différents annuaires et outils de gestion.</p>

                <div class="row mt-5">
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title">Employés</h5>
                                <p class="card-text">Consultez l'annuaire des employés de l'entreprise.</p>
                                <a href="annuaire_employe.php" class="btn btn-primary">Voir les employés</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title">Fournisseurs</h5>
                                <p class="card-text">Gérez les informations des partenaires et fournisseurs.</p>
                                <a href="annuaire_fournisseurs.php" class="btn btn-primary">Voir les fournisseurs</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title">Clients</h5>
                                <p class="card-text">Accédez à la base de données des clients.</p>
                                <a href="annuaire_client.php" class="btn btn-primary">Voir les clients</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php pieddepage(); ?>
</body>

</html>