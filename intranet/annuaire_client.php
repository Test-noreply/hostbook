<?php
session_start();
include 'scripts/fonctions.php';

if (!isset($_SESSION['pseudo'])) {
    header('Location: connexion.php');
    exit();
}

$fichier_clients = 'data/clients.json';
$clients = [];

if (file_exists($fichier_clients)) {
    $contenu = file_get_contents($fichier_clients);
    $clients = json_decode($contenu, true) ?? [];
}

$message = '';
$role = $_SESSION['role'] ?? 'user';

// Gestion du téléchargement de fiche
if (isset($_GET['action']) && $_GET['action'] === 'telecharger_fiche' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $client = null;
    foreach ($clients as $c) {
        if ($c['id'] === $id) {
            $client = $c;
            break;
        }
    }
    
    if ($client) {
        $contenu_fiche = "FICHE CLIENT\n";
        $contenu_fiche .= "==========================\n";
        $contenu_fiche .= "Référence : " . $client['id'] . "\n";
        $contenu_fiche .= "Nom       : " . $client['nom'] . "\n";
        $contenu_fiche .= "Prénom    : " . $client['prenom'] . "\n";
        $contenu_fiche .= "Téléphone : " . $client['telephone'] . "\n";
        $contenu_fiche .= "Email     : " . $client['email'] . "\n";
        $contenu_fiche .= "Adresse   : " . $client['adresse'] . "\n";
        
        $nom_fichier = "fiche_client_" . $client['id'] . "_" . preg_replace('/[^a-zA-Z0-9]/', '', $client['nom']) . ".txt";
        
        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $nom_fichier . '"');
        echo $contenu_fiche;
        exit();
    }
}

// Gestion des actions (Ajouter, Modifier, Supprimer)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($role, ['admin', 'direction', 'commercial'])) {
    $action = $_POST['action'] ?? '';

    if ($action === 'supprimer') {
        $id = (int)($_POST['id'] ?? 0);
        $clients = array_filter($clients, fn($c) => $c['id'] !== $id);
        file_put_contents($fichier_clients, json_encode(array_values($clients), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $message = "Le client a été supprimé avec succès.";
    } elseif ($action === 'ajouter' || $action === 'modifier') {
        $id = (int)($_POST['id'] ?? 0);
        $nom = $_POST['nom'] ?? '';
        $prenom = $_POST['prenom'] ?? '';
        $telephone = $_POST['telephone'] ?? '';
        $email = $_POST['email'] ?? '';
        $adresse = $_POST['adresse'] ?? '';

        if ($action === 'ajouter') {
            $new_id = empty($clients) ? 1 : max(array_column($clients, 'id')) + 1;
            $clients[] = [
                'id' => $new_id,
                'nom' => $nom,
                'prenom' => $prenom,
                'telephone' => $telephone,
                'email' => $email,
                'adresse' => $adresse
            ];
            $message = "Le client a été ajouté avec succès.";
        } else {
            foreach ($clients as &$c) {
                if ($c['id'] === $id) {
                    $c['nom'] = $nom;
                    $c['prenom'] = $prenom;
                    $c['telephone'] = $telephone;
                    $c['email'] = $email;
                    $c['adresse'] = $adresse;
                    break;
                }
            }
            $message = "Les informations du client ont été mises à jour.";
        }
        file_put_contents($fichier_clients, json_encode(array_values($clients), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php parametres("Annuaire Clients"); ?>

<body>
    <?php
    entete();
    navigation();
    ?>

    <div class="container mt-5 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Base de Données Clients</h1>
            <?php if (in_array($role, ['admin', 'direction', 'commercial'])): ?>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalClient" onclick="prepareModal('ajouter')">Ajouter un client</button>
            <?php endif; ?>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" id="rechercheClient" class="form-control" placeholder="Rechercher un client (nom, email, etc.)...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tableClients">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Téléphone</th>
                                <th>Email</th>
                                <th>Adresse</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clients as $c): ?>
                            <tr>
                                <td><?= htmlspecialchars($c['id']) ?></td>
                                <td><strong><?= htmlspecialchars($c['nom']) ?></strong></td>
                                <td><?= htmlspecialchars($c['prenom']) ?></td>
                                <td><?= htmlspecialchars($c['telephone']) ?></td>
                                <td><a href="mailto:<?= htmlspecialchars($c['email']) ?>"><?= htmlspecialchars($c['email']) ?></a></td>
                                <td><?= htmlspecialchars($c['adresse']) ?></td>
                                <td>
                                    <a href="annuaire_client.php?action=telecharger_fiche&id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-info" title="Télécharger la fiche">
                                        <i class="bi bi-download"></i> Fiche
                                    </a>
                                    <?php if (in_array($role, ['admin', 'direction', 'commercial'])): ?>
                                        <button class="btn btn-sm btn-outline-success" onclick='prepareModal("modifier", <?= json_encode($c, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' data-bs-toggle="modal" data-bs-target="#modalClient">Modifier</button>
                                        <form action="annuaire_client.php" method="POST" class="d-inline" onsubmit="return confirm('Voulez-vous vraiment supprimer ce client ?');">
                                            <input type="hidden" name="action" value="supprimer">
                                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
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

    <!-- Modal Client -->
    <?php if (in_array($role, ['admin', 'direction', 'commercial'])): ?>
    <div class="modal fade" id="modalClient" tabindex="-1" aria-labelledby="modalClientLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="annuaire_client.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalClientLabel">Ajouter un client</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" id="modalAction" value="ajouter">
                        <input type="hidden" name="id" id="modalId" value="">
                        
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="prenom" class="form-label">Prénom</label>
                            <input type="text" class="form-control" id="prenom" name="prenom" required>
                        </div>
                        <div class="mb-3">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="text" class="form-control" id="telephone" name="telephone" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="adresse" class="form-label">Adresse</label>
                            <textarea class="form-control" id="adresse" name="adresse" rows="2" required></textarea>
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
            document.getElementById('modalClientLabel').innerText = 'Ajouter un client';
            document.getElementById('modalSubmit').innerText = 'Ajouter';
            document.getElementById('modalId').value = '';
            document.getElementById('nom').value = '';
            document.getElementById('prenom').value = '';
            document.getElementById('telephone').value = '';
            document.getElementById('email').value = '';
            document.getElementById('adresse').value = '';
        } else if (action === 'modifier' && data) {
            document.getElementById('modalClientLabel').innerText = 'Modifier le client';
            document.getElementById('modalSubmit').innerText = 'Enregistrer';
            document.getElementById('modalId').value = data.id;
            document.getElementById('nom').value = data.nom;
            document.getElementById('prenom').value = data.prenom;
            document.getElementById('telephone').value = data.telephone;
            document.getElementById('email').value = data.email;
            document.getElementById('adresse').value = data.adresse;
        }
    }
    </script>
    <?php endif; ?>

    <!-- Script très basique pour la recherche (optionnel) -->
    <script>
        document.getElementById('rechercheClient').addEventListener('keyup', function() {
            var value = this.value.toLowerCase();
            var rows = document.querySelectorAll('#tableClients tbody tr');
            
            rows.forEach(function(row) {
                var text = row.textContent.toLowerCase();
                row.style.display = text.indexOf(value) > -1 ? '' : 'none';
            });
        });
    </script>

    <?php pieddepage(); ?>
</body>
</html>
