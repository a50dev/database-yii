<?php

declare(strict_types=1);

namespace A50\Database\Yii;

use A50\Database\DatabaseTransaction;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Db\Transaction\TransactionInterface;

final class DatabaseTransactionUsingYiiDB implements DatabaseTransaction
{
    private ?TransactionInterface $transaction = null;

    public function __construct(
        private readonly ConnectionInterface $connection
    )
    {
    }

    public function begin(): void
    {
        $this->transaction = $this->connection->beginTransaction();
    }

    public function commit(): void
    {
        $this->transaction?->commit();
    }

    public function rollback(): void
    {
        $this->transaction?->rollBack();
    }

    public function wrap(callable $callback): void
    {
        $this->connection->transaction($callback);
    }
}
