<?php
function isLoggedIn()
{
    return isset($_SESSION['admin_id']);
}

function redirectIfNotLoggedIn()
{
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}
