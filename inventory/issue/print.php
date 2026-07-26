<?php
declare(strict_types=1);
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/functions.php';
requireLogin();
requirePermission('viewinventory');
global $pdo;
$id = (int)($_GET['id'] ?? 0);
try { $voucher = issueLoadVoucher($pdo, $id); } catch (Throwable $e) { http_response_code(404); exit(issueEscape($e->getMessage())); }
$grand = array_sum(array_map(static fn(array $i): float => (float)$i['total_cost'], $voucher['items']));
?><!doctype html><html lang="fa" dir="rtl"><head><meta charset="utf-8"><title>چاپ حواله <?= issueEscape($voucher['voucher_number']) ?></title>
<style>body{font-family:Tahoma,Arial,sans-serif;margin:24px;color:#111;font-size:13px}h2{text-align:center}.meta{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin:20px 0}.box{border:1px solid #aaa;padding:8px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #777;padding:7px;text-align:center}th{background:#eee}.actions{text-align:left;margin-bottom:15px}@media print{.actions{display:none}body{margin:0}}</style></head><body>
<div class="actions"><button onclick="window.print()">چاپ</button> <button onclick="window.close()">بستن</button></div>
<h2>حواله خروج انبار</h2><div class="meta"><div class="box"><b>شماره:</b> <?= issueEscape($voucher['voucher_number']) ?></div><div class="box"><b>تاریخ:</b> <?= issueEscape($voucher['voucher_date']) ?></div><div class="box"><b>انبار:</b> <?= issueEscape($voucher['warehouse_name'] ?? '-') ?></div><div class="box"><b>وضعیت:</b> <?= issueEscape(issueStatusLabel((string)$voucher['status'])) ?></div></div>
<table><thead><tr><th>ردیف</th><th>کالا</th><th>تعداد</th><th>واحد</th><th>قیمت واحد</th><th>مبلغ</th><th>سریال/Batch</th><th>لوکیشن</th></tr></thead><tbody>
<?php foreach ($voucher['items'] as $item): ?><tr><td><?= (int)$item['line_no'] ?></td><td><?= issueEscape($item['product_title']) ?></td><td><?= issueEscape($item['quantity']) ?></td><td><?= issueEscape($item['unit']) ?></td><td><?= number_format((float)$item['unit_cost']) ?></td><td><?= number_format((float)$item['total_cost']) ?></td><td><?= issueEscape(trim(($item['serial_number'] ?? '') . ' ' . ($item['batch_number'] ?? '')) ?: '-') ?></td><td><?= issueEscape($item['warehouse_location'] ?? '-') ?></td></tr><?php endforeach; ?>
</tbody><tfoot><tr><th colspan="5">جمع کل</th><th><?= number_format($grand) ?></th><th colspan="2"></th></tr></tfoot></table>
<p><b>توضیحات:</b> <?= nl2br(issueEscape($voucher['description'] ?? '-')) ?></p><br><br><div style="display:flex;justify-content:space-between"><span>امضای تحویل‌دهنده: ....................</span><span>امضای تحویل‌گیرنده: ....................</span><span>تأیید: ....................</span></div>
<script>window.addEventListener('load',()=>{if(new URLSearchParams(location.search).get('auto')==='1')window.print();});</script></body></html>
