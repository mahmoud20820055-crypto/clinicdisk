<?php
class Paginator {
    private $totalItems;
    private $perPage;
    private $currentPage;
    private $totalPages;
    
    public function __construct($totalItems, $perPage = 10, $currentPage = 1) {
        $this->totalItems = $totalItems;
        $this->perPage = $perPage;
        $this->currentPage = max(1, $currentPage);
        $this->totalPages = (int)ceil($totalItems / $perPage);
    }
    
    public function offset() {
        return ($this->currentPage - 1) * $this->perPage;
    }
    
    public function totalPages() {
        return $this->totalPages;
    }
    
    public function hasPrev() {
        return $this->currentPage > 1;
    }
    
    public function hasNext() {
        return $this->currentPage < $this->totalPages;
    }
    
    public function prevPage() {
        return $this->currentPage - 1;
    }
    
    public function nextPage() {
        return $this->currentPage + 1;
    }
    
    public function currentPage() {
        return $this->currentPage;
    }
    
    public function perPage() {
        return $this->perPage;
    }
    
    public function getOffset() {
        return $this->offset();
    }
    
    public function render() {
        if ($this->totalPages <= 1) return '';
        
        $queryParams = $_GET;
        unset($queryParams['p']);
        $queryString = http_build_query($queryParams);
        $baseUrl = '?' . ($queryString ? $queryString . '&' : '');
        
        $html = '<nav><ul class="pagination justify-content-center">';
        
        if ($this->hasPrev()) {
            $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . 'p=' . $this->prevPage() . '">« السابق</a></li>';
        }
        
        $start = max(1, $this->currentPage - 2);
        $end = min($this->totalPages, $this->currentPage + 2);
        
        if ($start > 1) {
            $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . 'p=1">1</a></li>';
            if ($start > 2) $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        
        for ($i = $start; $i <= $end; $i++) {
            $active = ($i == $this->currentPage) ? 'active' : '';
            $html .= '<li class="page-item ' . $active . '"><a class="page-link" href="' . $baseUrl . 'p=' . $i . '">' . $i . '</a></li>';
        }
        
        if ($end < $this->totalPages) {
            if ($end < $this->totalPages - 1) $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . 'p=' . $this->totalPages . '">' . $this->totalPages . '</a></li>';
        }
        
        if ($this->hasNext()) {
            $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . 'p=' . $this->nextPage() . '">التالي »</a></li>';
        }
        
        $html .= '</ul></nav>';
        return $html;
    }
}
?>