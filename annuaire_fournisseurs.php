<?php
session_start();
include 'scripts/fonctions.php';

if (!isset($_SESSION['pseudo'])) {
    header('Location: connexion.php');
    exit();
}

$fichier_fournisseurs = 'data/fournisseurs.json';
$fournisseurs = [];

if (file_exists($fichier_fournisseurs)) {
    $contenu = file_get_contents($fichier_fournisseurs);
    $fournisseurs = json_decode($contenu, true) ?? [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php parametres("Annuaire Fournisseurs"); ?>

<body>
    <?php
    entete();
    navigation();
    ?>

    <div class="container mt-5 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Annuaire des Fournisseurs & Partenaires</h1>
            <?php if (in_array($_SESSION['role'] ?? '', ['admin', 'direction', 'managers'])): ?>
                <button class="btn btn-success">Ajouter un partenaire</button>
            <?php endif; ?>
        </div>
        
        <p class="text-muted mb-4">Note : Les données des partenaires affichées ci-dessous sont synchronisées avec le site vitrine (Wordpress).</p>

        <div class="row">
            <?php foreach ($fournisseurs as $fournisseur): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm border-0 bg-light">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <img src="<?= htmlspecialchars($fournisseur['logo']) ?>" class="rounded-circle bg-white shadow-sm p-1 me-3" alt="Logo de <?= htmlspecialchars($fournisseur['nom']) ?>" width="60" height="60" style="object-fit: contain;">
                            <h4 class="card-title mb-0"><?= htmlspecialchars($fournisseur['nom']) ?></h4>
                        </div>
                        <p class="card-text"><?= htmlspecialchars($fournisseur['description']) ?></p>
                        
                        <?php if (in_array($_SESSION['role'] ?? '', ['admin', 'direction', 'managers'])): ?>
                            <div class="mt-3">
                                <button class="btn btn-sm btn-outline-primary">Modifier Infos</button>
                                <button class="btn btn-sm btn-outline-secondary">Modifier Logo</button>
                                <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php pieddepage(); ?>
</body>
</html>
