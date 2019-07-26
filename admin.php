<?php
require "elements/header.php";
$error = null;
echo "<pre>";
var_dump(select_themes("choix_theme"));
echo "</pre>";
if (isset($_POST["ajout_theme"]) || isset($_POST["ajout_question"])) {
    try {
    $pdo = new PDO("mysql:host=localhost;dbname=qcm","root","");

    // AJOUT THEME
    if (isset($_POST["ajout_theme"])) {
        $requete = $pdo->prepare("INSERT INTO themes (nom, id_auteur) VALUES (:nom, :id_auteur)");
        $requete->execute([
            "nom" => $_POST["ajout_theme"],
            "id_auteur" => $_SESSION["id"]
        ]);
    }
    // AJOUT QUESTION
    if (isset($_POST["choix_theme"]) && isset($_POST["ajout_question"])) {
        $requete = $pdo->prepare("INSERT INTO questions (contenu, id_theme) VALUES (:question, :id_theme)");
        $requete->execute([
            "question" => $_POST["ajout_question"],
            "id_theme" => $_POST["choix_theme"]
        ]);
    }

    //AJOUT REPONSE
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<div class="container">
  <p class="display-4">Espace d'administration</p>
  <hr class="my-4">
  <?php if($error): ?>
  <div class="alert alert-danger">
      <?= $error ?>
  </div>
  <?php endif ?>
  <div class="row">
    
    <div class="card col-6 p-0 m-2">
      <div class="card-header text-center text-white" style="background-color:#218ed6">
        <h3>Création</h3>
      </div>
        <!-- <div class="card-body d-flex justify-content-center align-items-center">
          <a class="btn btn-primary mx-2" href="ajouter_theme.php" role="button">Créer thème</a>
          <a class="btn btn-primary" href="ajouter_theme.php" role="button">Déposer ajout_questions/réponses</a>
        </div> -->
        <div class="card-body">
            
            <!-- FORMULAIRE AJOUT THEME -->
            <form action="" method="POST">
                <div class="form-group">
                    <label for="ajout_theme"><h4>Ajouter un thème</h4></label>
                    <input type="text" class="form-control" id="ajout_theme" name="ajout_theme" placeholder="Nom du thème">
                </div>
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </form>


            <hr class="my-4">

            <!-- FORMULAIRE AJOUT QUESTION -->
            <form action="" method="POST">
                <div class="form-group">
                    <label for="choix_theme"><h4>Ajouter une question</h4></label>
                      <select class="form-control" name="choix_theme" id="choix_theme">
                        <option selected disabled>Choix du thème</option>
                        <?php $themes = select_themes("choix_theme"); ?>
                        <?php foreach($themes as $theme): ?>
                        <option value="<?= $theme->id ?>"><?= $theme->nom ?></option>
                        <?php endforeach ?>
                      </select>
                </div>
                <div class="form-group">
                    <input class="form-control" id="ajout_question" name="ajout_question" placeholder="Contenu de la question">
                </div>
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </form>

            <hr class="my-4">

            <!-- FORMULAIRE AJOUT REPONSE -->
            <form action="" method="POST">
                <div class="form-group">
                    <label for="choix_question"><h4>Ajouter une réponse</h4></label>
                      <select class="form-control" name="choix_question" id="choix_question">
                        <option selected disabled>Choix de la question</option>
                        <?php $themes = select_themes(); ?>
                        <?php foreach($themes as $theme): ?>
                            <option disabled value="<?=$theme->id?>"><strong><?= $theme->nom ?></strong></option>
                            <?php $questions = select_questions("choix_question",$theme->id); ?>
                            <?php foreach ($questions as $question): ?>
                            <option value="<?= $question->id ?>"><?= $question->contenu ?></option>
                            <?php endforeach ?>
                        <?php endforeach ?>
                      </select>
                </div>
                <div class="form-group">
                    <input class="form-control" id="ajout_reponse" nom="ajout_reponse" placeholder="Contenu de la réponse">
                </div>
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </form>
        </div>
    </div>

    <div class="card col p-0 m-2">
      <div class="card-header text-center text-white" style="background-color:#218ed6">
        <h3>Consultation/modification</h3>
      </div>
      <div class="card-body">
        Statut: <?= $_SESSION["statut"] ?>
      </div>
    </div>
  </div>
</div>
<?php
require "elements/footer.php";