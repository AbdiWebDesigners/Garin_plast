<?php

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireLogin();
requirePermission('view_inventory');
global $pdo;

$pageTitle = "کارتکس انبار";

$error = '';
$success = '';

$item_id  = isset($_GET['item_id']) ? (int)$_GET['item_id'] : 0;
$warehouse_id = isset($_GET['warehouse_id']) ? (int)$_GET['warehouse_id'] : 0;
$from_date    = $_GET['from_date'] ?? '';
$to_date      = $_GET['to_date'] ?? '';

/*
|--------------------------------------------------------------------------
| دریافت لیست کالاها
|--------------------------------------------------------------------------
*/

try {

    $products = $pdo->query("
        SELECT
           id,
        title,
        sku
        FROM products
        ORDER BY title
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch(Exception $e){

    $products = [];

}

/*
|--------------------------------------------------------------------------
| دریافت لیست انبارها
|--------------------------------------------------------------------------
*/

try {

    $warehouses = $pdo->query("
        SELECT
            id,
            warehouse_name
        FROM warehouses
        WHERE status='active'
        ORDER BY warehouse_name
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch(Exception $e){

    $warehouses=[];

}

/*
|--------------------------------------------------------------------------
| ساخت شرط جستجو
|--------------------------------------------------------------------------
*/

$where=[];

$params=[];

if($item_id){

    $where[]="it.item_id=?";

    $params[]=$item_id;

}

if($warehouse_id){

    $where[]="it.warehouse_id=?";

    $params[]=$warehouse_id;

}

if($from_date!=''){

    $where[]="DATE(it.transaction_date)>=?";

    $params[]=$from_date;

}

if($to_date!=''){

    $where[]="DATE(it.transaction_date)<=?";

    $params[]=$to_date;

}

$sqlWhere='';

if(count($where)>0){

    $sqlWhere='WHERE '.implode(' AND ',$where);

}
/*
|--------------------------------------------------------------------------
| دریافت اطلاعات کارتکس
|--------------------------------------------------------------------------
*/

try {

    $sql = "
        SELECT

            it.*,

           p.title,

               p.sku,

            w.warehouse_name,

            u.fullname

        FROM inventory_transactions it

        LEFT JOIN products p
            ON p.id = it.item_id

        LEFT JOIN warehouses w
            ON w.id = it.warehouse_id

        LEFT JOIN users u
ON it.created_by = u.id

        $sqlWhere

        ORDER BY
            it.transaction_date ASC,
            it.id ASC
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute($params);

    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(Exception $e){

    $transactions = [];

    $error = $e->getMessage();

}

/*
|--------------------------------------------------------------------------
| محاسبه موجودی لحظه‌ای
|--------------------------------------------------------------------------
*/

$runningBalance = 0;

foreach($transactions as $key => $row){

    $qty = (float)$row['quantity'];

    $type = strtolower($row['transaction_type']);

    $inQty = 0;

    $outQty = 0;

    switch($type){

        case 'receipt':

        case 'purchase':

        case 'production':

        case 'return_in':

        case 'opening':

            $inQty = $qty;
            $runningBalance += $qty;

        break;

        case 'issue':

        case 'sale':

        case 'consume':

        case 'return_out':

        case 'adjustment_minus':

            $outQty = $qty;
            $runningBalance -= $qty;

        break;

        case 'adjustment_plus':

            $inQty = $qty;
            $runningBalance += $qty;

        break;

        default:

            if($qty >= 0){

                $inQty = $qty;
                $runningBalance += $qty;

            }else{

                $outQty = abs($qty);
                $runningBalance -= abs($qty);

            }

    }

    $transactions[$key]['qty_in'] = $inQty;

    $transactions[$key]['qty_out'] = $outQty;

    $transactions[$key]['balance'] = $runningBalance;

}
/*
|--------------------------------------------------------------------------
| آمار کارتکس
|--------------------------------------------------------------------------
*/

$totalTransactions = count($transactions);

$totalIn = 0;
$totalOut = 0;

foreach ($transactions as $row) {

    $totalIn += (float)$row['qty_in'];
    $totalOut += (float)$row['qty_out'];

}

$currentBalance = $runningBalance;

/*
|--------------------------------------------------------------------------
| اطلاعات صفحه
|--------------------------------------------------------------------------
*/

$page_title = "کارتکس انبار";

$pageTitle = "کارتکس انبار";

$pageDescription = "گردش کامل ورود و خروج کالا";

$pageIcon = "fa-solid fa-right-left";

/*
|--------------------------------------------------------------------------
| ارسال متغیرها به View
|--------------------------------------------------------------------------
*/

$contentFile = __DIR__ . "/views/index_content.php";

/*
|--------------------------------------------------------------------------
| بارگذاری Layout اصلی ERP
|--------------------------------------------------------------------------
*/

require_once dirname(__DIR__, 2) . "/includes/layout.php";