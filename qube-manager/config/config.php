<?php
session_start();

define('BASE_URL', '/novosite/qube-manager/');
define('SITE_URL', '/novosite/');
define('UPLOAD_PATH', '../uploads/');
define('UPLOAD_URL', '/novosite/uploads/');

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

function generateUUID() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}
