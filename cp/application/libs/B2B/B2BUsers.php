<?php
namespace B2B;

use B2B\B2BUserSet;

class B2BUsers extends B2BFactory
{
  public function __construct( $db = null )
  {
    parent::__construct($db);
    unset($db);
  }

  public function get( $arg = array() )
  {
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
    $q .= "";

    try {
      $s = $this->db->db->prepare($q);
      $s->execute();
    } catch (\PDOException $e) {
      $this->db->printPDOErrorMsg($e, $q, true);
    }

    if($s->rowCount() === 0) return false;

    $datas = $s->fetchAll(\PDO::FETCH_ASSOC);

    $set = new B2BUserSet( $this->db );
    foreach ( $datas as $d )
    {
      $set->addUser( (new B2BUser( $this->db, $d )) );
    }

    unset($datas);

    return $set;
  }

  public function test()
  {
    echo \Hash::jelszo('demo');
  }
}

?>
