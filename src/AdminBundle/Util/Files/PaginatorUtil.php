<?php

namespace AdminBundle\Util\Files;
use Symfony\Component\Config\Definition\Exception\Exception;

class PaginatorUtil {

    private $total_pages;
    private $page;
    private $interval = 2;
    private $rpp;

    public function __construct($page, $total_count, $rpp){
        $this->rpp = $rpp;
        $this->total_pages = $this->setTotalPages($total_count, $rpp);
        $this->page = $this->setPage($page);
    }

    /*
     * var recCount: the total count of records
     * var $rpp: the record per page
     */
    private function setTotalPages($total_count, $rpp = 10){
        $this->total_pages = ceil($total_count / $rpp) > 0 ? ceil($total_count / $rpp) : 1;
        return $this->total_pages;
    }

    public function getTotalPages(){
        return $this->total_pages;
    }

    public function setPage($page){
        if ($page > $this->total_pages) {
            $this->page = $this->total_pages;
        } else {
            $this->page = $page > 0 ? $page : 1;
        }
        return $this->page;
    }

    public function getPage(){
        return $this->page;
    }

    public function getPagesList(){
        $min = $this->page - $this->interval > 0 ? $this->page - $this->interval : 1;
        $max = $this->page + $this->interval > $this->total_pages ? $this->total_pages : $this->page + $this->interval;
        $pages_arr = array();
        for ($i=$min; $i <= $max; $i++) { 
            $pages_arr[] = $i;
        }
        return $pages_arr;
    }
}