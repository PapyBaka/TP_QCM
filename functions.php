<?php

function array_key_last(array $array){
    return (!empty($array)) ? array_keys($array)[count($array)-1] : null;
}

function est_connecte() {
    if (isset($_SESSION["prenom"]) && isset($_SESSION["statut"]) and isset($_SESSION["id"])) {
        return true;
    }
    else {
        return false;
    }
}

function select_themes() {
    $pdo = new PDO("mysql:host=localhost;dbname=qcm;charset=utf8","root","");
    $requete = $pdo->query("SELECT id,nom FROM themes");
    $themes = $requete->fetchAll(PDO::FETCH_OBJ);
    return $themes;
}

function select_theme($theme_id) {
    $pdo = new PDO("mysql:host=localhost;dbname=qcm;charset=utf8","root","");
    $requete = $pdo->prepare("SELECT id,nom FROM themes WHERE id = :id_theme");
    $requete->execute(["id_theme" => $theme_id]);
    $themes = $requete->fetch(PDO::FETCH_OBJ);
    return $themes;
}

function select_questions($theme_id) {
    $pdo = new PDO("mysql:host=localhost;dbname=qcm;charset=utf8","root","");
    $requete = $pdo->prepare("SELECT * FROM questions WHERE id_theme = :id_theme");
    $requete->execute([
        "id_theme" => $theme_id
    ]);
    $questions = $requete->fetchAll(PDO::FETCH_OBJ);
    return $questions;
}

function select_reponses($question_id) {
    $pdo = new PDO("mysql:host=localhost;dbname=qcm;charset=utf8","root","");
    $requete = $pdo->prepare("SELECT contenu,vrai_rep,id FROM reponses WHERE id_question = :id_question");
    $requete->execute([
        "id_question" => $question_id
    ]);
    $reponses = $requete->fetchAll(PDO::FETCH_OBJ);
    return $reponses;
}

function ajout_theme($nom,$pdo)
{
    $nom = mb_strtoupper(trim(htmlspecialchars($nom)));
    if (empty($nom)) {
        throw new Exception("Le thème doit avoir un nom");
      }
      // ON VERIFIE QUE LE THEME N'EXISTE PAS
      $requete = $pdo->prepare("SELECT nom FROM themes WHERE nom = :nom");
      $requete->execute([
        "nom" => $nom
      ]);
      if ($requete->rowCount() != 0) {
        throw new Exception("Thème déjà existant");
      }
      // PUIS ON L'INSERE
      $requete = $pdo->prepare("INSERT INTO themes (nom, id_auteur) VALUES (:nom, :id_auteur)");
      $requete->execute([
        "nom" => $nom,
        "id_auteur" => $_SESSION["id"]
      ]);
      return true;
}

