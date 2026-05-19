<?php
session_start();
include 'scripts/fonctions.php';

// Sécurisation : vérifier que l'utilisateur est connecté et fait partie d'un groupe autorisé (ex: admin ou moderateur)
if (!isset($_SESSION['pseudo']) || !in_array($_SESSION['role'] ?? '', ['admin', 'moderateur'])) {
    header('Location: accueil_intranet.php');
    exit();
}

$fichier_utilisateurs = 'data/utilisateurs.json';
$utilisateurs = [];

if (file_exists($fichier_utilisateurs)) {
    $contenu = file_get_contents($fichier_utilisateurs);
    $utilisateurs = json_decode($contenu, true) ?? [];
}

$message = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'supprimer') {
        $user_to_delete = $_POST['utilisateur_id'] ?? '';
        $utilisateurs = array_filter($utilisateurs, fn($u) => $u['utilisateur'] !== $user_to_delete);
        file_put_contents($fichier_utilisateurs, json_encode(array_values($utilisateurs), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $message = "L'utilisateur a été supprimé.";
    } elseif ($action === 'ajouter' || $action === 'modifier') {
        $old_utilisateur = $_POST['old_utilisateur'] ?? '';
        $utilisateur = trim($_POST['utilisateur'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $groupe = $_POST['groupe'] ?? '';
        $motdepasse = trim($_POST['motdepasse'] ?? '');

        if ($action === 'ajouter') {
            // verifier si l'utilisateur existe
            $exists = false;
            foreach ($utilisateurs as $u) {
                if ($u['utilisateur'] === $utilisateur) {
                    $exists = true;
                    break;
                }
            }
            if ($exists) {
                $message = "Erreur : Ce nom d'utilisateur existe déjà.";
            } else {
                $utilisateurs[] = [
                    'utilisateur' => $utilisateur,
                    'motdepasse' => password_hash($motdepasse, PASSWORD_DEFAULT),
                    'email' => $email,
                    'groupe' => $groupe
                ];
                $message = "L'utilisateur a été ajouté avec succès.";
                file_put_contents($fichier_utilisateurs, json_encode(array_values($utilisateurs), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }
        } else {
            // Modification
            $exists = false;
            if ($utilisateur !== $old_utilisateur) {
                foreach ($utilisateurs as $u) {
                    if ($u['utilisateur'] === $utilisateur) {
                        $exists = true;
                        break;
                    }
                }
            }

            if ($exists) {
                $message = "Erreur : Ce nom d'utilisateur est déjà utilisé par un autre compte.";
            } else {
                foreach ($utilisateurs as &$u) {
                    if ($u['utilisateur'] === $old_utilisateur) {
                        $u['utilisateur'] = $utilisateur;
                        $u['email'] = $email;
                        $u['groupe'] = $groupe;
                        if (!empty($motdepasse)) {
                            $u['motdepasse'] = password_hash($motdepasse, PASSWORD_DEFAULT);
                        }
                        break;
                    }
                }
                $message = "Les informations de l'utilisateur ont été mises à jour.";
                file_put_contents($fichier_utilisateurs, json_encode(array_values($utilisateurs), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php parametres("Gestion des Utilisateurs"); ?>

<body>
    <?php
    entete();
    navigation();
    ?>

    <div class="container mt-5 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gestion des Utilisateurs</h1>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal"
                onclick="prepareModal('ajouter')">Ajouter un utilisateur</button>
        </div>

        <?php if ($message): ?>
            <div class="alert <?= strpos($message, 'Erreur') !== false ? 'alert-danger' : 'alert-success' ?>">
                <?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Utilisateur</th>
                                <th>Email</th>
                                <th>Groupe</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($utilisateurs as $index => $u): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($u['utilisateur']) ?></strong></td>
                                    <td><?= htmlspecialchars($u['email']) ?></td>
                                    <td><span class="badge bg-primary"><?= htmlspecialchars($u['groupe']) ?></span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick='prepareModal("modifier", <?= json_encode(["utilisateur" => $u["utilisateur"], "email" => $u["email"], "groupe" => $u["groupe"]], JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'
                                            data-bs-toggle="modal" data-bs-target="#addUserModal">Modifier</button>
                                        <?php if ($u['utilisateur'] !== $_SESSION['pseudo']): ?>
                                            <form action="gestion_utilisateurs.php" method="POST" class="d-inline"
                                                onsubmit="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');">
                                                <input type="hidden" name="action" value="supprimer">
                                                <input type="hidden" name="utilisateur_id"
                                                    value="<?= htmlspecialchars($u['utilisateur']) ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'ajout/modification -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="gestion_utilisateurs.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUserLabel">Nouvel utilisateur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" id="modalAction" value="ajouter">
                        <input type="hidden" name="old_utilisateur" id="old_utilisateur" value="">

                        <div class="mb-3">
                            <label class="form-label">Nom d'utilisateur</label>
                            <input type="text" name="utilisateur" id="utilisateur" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" name="motdepasse" id="motdepasse" class="form-control" required>
                            <div class="form-text" id="motdepasseHelp"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Groupe</label>
                            <select name="groupe" id="groupe" class="form-select" required>
                                <option value="admin">Admin</option>
                                <option value="direction">Direction</option>
                                <option value="managers">Managers</option>
                                <option value="salarie">Salarié</option>
                                <option value="moderateur">Modérateur</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success" id="modalSubmit">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function prepareModal(action, data = null) {
            document.getElementById('modalAction').value = action;

            if (action === 'ajouter') {
                document.getElementById('modalUserLabel').innerText = 'Ajouter un utilisateur';
                document.getElementById('modalSubmit').innerText = 'Ajouter';
                document.getElementById('old_utilisateur').value = '';
                document.getElementById('utilisateur').value = '';
                document.getElementById('email').value = '';
                document.getElementById('motdepasse').required = true;
                document.getElementById('motdepasse').placeholder = '';
                document.getElementById('motdepasseHelp').innerText = '';
                document.getElementById('groupe').value = 'salarie';
            } else if (action === 'modifier' && data) {
                document.getElementById('modalUserLabel').innerText = 'Modifier l\'utilisateur';
                document.getElementById('modalSubmit').innerText = 'Enregistrer';
                document.getElementById('old_utilisateur').value = data.utilisateur;
                document.getElementById('utilisateur').value = data.utilisateur;
                document.getElementById('email').value = data.email;
                document.getElementById('motdepasse').required = false;
                document.getElementById('motdepasse').placeholder = '********';
                document.getElementById('motdepasseHelp').innerText = '(Laisser vide pour conserver le mot de passe actuel)';

                // Assigner le groupe s'il existe dans les options
                let groupeSelect = document.getElementById('groupe');
                let optionExists = Array.from(groupeSelect.options).some(opt => opt.value === data.groupe);
                if (optionExists) {
                    groupeSelect.value = data.groupe;
                } else {
                    // S'il a un groupe personnalisé, l'ajouter temporairement pour l'afficher correctement
                    let newOption = new Option(data.groupe, data.groupe);
                    groupeSelect.add(newOption);
                    groupeSelect.value = data.groupe;
                }
            }
        }
    </script>

    <?php pieddepage(); ?>
</body>

</html>