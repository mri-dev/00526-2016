<?php
namespace B2B;

class B2BFactory
{
  const DB_USERS = 'b2b_felhasznalok';
  const DB_SESSION = 'b2b_login_session';

  public $db = null;

  public function __construct( $db = null )
  {
    $this->db = $db;

    if(!$db) die(__CLASS__.': Hiányzik az adatbázis link.');
  }

  public function __destruct()
  {
    $this->db = null;
  }
}

?>
