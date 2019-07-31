<?php
require "elements/header.php";

if (isset($_POST["theme"])) {
    $questions = select_questions($_POST["theme"]);
    
}
?>

<div class="container">
<?php foreach($questions as $question): ?>
    <?php $reponses = select_reponses($question->id); ?> 
    <div class="card text-center m-4">
        <div class="card-header bg-info text-white">
            <h3><?= $question->contenu ?></h3>
        </div>
        <div class="card-body">
            <?php foreach($reponses as $k => $reponse): ?>
            <div class="custom-control custom-checkbox py-1">
                <input type="checkbox" name="reponses[]" value="<?= $reponse->vrai_rep ?>" class="custom-control-input" id="<?= $reponse->id ?>">
                <label class="custom-control-label" for="<?= $reponse->id ?>"><?= $reponse->contenu ?></label>
            </div>
            <?php if ($k != array_key_last($reponses)): ?>
            <hr class="my-4">
            <?php endif ?>
            <?php endforeach ?>
        </div>
    </div>
<?php endforeach ?>
</div>

<?php
echo "<pre>";

echo "</pre>";