function ajout_question($nom_question,$choix_theme,$reponses,$pdo)
{
    $nom_question = ucfirst(trim(htmlspecialchars($nom_question)));
    if (empty($nom_question)) {
        throw new Exception("La question doit avoir un contenu");
    }
    // ON VERIFIE SI LA QUESTION EXISTE
    $requete = $pdo->prepare("SELECT contenu FROM questions WHERE contenu = :contenu");
    $requete->execute([
        "contenu" => $nom_question
    ]);
    if ($requete->rowCount() !== 0) {
        throw new Exception("Question déjà existante");
    }
    
    // ON VERIFIE SI IL Y A AU MOINS 2 REPONSES ET 1 BONNE
    $nombre_rep = 0;
    $nb_vrai_rep = 0;
    $i = 1;
    foreach ($reponses as $reponse) {
        if ($nombre_rep < 2) {
            if (!empty(trim($reponse))) {
                $nombre_rep++;
                if (isset($_POST["rep".$i])) {
                    $nb_vrai_rep++;
                }
            }
        }
    $i++;
    }
    if ($nombre_rep < 2) {
        throw new Exception("Deux réponses minimum");
    }
    if ($nb_vrai_rep == 0) {
        throw new Exception("Il faut au moins une bonne réponse");
    }
    
    //SI C'EST BON, ON INSERE LA QUESTION...
    $requete = $pdo->prepare("INSERT INTO questions (contenu, id_theme, nb_vrai_rep) VALUES (:question, :id_theme, :nb_vrai_rep)");
    $requete->execute([
        "question" => $nom_question,
        "id_theme" => $choix_theme,
        "nb_vrai_rep" => $nb_vrai_rep
    ]);
    $last_id = $pdo->prepare("SELECT id FROM questions ORDER BY id DESC LIMIT 1");
    $last_id->execute();
    $id_question = $last_id->fetch();

    //... PUIS LES REPONSES NON VIDES EN LES RELIANT A L'ID DE LA QUESTION
    $i = 1;
    foreach ($reponses as $reponse) {
        if (!empty($reponse)) {
            $requete = $pdo->prepare("SELECT contenu FROM reponses WHERE contenu = :contenu");
            $requete->execute([
            "contenu" => ucfirst(strtolower(trim($reponse)))
            ]);
            if ($requete->rowCount() == 0) {
            $insert_rep = $pdo->prepare("INSERT INTO reponses (contenu,id_question,vrai_rep) VALUES (:contenu, :id_question, :vrai_rep)");
            $insert_rep->execute([
                "contenu" => $reponse,
                "id_question" => $id_question["id"],
                "vrai_rep" => isset($_POST["rep".$i]) ? "1" : "0"
            ]);
            }
        $i++;
        }
    }
    return true;
}

function ajout_rep_supp($choix_question,$reponse,$bonne_reponse,$pdo)
{
    if (empty(trim($reponse))) {
        throw new Exception("Votre réponse doit posséder un contenu");
    }
    $requete = $pdo->prepare("INSERT INTO reponses (contenu,id_question,vrai_rep) VALUES (:contenu, :id_question, :bonne_rep)");
    $requete->execute([
    "contenu" => $reponse,
    "id_question" => $choix_question,
    "bonne_rep" => $bonne_reponse == "1" ? "1" : "0"
    ]);
    if ($bonne_reponse == "1") {
        $requete = $pdo->prepare("UPDATE questions SET nb_vrai_rep = nb_vrai_rep + 1 WHERE id = :id_question");
        $requete->execute([
            "id_question" => $choix_question
        ]);
    }
    return true;
}

function modif_theme($id_theme,$nouveau_nom,$pdo)
{
    $nouveau_nom = mb_strtoupper(trim(htmlspecialchars($nouveau_nom)));
    if (empty($nouveau_nom)) {
        throw new Exception("Le thème doit avoir un nom");
    }
    $requete = $pdo->prepare("UPDATE themes SET nom = :nom WHERE id = :id_theme");
    $requete->execute([
        "nom" => $nouveau_nom,
        "id_theme" => $id_theme
    ]);
    return true;
}

function supp_theme($id_theme,$pdo) {
    $requete = $pdo->prepare("DELETE FROM themes WHERE id = :id_theme");
    $requete->execute([
        "id_theme" => $id_theme
    ]);
    return true;
}

function modif_question($id_question,$nouveau_nom,$pdo) {
    $nouveau_nom = ucfirst(strtolower(trim(htmlspecialchars($nouveau_nom))));
    if (empty($nouveau_nom)) {
        throw new Exception("La question ne peut être vide");
    }
    $requete = $pdo->prepare("UPDATE questions SET contenu = :contenu WHERE id = :id_question");
    $requete->execute([
        "contenu" => $nouveau_nom,
        "id_question" => $id_question
    ]);
    return true;
}

function supp_question($id_question,$pdo) {
    $requete = $pdo->prepare("DELETE FROM questions WHERE id = :id_question");
    $requete->execute([
        "id_question" => $id_question
    ]);
    $requete = $pdo->prepare("DELETE FROM reponses WHERE id_question = :id_question");
    $requete->execute([
        "id_question" => $id_question
    ]);
    return true;
}

