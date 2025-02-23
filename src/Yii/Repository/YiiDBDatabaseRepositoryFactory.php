<?php

declare(strict_types=1);

namespace A50\Database\Yii\Repository;

use A50\Database\Repository\DatabaseRepositoryFactory;
use A50\Database\Repository\ObjectRepository;
use A50\Database\Repository\SelectRepository;
use A50\Database\Repository\TableName;
use Yiisoft\Db\Connection\ConnectionInterface;

final readonly class YiiDBDatabaseRepositoryFactory implements DatabaseRepositoryFactory
{
    public function __construct(
        private ConnectionInterface $connection,
    )
    {
    }

    public function createSelectRepository(TableName $tableName, bool $withCamelCase = false): SelectRepository
    {
        if ($withCamelCase) {
            return new YiiDBSelectRepositoryWithCamelCase(
                new YiiDBSelectRepository($this->connection, $tableName)
            );
        }

        return new YiiDBSelectRepository($this->connection, $tableName);
    }

    public function createObjectRepository(TableName $tableName): ObjectRepository
    {
        return new YiiDBObjectRepository($this->connection, $tableName);
    }
}
