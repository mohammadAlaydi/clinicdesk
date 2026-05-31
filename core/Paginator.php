<?php

class Paginator {

    private $totalItems;
    private $perPage;
    private $currentPage;

    public function __construct($totalItems, $perPage, $currentPage) {
        $this->totalItems  = max(0, $totalItems);
        $this->perPage     = max(1, $perPage);
        $this->currentPage = max(1, $currentPage);
        if ($this->totalPages() > 0 && $this->currentPage > $this->totalPages()) {
            $this->currentPage = $this->totalPages();
        }
    }

    public function offset() {
        return ($this->currentPage - 1) * $this->perPage;
    }

    public function totalPages() {
        return (int) ceil($this->totalItems / $this->perPage);
    }

    public function currentPage() {
        return $this->currentPage;
    }

    public function perPage() {
        return $this->perPage;
    }

    public function hasPrev() {
        return $this->currentPage > 1;
    }

    public function hasNext() {
        return $this->currentPage < $this->totalPages();
    }
}
