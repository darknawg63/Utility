<?php

require 'connect.php';

class Paginator
{
    private $_articles,
            $_db,
            $_table;


    public function __construct($db, $table)
    {
        $this->_db      = $db;
        $this->_table   = $table;
    }

    // Grab user's input
    public function getPage()
    {
        return isset($_GET['page']) ? (int)$_GET['page'] : 1;
    }

    public function getPerPage()
    {
        return isset($_GET['per-page']) && $_GET['per-page'] <= 50 ? (int)$_GET['per-page'] : 10;
    }


    // Positioning
    public function getStart()
    {
        return ($this->getPage() > 1) ? ($this->getPage() * $this->getPerPage()) - $this->getPerPage() : 0;
    }
    

    // Query
    public function getArticles()
    {
        $this->_articles = $this->_db->prepare("
            SELECT SQL_CALC_FOUND_ROWS film_id, title
            FROM {$this->_table}
            LIMIT {$this->getStart()}, {$this->getPerPage()}
        ");
                
        $this->_articles->execute();
        return $this->_articles->fetchAll(PDO::FETCH_ASSOC);

    }

    // Total pages
    public function getTotal()
    {
        // "SELECT SQL_CALC_FOUND_ROWS" must run before executing the "SELECT FOUND_ROWS"
        $this->getArticles();
        
        return $this->_db->query("SELECT FOUND_ROWS() as total")->fetch()['total'];    
    }
    
    public function getPages()
    {
        return ceil($this->getTotal() / $this->getPerPage());
    }
      
}

$paginator = new Paginator($db, 'film');