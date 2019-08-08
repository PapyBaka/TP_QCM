<?php
require "elements/header.php";
$error = null;
$success = null;
// VERIF DONNEES POST
if (isset($_POST)) {
  try {
    $pdo = new PDO("mysql:host=localhost;dbname=qcm;charset=utf8", "root", "");

    if (isset($_POST["form_ajout_theme"])) {
      if (ajout_theme($_POST["ajout_theme"], $pdo)) {
        $success = "Thème ajouté avec succès !";
      }
    }

    if (isset($_POST["form_ajout_question"])) {
      if (!isset($_POST["choix_theme"])) {
        throw new Exception("Un thème doit être choisi");
      }
      if (ajout_question($_POST["ajout_question"], $_POST["choix_theme"], $_POST["ajout_reponse"], $pdo)) {
        $success = "Question/réponses ajoutées avec succès !";
      }
    }

    if (isset($_POST["form_ajout_rep_supp"])) {
      if (!isset($_POST["choix_question"])) {
        throw new Exception("La réponse doit être liée à une question");
      }
      $bonne_rep = isset($_POST["bonne_rep_supp"]) ? $_POST["bonne_rep_supp"] : "0";
      if (ajout_rep_supp($_POST["choix_question"], $_POST["ajout_rep_supp"], $bonne_rep, $pdo)) {
        $success = "Réponse ajoutée avec succès !";
      }
    }

    if (isset($_POST["form_modif_theme"])) {
      if (!isset($_POST["choix_modif_theme"])) {
        throw new Exception("Un thème doit être sélectionné");
      }
      if (modif_theme($_POST["choix_modif_theme"], $_POST["nom_modif_theme"], $pdo)) {
        $success = "Thème modifié avec succès";
      }
    }

    if (isset($_POST["form_supp_theme"])) {
      if (!isset($_POST["choix_modif_theme"])) {
        throw new Exception("Un thème doit être sélectionné");
      }
      $success = "Thème supprimé avec succès";
    }

    if (isset($_POST["form_modif_question"])) {
      if (!isset($_POST["choix_modif_question"])) {
        throw new Exception("Une question doit être sélectionnée");
      }
      if (modif_question($_POST["choix_modif_question"], $_POST["nom_modif_question"], $pdo)) {
        $success = "La question a bien été modifié";
      }
    }

    if (isset($_POST["form_supp_question"])) {
      if (!isset($_POST["choix_modif_question"])) {
        throw new Exception("Une question doit être sélectionné");
      }
      if (supp_question($_POST["choix_modif_question"], $pdo)) {
        $success = "Question/réponses supprimées avec succès";
      }
    }

    if (isset($_POST["form_modif_rep"])) {
      if (!isset($_POST["choix_modif_rep"])) {
        throw new Exception("Une réponse doit être sélectionné");
      }
      if (modif_reponse($_POST["choix_modif_rep"], $_POST["nom_modif_rep"], $pdo)) {
        $success = "Réponse modifiée avec succès";
      }
    }

    if (isset($_POST["form_supp_rep"])) {
      if (!isset($_POST["choix_modif_rep"])) {
        throw new Exception("Une réponse doit être sélectionné");
      }
      if (supp_reponse($_POST["choix_modif_rep"], $pdo)) {
        $success = "Réponse supprimée avec succès";
      }
    }
  } catch (Exception $e) {
    $error = $e->getMessage();
  }
}
?>

