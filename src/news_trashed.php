<?php
require_once 'db_connect.php';
check_login();
$message = '';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $news_id = $_GET['id'];
    try {
        if ($action === 'restore') {
            $stmt = $pdo->prepare("UPDATE news SET status = 'published' WHERE id = ?");
            if ($stmt->execute([$news_id])) { $message = '<div class="alert alert-success">تم استعادة الخبر.</div>'; }
        } elseif ($action === 'force_delete') {
            $stmt_img = $pdo->prepare("SELECT image FROM news WHERE id = ?");
            $stmt_img->execute([$news_id]);
            $news_item = $stmt_img->fetch();
            $stmt_del = $pdo->prepare("DELETE FROM news WHERE id = ?");
            if ($stmt_del->execute([$news_id])) {
                if ($news_item && !empty($news_item['image'])) {
                    $image_path = 'uploads/' . $news_item['image'];
                    if (file_exists($image_path)) { unlink($image_path); }
                }
                $message = '<div class="alert alert-success">تم حذف الخبر نهائيًا.</div>';
            }
        }
    } catch (PDOException $e) { $message = '<div class="alert alert-danger">خطأ في قاعدة البيانات.</div>'; }
}

try {
    $stmt = $pdo->query("SELECT news.id, news.title, categories.name AS category_name, users.name AS user_name FROM news JOIN categories ON news.category_id = categories.id JOIN users ON news.user_id = users.id WHERE news.status = 'deleted' ORDER BY news.updated_at DESC");
    $trashed_news = $stmt->fetchAll();
} catch (PDOException $e) { die("خطأ في جلب الأخبار المحذوفة: " . $e->getMessage()); }
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الأخبار المحذوفة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3"><h2 class="mb-0">سلة المحذوفات</h2><a href="news_view.php" class="btn btn-outline-secondary"><i class="fa fa-arrow-left"></i> العودة</a></div>
        <?php echo $message; ?>
        <div class="card">
            <div class="card-header"><h5>الأخبار التي تم حذفها</h5></div>
            <div class="card-body">
                <?php if (empty($trashed_news )): ?>
                    <div class="alert alert-info">سلة المحذوفات فارغة.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle text-center">
                            <thead class="table-dark"><tr><th>#</th><th>العنوان</th><th>الفئة</th><th>إجراءات</th></tr></thead>
                            <tbody>
                                <?php foreach ($trashed_news as $index => $news): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td class="text-end"><?php echo htmlspecialchars($news['title']); ?></td>
                                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($news['category_name']); ?></span></td>
                                        <td>
                                            <a href="news_trashed.php?action=restore&id=<?php echo $news['id']; ?>" class="btn btn-sm btn-info" title="استعادة"><i class="fa fa-undo"></i> استعادة</a>
                                            <a href="news_trashed.php?action=force_delete&id=<?php echo $news['id']; ?>" class="btn btn-sm btn-danger" title="حذف نهائي" onclick="return confirm('تحذير: سيتم حذف الخبر نهائيًا. هل أنت متأكد؟');"><i class="fa fa-times-circle"></i> حذف نهائي</a>
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
