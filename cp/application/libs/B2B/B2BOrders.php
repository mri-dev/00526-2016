<?php
namespace B2B;

class B2BOrders extends B2BFactory
{
  public $uid = false;
  public $count = 0;
  public $not_finished_count = 0;
  public $finish_status_ids = array( 4, 13 );
  public $status_code_counts = array();
  public $status_texts = array();
  public $status_text_colors = array();
  protected $dataset = array();

  public function __construct( $user_id, $db = null )
  {
    parent::__construct($db);
    unset($db);
    $this->uid = $user_id;

    try {
      $s = $this->db->db->prepare("SELECT
        o.*,
        os.nev as allapot_text,
        os.szin as allapot_szin
      FROM orders as o
      LEFT OUTER JOIN order_allapot as os ON os.ID = o.allapot
      WHERE
        o.userID = :id and
        b2b = 1
      ORDER BY allapot ASC;");
      $s->execute(array(
        ':id' => $this->uid
      ));
      $this->count = $s->rowCount();
      $data = $s->fetchAll(\PDO::FETCH_ASSOC);

      foreach ($data as $d) {
        $d['szamlazas'] = json_decode($d['szamlazasi_keys'], true);
        $d['szallitas'] = json_decode($d['szallitasi_keys'], true);
        $d['items'] = $this->getItems($d['ID']);
        $d['vegosszeg'] = ($d['items']['sum'] + $d['szallitasi_koltseg'] - $d['kedvezmeny']);
        $this->status_code_counts[$d['allapot']] += 1;
        $this->status_texts[$d['allapot']] = $d['allapot_text'];
        $this->status_text_colors[$d['allapot']] = $d['allapot_szin'];

        if ( !in_array( $d['allapot'], $this->finish_status_ids) ) {
          $this->not_finished_count++;
        }

        $this->dataset[] = $d;
      }
    } catch (\PDOException $e) {
      $this->db->printPDOErrorMsg($e, $q, true);
    }

    return $this;
  }

  protected function getItems( $orderid )
  {
    $ret = array(
      'products' => 0,
      'items' => 0,
      'sum' => 0,
      'list' => array()
    );
    if (!$orderid) {
      return $ret;
    }

    try {
      $s = $this->db->db->prepare("SELECT
          oi.termekID,
          oi.me,
          oi.egysegAr,
          t.nev as termek,
          t.meret,
          t.szin,
          CONCAT(t.raktar_articleid,'-',t.raktar_variantid) as code
        FROM order_termekek as oi
        LEFT OUTER JOIN shop_termekek as t ON t.ID = oi.termekID
        WHERE oi.orderKey = :id;");
      $s->execute(array(
        ':id' => $orderid
      ));
      $ret['products'] = $s->rowCount();
      $data = $s->fetchAll(\PDO::FETCH_ASSOC);

      foreach ($data as $d) {
        $ret['items'] += $d['me'];
        $ret['sum'] += ($d['me']*$d['egysegAr']);
        $ret['list'][] = $d;
      }
    } catch (\PDOException $e) {
      $this->db->printPDOErrorMsg($e, $q, true);
    }

    return $ret;
  }

  public function get()
  {
    return $this->dataset;
  }
}
?>
