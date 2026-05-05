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
            <?php if (in_array($_SESSION['role'] ?? '', ['admin', 'direction', 'commercial'])): ?>
                <button class="btn btn-success">Ajouter un client</button>
            <?php endif; ?>
        </div>

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
                                    <!-- Bouton factice pour la génération de fiche client -->
                                    <button class="btn btn-sm btn-outline-info" title="Télécharger la fiche">
                                        <i class="bi bi-download"></i> Fiche
                                    </button>
                                    <?php if (in_array($_SESSION['role'] ?? '', ['admin', 'direction', 'commercial'])): ?>
                                        <button class="btn btn-sm btn-outline-primary">Modifier</button>
                                        <button class="btn btn-sm btn-outline-danger">Supprimer</button>
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
