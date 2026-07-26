<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'افزودن مشتری بالقوه';

$errorMessage = '';
$successMessage = '';

try {
    $salesAgents = $pdo->query("
        SELECT sa.id, sa.position, u.fullname, u.role
        FROM sales_agents sa
        LEFT JOIN users u ON u.id = sa.user_id
        WHERE u.role = 'sales' OR u.role IS NULL
        ORDER BY sa.id DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $products = $pdo->query("
        SELECT id, title, sku, price
        FROM products
        WHERE status = 'active'
        ORDER BY id DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("خطا در دریافت داده‌ها: " . $e->getMessage());
}

$old = [
    'sales_agent_id' => '',
    'customer_name' => '',
    'company_name' => '',
    'phone' => '',
    'email' => '',
    'source' => 'new',
    'status' => 'new',
    'interest_product_id' => '',
    'next_followup' => '',
    'notes' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $key => $value) {
        $old[$key] = trim($_POST[$key] ?? $value);
    }

    if ($old['customer_name'] === '') {
        $errorMessage = 'نام مشتری را وارد کنید.';
    } else {
        try {
            $salesAgentId = $old['sales_agent_id'] !== '' ? (int)$old['sales_agent_id'] : null;
            $interestProductId = $old['interest_product_id'] !== '' ? (int)$old['interest_product_id'] : null;

            $stmt = $pdo->prepare("
                INSERT INTO sales_leads
                (sales_agent_id, customer_name, company_name, phone, email, source, status, interest_product_id, next_followup, notes)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $salesAgentId,
                $old['customer_name'],
                $old['company_name'] ?: null,
                $old['phone'] ?: null,
                $old['email'] ?: null,
                $old['source'],
                $old['status'],
                $interestProductId,
                $old['next_followup'] ?: null,
                $old['notes'] ?: null
            ]);

            header("Location: index.php?added=1");
            exit;
        } catch (PDOException $e) {
            $errorMessage = 'خطا در ذخیره مشتری بالقوه: ' . $e->getMessage();
        }
    }
}

function old($key, $default = '') {
    global $old;
    return htmlspecialchars($old[$key] ?? $default);
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-body-secondary text-dark">

<div class="py-3 mb-4 bg-light border-bottom shadow-sm">
    <div class="container-fluid px-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-0 text-dark"><i class="fas fa-user-plus me-2"></i>افزودن مشتری بالقوه</h4>
            </div>
            <div class="col-md-6 text-end">
                <a href="index.php" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-right me-1"></i>بازگشت
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 py-2">
    <?php if ($errorMessage): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <?= htmlspecialchars($errorMessage) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-4 shadow-sm p-4">
        <div class="row mb-4">
            <div class="col-12">
                <h5 class="fw-bold mb-1">فرم ثبت مشتری بالقوه</h5>
                <p class="text-muted small mb-0">اطلاعات را با دقت وارد کنید تا بتوانید پیگیری فروش را بهتر انجام دهید.</p>
            </div>
        </div>

        <form method="post" autocomplete="off">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">نام مشتری <span class="text-danger">*</span></label>
                    <input type="text" name="customer_name" class="form-control" value="<?= old('customer_name') ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">نام شرکت</label>
                    <input type="text" name="company_name" class="form-control" value="<?= old('company_name') ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">تلفن</label>
                    <input type="text" name="phone" class="form-control" value="<?= old('phone') ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">ایمیل</label>
                    <input type="email" name="email" class="form-control" value="<?= old('email') ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">منبع</label>
                    <select name="source" class="form-select">
                        <option value="new" <?= $old['source'] === 'new' ? 'selected' : '' ?>>new</option>
                        <option value="website" <?= $old['source'] === 'website' ? 'selected' : '' ?>>website</option>
                        <option value="instagram" <?= $old['source'] === 'instagram' ? 'selected' : '' ?>>instagram</option>
                        <option value="referral" <?= $old['source'] === 'referral' ? 'selected' : '' ?>>referral</option>
                        <option value="phone" <?= $old['source'] === 'phone' ? 'selected' : '' ?>>phone</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">وضعیت</label>
                    <select name="status" class="form-select">
                        <option value="new" <?= $old['status'] === 'new' ? 'selected' : '' ?>>new</option>
                        <option value="contacted" <?= $old['status'] === 'contacted' ? 'selected' : '' ?>>contacted</option>
                        <option value="quotation_sent" <?= $old['status'] === 'quotation_sent' ? 'selected' : '' ?>>quotation_sent</option>
                        <option value="negotiation" <?= $old['status'] === 'negotiation' ? 'selected' : '' ?>>negotiation</option>
                        <option value="won" <?= $old['status'] === 'won' ? 'selected' : '' ?>>won</option>
                        <option value="lost" <?= $old['status'] === 'lost' ? 'selected' : '' ?>>lost</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">کارشناس فروش</label>
                    <select name="sales_agent_id" class="form-select">
                        <option value="">انتخاب کنید</option>
                        <?php foreach ($salesAgents as $agent): ?>
                            <?php
                                $label = trim(($agent['fullname'] ?? 'کارشناس فروش') . ' - ' . ($agent['position'] ?? ''));
                                if ($label === '-') $label = 'کارشناس فروش #' . $agent['id'];
                            ?>
                            <option value="<?= (int)$agent['id'] ?>" <?= (string)$old['sales_agent_id'] === (string)$agent['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">محصول مورد علاقه</label>
                    <select name="interest_product_id" class="form-select">
                        <option value="">انتخاب کنید</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?= (int)$product['id'] ?>" <?= (string)$old['interest_product_id'] === (string)$product['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($product['title'] . (!empty($product['sku']) ? ' | SKU: ' . $product['sku'] : '')) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">پیگیری بعدی</label>
                    <input type="datetime-local" name="next_followup" class="form-control" value="<?= old('next_followup') ?>">
                </div>

                <div class="col-12">
                    <label class="form-label">یادداشت‌ها</label>
                    <textarea name="notes" class="form-control" rows="5"><?= old('notes') ?></textarea>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i>ثبت مشتری بالقوه
                    </button>
                    <a href="index.php" class="btn btn-outline-secondary px-4">انصراف</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>