<?php
// فایل: garin/inventory/transactions.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$success = $error = '';

// پردازش فرم ثبت تراکنش (رسید/حواله)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? 0;
    $type = $_POST['transaction_type'] ?? '';
    $quantity = $_POST['quantity'] ?? 0;
    $note = $_POST['reference_note'] ?? '';

    if ($product_id && $type && $quantity > 0) {
        try {
            // شروع تراکنش دیتابیس برای اطمینان از انجام همزمان هر دو کار
            $pdo->beginTransaction();

            // ۱. ثبت در جدول تاریخچه انبار
            $stmt = $pdo->prepare("INSERT INTO inventory_transactions (product_id, transaction_type, quantity, reference_note) VALUES (?, ?, ?, ?)");
            $stmt->execute([$product_id, $type, $quantity, $note]);

            // ۲. آپدیت هوشمند موجودی در جدول محصولات
            if ($type === 'in') {
                $updateStmt = $pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
            } else {
                $updateStmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            }
            $updateStmt->execute([$quantity, $product_id]);

            // تایید نهایی عملیات
            $pdo->commit();
            $success = "تراکنش با موفقیت ثبت شد و موجودی کالا به‌روزرسانی گردید.";
        } catch (PDOException $e) {
            $pdo->rollBack(); // در صورت خطا، هیچ تغییری اعمال نمی‌شود
            $error = "خطا در ثبت اطلاعات: " . $e->getMessage();
        }
    } else {
        $error = "لطفاً کالا، نوع تراکنش و مقدار معتبر را وارد کنید.";
    }
}

// واکشی لیست کالاها برای فرم انتخاب
$products = $pdo->query("SELECT id, title, stock FROM products ORDER BY title ASC")->fetchAll(PDO::FETCH_ASSOC);

// واکشی ۵۰ تراکنش آخر برای نمایش در جدول گزارش
$transactions = $pdo->query("
    SELECT t.*, p.title as product_title 
    FROM inventory_transactions t 
    JOIN products p ON t.product_id = p.id 
    ORDER BY t.created_at DESC 
    LIMIT 50
")->fetchAll(PDO::FETCH_ASSOC);

// اتصال به فایل گرافیکی و قالب اصلی
$contentFile = __DIR__ . '/views/transactions_content.php';
require_once __DIR__ . '/../includes/layout.php';