function modif_reponse($id_reponse,$nouveau_nom,$pdo) {
    if (empty($nouveau_nom)) {
        throw new Exception("La réponse ne peut être vide");
    }
    $requete = $pdo->prepare("UPDATE reponses SET contenu = :nouveau_nom WHERE id = :id_reponse");
    $requete->execute([
        "nouveau_nom" => $nouveau_nom,
        "id_reponse" => $id_reponse
    ]);
    return true;
}

function supp_reponse($id_reponse,$pdo) {
    $requete = $pdo->prepare("SELECT vrai_rep,id_question FROM reponses WHERE id = :id_reponse");
    $requete->execute([
        "id_reponse" => $id_reponse
    ]);
    $reponse = $requete->fetch(PDO::FETCH_ASSOC);
    // ON VERIFIE QU'IL RESTE AU MOINS UNE BONNE REPONSE
    $requete = $pdo->prepare("SELECT nb_vrai_rep FROM questions WHERE id = :id_question");
    $requete->execute([
        "id_question" => $reponse["id_question"]
    ]);
    $question = $requete->fetch(PDO::FETCH_OBJ);
    
    if ($reponse["vrai_rep"] == 1) {
        if ($question->nb_vrai_rep <= 1) {
            throw new Exception("Il doit forcément rester une bonne réponse. Vous pouvez créer une autre bonne réponse et supprimer celle-ci.");
        }
        $requete = $pdo->prepare("UPDATE questions SET nb_vrai_rep = nb_vrai_rep - 1 WHERE id = :id_question");
        $requete->execute([
            "id_question" => $reponse["id_question"]
        ]);
    }
    $requete = $pdo->prepare("DELETE FROM reponses WHERE id = :id_reponse");
    $requete->execute([
        "id_reponse" => $id_reponse
    ]);
    return true;
}

function calculScore($reponsesUser,$questions)
{
    $pdo = new PDO("mysql:host=localhost;dbname=qcm;charset=utf8","root","");
    $score = 0;
    $scoreMax = count($questions);

    //CALCUL NOMBRE BONNES REPONSES
    foreach($questions as $question) {
        $reponsesBdd = select_reponses($question->id);
        $nb_vrai_rep_user = 0;
        foreach ($reponsesUser as $reponseUser) {
            foreach($reponsesBdd as $reponseBdd) {
                if ($reponseUser == $reponseBdd->id) {
                    if ($reponseBdd->vrai_rep == 1) {
                        $nb_vrai_rep_user++;
                    } elseif ($reponseBdd->vrai_rep == 0) {
                        $nb_vrai_rep_user--;
                    }
                }
            }
        }
        if ($nb_vrai_rep_user == $question->nb_vrai_rep) {
            $score++;
        }
    }
    $multiple = 20 / $scoreMax;
    $note_sur_20 = round($score * $multiple,1);
    return ["score" => $score, "scoreMax" => $scoreMax, "note_sur_20" => $note_sur_20];
}

function insererResultat($note_sur_20) {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=qcm;charset=utf8","root","");
        $requete = $pdo->prepare("INSERT INTO resultats(score,id_personne,date_creation,id_theme) VALUES (:score,:id_personne,:date_creation,:id_theme)");
        $requete->execute([
            "score" => $note_sur_20,
            "id_personne" => $_SESSION["id"],
            "date_creation" => date("Y-m-d"),
            "id_theme" => $_SESSION["theme"]
        ]);
        return true;
    } catch (Exception $e) {
        return $e->getMessage();
    }
    
}

function selectResultats() {
    $pdo = new PDO("mysql:host=localhost;dbname=qcm;charset=utf8","root","");
    $requete = $pdo->prepare("SELECT resultats.id,score,date_creation,nom FROM resultats INNER JOIN themes ON resultats.id_theme = themes.id WHERE resultats.id_personne = :id_personne");
    $requete->execute([
        "id_personne" => $_SESSION["id"]
    ]);
    $resultats = $requete->fetchAll(PDO::FETCH_OBJ);
    foreach ($resultats as $resultat) {
        $date = new DateTime($resultat->date_creation);
        $resultat->date_creation = $date->format("d/m/Y");
    }
    return $resultats;
}