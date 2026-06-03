<?php

function validarIdGet($nome = 'id') {
    if (!isset($_GET[$nome]) || !ctype_digit($_GET[$nome])) {
        header("Location: index.php");
        exit;
    }

    return (int) $_GET[$nome];
}