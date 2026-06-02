<?php
session_start();
include 'scripts/fonctions.php';
?>
<!DOCTYPE html>
<html lang="fr">
<?php parametres("Wiki Intranet"); ?>

<body>
    <?php
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
                                    <strong>Fonctionnalités :</strong>
                                    <ul>
                                        <li>Redirige vers la page de connexion si l'utilisateur n'est pas authentifié.</li>
                                        <li>Affiche tous les employés de l'entreprise sous forme de cartes (photo, nom, rôle, biographie) lus depuis <code>data/employes.json</code>.</li>
                                        <li><strong>Pour les administrateurs et la direction :</strong>
                                            <ul>
                                                <li>Possibilité d'ajouter un nouvel employé via une fenêtre modale (avec pré-remplissage d'une URL de photo de profil aléatoire).</li>
                                                <li>Possibilité de modifier les informations d'un employé existant (pré-remplissage des champs dans la modale).</li>
                                                <li>Possibilité de supprimer un employé (avec alerte de confirmation JavaScript pour éviter les erreurs).</li>
                                                <li>Affichage de messages de succès ("L'employé a été ajouté/modifié/supprimé avec succès").</li>
                                            </ul>
                                        </li>
                                    </ul>
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
                                    <strong>Fonctionnalités :</strong>
                                    <ul>
                                        <li>Redirection si non authentifié.</li>
                                        <li>Affiche les partenaires commerciaux depuis <code>data/fournisseurs.json</code>. Les données modifiées ici sont prévues pour être exploitées par le site Vitrine (Wordpress).</li>
                                        <li><strong>Pour les rôles admin, direction et managers :</strong>
                                            <ul>
                                                <li>Ajout d'un partenaire via une modale (avec génération automatique d'une image "placeholder" aléatoire).</li>
                                                <li>Modification d'un partenaire existant.</li>
                                                <li>Suppression d'un partenaire avec confirmation JavaScript.</li>
                                            </ul>
                                        </li>
                                    </ul>
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
                                    <strong>Fonctionnalités :</strong>
                                    <ul>
                                        <li>Redirection si non authentifié.</li>
                                        <li>Affiche la liste des clients dans un tableau interactif depuis <code>data/clients.json</code>.</li>
                                        <li>Barre de recherche en direct (filtre dynamiquement les lignes du tableau en JavaScript selon le texte saisi).</li>
                                        <li>Génération et téléchargement dynamiques de fiches clients (génère un fichier <code>.txt</code> formaté contenant les détails du client).</li>
                                        <li><strong>Pour les rôles admin, direction et commercial :</strong>
                                            <ul>
                                                <li>Ajout d'un nouveau client via modale.</li>
                                                <li>Modification des informations du client.</li>
                                                <li>Suppression d'un client avec demande de confirmation.</li>
                                            </ul>
                                        </li>
                                    </ul>
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
                                    <strong>Fonctionnalités :</strong>
                                    <ul>
                                        <li>Redirection si non authentifié.</li>
                                        <li>Création automatique du dossier <code>uploads/</code> s'il n'existe pas.</li>
                                        <li>Upload de fichiers (limité aux extensions <code>.txt</code> et <code>.csv</code>).</li>
                                        <li>Création directe de fichiers texte/csv depuis l'interface avec un éditeur intégré.</li>
                                        <li>Empêche l'écrasement involontaire : affiche une alerte si un fichier téléchargé ou créé porte un nom déjà existant.</li>
                                        <li>Lecture des fichiers du dossier <code>uploads/</code>.</li>
                                        <li>Édition du contenu des fichiers existants directement depuis la page web avec nettoyage automatique de l'ancien fichier si renommé pendant l'édition.</li>
                                        <li><strong>Pour les rôles admin, direction et managers :</strong>
                                            <ul>
                                                <li>Renommage de fichiers (via une boîte de dialogue JavaScript <code>prompt</code>, en vérifiant que la nouvelle extension est autorisée et que le nom n'est pas déjà pris).</li>
                                                <li>Suppression de fichiers avec boîte de confirmation.</li>
                                            </ul>
                                        </li>
                                    </ul>
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
                                    <strong>Fonctionnalités :</strong>
                                    <ul>
                                        <li><strong>Sécurisation stricte :</strong> Seuls les utilisateurs avec le rôle <code>admin</code> ou <code>moderateur</code> peuvent accéder à cette page. Les autres sont redirigés vers l'accueil.</li>
                                        <li>Liste tous les utilisateurs depuis <code>data/utilisateurs.json</code>.</li>
                                        <li>Ajout d'un utilisateur (avec hachage automatique du mot de passe via <code>password_hash()</code> et vérification pour empêcher la création de doublons de noms d'utilisateur).</li>
                                        <li>Modification des utilisateurs (permet de changer le rôle/groupe, l'email, ou le nom).
                                            <ul>
                                                <li>Vérifie que le nouveau nom d'utilisateur n'est pas déjà utilisé par un autre compte.</li>
                                                <li>Permet de changer le mot de passe (le champ peut être laissé vide pour conserver l'ancien).</li>
                                                <li>Conservation temporaire des groupes personnalisés dans la liste déroulante lors de l'édition.</li>
                                            </ul>
                                        </li>
                                        <li>Suppression d'utilisateurs (avec sécurité empêchant l'utilisateur de supprimer son propre compte connecté).</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingSix">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                    Accueil de l'Intranet (accueil_intranet.php)
                                </button>
                            </h2>
                            <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#accordionPages">
                                <div class="accordion-body">
                                    <strong>Fonctionnalités :</strong>
                                    <ul>
                                        <li>Redirection vers <code>connexion.php</code> si l'utilisateur n'est pas connecté.</li>
                                        <li>Page d'atterrissage principale après connexion, présentant des raccourcis vers les annuaires des employés, fournisseurs et clients.</li>
                                    </ul>
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
