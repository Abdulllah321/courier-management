<?php
session_start();
include "../config/database.php";
include "../config/funciones.php";
redirectIfNotLoggedIn();

function fetchDailyItems($db)
{
    $query = "SELECT * FROM parcels WHERE DATE(created_at) = CURDATE()";
    return $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
}

function fetchMonthlyItems($db)
{
    $query = "SELECT * FROM parcels WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
    return $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
}

function fetchYearlyItems($db)
{
    $query = "SELECT * FROM parcels WHERE YEAR(created_at) = YEAR(CURDATE())";
    return $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
}
?>