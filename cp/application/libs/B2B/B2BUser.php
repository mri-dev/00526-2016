<?php
namespace B2B;

use Exceptions\FormException;

class B2BUser extends B2BFactory
{
  private $data = false;
  public $required_create_fields = array(
    'nev', 'email', 'telephely', 'adoszam', 'jelszo',
    'kapcsolat_nev', 'kapcsolat_telefon',
    'szallitas' => array( 'nev', 'irsz', 'uhsz', 'city', 'kozterulet_jellege', 'hazszam', 'phone' ),
    'szamlazas' => array( 'nev', 'irsz', 'uhsz', 'city', 'kozterulet_jellege', 'hazszam' ),
  );
  public function __construct( $db = null, $data = null )
  {
    parent::__construct($db);
    unset($db);
    $this->data = $data;
    return $this;
  }

  public function create($post)
  {
    $new_id = false;
    $missed_field_count = 0;
    $missed_fields = array();

    foreach ($post as $key => $value) {
      if( !in_array($key, array('szallitas', 'szamlazas')) ){
        if (in_array($key, $this->required_create_fields) && empty($value)) {
          $missed_field_count++;
          $missed_fields[] = $key;
        }
      } else {
        // Számlázás & Szállítás check
        foreach ($post[$key] as $skey => $svalue) {
          if (in_array($skey, $this->required_create_fields[$key]) && empty($svalue)) {
            $missed_field_count++;
            $missed_fields[] = $key.'_'.$skey;
          }
        }
      }
    }

    if($missed_field_count != 0) {
      throw new FormException("Kötelező mezők adatai hiányoznak.", $missed_fields);
    }

    $jelszohash = \Hash::jelszo(trim($post['jelszo']));

    $usage = $this->checkEmailUsage($post['email']);

    if ($usage === false) { throw new FormException("Nem lett megadva az e-mail cím."); }
    if ($usage !== 0) { throw new FormException("Ezzel az e-mail címmel (".$post['email'].") már regisztráltak. Felh. ID: ".$usage); }

    // Save to database
    $dataset = array();
    $szallitas = json_encode($post['szallitas'], \JSON_UNESCAPED_UNICODE);
    $szamlazas = json_encode($post['szamlazas'], \JSON_UNESCAPED_UNICODE);
    unset($post['szamlazas']);
    unset($post['szallitas']);
    $dataset[':szamlazas'] = $szamlazas;
    $dataset[':szallitas'] = $szallitas;
    $dataset[':jelszohash'] = $jelszohash;

    foreach ($post as $key => $value) {
      $dataset[':'.$key] = $value;
    }

    try {
      $s = $this->db->db->prepare("INSERT INTO ".self::DB_USERS."
      (email, nev, jelszo, jelszo_str, adoszam, telephely, kapcsolat_nev, kapcsolat_telefon, szamlazasi_adatok, szallitasi_adatok) VALUES
      (:email, :nev, :jelszohash, :jelszo, :adoszam, :telephely, :kapcsolat_nev, :kapcsolat_telefon, :szamlazas, :szallitas);");
      $s->execute($dataset);
      $new_id = $this->db->db->lastInsertId();
    } catch (\PDOException $e) {
      $this->db->printPDOErrorMsg($e, $q, true);
    }

    return $new_id;
  }

  public function checkEmailUsage( $email = false )
  {
    if (!$email) {
      return false;
    }

    try {
      $s = $this->db->db->prepare("SELECT ID FROM ".self::DB_USERS." WHERE email = :email;");
      $s->execute(array(
        ':email' => trim($email)
      ));
    } catch (\PDOException $e) {
      $this->db->printPDOErrorMsg($e, $q, true);
    }

    if($s->rowCount() === 0) return 0;

    $id = $s->fetchColumn();

    return $id;
  }

  public function validateToLogin( $email = false, $pass = false, $return_record = 'ID' )
  {
    if (!$email) { return false; }
    if (!$pass) { return false; }

    $passhash = \Hash::jelszo(trim($pass));

    try {
      $s = $this->db->db->prepare("SELECT ".$return_record." FROM ".self::DB_USERS." WHERE email = :email and jelszo = :pw;");
      $s->execute(array(
        ':email'  => trim($email),
        ':pw'     => $passhash
      ));
    } catch (\PDOException $e) {
      $this->db->printPDOErrorMsg($e, $q, true);
    }

    if($s->rowCount() === 0) return false;

    $backrecord = $s->fetchColumn();

    return $backrecord;
  }

  public function get( $id )
  {
    $excp = array();

    $q = "
    SELECT
      u.*
    FROM ".self::DB_USERS." as u
    WHERE
      1 = 1 and
      u.ID = :id
    ";

    $excp[':id'] = $id;

    try {
      $s = $this->db->db->prepare($q);
      $s->execute($excp);
    } catch (\PDOException $e) {
      $this->db->printPDOErrorMsg($e, $q, true);
    }

    if($s->rowCount() === 0) return $this;

    $this->data = $s->fetch(\PDO::FETCH_ASSOC);

    return $this;
  }

  public function getForWebsite( $id )
  {
    $ret = array();
    $this->get( $id );

    $ret['email'] = $this->data['email'];
    $ret['data'] = $this->data;
    $ret['szamlazasi_adat'] = json_decode($this->data['szamlazasi_adatok'], true);
    $ret['szallitasi_adat'] = json_decode($this->data['szallitasi_adatok'], true);
    $ret['kedvezmeny'] = 0;
    $ret['kedvezmenyek'] = array();

    return $ret;
  }

  public function save($post)
  {
    extract($post);
    $excp = array();

    if (isset($post['szamlazas'])) {
      $post['szamlazasi_adatok'] = json_encode($post['szamlazas'], \JSON_UNESCAPED_UNICODE);
    }
    if (isset($post['szallitas'])) {
      $post['szallitasi_adatok'] = json_encode($post['szallitas'], \JSON_UNESCAPED_UNICODE);
    }

    unset($post['szallitas']);
    unset($post['szamlazas']);

    $q = "UPDATE ".self::DB_USERS . " SET ";
    $update = '';
    foreach ($post as $key => $value) {
      if($key == 'jelszo'){
        $value = \Hash::jelszo($value);
      }
      $update .= $key . " = :key_".$key.", ";
      $excp[':key_'.$key] = trim($value);
    }

    $update .= "utolso_frissites = :lastrefresh, ";
    $update .= "jelszo_str = :jelszo_str, ";
    $excp[':lastrefresh'] = trim(NOW);
    $excp[':jelszo_str']  = $post['jelszo'];

    $q .= rtrim($update, ", ");
    $q .= " WHERE ID = :id;";

    $excp[':id'] = $this->ID();

    try {
      $s = $this->db->db->prepare($q);
      $s->execute($excp);
    } catch (\PDOException $e) {
      $this->db->printPDOErrorMsg($e, $q, true);
    }

  }

  public function hasUser()
  {
    return ($this->data) ? true : false;
  }

  public function activate()
  {
    try {
      $s = $this->db->db->prepare("UPDATE ".self::DB_USERS." SET engedelyezve = :s WHERE ID = :id;");
      $s->execute(array(
        ':s'  => 1,
        ':id' => $this->ID()
      ));
    } catch (\PDOException $e) {
      $this->db->printPDOErrorMsg($e, $q, true);
    }
  }

  public function deactivate()
  {
    try {
      $s = $this->db->db->prepare("UPDATE ".self::DB_USERS." SET engedelyezve = :s WHERE ID = :id;");
      $s->execute(array(
        ':s'  => 0,
        ':id' => $this->ID()
      ));
    } catch (\PDOException $e) {
      $this->db->printPDOErrorMsg($e, $q, true);
    }
  }

  public function ID()
  {
    return $this->data['ID'];
  }
  public function Name()
  {
    return $this->data['nev'];
  }
  public function Taxnumber()
  {
    return $this->data['adoszam'];
  }
  public function PasswordString()
  {
    return $this->data['jelszo_str'];
  }
  public function PasswordHash()
  {
    return $this->data['jelszo'];
  }
  public function ContactName()
  {
    return $this->data['kapcsolat_nev'];
  }
  public function ContactPhone()
  {
    return $this->data['kapcsolat_telefon'];
  }
  public function Email()
  {
    return $this->data['email'];
  }
  public function Address()
  {
    return $this->data['telephely'];
  }
  public function CreatedAt()
  {
    return $this->data['letrehozva'];
  }
  public function Active()
  {
    return ($this->data['engedelyezve'] == '1') ? true : false;
  }
  public function Lastlogin()
  {
    return $this->data['utoljara_belepett'];
  }
  public function Lastupdate()
  {
    return $this->data['utolso_frissites'];
  }
  public function SzallitasList()
  {
    return json_decode($this->data['szallitasi_adatok'], true);
  }
  public function SzamlazasList()
  {
    return json_decode($this->data['szamlazasi_adatok'], true);
  }
}

?>
