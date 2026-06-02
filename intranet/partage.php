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

// Gérer l'upload et l'enregistrement de fichiers
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'upload' && isset($_FILES['fichier'])) {
        $fichier = $_FILES['fichier'];
        $extension = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));

        if (in_array($extension, ['txt', 'csv'])) {
            $nom_complet = basename($fichier['name']);
            $chemin_destination = $dossier_uploads . $nom_complet;
            
            if (file_exists($chemin_destination)) {
                $message = "Erreur : Le fichier '$nom_complet' existe déjà.";
            } else {
                if (move_uploaded_file($fichier['tmp_name'], $chemin_destination)) {
                    $message = "Le fichier a été téléchargé avec succès.";
                } else {
                    $message = "Erreur lors de l'upload du fichier.";
                }
            }
        } else {
            $message = "Seuls les fichiers .txt et .csv sont autorisés.";
        }
    } elseif ($action === 'sauvegarder') {
        $nom_fichier = $_POST['nom_fichier'] ?? '';
        $extension = $_POST['extension'] ?? 'txt';
        $contenu = $_POST['contenu'] ?? '';
        $original_nom = $_POST['original_nom'] ?? '';

        if (!empty($nom_fichier) && in_array($extension, ['txt', 'csv'])) {
            $nom_fichier_base = pathinfo($nom_fichier, PATHINFO_FILENAME);
            $nom_complet = basename($nom_fichier_base) . '.' . $extension;
            $chemin = $dossier_uploads . $nom_complet;

            if (file_exists($chemin) && $nom_complet !== $original_nom) {
                $message = "Erreur : Un fichier nommé '$nom_complet' existe déjà. Veuillez choisir un autre nom.";
            } else {
                if (file_put_contents($chemin, $contenu) !== false) {
                    $message = "Le fichier a été enregistré avec succès.";
                    // Supprimer l'ancien fichier si le nom a été modifié pendant l'édition
                    if (!empty($original_nom) && $nom_complet !== $original_nom) {
                        $ancien_chemin = $dossier_uploads . basename($original_nom);
                        if (file_exists($ancien_chemin)) {
                            unlink($ancien_chemin);
                        }
                    }
                } else {
                    $message = "Erreur lors de l'enregistrement du fichier.";
                }
            }
        } else {
            $message = "Nom de fichier invalide ou extension non autorisée.";
        }
    } elseif ($action === 'supprimer') {
        $nom_fichier = $_POST['nom_fichier'] ?? '';
        $chemin = $dossier_uploads . basename($nom_fichier);
        if (file_exists($chemin) && !is_dir($chemin)) {
            unlink($chemin);
            $message = "Le fichier a été supprimé.";
        } else {
            $message = "Erreur : Fichier introuvable.";
        }
    } elseif ($action === 'renommer') {
        $ancien_nom = $_POST['ancien_nom'] ?? '';
        $nouveau_nom = $_POST['nouveau_nom'] ?? '';
        
        $extension_nouveau = strtolower(pathinfo($nouveau_nom, PATHINFO_EXTENSION));
        if (!in_array($extension_nouveau, ['txt', 'csv'])) {
            $message = "Erreur : Seuls les fichiers .txt et .csv sont autorisés.";
        } else {
            $ancien_chemin = $dossier_uploads . basename($ancien_nom);
            $nouveau_chemin = $dossier_uploads . basename($nouveau_nom);
            
            if (file_exists($ancien_chemin) && !file_exists($nouveau_chemin)) {
                rename($ancien_chemin, $nouveau_chemin);
                $message = "Le fichier a été renommé.";
            } elseif (file_exists($nouveau_chemin)) {
                $message = "Erreur : Un fichier nommé '$nouveau_nom' existe déjà.";
            } else {
                $message = "Erreur : Fichier source introuvable.";
            }
        }
    }
}

// Gérer le mode édition
$edit_nom = '';
$edit_contenu = '';
$edit_ext = 'txt';

