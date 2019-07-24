<?php
session_start();
unset($_SESSION["id"]);
unset($_SESSION["statut"]);
unset($_SESSION["prenom"]);
header("Location:index.php");