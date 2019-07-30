<?php
require "elements/header.php";
$questions = select_questions($_POST["theme"]);
$reponses = select_reponses($questions[2]->id);
?>

<div class="container">
    <div class="card text-center">
        <div class="card-header">
            <h3><?= $questions[2]->contenu ?></h3>
        </div>
        <div class="card-body">
            <ol class="list-group">
            <?php foreach($reponses as $reponse): ?>
                <li class="list-group-item"><?= $reponse->contenu ?></li>
            <?php endforeach ?>
            </ol>
            <hr class="my-4">
            <form action="" method="POST">
                <button class="btn btn-primary" type="submit">Next</button>
            </form>
        </div>
    </div>
</div>

<?php
echo "<pre>";

var_dump($questions);
echo "</pre>";