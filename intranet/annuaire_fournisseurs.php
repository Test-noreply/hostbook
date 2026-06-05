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

$message = '';
$role = $_SESSION['role'] ?? 'user';

// Gestion des actions (Ajouter, Modifier, Supprimer)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($role, ['admin', 'direction', 'managers'])) {
    $action = $_POST['action'] ?? '';

    if ($action === 'supprimer') {
        $id = (int)($_POST['id'] ?? 0);
        $fournisseurs = array_filter($fournisseurs, fn($f) => $f['id'] !== $id);
        file_put_contents($fichier_fournisseurs, json_encode(array_values($fournisseurs), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $message = "Le partenaire a été supprimé avec succès.";
    } elseif ($action === 'ajouter' || $action === 'modifier') {
        $id = (int)($_POST['id'] ?? 0);
        $nom = $_POST['nom'] ?? '';
        $logo = $_POST['logo'] ?? '';
        $description = $_POST['description'] ?? '';

        if ($action === 'ajouter') {
            $new_id = empty($fournisseurs) ? 1 : max(array_column($fournisseurs, 'id')) + 1;
            $fournisseurs[] = [
                'id' => $new_id,
                'nom' => $nom,
                'logo' => $logo,
                'description' => $description
            ];
            $message = "Le partenaire a été ajouté avec succès.";
        } else {
            foreach ($fournisseurs as &$fourn) {
                if ($fourn['id'] === $id) {
                    $fourn['nom'] = $nom;
                    $fourn['logo'] = $logo;
                    $fourn['description'] = $description;
                    break;
                }
            }
            $message = "Les informations du partenaire ont été mises à jour.";
        }
        file_put_contents($fichier_fournisseurs, json_encode(array_values($fournisseurs), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php parametres("Annuaire Fournisseurs"); ?>

<body>
    <?php
    navigation();
    ?>

    <div class="container mt-5 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Annuaire des Fournisseurs & Partenaires</h1>
            <?php if (in_array($role, ['admin', 'direction', 'managers'])): ?>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalFournisseur" onclick="prepareModal('ajouter')">Ajouter un partenaire</button>
            <?php endif; ?>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
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
                        
                        <?php if (in_array($role, ['admin', 'direction', 'managers'])): ?>
                            <div class="mt-3">
                                <button class="btn btn-sm btn-outline-success" onclick='prepareModal("modifier", <?= json_encode($fournisseur, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' data-bs-toggle="modal" data-bs-target="#modalFournisseur">Modifier</button>
                                <form action="annuaire_fournisseurs.php" method="POST" class="d-inline" onsubmit="return confirm('Voulez-vous vraiment supprimer ce partenaire ?');">
                                    <input type="hidden" name="action" value="supprimer">
                                    <input type="hidden" name="id" value="<?= $fournisseur['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal Fournisseur -->
    <?php if (in_array($role, ['admin', 'direction', 'managers'])): ?>
    <div class="modal fade" id="modalFournisseur" tabindex="-1" aria-labelledby="modalFournisseurLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="annuaire_fournisseurs.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalFournisseurLabel">Ajouter un partenaire</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" id="modalAction" value="ajouter">
                        <input type="hidden" name="id" id="modalId" value="">
                        
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom du partenaire</label>
                            <input type="text" class="form-control" id="nom" name="nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="logo" class="form-label">URL du logo</label>
                            <input type="url" class="form-control" id="logo" name="logo" placeholder="https://..." >
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success" id="modalSubmit">Sauvegarder</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        //fonction pour préremplir la fenetre modal
    function prepareModal(action, data = null) {
        document.getElementById('modalAction').value = action;
        
        if (action === 'ajouter') {
            document.getElementById('modalFournisseurLabel').innerText = 'Ajouter un partenaire';
            document.getElementById('modalSubmit').innerText = 'Ajouter';
            document.getElementById('modalId').value = '';
            document.getElementById('nom').value = '';
            document.getElementById('logo').value ='';
            document.getElementById('description').value = '';
        } else if (action === 'modifier' && data) {
            document.getElementById('modalFournisseurLabel').innerText = 'Modifier le partenaire';
            document.getElementById('modalSubmit').innerText = 'Enregistrer';
            document.getElementById('modalId').value = data.id;
            document.getElementById('nom').value = data.nom;
            document.getElementById('logo').value = data.logo;
            document.getElementById('description').value = data.description;
        }
    }
    </script>
    <?php endif; ?>

    <?php pieddepage(); ?>
</body>
</html>
