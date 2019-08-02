<?php
require "elements/header.php";

?>

<div class="container">
<?php if (!isset($_POST["reponses"])): ?>
    <?php if (isset($_POST["theme"])): ?>
        <?php $_SESSION["theme"] = $_POST["theme"]; ?>
    <?php else: ?>
        <?php header("Location:index.php"); ?>
    <?php endif ?>
<?php endif ?>
<?php $questions = select_questions($_SESSION["theme"]); ?>
<h1 class="display-4 text-center"><?= "QCM - " . select_theme($_SESSION["theme"])->nom ?></h1>
<hr class="my-4">
<?php if(!empty($_POST["reponses"])): ?>
<?php $resultats = calculScore($_POST["reponses"],$questions); ?>
<div class="alert alert-success">Votre score est de <?= $resultats["points"] ?> sur <?= $resultats["scoreMax"] ?></div>
<?php endif ?>
<form action="jeu.php" method="post">
<?php foreach($questions as $question): ?>
    <?php $reponses = select_reponses($question->id); ?> 
    <div class="card text-center m-5">
        <div class="card-header text-white" style="background-color: #218ed6;">
            <h3><?= $question->contenu ?></h3>
        </div>
        <div class="card-body">
            <?php foreach($reponses as $k => $reponse): ?>
                <?php if (!empty($_POST["reponses"])): ?>
                    <?php foreach($_POST["reponses"] as $idReponseUser): ?>
                        <?php if ($reponse->id == $idReponseUser): ?>
                            <?php if ($reponse->vrai_rep == 1): ?>
                                <p class="text-success">BONNE REPONSE</p>
                            <?php elseif ($reponse->vrai_rep == 0): ?>
                                <p class="text-danger">MAUVAISE REPONSE</p>
                            <?php endif ?>
                        <?php endif ?>
                    <?php endforeach ?>
                    <?php if ($reponse->vrai_rep == 1 && !in_array($reponse->id,$_POST["reponses"])): ?>
                        CETTE REPONSE DEVAIT ETRE COCHEE
                    <?php endif ?>
                <?php endif ?>
                <div class="custom-control custom-checkbox py-1">
                <input type="checkbox" name="reponses[]" value="<?= $reponse->id ?>" class="custom-control-input" id="<?= $reponse->id ?>">
                <label class="custom-control-label" for="<?= $reponse->id ?>"><?= $reponse->contenu ?></label>
            </div>
            <?php if ($k != array_key_last($reponses)): ?>
            <hr class="my-4">
            <?php endif ?>
            <?php endforeach ?>
        </div>
    </div>

<?php endforeach ?>
<button class="btn btn-success btn-lg btn-block w-auto m-auto px-5" type="submit">Valider</button>
</form>
</div>

<?php
echo "<pre>";
var_dump($_POST);
var_dump($reponses);
var_dump($resultats);
echo "</pre>";