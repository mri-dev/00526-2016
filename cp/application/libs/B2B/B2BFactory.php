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

    if(!$db) {
      die(get_class($this).' > '.__CLASS__.': Hiányzik az adatbázis link.');
    }
  }

  public function __destruct()
  {
    $this->db = null;
  }

  public static function getSzamlazasFields()
  {
    return array(
      "nev",
      "irsz",
      "city",
      "kerulet",
      "uhsz",
      "kozterulet_jellege",
      "hazszam",
      "epulet",
      "lepcsohaz",
      "szint",
      "ajto"
    );
  }

  public static function getSzallitasFields()
  {
    return array(
      "nev",
      "irsz",
      "city",
      "kerulet",
      "uhsz",
      "kozterulet_jellege",
      "hazszam",
      "epulet",
      "lepcsohaz",
      "szint",
      "ajto",
      "phone"
    );
  }

  /**
  * Szállítás és Számlázási kulcs elnevezése
  **/
  public static function szmfieldName( $key )
  {
    $arr = array(
      "nev" => "Név",
      "irsz" => "Irányítószám",
      "city" => "Város / Község",
      "kerulet" => "Kerület",
      "uhsz" => "Közterület neve (utca elnevezés)",
      "kozterulet_jellege" => "Közterület jellege",
      "hazszam" => "Házszám",
      "epulet" => "Épület",
      "lepcsohaz" => "Lépcsőház",
      "szint" => "Szint",
      "ajto" => "Ajtó",
      "phone" => "Telefonszám"
    );

    return $arr[$key];
  }
}

?>
