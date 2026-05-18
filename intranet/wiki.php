<?php
session_start();
include 'scripts/fonctions.php';
?>
<!DOCTYPE html>
<html lang="fr">
<?php parametres("Wiki Intranet"); ?>

<body>
    <?php
    entete();
    navigation();
    ?>

    <div class="container mt-5 mb-5">
        <h1 class="mb-4 border-bottom pb-2">Wiki Intranet SAE 203</h1>
        
        <div class="row">
            <div class="col-md-3">
                <div class="list-group sticky-top" style="top: 20px;">
                    <a href="#infos" class="list-group-item list-group-item-action">Infos de connexion</a>
                    <a href="#arch" class="list-group-item list-group-item-action">Architecture</a>
                    <a href="#json" class="list-group-item list-group-item-action">Fichiers JSON</a>
                    <a href="#pages" class="list-group-item list-group-item-action">Description des pages</a>
                </div>
            </div>
            
            <div class="col-md-9">
                <section id="infos" class="mb-5">
                    <h2>Comptes de test</h2>
                    <p>Voici la liste des utilisateurs pour tester les différents niveaux d'accès de l'intranet :</p>
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Utilisateur</th>
                                <th>Mot de passe</th>
                                <th>Groupe</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>admin</td><td>admin</td><td>admin</td></tr>
                            <tr><td>user</td><td>user</td><td>salarie</td></tr>
                            <tr><td>modo</td><td>modo</td><td>managers</td></tr>
                            <tr><td>anonymous</td><td>anonymous</td><td>direction</td></tr>
                        </tbody>
                    </table>
                </section>

                <section id="arch" class="mb-5">
                    <h2>Architecture du projet</h2>
                    <p>Le projet se divise en deux parties principales :</p>
                    <ul>
                        <li><strong>Vitrine :</strong> Un site Wordpress (en cours de configuration) qui met en avant les activités de l'entreprise.</li>
                        <li><strong>Intranet :</strong> Une application développée "from scratch" en PHP, HTML, CSS (Bootstrap) qui permet aux employés de collaborer et de gérer les données de l'entreprise.</li>
                    </ul>
                </section>

                <section id="json" class="mb-5">
                    <h2>Utilisation des fichiers JSON</h2>
                    <p>Pour l'intranet, aucune base de données de type MySQL n'est utilisée. Toutes les informations sont stockées dans des fichiers texte au format JSON dans le dossier <code>data/</code> :</p>
                    <ul>
                        <li><code>utilisateurs.json</code> : Gère les accès à l'intranet.</li>
                        <li><code>employes.json</code> : Données de l'annuaire d'entreprise.</li>
                        <li><code>fournisseurs.json</code> : Partenaires qui seront affichés sur la vitrine via un module spécifique.</li>
                        <li><code>clients.json</code> : Informations confidentielles accessibles via l'intranet.</li>
                    </ul>
                    <p>PHP lit ces fichiers avec <code>file_get_contents()</code> et <code>json_decode()</code>, puis les modifie avec <code>json_encode()</code> et <code>file_put_contents()</code>.</p>
                </section>

                <section id="pages" class="mb-5">
                    <h2>Description des pages de l'Intranet</h2>
                    <div class="accordion" id="accordionPages">
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    Annuaire des employés (annuaire_employe.php)
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionPages">
                                <div class="accordion-body">
                                    Affiche tous les employés de l'entreprise sous forme de cartes avec photo, fonction et biographie. Permet d'ajouter, modifier et supprimer un employé.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Annuaire des fournisseurs (annuaire_fournisseurs.php)
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionPages">
                                <div class="accordion-body">
                                    Gère les partenaires commerciaux. Les données modifiées ici sont exploitées par le site Vitrine (Wordpress) pour afficher les logos et descriptions.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Annuaire des clients (annuaire_client.php)
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionPages">
                                <div class="accordion-body">
                                    Affiche la liste des clients dans un tableau. Permet également la création de fiches clients téléchargeables dynamiquement.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    Partage de fichiers (partage.php)
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionPages">
                                <div class="accordion-body">
                                    Espace d'échange de fichiers TXT et CSV. L'affichage et les droits (suppression/modification) dépendent du groupe de l'utilisateur connecté.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFive">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                    Gestion des utilisateurs (gestion_utilisateurs.php)
                                </button>
                            </h2>
                            <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#accordionPages">
                                <div class="accordion-body">
                                    Réservé aux administrateurs. Permet de gérer les comptes d'accès à l'intranet (modification des rôles/groupes, réinitialisation de mots de passe, etc.).
                                </div>
                            </div>
                        </div>

                    </div>
                </section>
            </div>
        </div>
    </div>

    <?php pieddepage(); ?>
</body>
</html>
