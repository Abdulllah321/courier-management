<?php
function isLoggedIn()
{
    return isset($_SESSION['admin_id']);
}
function isLoggedInAsAgent()
{
    return isset($_SESSION['agent_id']);
}

function redirectIfNotLoggedIn()
{
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

function redirectIfNotLoggedInAsAgent()
{
    if (!isLoggedInAsAgent()) {
        header("Location: login.php");
        exit;
    }
}
