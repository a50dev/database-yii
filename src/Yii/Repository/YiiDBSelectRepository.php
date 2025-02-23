<?php

declare(strict_types=1);

namespace A50\Database\Yii\Repository;

use A50\Database\Repository\Exception\CouldNotGetEntity;
use A50\Database\Repository\Exception\CouldNotGetEntityById;
use A50\Database\Repository\SelectRepository;
use A50\Database\Repository\TableName;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Db\Query\Query;

final readonly class YiiDBSelectRepository implements SelectRepository
{
    public function __construct(
        private ConnectionInterface $connection,
        private TableName $tableName,
    )
    {
    }

    private function createQuery(
        ?array $criteria = null,
        ?array $select = null,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): Query
    {
        $query = (new Query($this->connection))
            ->select($select ?? '*')
            ->from($this->tableName->quoted())
            ->where($criteria);

        if ($orderBy !== null) {
            $query = $query->orderBy($orderBy);
        }

        if ($limit !== null) {
            $query = $query->limit($limit);
        }

        if ($offset !== null) {
            $query = $query->offset($offset);
        }

        return $query;
    }

    public function findAll(?array $select = null, ?array $orderBy = null): ?array
    {
        $query = $this->createQuery(null, $select, $orderBy);
        $data = $query->all();

        if (empty($data)) {
            return null;
        }

        return $data;
    }

    public function findBy(
        array $criteria,
        ?array $select = null,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): ?array {
        $query = $this->createQuery($criteria, $select, $orderBy, $limit, $offset);
        $data = $query->all();

        if (empty($data)) {
            return null;
        }

        return $data;
    }

    public function findOneBy(array $criteria, ?array $select = null): ?array
    {
        $query = $this->createQuery($criteria, $select);
        $data = $query->one();

        if (empty($data)) {
            return null;
        }

        return $data;
    }

    public function findOneById(string $id, ?array $select = null): ?array
    {
        return $this->findOneBy(['id' => $id], $select);
    }

    public function count(?array $criteria = null): int
    {
        $query = $this->createQuery($criteria);

        return $query->count();
    }

    public function getBy(
        array $criteria,
        ?array $select = null,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        try {
            $data = $this->findBy($criteria, $select, $orderBy, $limit, $offset);

            if ($data === null) {
                throw new \RuntimeException('Data not found');
            }

            return $data;
        } catch (\Throwable $throwable) {
            throw CouldNotGetEntity::becauseOf($throwable->getMessage());
        }
    }

    public function getOneBy(
        array $criteria,
        ?array $select = null,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        try {
            $data = $this->findOneBy($criteria, $select);

            if ($data === null) {
                throw new \RuntimeException('Data not found');
            }

            return $data;
        } catch (\Throwable $throwable) {
            throw CouldNotGetEntity::becauseOf($throwable->getMessage());
        }
    }

    public function getOneById(string $id, ?array $select = null): array
    {
        try {
            $data = $this->findOneById($id, $select);

            if ($data === null) {
                throw new \RuntimeException('Data not found');
            }

            return $data;
        } catch (\Throwable $throwable) {
            throw CouldNotGetEntityById::withId($id, $throwable->getMessage());
        }
    }

    public function existsBy(array $criteria): bool
    {
        return $this->count($criteria) > 0;
    }
}
