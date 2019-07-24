<?php

function est_connecte() {
    if (isset($_SESSION["prenom"]) && isset($_SESSION["statut"]) and isset($_SESSION["id"])) {
        return true;
    }
    else {
        return false;
    }
}