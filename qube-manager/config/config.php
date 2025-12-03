<?php
session_start();

define('BASE_URL', '/qube-manager/');
define('SITE_URL', '/');
define('UPLOAD_PATH', '../uploads/');
define('UPLOAD_URL', '/uploads/');

date_default_timezone_set('America/Sao_Paulo');

require_once 'database.php';

function checkLogin() {
    if (!isset($_SESSION['qube_user_id'])) {
        header('Location: ' . BASE_URL . 'login.php');
        exit();
    }
}

function redirect($url) {
    header('Location: ' . $url);
    exit();
}

function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}