<div class="container">
  <p class="display-4">Espace d'administration</p>
  <hr class="my-4">
  <?php if ($error) : ?>
    <div class="alert alert-danger">
      <?= $error ?>
    </div>
  <?php endif ?>
  <?php if ($success) : ?>
    <div class="alert alert-success">
      <?= $success ?>
    </div>
  <?php endif ?>
  <div class="row">

    <div class="card col-6 p-0 m-2">
      <div class="card-header text-center text-white" style="background-color:#218ed6">
        <h3>Création</h3>
      </div>

      <div class="card-body text-center">

        <!-- FORMULAIRE AJOUT THEME -->
        <form action="" method="POST">
          <div class="form-group">
            <label for="ajout_theme">
              <h4>Ajouter un thème</h4>
            </label>
            <input type="text" class="form-control" id="ajout_theme" name="ajout_theme" placeholder="Nom du thème">
          </div>
          <button type="submit" name="form_ajout_theme" class="btn btn-primary">Ajouter</button>
        </form>


        <hr class="my-4">

        <!-- FORMULAIRE AJOUT QUESTION -->
        <form action="" method="POST">
          <div class="form-group">
            <label for="choix_theme">
              <h4>Ajouter une question</h4>
            </label>
            <select class="form-control" name="choix_theme" id="choix_theme">
              <option selected disabled>Choix du thème</option>
              <?php $themes = select_themes("choix_theme", $pdo); ?>
              <?php foreach ($themes as $theme) : ?>
                <option <?= isset($_POST["choix_theme"]) && $_POST["choix_theme"] == $theme->id && isset($error) ? "selected" : "" ?> value="<?= $theme->id ?>"><?= $theme->nom ?></option>
              <?php endforeach ?>
            </select>
          </div>

          <div class="form-group">
            <input class="form-control" id="ajout_question" name="ajout_question" value="<?= !empty($_POST["ajout_question"]) && isset($error) ? $_POST["ajout_question"] : '' ?>" placeholder="Contenu de la question">
          </div>

          <!-- REPONSE 1 -->
          
          <label for="ajout_reponse">
            <h4>Réponses associées</h4>
            <p class="form-text text-muted m-0">Cochez la/les bonne(s) réponse(s)</p>
          </label>
          
          <div class="form-group">
            <div class="input-group">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <input type="checkbox" name="rep1" value="1">
                </div>
              </div>
              <input class="form-control" id="ajout_reponse" name="ajout_reponse[]" value="<?= !empty($_POST["ajout_reponse"][0]) && isset($error) ? $_POST["ajout_reponse"][0] : '' ?>" placeholder="Reponse 1">
            </div>
          </div>

          <!-- REPONSE 2 -->
          <div class="form-group">
            <div class="input-group">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <input type="checkbox" name="rep2" value="1">
                </div>
              </div>
              <input class="form-control" id="ajout_reponse" name="ajout_reponse[]" value="<?= !empty($_POST["ajout_reponse"][1]) && isset($error) ? $_POST["ajout_reponse"][1] : '' ?>" placeholder="Reponse 2">
            </div>
          </div>

          <!-- REPONSE 3 -->
          <div class="form-group">
            <div class="input-group">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <input type="checkbox" name="rep3" value="1">
                </div>
              </div>
              <input class="form-control" id="ajout_reponse" name="ajout_reponse[]" value="<?= !empty($_POST["ajout_reponse"][2]) && isset($error) ? $_POST["ajout_reponse"][2] : '' ?>" placeholder="Reponse 3">
            </div>
          </div>
          <button type="submit" name="form_ajout_question" class="btn btn-primary">Ajouter</button>
        </form>

        <hr class="my-4">

        <!-- FORMULAIRE AJOUT REPONSE -->
        <form action="" method="POST">
          <div class="form-group">
            <label for="choix_question">
              <h4>Ajouter une réponse</h4>
            </label>
            <select class="form-control" name="choix_question" id="choix_question">
              <option selected disabled>Choix de la question</option>
              <?php $themes = select_themes(); ?>
              <?php foreach ($themes as $theme) : ?>
                <option disabled value="<?= $theme->id ?>">---------<?= $theme->nom ?>---------</option>
                <?php $questions = select_questions($theme->id); ?>
                <?php foreach ($questions as $question) : ?>
                  <option <?= isset($_POST["choix_question"]) && $_POST["choix_question"] == $question->id && isset($error) ? "selected" : "" ?> value="<?= $question->id ?>"><?= $question->contenu ?></option>
                <?php endforeach ?>
              <?php endforeach ?>
            </select>
          </div>
          <div class="form-group">
            <div class="input-group">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <input type="checkbox" name="bonne_rep_supp" value="1">
                </div>
              </div>
              <input type="text" class="form-control" id="ajout_rep_supp" name="ajout_rep_supp" value="<?= !empty($_POST["ajout_rep_supp"]) && isset($error) ? $_POST["ajout_rep_supp"] : '' ?>" placeholder="Contenu de la réponse">
            </div>
            <small class="form-text text-muted" id="help">
              Cochez la case s'il s'agit d'une bonne réponse
            </small>
          </div>
          <button type="submit" name="form_ajout_rep_supp" class="btn btn-primary">Ajouter</button>
        </form>
      </div>
    </div>

    <!-- SECTION MODIFICATION -->
    <div class="card col p-0 m-2">
      <div class="card-header text-center text-white" style="background-color:#218ed6">
        <h3>Consultation/modification</h3>
      </div>
      <div class="card-body text-center">

        <!-- FORM MODIFICATION THEME -->
        <form method="post" action="">
          <div class="form-group">
            <label for="choix_modif_theme">
              <h4>Modifier un thème</h4>
            </label>
            <select class="form-control" id="choix_modif_theme" name="choix_modif_theme">
              <option disabled selected value="">Choix du thème</option>
              <?php foreach ($themes as $theme) : ?>
                <option <?= isset($_POST["choix_modif_theme"]) && $_POST["choix_modif_theme"] == $theme->id && isset($error) ? "selected" : "" ?> value="<?= $theme->id ?>"><?= $theme->nom ?></option>
              <?php endforeach ?>
            </select>
          </div>
          <div class="form-group">
            <input class="form-control" id="nom_modif_theme" name="nom_modif_theme" value="<?= !empty($_POST["nom_modif_theme"]) && isset($error) ? $_POST["nom_modif_theme"] : '' ?>" placeholder="Nouveau nom du thème">
          </div>
          <div class="d-flex justify-content-center">
            <button type="submit" name="form_modif_theme" class="btn btn-primary mr-2">Modifier</button>
            <button type="submit" name="form_supp_theme" class="btn btn-danger ml-2">Supprimer</button>
          </div>
          <div class="alert alert-warning mt-2" role="alert">
            Attention ! Supprimer un thème effacera aussi toutes les questions/réponses associées
          </div>
        </form>

        <hr class="my-4">

        <!-- FORM MODIFICATION QUESTION -->
        <form method="post" action="">
          <div class="form-group">
            <label for="choix_modif_question">
              <h4>Modifier une question</h4>
            </label>
            <select class="form-control" name="choix_modif_question" id="choix_modif_question">
              <option selected disabled>Choix de la question</option>
              <?php foreach ($themes as $theme) : ?>
                <option disabled value="">---------<?= $theme->nom ?>---------</option>
                <?php $questions = select_questions($theme->id); ?>
                <?php foreach ($questions as $question) : ?>
                  <option <?= isset($_POST["choix_modif_question"]) && $_POST["choix_modif_question"] == $question->id && isset($error) ? "selected" : "" ?> value="<?= $question->id ?>"><?= $question->contenu ?></option>
                <?php endforeach ?>
              <?php endforeach ?>
            </select>
          </div>
          <div class="form-group">
            <input class="form-control" name="nom_modif_question" value="<?= isset($_POST['nom_modif_question']) && isset($error) ? $_POST['nom_modif_question'] : '' ?>" placeholder="Nouveau contenu de la question">
          </div>
          <div class="d-flex justify-content-center">
            <button type="submit" name="form_modif_question" class="btn btn-primary mr-2">Modifier</button>
            <button type="submit" name="form_supp_question" class="btn btn-danger ml-2">Supprimer</button>
          </div>
          <div class="alert alert-warning mt-2" role="alert">
            Attention ! Supprimer une question effacera aussi toutes les réponses associées
          </div>
        </form>

        <hr class="my-4">

        <!-- FORM MODIFICATION REPONSE -->
        <form method="post" action="">
          <div class="form-group">
            <label for="choix_modif_rep">
              <h4>Modifier une réponse</h4>
            </label>
            <select class="form-control" name="choix_modif_rep">
              <option disabled selected value="">Choix de la question</option>
              <?php foreach ($questions as $question) : ?>
                <option disabled value="">------<?= $question->contenu ?>------</option>
                <?php $reponses = select_reponses($question->id); ?>
                <?php foreach ($reponses as $reponse) : ?>
                  <option <?= $reponse->vrai_rep == 1 ? "style='background-color:#8ae888'" : "" ?><?= isset($_POST["choix_modif_rep"]) && $_POST["choix_modif_rep"] == $reponse->id && isset($error) ? "selected" : "" ?> value="<?= $reponse->id ?>"><?= $reponse->contenu ?></option>
                <?php endforeach ?>
              <?php endforeach ?>
            </select>
          </div>
          <div class="form-group">
            <div class="input-group">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <input type="checkbox" name="bonne_rep_supp" value="1">
                </div>
              </div>
              <input class="form-control" name="nom_modif_rep" value="<?= !empty($_POST["nom_modif_rep"]) && isset($error) ? $_POST["nom_modif_rep"] : '' ?>" placeholder="Nouveau contenu de la réponse">
            </div>
            <small class="form-text text-muted" id="help">
              Cochez ou décochez la case pour modifier l'indicateur de bonne réponse
            </small>
          </div>
          <div class="d-flex justify-content-center">
            <button type="submit" name="form_modif_rep" class="btn btn-primary mr-2">Modifier</button>
            <button type="submit" name="form_supp_rep" class="btn btn-danger ml-2">Supprimer</button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>
<?php
require "elements/footer.php";
