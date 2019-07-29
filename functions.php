<?php

function est_connecte() {
    if (isset($_SESSION["prenom"]) && isset($_SESSION["statut"]) and isset($_SESSION["id"])) {
        return true;
    }
    else {
        return false;
    }
}

function select_themes() {
    $pdo = new PDO("mysql:host=localhost;dbname=qcm","root","");
    $requete = $pdo->query("SELECT id,nom FROM themes");
    $themes = $requete->fetchAll(PDO::FETCH_OBJ);
    return $themes;
}

function select_questions($theme_id) {
    $pdo = new PDO("mysql:host=localhost;dbname=qcm","root","");
    $requete = $pdo->prepare("SELECT id,contenu FROM questions WHERE id_theme = :id_theme");
    $requete->execute([
        "id_theme" => $theme_id
    ]);
    $questions = $requete->fetchAll(PDO::FETCH_OBJ);
    return $questions;
}