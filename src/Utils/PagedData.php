<?php

namespace App\Utils;

use Yabx\TypeScriptBundle\Attributes\Definition;
use Symfony\Component\Serializer\Annotation\Groups;

#[Definition('IPagedData<T>')]
class PagedData {

    #[Groups(['main'])]
    protected int $page;

    #[Groups(['main'])]
    protected int $limit;

    #[Groups(['main'])]
    protected ?int $count;

    #[Groups(['main'])]
    protected ?int $pages;

    #[Groups(['main'])]
    #[Definition('T[]')]
    protected array $data;

    public function __construct(int $page, int $limit, ?int $count, ?int $pages, array $data) {
        $this->page = $page;
        $this->limit = $limit;
        $this->pages = $pages;
        $this->data = $data;
        $this->count = $count;
    }

    public function getPage(): int {
        return $this->page;
    }

    public function getLimit(): int {
        return $this->limit;
    }

    public function getCount(): ?int {
        return $this->count;
    }

    public function getPages(): ?int {
        return $this->pages;
    }

    public function getData(): array {
        return $this->data;
    }

    public function setData(array $data): self {
        $this->data = $data;
        return $this;
    }

    public function mapData(callable $function): self {
        $this->data = array_map($function, $this->data);
        return $this;
    }

}
