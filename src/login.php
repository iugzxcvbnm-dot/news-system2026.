<?php
require_once 'db_connect.php';
if (isset($_SESSION['user_id'])) { header("Location: dashboard.php"); exit; }
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    if (empty($email) || empty($password)) {
        $errors[] = "البريد الإلكتروني وكلمة المرور مطلوبان.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                header("Location: dashboard.php");
                exit;
            } else {
                $errors[] = "البريد الإلكتروني أو كلمة المرور غير صحيحة.";
            }
        } catch (PDOException $e) { $errors[] = "خطأ في قاعدة البيانات."; }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style> body { background-color: #f8f9fa; } .login-container { max-width: 400px; margin-top: 100px; } </style>
</head>
<body>
    <div class="container">
        <div class="card shadow-sm border-0 mx-auto login-container">
            <div class="card-body p-4">
                <h3 class="card-title text-center mb-4">تسجيل الدخول</h3>
                <?php if (!empty($errors )): ?>
                    <div class="alert alert-danger"><?php foreach ($errors as $error): ?><p class="mb-0"><?php echo $error; ?></p><?php endforeach; ?></div>
                <?php endif; ?>
                <form action="login.php" method="POST">
                    <div class="mb-3"><label for="email" class="form-label">البريد الإلكتروني</label><input type="email" class="form-control" name="email" required></div>
                    <div class="mb-3"><label for="password" class="form-label">كلمة المرور</label><input type="password" class="form-control" name="password" required></div>
                    <div class="d-grid"><button type="submit" class="btn btn-primary">دخول</button></div>
                </form>
                <div class="text-center mt-3"><p>ليس لديك حساب؟ <a href="register.php">أنشئ حسابًا جديدًا</a></p></div>
            </div>
        </div>
    </div>
</body>
</html>

