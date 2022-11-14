<?php

namespace App\Utils;

use Iterator;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;

class Pager implements Iterator {

    protected Query $query;
    protected int $page;
    protected int $limit;
    private ?int $count;
    private ?int $pages;
    protected ?Paginator $paginator = null;

    public function __construct(Query $query, int $page, int $limit, bool $withCount = true) {
        $this->page = $page;
        $this->limit = $limit;
        if($withCount) {
            $this->paginator = new Paginator($query);
            $this->count = count($this->paginator);
            $this->pages = ceil($this->count / $this->limit);
            if($page > $this->pages) {
                $this->page = $this->pages ?: 1;
            }
        } else {
            $this->count = null;
            $this->pages = null;
        }
        $this->query = $query;
    }

    public static function factory(Query $query, mixed $page = 1, mixed $limit = 10, bool $withCount = true): self {
        $page = (int)$page ?: 1;
        $limit = (int)$limit ?: 10;
        if($page < 1) $page = 1;
        if($limit < 1) $limit = 1;
        return new self($query, $page, $limit, $withCount);
    }

    public function current(): PagedData {
        return $this->getPagedData();
    }

    public function getPagedData(int $hydrationMode = null): PagedData {
        $data = ($this->paginator?->getQuery() ?? $this->query)
            ->setFirstResult($this->limit * ($this->page - 1))
            ->setMaxResults($this->limit)
            ->getResult($hydrationMode)
        ;
        return new PagedData($this->page, $this->limit, $this->count, $this->pages, $data);
    }

    public function next(): void {
        $this->page++;
    }

    public function key(): int {
        return $this->page;
    }

    public function valid(): bool {
        return $this->page >= 1 && ($this->pages === null || $this->page <= $this->pages);
    }

    public function rewind(): void {
        $this->page = 1;
    }

}
