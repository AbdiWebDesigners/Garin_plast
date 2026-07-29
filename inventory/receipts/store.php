require ...

$service = new ReceiptService($pdo);

$result = $service->store($_POST);

if ($result['success']) {

    header(...);

}