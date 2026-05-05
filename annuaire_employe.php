<?php
session_start();
include 'scripts/fonctions.php';

if (!isset($_SESSION['pseudo'])) {
    header('Location: connexion.php');
    exit();
}

$fichier_employes = 'data/employes.json';
$employes = [];

if (file_exists($fichier_employes)) {
    $contenu = file_get_contents($fichier_employes);
    $employes = json_decode($contenu, true) ?? [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php parametres("Annuaire Employés"); ?>

<body>
    <?php
    entete();
    navigation();
    ?>

    <div class="container mt-5 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Annuaire des Employés</h1>
            <?php if (in_array($_SESSION['role'] ?? '', ['admin', 'direction'])): ?>
                <button class="btn btn-success">Ajouter un employé</button>
            <?php endif; ?>
        </div>

        <div class="row">
            <?php foreach ($employes as $emp): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="row g-0">
                        <div class="col-4">
                            <img src="<?= htmlspecialchars($emp['photo']) ?>" class="img-fluid rounded-start h-100 object-fit-cover" alt="Photo de <?= htmlspecialchars($emp['prenom']) ?>">
                        </div>
                        <div class="col-8">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($emp['prenom']) ?> <?= htmlspecialchars($emp['nom']) ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($emp['fonction']) ?></h6>
                                <p class="card-text small"><?= htmlspecialchars($emp['bio']) ?></p>
                                <?php if (in_array($_SESSION['role'] ?? '', ['admin', 'direction'])): ?>
                                    <div class="mt-2 text-end">
                                        <button class="btn btn-sm btn-outline-primary">Modifier</button>
                                        <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php pieddepage(); ?>
</body>
</html>
