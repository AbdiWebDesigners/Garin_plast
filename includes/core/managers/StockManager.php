public function update(int $id,array $data): bool
{
    throw new Exception("Not implemented.");
}

public function delete(int $id): bool
{
    throw new Exception("Not implemented.");
}

public function find(int $id): ?array
{
    $sql="SELECT * FROM inventory_transactions WHERE id=? LIMIT 1";

    $stmt=$this->pdo->prepare($sql);

    $stmt->execute([$id]);

    $row=$stmt->fetch(PDO::FETCH_ASSOC);

    return $row ?: null;
}