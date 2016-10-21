<?php
namespace B2B;

class B2BUser extends B2BFactory
{
  private $data = false;
  public function __construct( $db = null, $data = null )
  {
    parent::__construct($db);
    unset($db);
    $this->data = $data;
    return $this;
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

  public function save($post)
  {
    extract($post);
    $excp = array();

    $post['szamlazasi_adatok'] = json_encode($post['szamlazas'], \JSON_UNESCAPED_UNICODE);
    $post['szallitasi_adatok'] = json_encode($post['szallitas'], \JSON_UNESCAPED_UNICODE);
    unset($post['szallitas']);
    unset($post['szamlazas']);

    $q = "UPDATE ".self::DB_USERS . " SET ";
    $update = '';
    foreach ($post as $key => $value) {
      $update .= $key . " = :key_".$key.", ";
      $excp[':key_'.$key] = trim($value);
    }

    $update .= "utolso_frissites = :lastrefresh, ";
    $excp[':lastrefresh'] = trim(NOW);

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
