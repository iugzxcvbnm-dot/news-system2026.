<?php
// منع الوصول المباشر
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    die('Direct access not allowed.');
}

// بدء الجلسة إذا لم تكن مفعلة
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $host = 'db'; // يجب أن يطابق اسم الخدمة في docker-compose
    $dbname = 'newsdb';
    $user = 'user';
    $pass = 'password123';

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
    
} catch (PDOException $e) {
    // تسجيل الخطأ داخلياً وعرض رسالة بسيطة للمستخدم
    error_log("Connection Error: " . $e->getMessage());
    die("عذرًا، لا يمكن الاتصال بقاعدة البيانات.");
}

// دالة التحقق من تسجيل الدخول (مطلوبة في dashboard.php وغيرها)
function check_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

