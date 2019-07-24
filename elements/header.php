<?php
session_start();
require "functions.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>QCM</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
    
<nav class="navbar navbar-expand-md mb-4 justify-content-between">
    <div class="d-flex col-6">
        <a class="navbar-brand" href="index.php"><h2>Super QCM</h2></a>
        <?php if (est_connecte() && $_SESSION["statut"] == "admin"): ?>
        <div class="navbar-nav my-2">
            <a class="nav-item nav-link" href="#"><strong>Administration</strong></a>
        </div>
        <?php endif; ?>
    </div>
    <?php if (est_connecte()): ?>
    <a class="btn btn-danger" href="deconnexion.php">Déconnexion</a> 
    <?php endif ?>
</nav>