if (isset($_GET['edit'])) {
    $f = basename($_GET['edit']);
    $chemin = $dossier_uploads . $f;
    if (file_exists($chemin) && in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), ['txt', 'csv'])) {
        $edit_nom = pathinfo($f, PATHINFO_FILENAME);
        $edit_ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
        $edit_contenu = file_get_contents($chemin);
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
    navigation();
    ?>

    <div class="container mt-5 mb-5">
        <h1 class="mb-4">Partage de Fichiers (TXT & CSV)</h1>

        <?php if ($message): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- Colonne de gauche: Upload et Liste -->
            <div class="col-lg-4 mb-4">
                <!-- Upload -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Ajouter un fichier</h5>
                    </div>
                    <div class="card-body">
                        <form action="partage.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="upload">
                            <div class="mb-3">
                                <label for="fichier" class="form-label">Sélectionner un fichier (.txt ou .csv)</label>
                                <input class="form-control" type="file" id="fichier" name="fichier" accept=".txt,.csv"
                                    required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Uploader</button>
                        </form>
                    </div>
                </div>

                <!-- Liste -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Fichiers partagés</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($fichiers)): ?>
                            <p class="text-muted p-3 mb-0">Aucun fichier partagé pour le moment.</p>
                        <?php else: ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($fichiers as $f): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="<?= htmlspecialchars($dossier_uploads . $f) ?>" target="_blank"
                                            class="text-truncate me-2" style="max-width: 150px;"
                                            title="<?= htmlspecialchars($f) ?>">
                                            <i class="bi bi-file-earmark-text"></i> <?= htmlspecialchars($f) ?>
                                        </a>
                                        <div class="btn-group">
                                            <a href="?edit=<?= urlencode($f) ?>" class="btn btn-sm btn-outline-success"
                                                title="Éditer">Éditer</a>
                                            <?php if (in_array($role, ['admin', 'managers', 'direction'])): ?>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" title="Renommer" onclick="renommerFichier('<?= htmlspecialchars($f) ?>')"><i
                                                        class="bi bi-pencil-square"></i></button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Supprimer" onclick="supprimerFichier('<?= htmlspecialchars($f) ?>')"><i
                                                        class="bi bi-trash"></i></button>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Colonne de droite: Éditeur -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?= $edit_nom ? 'Éditer le fichier' : 'Créer un fichier web' ?></h5>
                        <?php if ($edit_nom): ?>
                            <a href="partage.php" class="btn btn-sm btn-outline-secondary">Nouveau fichier</a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <form action="partage.php" method="POST">
                            <input type="hidden" name="action" value="sauvegarder">
                            <input type="hidden" name="original_nom" value="<?= htmlspecialchars($edit_nom ? $edit_nom . '.' . $edit_ext : '') ?>">

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="nom_fichier" class="form-label">Nom du fichier</label>
                                    <input type="text" class="form-control" id="nom_fichier" name="nom_fichier"
                                        value="<?= htmlspecialchars($edit_nom) ?>" placeholder="mon_fichier" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="extension" class="form-label">Format</label>
                                    <select class="form-select" id="extension" name="extension">
                                        <option value="txt" <?= $edit_ext === 'txt' ? 'selected' : '' ?>>.txt</option>
                                        <option value="csv" <?= $edit_ext === 'csv' ? 'selected' : '' ?>>.csv</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="contenu" class="form-label">Contenu du fichier</label>
                                <textarea class="form-control font-monospace" id="contenu" name="contenu" rows="15"
                                    placeholder="Saisissez le contenu de votre fichier ici..."><?= htmlspecialchars($edit_contenu) ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-success w-100">
                                <?= $edit_nom ? 'Enregistrer les modifications' : 'Enregistrer le nouveau fichier' ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php pieddepage(); ?>
    
    <script>
    function renommerFichier(ancienNom) {
        let nouveauNom = prompt("Nouveau nom pour " + ancienNom + " (inclure l'extension .txt ou .csv) :", ancienNom);
        if (nouveauNom && nouveauNom !== ancienNom) {
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = 'partage.php';
            
            form.appendChild(createHiddenInput('action', 'renommer'));
            form.appendChild(createHiddenInput('ancien_nom', ancienNom));
            form.appendChild(createHiddenInput('nouveau_nom', nouveauNom));
            
            document.body.appendChild(form);
            form.submit();
        }
    }

    function supprimerFichier(nom) {
        if (confirm("Voulez-vous vraiment supprimer le fichier " + nom + " ?")) {
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = 'partage.php';
            
            form.appendChild(createHiddenInput('action', 'supprimer'));
            form.appendChild(createHiddenInput('nom_fichier', nom));
            
            document.body.appendChild(form);
            form.submit();
        }
    }

    function createHiddenInput(name, value) {
        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        return input;
    }
    </script>
</body>

</html>