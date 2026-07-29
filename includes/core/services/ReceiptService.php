public function store(array $data): array
{
    $this->pdo->beginTransaction();

    $receiptId = $this->headerService->save($data);

    $this->itemService->save($receiptId, $data['items']);

    $this->inventoryService->update($receiptId);

    $this->transactionService->create($receiptId);

    $this->pdo->commit();

    return [
        'success' => true
    ];
}