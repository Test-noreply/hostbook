<?php
session_start();
include 'scripts/fonctions.php';

if (!isset($_SESSION['pseudo'])) {
    header('Location: connexion.php');
    exit();
}

$dossier_uploads = 'uploads/';
if (!is_dir($dossier_uploads)) {
    mkdir($dossier_uploads, 0777, true);
}

$message = '';
$role = $_SESSION['role'] ?? 'user';

// Gérer l'upload de fichiers
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fichier'])) {
    $fichier = $_FILES['fichier'];
    $extension = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));
    
    if (in_array($extension, ['txt', 'csv'])) {
        $chemin_destination = $dossier_uploads . basename($fichier['name']);
        if (move_uploaded_file($fichier['tmp_name'], $chemin_destination)) {
            $message = "Le fichier a été téléchargé avec succès.";
        } else {
            $message = "Erreur lors de l'upload du fichier.";
        }
    } else {
        $message = "Seuls les fichiers .txt et .csv sont autorisés.";
    }
}

// Lister les fichiers
$fichiers = [];
foreach (scandir($dossier_uploads) as $f) {
    if ($f !== '.' && $f !== '..' && $f !== 'index.html') {
        $fichiers[] = $f;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php parametres("Partage de Fichiers"); ?>

<body>
    <?php
    entete();
    navigation();
    ?>

    <div class="container mt-5 mb-5">
        <h1 class="mb-4">Partage de Fichiers (TXT & CSV)</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Fichiers partagés</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($fichiers)): ?>
                            <p class="text-muted">Aucun fichier partagé pour le moment.</p>
                        <?php else: ?>
                            <ul class="list-group">
                                <?php foreach ($fichiers as $f): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="<?= htmlspecialchars($dossier_uploads . $f) ?>" target="_blank">
                                            <i class="bi bi-file-earmark-text"></i> <?= htmlspecialchars($f) ?>
                                        </a>
                                        <?php if (in_array($role, ['admin', 'managers', 'direction'])): ?>
                                            <div>
                                                <button class="btn btn-sm btn-outline-secondary">Renommer</button>
                                                <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                                            </div>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Ajouter un fichier</h5>
                    </div>
                    <div class="card-body">
                        <form action="partage.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="fichier" class="form-label">Sélectionner un fichier (.txt ou .csv)</label>
                                <input class="form-control" type="file" id="fichier" name="fichier" accept=".txt,.csv" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Uploader</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php pieddepage(); ?>
</body>
</html>
