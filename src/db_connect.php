<?php
session_start();

// --- إعدادات قاعدة البيانات ---
$host = 'host.docker.internal';
$db_name = 'news_system_db';
$username = 'root';
$password = '';

$dsn = "mysql:host=$host;dbname=$db_name;charset=utf8";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // ✅ لا تستخدم die() أو echo — استخدم إعادة توجيه نظيفة
    error_log("Database connection failed: " . $e->getMessage());
    header("Location: error.php");
    exit;
}

// --- دالة حماية الصفحات ---
function check_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}


