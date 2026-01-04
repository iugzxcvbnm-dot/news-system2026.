<?php
require_once 'db_connect.php';
check_login();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = trim($_POST['category_name']);
    if (empty($category_name)) {
        $error = 'اسم الفئة مطلوب.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
            $stmt->execute([$category_name]);
            if ($stmt->fetch()) {
                $error = 'هذه الفئة موجودة بالفعل.';
            } else {
                $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
                if ($stmt->execute([$category_name])) {
                    $success = 'تمت إضافة الفئة بنجاح!';
                }
            }
        } catch (PDOException $e) { $error = 'خطأ في قاعدة البيانات.'; }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة فئة جديدة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><h4 class="mb-0">إضافة فئة جديدة</h4></div>
                    <div class="card-body">
                        <?php if ($success ): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
                        <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
                        <form action="category_create.php" method="POST">
                            <div class="mb-3">
                                <label for="category_name" class="form-label">اسم الفئة</label>
                                <input type="text" class="form-control" name="category_name" placeholder="مثال: أخبار سياسية" required>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> إضافة</button>
                            <a href="categories_view.php" class="btn btn-secondary">عرض كل الفئات</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

