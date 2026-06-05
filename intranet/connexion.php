<?php
session_start();
//lien avec le fichier des fonctions
include 'scripts/fonctions.php';

$message_erreur = '';

//traitement du formulaire lorsque l'utilisateur le soumet
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pseudo_saisi = $_POST['pseudo'] ?? '';
    $motdepasse_saisi = $_POST['motdepasse'] ?? '';

    //on vérifie que les champs ne sont pas vides
    if (!empty($pseudo_saisi) && !empty($motdepasse_saisi)) {

        $chemin_utilisateurs = 'data/utilisateurs.json';

        if (file_exists($chemin_utilisateurs)) {

            $contenu_json = file_get_contents($chemin_utilisateurs);
            $utilisateurs = json_decode($contenu_json, true);

            $utilisateur_trouve = false;

            //parcours de tous les utilisateurs pour trouver une correspondance
            foreach ($utilisateurs as $user) {
                if ($user['utilisateur'] === $pseudo_saisi) {
                    $utilisateur_trouve = true;

                    //l'utilisateur existe, on vérifie maintenant le mot de passe avec password_verify()
                    if (password_verify($motdepasse_saisi, $user['motdepasse'])) {
                        //mdp correct : création des variables de session
                        $_SESSION['pseudo'] = $user['utilisateur'];
                        $_SESSION['role'] = $user['groupe'] ?? 'user'; //mémorise le rôle de l'utilisateur (initialise à user si il n'existe pas)

                        //redirection vers la page d'accueil
                        header('Location: accueil_intranet.php');
                        exit(); //arreter l'exécution de la page après la redirection
                    } else {
                        //mdp incorrect
                        $message_erreur = "Mot de passe incorrect.";
                    }
                    //arreter la boucle foreach puisqu'on a trouvé l'utilisateur
                    break;
                }
            }

            //si la boucle est terminée et qu'on n'a pas trouvé le pseudo
            if (!$utilisateur_trouve) {
                $message_erreur = "Utilisateur introuvable.";
            }

        } else {
            $message_erreur = "Erreur système : impossible de charger la base de données.";
        }
    } else {
        $message_erreur = "Veuillez remplir tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php parametres("Connexion Intranet"); ?>

<body>
    <?php
    navigation();
    ?>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <h2 class="text-center mb-4">S'identifier</h2>

                <!-- affichage du message d'erreur s'il y en a un -->
                <?php if (!empty($message_erreur)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($message_erreur); ?>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <form action="connexion.php" method="POST">
                            <div class="mb-3">
                                <label for="pseudo" class="form-label">Nom d'utilisateur</label>
                                <!-- champ texte simple avec required -->
                                <input type="text" class="form-control" id="pseudo" name="pseudo" required>
                            </div>
                            <div class="mb-4">
                                <label for="motdepasse" class="form-label">Mot de passe</label>
                                <!-- champ de type password pour masquer la saisie -->
                                <input type="password" class="form-control" id="motdepasse" name="motdepasse" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">Se connecter</button>
                            </div>
                        </form>

                        <div class="mt-3 text-center">
                            <a href="/wordpress" class="btn btn-outline-success w-100">Retour au site Vitrine</a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php pieddepage(); ?>
</body>

</html>