<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle . ' - ' . $slip['first_name'] . ' ' . $slip['last_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* استایل‌های اختصاصی برای نسخه چاپی */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: #fff !important;
                padding: 0 !important;
            }
            .card {
                border: 1px solid #000 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }
            .table-light {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        .slip-header {
            border-bottom: 2px solid #333;
        }
        .table-bordered th, .table-bordered td {
            border-color: #dee2e6 !important;
        }
    </style>
</head>
<body class="bg-light">
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        
        <div class="col-md-9 no-print mb-3 d-flex justify-content-between">
            <a href="index.php" class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-arrow-right me-1"></i> بازگشت به لیست فیش‌ها
            </a>
            <button onclick="window.print();" class="btn btn-sm btn-primary px-3">
                <i class="fa fa-print me-1"></i> چاپ فیش حقوقی / خروجی PDF
            </button>
        </div>

        <div class="col-md-9">
            <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                
                <div class="row align-items-center pb-3 mb-4 slip-header">
                    <div class="col-6">
                        <h4 class="fw-bold text-dark mb-1">فیش رسمی حقوق و دستمزد</h4>
                        <div class="text-muted small">پروژه مدیریت مالی شرکت (گروه گارین)</div>
                    </div>
                    <div class="col-6 text-end">
                        <div class="fw-bold text-primary">دوره کارکرد: <?= getMonthName($slip['salary_month']) ?> ماه <?= (int)$slip['salary_year'] ?></div>
                        <div class="small text-muted mt-1">تاریخ صدور: <?= date("Y-m-d", strtotime($slip['created_at'])) ?></div>
                        <div class="small text-muted">شماره فیش: #<?= (int)$slip['id'] ?></div>
                    </div>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle small">
                        <thead class="table-light">
                            <tr>
                                <th>نام و نام خانوادگی</th>
                                <th>کد ملی</th>
                                <th>شماره همراه</th>
                                <th>شماره حساب / شبا</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($slip['first_name'] . ' ' . $slip['last_name']) ?></td>
                                <td><code><?= htmlspecialchars($slip['national_code']) ?></code></td>
                                <td><?= htmlspecialchars($slip['phone'] ?: '-') ?></td>
                                <td class="text-secondary"><?= htmlspecialchars($slip['bank_account'] ?: '-') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row g-3 mb-4">
                    
                    <div class="col-md-6">
                        <table class="table table-bordered align-middle small h-100">
                            <thead class="table-light text-success">
                                <tr>
                                    <th>شرح درآمدها و مزایا</th>
                                    <th class="text-end">مبلغ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>حقوق پایه مصوب ماهانه</td>
                                    <td class="text-end"><?= money($slip['base_salary']) ?></td>
                                </tr>
                                <tr>
                                    <td>پاداش، اضافه کار و مزایا</td>
                                    <td class="text-end text-success">+ <?= money($slip['bonuses']) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">-</td>
                                    <td class="text-end">-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <table class="table table-bordered align-middle small h-100">
                            <thead class="table-light text-danger">
                                <tr>
                                    <th>شرح کسورات قانونی</th>
                                    <th class="text-end">مبلغ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>حق بیمه سهم کارمند</td>
                                    <td class="text-end text-danger">- <?= money($slip['insurance_amount']) ?></td>
                                </tr>
                                <tr>
                                    <td>مالیات بر درآمد ماهانه</td>
                                    <td class="text-end text-danger">- <?= money($slip['tax_amount']) ?></td>
                                </tr>
                                <tr>
                                    <td>مساعده و سایر کسورات</td>
                                    <td class="text-end text-danger">- <?= money($slip['deductions']) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>

                <div class="row justify-content-end mb-5">
                    <div class="col-md-6">
                        <table class="table table-bordered small">
                            <tr>
                                <td class="bg-light fw-semibold">جمع ناخالص مزایا:</td>
                                <td class="text-end text-success fw-bold"><?= money($slip['base_salary'] + $slip['bonuses']) ?></td>
                            </tr>
                            <tr>
                                <td class="bg-light fw-semibold">جمع کل کسورات:</td>
                                <td class="text-end text-danger fw-bold"><?= money($slip['insurance_amount'] + $slip['tax_amount'] + $slip['deductions']) ?></td>
                            </tr>
                            <tr class="table-dark text-white align-middle">
                                <td class="fw-bold fs-6 text-warning">خالص دریافتی کارمند:</td>
                                <td class="text-end fw-bold fs-5 text-warning"><?= money($slip['net_salary']) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row text-center mt-4 small pt-4 border-top border-light">
                    <div class="col-4">
                        <p class="fw-semibold text-muted mb-5">امضا و تایید کارمند</p>
                        <br>
                        <span class="text-muted">...........................</span>
                    </div>
                    <div class="col-4">
                        <p class="fw-semibold text-muted mb-5">مهر و تایید امور مالی</p>
                        <br>
                        <span class="text-muted">...........................</span>
                    </div>
                    <div class="col-4">
                        <p class="fw-semibold text-muted mb-5">وضعیت تسویه</p>
                        <br>
                        <?php if($slip['status'] === 'paid'): ?>
                            <span class="badge bg-success-subtle text-success border border-success p-2">تسویه شده و پرداخت گردید</span>
                        <?php else: ?>
                            <span class="badge bg-danger-subtle text-danger border border-danger p-2">معوقه / در انتظار پرداخت</span>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
</body>
</html>