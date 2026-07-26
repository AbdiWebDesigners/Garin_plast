<?php
declare(strict_types=1);

/**
 * تبدیل متن برای نمایش امن
 */
function issueEscape(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * ایجاد CSRF Token
 */
function issueCsrfToken(): string
{
    if (empty($_SESSION['inventory_issue_csrf'])) {
        $_SESSION['inventory_issue_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['inventory_issue_csrf'];
}

/**
 * بررسی CSRF
 */
function issueVerifyCsrf(): void
{
    $token = $_POST['csrf_token'] ?? '';

    if (
        empty($_SESSION['inventory_issue_csrf']) ||
        !hash_equals($_SESSION['inventory_issue_csrf'], $token)
    ) {
        throw new RuntimeException('درخواست نامعتبر است.');
    }
}

/**
 * عنوان وضعیت
 */
function issueStatusLabel(string $status): string
{
    return match ($status) {
        'draft'     => 'پیش‌نویس',
        'approved'  => 'تأیید شده',
        'issued'    => 'خروج شده',
        'cancelled' => 'لغو شده',
        default     => $status,
    };
}

/**
 * تبدیل تاریخ فرم
 */
function issueParseDate(string $date): string
{
    if ($date === '') {
        return date('Y-m-d H:i:s');
    }

    return date('Y-m-d H:i:s', strtotime($date));
}

/**
 * حذف تراکنش‌های حواله
 */
function issueDeleteTransactions(PDO $pdo, int $voucherId): void
{
    $stmt = $pdo->prepare("
        DELETE FROM inventory_transactions
        WHERE reference_type='inventory_issue'
        AND reference_id=?
    ");

    $stmt->execute([$voucherId]);
}

/**
 * بارگذاری حواله
 */
function issueLoadVoucher(PDO $pdo, int $id): array
{
    $stmt = $pdo->prepare("
        SELECT

            v.*,

            w.warehouse_name,

            req.fullname AS requested_by_name,

            app.fullname AS approved_by_name

        FROM inventory_issue_vouchers v

        LEFT JOIN warehouses w
            ON w.id = v.warehouse_id

        LEFT JOIN users req
            ON req.id = v.requested_by

        LEFT JOIN users app
            ON app.id = v.approved_by

        WHERE v.id = ?

        LIMIT 1
    ");

    $stmt->execute([$id]);

    $voucher = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$voucher) {
        throw new RuntimeException('حواله موردنظر پیدا نشد.');
    }

    $itemsStmt = $pdo->prepare("
        SELECT

            i.*,

            COALESCE(
                NULLIF(p.title,''),
                NULLIF(p.sku,''),
                CONCAT('کالا شماره ',i.item_id)
            ) AS product_title,

            p.sku

        FROM inventory_issue_voucher_items i

        LEFT JOIN products p
            ON p.id=i.item_id

        WHERE i.voucher_id=?

        ORDER BY i.line_no ASC,i.id ASC
    ");

    $itemsStmt->execute([$id]);

    $voucher['items'] = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

    return $voucher;
}