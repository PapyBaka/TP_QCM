<?php
require "elements/header.php";
$error = null;
if (!isset($_POST["reponses"]) && !isset($_POST["theme"]) && !isset($_SESSION["theme"])) {
    header("Location:index.php");
}
if (isset($_POST["theme"])) {
    $_SESSION["theme"] = $_POST["theme"];
}
if (!isset($_POST["theme"]) && (isset($_SESSION["theme"]) && empty($_POST["reponses"]))) {
    $error = "Il faut sélectionner au moins une réponse.";
}
?>



<div class="container">
<?php $questions = select_questions($_SESSION["theme"]);?>
<h1 class="display-4 text-center"><?="QCM - " . select_theme($_SESSION["theme"])->nom?></h1>
<hr class="my-4">
<?php if (isset($error)): ?>
<div class="alert alert-danger w-50 m-auto text-center">
    <?=$error?>
</div>
<?php endif?>
<?php if (!empty($_POST["reponses"])): ?>
    <?php $resultats = calculScore($_POST["reponses"], $questions);?>
    <div class="alert alert-info w-50 m-auto text-center font-weight-bold">
        Votre score est de <?=$resultats["score"]?> sur <?=$resultats["scoreMax"]?>, soit une note de <?=$resultats["note_sur_20"] ?>/20<br>
        <a href="index.php">Retourner à l'accueil</a>
    </div>
    <?php insererResultat($resultats["note_sur_20"]) ?>
<?php endif?>
<?php if (empty($_POST["reponses"])): ?>
<form action="jeu.php" method="post">
<?php endif ?>
<?php foreach ($questions as $question): ?>
    <?php $reponses = select_reponses($question->id);?>
    <div class="card text-center m-5">
        <div class="card-header text-white" style="background-color: #218ed6;">
            <h3><?=$question->contenu?></h3>
        </div>
        <div class="card-body">
            <?php foreach ($reponses as $k => $reponse): ?>
                <?php if (!empty($_POST["reponses"])): ?>
                    <?php foreach ($_POST["reponses"] as $idReponseUser): ?>
                        <?php if ($reponse->id == $idReponseUser): ?>
                            <?php if ($reponse->vrai_rep == 1): ?>
                                    <p class="text-success font-weight-bold"><i class="material-icons">sentiment_very_satisfied</i> <?=$reponse->contenu?></p>
                                    <p class="text-success">Bonne réponse</p>
                            <?php elseif ($reponse->vrai_rep == 0): ?>
                                    <p class="text-danger font-weight-bold"><i class="material-icons">sentiment_dissatisfied</i> <?=$reponse->contenu?></p>
                                    <p class="text-danger">Mauvaise réponse</p>
                            <?php endif?>
                        <?php endif?>
                    <?php endforeach?>
                    <?php if ($reponse->vrai_rep == 1 && !in_array($reponse->id, $_POST["reponses"])): ?>
                        <p class="text-warning font-weight-bold"><i class="material-icons">sentiment_dissatisfied</i> <?=$reponse->contenu?></p>
                        <p class="text-warning">Cette réponse devait être cochée</p>
                    <?php endif?>
                    <?php if ($reponse->vrai_rep == 0 && !in_array($reponse->id, $_POST["reponses"])): ?>
                    <p><?=$reponse->contenu?></p>
                    <?php endif?>

                <?php else: ?>
                <div class="custom-control custom-checkbox py-1">
                    <input type="checkbox" name="reponses[]" value="<?=$reponse->id?>" class="custom-control-input" id="<?=$reponse->id?>">
                    <label class="custom-control-label" for="<?=$reponse->id?>"><?=$reponse->contenu?></label>
                </div>
                <?php endif?>
            <?php if ($k != array_key_last($reponses)): ?>
            <hr class="my-4">
            <?php endif?>
            <?php endforeach?>
        </div>
    </div>

<?php endforeach ?>
<?php if (empty($_POST["reponses"])): ?>
<button class="btn btn-success btn-lg btn-block w-auto m-auto px-5" type="submit">Valider</button>
</form>
<?php endif ?>
</div>

<?php
echo "<pre>";
var_dump($_POST);
var_dump($reponses);
var_dump($resultats);
echo "</pre>";