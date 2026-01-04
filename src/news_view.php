<?php
// 1. تضمين ملف الاتصال وحماية الصفحة
require_once 'db_connect.php';
check_login();

$message = ''; // لعرض رسائل النجاح أو الخطأ

// --- معالجة طلب الحذف (Soft Delete) ---
// 2. التحقق إذا كان هناك طلب حذف قادم عبر الرابط (GET)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $news_id_to_delete = $_GET['id'];

    try {
        // تحديث حالة الخبر إلى 'deleted' بدلاً من حذفه فعليًا
        $stmt = $pdo->prepare("UPDATE news SET status = 'deleted' WHERE id = ?");
        if ($stmt->execute([$news_id_to_delete])) {
            $message = '<div class="alert alert-success">تم نقل الخبر إلى سلة المحذوفات بنجاح.</div>';
        } else {
            $message = '<div class="alert alert-danger">فشل حذف الخبر.</div>';
        }
    } catch (PDOException $e) {
        $message = '<div class="alert alert-danger">خطأ في قاعدة البيانات: ' . $e->getMessage() . '</div>';
    }
}

// 3. جلب جميع الأخبار المنشورة لعرضها في الجدول
try {
    $stmt = $pdo->query("
        SELECT 
            news.id, 
            news.title, 
            news.image,
            categories.name AS category_name,
            users.name AS user_name,
            news.created_at
        FROM news
        JOIN categories ON news.category_id = categories.id
        JOIN users ON news.user_id = users.id
        WHERE news.status = 'published'
        ORDER BY news.created_at DESC
    ");
    $news_list = $stmt->fetchAll();
} catch (PDOException $e) {
    die("خطأ في جلب الأخبار: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض جميع الأخبار</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

    <?php include 'navbar.php'; // تضمين القائمة العلوية ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">إدارة الأخبار</h2>
            <a href="news_create.php" class="btn btn-primary"><i class="fa fa-plus"></i> إضافة خبر جديد</a>
        </div>

        <?php echo $message; // عرض رسالة الحذف هنا ?>

        <div class="card">
            <div class="card-header">
                <h5>الأخبار المنشورة</h5>
            </div>
            <div class="card-body">
                <?php if (empty($news_list )): ?>
                    <div class="alert alert-info">لا توجد أخبار منشورة حاليًا.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>صورة مصغرة</th>
                                    <th>العنوان</th>
                                    <th>الفئة</th>
                                    <th>الكاتب</th>
                                    <th>تاريخ النشر</th>
                                    <th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($news_list as $index => $news): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td>
                                            <img src="uploads/<?php echo htmlspecialchars($news['image']); ?>" alt="صورة الخبر" width="80" class="img-thumbnail">
                                        </td>
                                        <td class="text-end"><?php echo htmlspecialchars($news['title']); ?></td>
                                        <td><span class="badge bg-info"><?php echo htmlspecialchars($news['category_name']); ?></span></td>
                                        <td><?php echo htmlspecialchars($news['user_name']); ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($news['created_at'])); ?></td>
                                        <td>
                                            <!-- 4. أيقونة التعديل -->
                                            <a href="news_edit.php?id=<?php echo $news['id']; ?>" class="btn btn-sm btn-success" title="تعديل">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <!-- 5. أيقونة الحذف -->
                                            <a href="news_view.php?action=delete&id=<?php echo $news['id']; ?>" class="btn btn-sm btn-danger" title="حذف" onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذا الخبر؟');">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
