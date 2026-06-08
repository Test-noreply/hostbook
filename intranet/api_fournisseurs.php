<?php
// api_fournisseurs.php
header('Content-Type: application/json; charset=utf-8');

// Autoriser le site WordPress à interroger cette API
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

$fichier_fournisseurs = 'data/fournisseurs.json';

if (file_exists($fichier_fournisseurs)) {
    // On récupère directement le contenu du fichier JSON
    $contenu = file_get_contents($fichier_fournisseurs);
    // On l'affiche brut pour WordPress
    echo $contenu;
} else {
    http_response_code(404);
    echo json_encode(["error" => "Fichier de données introuvable."]);
}
exit();
