<?php

declare(strict_types=1);

namespace A50\Database\Yii\Repository;

use A50\Database\Repository\Exception\CouldNotDeleteEntity;
use A50\Database\Repository\Exception\CouldNotSaveEntity;
use A50\Database\Repository\Exception\CouldNotUpdateEntity;
use A50\Database\Repository\ObjectRepository;
use A50\Database\Repository\TableName;
use Throwable;
use Yiisoft\Db\Connection\ConnectionInterface;

final readonly class YiiDBObjectRepository implements ObjectRepository
{
    public function __construct(
        private ConnectionInterface $connection,
        private TableName $tableName,
    )
    {
    }

    public function save(array $data): void
    {
        try {
            $command = $this->connection->createCommand();
            $command->insert($this->tableName->quoted(), $data)->execute();
        } catch (Throwable $exception) {
            throw CouldNotSaveEntity::withReason($exception->getMessage());
        }
    }

    public function updateBy(array $data, array $criteria): void
    {
        try {
            $command = $this->connection->createCommand();
            $command->update($this->tableName->quoted(), $data, $criteria)->execute();
        } catch (Throwable $exception) {
            throw CouldNotUpdateEntity::withReason($exception->getMessage());
        }
    }

    public function updateOneById(array $data, string $id): void
    {
        $this->updateBy($data, ['id' => $id]);
    }

    public function deleteBy(array $criteria): void
    {
        try {
            $command = $this->connection->createCommand();
            $command->delete($this->tableName->quoted(), $criteria)->execute();
        } catch (Throwable $exception) {
            throw CouldNotDeleteEntity::withReason($exception->getMessage());
        }
    }

    public function deleteOneById(string $id): void
    {
        $this->deleteBy(['id' => $id]);
    }

    public function deleteManyByIds(array $ids): void
    {
        $this->deleteBy(['id' => $ids]);
    }
}
