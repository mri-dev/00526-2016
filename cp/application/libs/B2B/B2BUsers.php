<?php
namespace B2B;

use B2B\B2BUserSet;

class B2BUsers extends B2BFactory
{
  public $total_users = false;
  public $list_max_page = 1;
  public $list_current_page = 1;
  public $list_page_limit = 50;

  public function __construct( $db = null )
  {
    parent::__construct($db);
    unset($db);
  }

  public function setPageLimit( $limit = 50 )
  {
    $this->list_page_limit = $limit;
  }

  public function get( $arg = array() )
  {
    $excp = array();

    $q = "SELECT SQL_CALC_FOUND_ROWS";
    // Get
    $q .= " u.*";
    // From
    $q .= " FROM ".self::DB_USERS." as u";
    // Where
    $q .= " WHERE 1 = 1";
    // Order
    $q .= "";
    // Limit
    $page = (isset($arg['page'])) ? (int)$arg['page'] : (int)\Helper::getLastParam();
    $this->list_current_page = (is_numeric($page) && $page > 0) ? $page : 1;
    $l_min = 0;
    $l_min = $this->list_current_page * $this->list_page_limit - $this->list_page_limit;

    $q .= " LIMIT $l_min, $this->list_page_limit";
    $q .= ";";

    //echo $q;

    try {
      $s = $this->db->db->prepare($q);
      $s->execute($excp);
    } catch (\PDOException $e) {
      $this->db->printPDOErrorMsg($e, $q, true);
    }

    if($s->rowCount() === 0) return false;

    $datas = $s->fetchAll(\PDO::FETCH_ASSOC);
    $this->total_users = $this->db->query("SELECT FOUND_ROWS();")->fetchColumn();
    $this->list_max_page 	= ($this->total_users == 0) ? 0 : ceil($this->total_users / $this->list_page_limit);

    $set = new B2BUserSet( $this->db );
    foreach ( $datas as $d )
    {
      $set->addUser( (new B2BUser( $this->db, $d )) );
    }

    unset($datas);

    return $set;
  }
  public function totalUsers()
  {
    return $this->total_users;
  }
  public function listMaxPage()
  {
    return $this->list_max_page;
  }
  public function listCurrentPage()
  {
    return $this->list_current_page;
  }
}

?>
