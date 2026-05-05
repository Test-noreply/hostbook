<?php
session_start();
include 'scripts/fonctions.php';

// Sécurisation : vérifier que l'utilisateur est connecté et fait partie d'un groupe autorisé (ex: admin ou direction)
if (!isset($_SESSION['pseudo']) || !in_array($_SESSION['role'] ?? '', ['admin', 'direction'])) {
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

// Traitement basique du formulaire (exemple pour l'ajout/suppression à étoffer plus tard)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Logique d'ajout / modification / suppression à implémenter ici
    $message = "Action enregistrée (Logique d'écriture JSON à compléter pour l'ajout/modification).";
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
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">Ajouter un utilisateur</button>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
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
                                <td><?= htmlspecialchars($u['utilisateur']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><span class="badge bg-primary"><?= htmlspecialchars($u['groupe']) ?></span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">Modifier</button>
                                    <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'ajout (Structure de base) -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="gestion_utilisateurs.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Nouvel utilisateur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nom d'utilisateur</label>
                            <input type="text" name="utilisateur" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" name="motdepasse" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Groupe</label>
                            <select name="groupe" class="form-select">
                                <option value="admin">Admin</option>
                                <option value="salarie">Salarié</option>
                                <option value="managers">Managers</option>
                                <option value="direction">Direction</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php pieddepage(); ?>
</body>
</html>
