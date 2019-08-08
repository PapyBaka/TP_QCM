<?php
require "elements/header.php";
?>

<?php if (est_connecte()): ?>
<div class="container">
  <p class="display-4 text-center">Bienvenue <?= $_SESSION["prenom"] ?> !</p>
  <hr class="my-4">
  <div class="row">
    
    <div class="card col p-0 m-2">
      <div class="card-header text-center text-white" style="background-color:#218ed6">
        <h3>Jouer</h3>
      </div>
      <div class="card-body text-center">
        <form method="POST" action="jeu.php">
          <div class="form-group">
            <label for="theme">Choix du thème</label>
            <?php $themes = select_themes() ?>
            <select class="form-control w-50 m-auto" name="theme" id="theme">
              <?php foreach($themes as $theme): ?>
                <option value="<?= $theme->id ?>"><?= $theme->nom ?></option>
              <?php endforeach ?>
            </select>
          </div>
          <button class="btn btn-primary btn-lg m-auto" type="submit">C'est parti !</button>
        </form>
      </div>
    </div>

    <div class="card col p-0 m-2">
      <div class="card-header text-center text-white" style="background-color:#218ed6">
        <h3>Résultats</h3>
      </div>
      <div class="card-body">
        <?php $resultats = selectResultats() ?>
        
          <table class="table table-striped table-inverse">
            <thead class="thead-inverse">
              <tr>
                <th>N° essai</th>
                <th>Theme</th>
                <th>Date</th>
                <th>Score</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($resultats as $resultat): ?>
              <tr>
                <td scope="row"><?= $resultat->id ?></td>
                <td><?= $resultat->nom ?></td>
                <td><?= $resultat->date_creation ?></td>
                <td><strong><?= $resultat->score ?>/20</strong></td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
      </div>
    </div>
  </div>
</div>
<?php else: ?>
<div class="jumbotron">
  <h1 class="display-4">Bienvenue sur le site du Super QCM !</h1>
  <p class="lead">Découvrez des milliers de thèmes et de nouvelles questions tous les jours.</p>
  <hr class="my-4">
  <p>Rejoignez dés à présent notre communauté !</p>
  <a class="btn btn-primary btn-lg" href="connexion.php" role="button">Se connecter</a>
  <a class="btn btn-primary btn-lg" href="inscription.php" role="button">Inscription</a>
</div>
<?php endif ?>
<?php
require "elements/footer.php";