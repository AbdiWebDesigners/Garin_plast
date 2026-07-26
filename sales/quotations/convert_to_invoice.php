<?php
// sales/quotations/convert_to_invoice.php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

if (empty($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

$quotationId = (int)($_GET['id'] ?? 0);

if ($quotationId <= 0) {
    die('شناسه نامعتبر است.');
}

try {
    $stmt = $pdo->prepare("SELECT * FROM quotations WHERE id = ? AND status = 'accepted' LIMIT 1");
    $stmt->execute([$quotationId]);
    $quotation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$quotation) {
        die('پیش‌فاکتور یافت نشد یا هنوز تأیید نشده است.');
    }

    // تولید شماره فاکتور
    $invoice_number = 'INV-' . date('Ymd') . '-' . rand(100, 999);

    // ایجاد فاکتور
    $stmt = $pdo->prepare("
        INSERT INTO invoices 
        (quotation_id, customer_id, sales_agent_id, invoice_number, subtotal, tax_amount, 
         discount_amount, total_amount, status, due_date, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'unpaid', DATE_ADD(NOW(), INTERVAL 30 DAY), ?)
    ");
    $stmt->execute([
        $quotation['id'],
        $quotation['customer_id'],
        $quotation['sales_agent_id'] ?? null,
        $invoice_number,
        $quotation['total_price'] ?? 0,
        $quotation['tax'] ?? 0,
        $quotation['discount'] ?? 0,
        $quotation['final_price'] ?? $quotation['total_price'],
        'تبدیل شده از پیش‌فاکتور #' . $quotation['id']
    ]);

    $newInvoiceId = $pdo->lastInsertId();

    // به‌روزرسانی وضعیت پیش‌فاکتور
    $pdo->prepare("UPDATE quotations SET status = 'converted' WHERE id = ?")
         ->execute([$quotationId]);

    // هدایت به صفحه فاکتور جدید
    header("Location: ../../invoices/view.php?id=" . $newInvoiceId . "&converted=1");
    exit;

} catch (PDOException $e) {
    die('خطا در تبدیل به فاکتور: ' . $e->getMessage());
}