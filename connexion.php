<?php
require "elements/header.php";
$error = null;
$succes = null;
if (isset($_POST["email"]) && isset($_POST["mdp"])) {
    if (!filter_var($_POST["email"],FILTER_VALIDATE_EMAIL)) {
        $error["email"] = "L'email doit être au format approprié. Ex: adresse@domaine.com";
    }
    if (empty($error)) {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=qcm","root","");
            $requete = $pdo->prepare("SELECT mot_de_passe FROM personnes WHERE mail = :email");
            $requete->execute([
                "email" => $_POST["email"]
            ]);
            $donnees = $requete->fetch(PDO::FETCH_ASSOC);
            if (password_verify($_POST["mdp"],$donnees["mot_de_passe"])) {
                $succes = "Connexion réussie";
            }
            else {
                throw new Exception("Combinaison mail/mdp inexistante");
            }
        } catch (Exception $e) {
            $error["exception"] = $e->getMessage();
        }
    }   
}
?>

<div class="container">
<?php if (isset($error["exception"])): ?>
    <div class="alert alert-danger">
        <?= $error["exception"]; ?>
    </div>
<?php endif ?>
<?php if (isset($succes)): ?>
    <div class="alert alert-success">
        <?= $succes; ?>
    </div>
<?php endif ?>
    <h1>Se connecter</h1>
    <hr class="my-4">
<form class="form" method="post" action="">
    <div class="form-group">
      <label for="email">Adresse email</label>
      <input required type="email" class="form-control <?= isset($error["email"]) ? 'is-invalid' : '' ?>" name="email" id="email" value="<?= isset($error) ? $_POST["email"] : '' ?>">
      <?php if (isset($error["email"])): ?>
      <div class="invalid-feedback">
          <?= $error["email"]; ?>
      </div>
<?php endif; ?>
    </div>
    <div class="form-group">
      <label for="mdp">Mot de passe</label>
      <input required type="password" class="form-control " name="mdp" id="mdp">
      <small id="helpId" class="form-text text-muted">Entrez votre mot de passe</small>
    </div>
    <button type="submit" class="btn btn-primary">Se connecter</button>
</form>
</div>
<?php
include("elements/footer.php");