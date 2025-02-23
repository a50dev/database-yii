<?php

declare(strict_types=1);

namespace A50\Database\Yii\Repository;

use A50\Database\Repository\SelectRepository;

final readonly class YiiDBSelectRepositoryWithCamelCase implements SelectRepository
{
    public function __construct(
        private YiiDBSelectRepository $selectRepository,
    )
    {
    }

    private function toCamelCase(string $string): string
    {
        return lcfirst(str_replace('_', '', ucwords($string, '_')));
    }

    private function formatPropertyNameToCamelCaseForArray(array $data): array
    {
        $new = [];

        foreach ($data as $key => $value) {
            $new[$this->toCamelCase($key)] = $value;
        }

        return $new;
    }

    private function mapItems(?array $data): ?array
    {
        if ($data === null) {
            return null;
        }

        return \array_map(fn($item) => $this->formatPropertyNameToCamelCaseForArray($item), $data);
    }

    private function mapItem(?array $item): ?array
    {
        if ($item === null) {
            return null;
        }

        return $this->formatPropertyNameToCamelCaseForArray($item);
    }

    public function findAll(?array $select = null, ?array $orderBy = null): ?array
    {
        return $this->mapItems(
            $this->selectRepository->findAll($select, $orderBy)
        );
    }

    public function findBy(
        array $criteria,
        ?array $select = null,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): ?array {
        return $this->mapItems(
            $this->selectRepository->findBy($criteria, $select, $orderBy, $limit, $offset)
        );
    }

    public function findOneBy(array $criteria, ?array $select = null): ?array
    {
        return $this->mapItem(
            $this->selectRepository->findOneBy($criteria, $select)
        );
    }

    public function findOneById(string $id, ?array $select = null): ?array
    {
        return $this->mapItem(
            $this->selectRepository->findOneById($id, $select)
        );
    }

    public function count(?array $criteria = null): int
    {
        return $this->selectRepository->count($criteria);
    }

    public function getBy(
        array $criteria,
        ?array $select = null,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        return $this->mapItems(
            $this->selectRepository->getBy($criteria, $select, $orderBy, $limit, $offset)
        );
    }

    public function getOneBy(
        array $criteria,
        ?array $select = null,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        return $this->mapItems(
            $this->selectRepository->getOneBy($criteria, $select, $orderBy, $limit, $offset)
        );
    }

    public function getOneById(string $id, ?array $select = null): array
    {
        return $this->mapItem(
            $this->selectRepository->getOneById($id, $select)
        );
    }

    public function existsBy(array $criteria): bool
    {
        return $this->selectRepository->existsBy($criteria);
    }
}
