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

$message = '';
$role = $_SESSION['role'] ?? 'user';

// Gestion des actions (Ajouter, Modifier, Supprimer)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($role, ['admin', 'direction'])) {
    $action = $_POST['action'] ?? '';

    if ($action === 'supprimer') {
        $id = (int) ($_POST['id'] ?? 0);
        $employes = array_filter($employes, fn($e) => $e['id'] !== $id);
        file_put_contents($fichier_employes, json_encode(array_values($employes), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $message = "L'employé a été supprimé avec succès.";
    } elseif ($action === 'ajouter' || $action === 'modifier') {
        $id = (int) ($_POST['id'] ?? 0);
        $nom = $_POST['nom'] ?? '';
        $prenom = $_POST['prenom'] ?? '';
        $fonction = $_POST['fonction'] ?? '';
        $photo = $_POST['photo'] ?? '';
        $bio = $_POST['bio'] ?? '';

        if ($action === 'ajouter') {
            $new_id = empty($employes) ? 1 : max(array_column($employes, 'id')) + 1;
            $employes[] = [
                'id' => $new_id,
                'nom' => $nom,
                'prenom' => $prenom,
                'fonction' => $fonction,
                'photo' => $photo,
                'bio' => $bio
            ];
            $message = "L'employé a été ajouté avec succès.";
        } else {
            foreach ($employes as &$emp) {
                if ($emp['id'] === $id) {
                    $emp['nom'] = $nom;
                    $emp['prenom'] = $prenom;
                    $emp['fonction'] = $fonction;
                    $emp['photo'] = $photo;
                    $emp['bio'] = $bio;
                    break;
                }
            }
            $message = "Les informations de l'employé ont été mises à jour.";
        }
        file_put_contents($fichier_employes, json_encode(array_values($employes), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php parametres("Annuaire Employés"); ?>

<body>
    <?php
    navigation();
    ?>

    <div class="container mt-5 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Annuaire des Employés</h1>
            <?php if (in_array($role, ['admin', 'direction'])): ?>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalEmploye"
                    onclick="prepareModal('ajouter')">Ajouter un employé</button>
            <?php endif; ?>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="row">
            <?php foreach ($employes as $emp): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="row g-0">
                            <div class="col-4">
                                <img src="<?= htmlspecialchars($emp['photo']) ?>"
                                    class="img-fluid rounded-start h-100 object-fit-cover"
                                    alt="Photo de <?= htmlspecialchars($emp['prenom']) ?>">
                            </div>
                            <div class="col-8">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($emp['prenom']) ?>
                                        <?= htmlspecialchars($emp['nom']) ?></h5>
                                    <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($emp['fonction']) ?></h6>
                                    <p class="card-text small"><?= htmlspecialchars($emp['bio']) ?></p>
                                    <?php if (in_array($role, ['admin', 'direction'])): ?>
                                        <div class="mt-2 text-end">
                                            <button class="btn btn-sm btn-outline-success"
                                                onclick='prepareModal("modifier", <?= json_encode($emp, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'
                                                data-bs-toggle="modal" data-bs-target="#modalEmploye">Modifier</button>
                                            <form action="annuaire_employe.php" method="POST" class="d-inline"
                                                onsubmit="return confirm('Voulez-vous vraiment supprimer cet employé ?');">
                                                <input type="hidden" name="action" value="supprimer">
                                                <input type="hidden" name="id" value="<?= $emp['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                                            </form>
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

    <!-- Modal Employé -->
    <?php if (in_array($role, ['admin', 'direction'])): ?>
        <div class="modal fade" id="modalEmploye" tabindex="-1" aria-labelledby="modalEmployeLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="annuaire_employe.php" method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalEmployeLabel">Ajouter un employé</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="action" id="modalAction" value="ajouter">
                            <input type="hidden" name="id" id="modalId" value="">

                            <div class="mb-3">
                                <label for="prenom" class="form-label">Prénom</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" required>
                            </div>
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>
                            <div class="mb-3">
                                <label for="fonction" class="form-label">Fonction</label>
                                <input type="text" class="form-control" id="fonction" name="fonction" required>
                            </div>
                            <div class="mb-3">
                                <label for="photo" class="form-label">URL de la photo</label>
                                <input type="url" class="form-control" id="photo" name="photo" placeholder="https://..."
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="bio" class="form-label">Biographie</label>
                                <textarea class="form-control" id="bio" name="bio" rows="3" required></textarea>
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
            function prepareModal(action, data = null) {
                document.getElementById('modalAction').value = action;

                if (action === 'ajouter') {
                    document.getElementById('modalEmployeLabel').innerText = 'Ajouter un employé';
                    document.getElementById('modalSubmit').innerText = 'Ajouter';
                    document.getElementById('modalId').value = '';
                    document.getElementById('prenom').value = '';
                    document.getElementById('nom').value = '';
                    document.getElementById('fonction').value = '';
                    document.getElementById('photo').value = 'https://randomuser.me/api/portraits/lego/' + Math.floor(Math.random() * 9 + 1) + '.jpg';
                    document.getElementById('bio').value = '';
                } else if (action === 'modifier' && data) {
                    document.getElementById('modalEmployeLabel').innerText = 'Modifier l\'employé';
                    document.getElementById('modalSubmit').innerText = 'Enregistrer';
                    document.getElementById('modalId').value = data.id;
                    document.getElementById('prenom').value = data.prenom;
                    document.getElementById('nom').value = data.nom;
                    document.getElementById('fonction').value = data.fonction;
                    document.getElementById('photo').value = data.photo;
                    document.getElementById('bio').value = data.bio;
                }
            }
        </script>
    <?php endif; ?>

    <?php pieddepage(); ?>
</body>

</html>