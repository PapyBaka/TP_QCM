<?php
require "elements/header.php";
$error = null;
$succes = null;
if (isset($_POST["email"]) && isset($_POST["mdp"]) && isset($_POST["prenom"]) && isset($_POST["nom"])) {

    if (trim(strlen($_POST["mdp"]) < 6)) {
        $error["mdp"] = "Mot de passe trop court";
    }
    if (!filter_var($_POST["email"],FILTER_VALIDATE_EMAIL)) {
        $error["email"] = "L'email doit être au format approprié. Ex: adresse@domaine.com";
    }
    if (preg_match("/([^A-Za-z])/",$_POST["prenom"]) === 1) {
        $error["prenom"] = "Votre prénom n'est probablement composé que de lettres de l'alphabet";
    }
    if (preg_match("/([^A-Za-z])/",$_POST["nom"]) === 1) {
        $error["nom"] = "Votre nom n'est probablement composé que de lettres de l'alphabet";
    }
    if (empty($error)) {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=qcm","root","");
            $requete = 'INSERT INTO personnes (prenom, nom, mail, mot_de_passe) VALUES (:prenom, :nom, :mail, :motdepasse)';
            $query = $pdo->prepare($requete);
            $query->execute([
                "prenom" => ucfirst($_POST["prenom"]),
                "nom" => ucfirst($_POST["nom"]),
                "mail" => $_POST["email"],
                "motdepasse" => password_hash($_POST["mdp"],PASSWORD_DEFAULT)
            ]);
            $succes = "Inscription réussie. Vous serez redirigé vers la page de connexion dans 5 secondes. Si ce n'est pas le cas, <a href='connexion.php'>cliquez ici";
            header("refresh:5;url=connexion.php"); 
        } catch (Exception $e) {
            $error["pdo"] = $e->getMessage();
        }
    }           
}
?>
<div class="container my-4">
    <h1>S'inscrire</h1>
    <hr class="my-4">
    <?php if ($succes): ?>
    <div class="alert alert-success">
        <?= $succes ?>
    </div>
    <?php endif ?>
    <?php if (isset($error["exception"])): ?>
    <div class="alert alert-danger">
        <?= $error["exception"] ?>
    </div>
    <?php endif ?>
<form class="form" method="post" action="">
    <div class="form-group">
      <label for="prenom">Prénom</label>
      <input required type="text" class="form-control <?= isset($error["prenom"]) ? 'is-invalid' : '' ?>" name="prenom" id="prenom" value="<?= !empty($error) ? htmlentities($_POST["prenom"]) : '' ?>">
      <?php if (isset($error["prenom"])): ?>
      <div class="invalid-feedback">
          <?= $error["prenom"]; ?>
      </div>
      <?php endif ?>
    </div>
    <div class="form-group">
      <label for="nom">Nom</label>
      <input required type="text" class="form-control <?= isset($error["nom"]) ? 'is-invalid' : '' ?>" name="nom" id="nom" value="<?= !empty($error) ? htmlentities($_POST["nom"]) : '' ?>">
      <?php if (isset($error["nom"])): ?>
      <div class="invalid-feedback">
          <?= $error["nom"]; ?>
      </div>
      <?php endif ?>
    </div>
    <div class="form-group">
      <label for="email">Adresse email</label>
      <input required type="email" class="form-control <?= isset($error["email"]) ? 'is-invalid' : '' ?>" name="email" id="email" value="<?= !empty($error) ? htmlentities($_POST["email"]) : '' ?>">
      <?php if (isset($error["email"])): ?>
      <div class="invalid-feedback">
          <?= $error["email"]; ?>
      </div>
      <?php endif ?>
    </div>
    <div class="form-group">
      <label for="mdp">Mot de passe</label>
      <input required type="password" class="form-control <?= isset($error["mdp"]) ? 'is-invalid' : '' ?>" name="mdp" id="mdp" value="<?= !empty($error) ? htmlentities($_POST["mdp"]) : '' ?>">
      <?php if (isset($error["mdp"])): ?>
      <div class="invalid-feedback">
          <?= $error["mdp"]; ?>
      </div>
      <?php endif ?>
    </div>
    <button type="submit" class="btn btn-primary">S'inscrire</button>
</form>
</div>

<?php
require "elements/footer.php";
?>