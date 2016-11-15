<?php
namespace B2B;

class B2BStats extends B2BFactory
{
  public function __construct( $db = null )
  {
    parent::__construct($db);
    unset($db);

    return $this;
  }

  public function users()
  {
    $stat = array();

    $total = $this->db->query("SELECT count(ID) FROM ".self::DB_USERS)->fetchColumn();
    $stat['total'] = $total;

    $active_month = $this->db->query("SELECT count(ID) FROM ".self::DB_USERS." WHERE utoljara_belepett IS NOT NULL and datediff(now(), utoljara_belepett) < 30;")->fetchColumn();
    $stat['last_month'] = $active_month;

    return $stat;
  }

  public function traffics()
  {
    $stat = array();
    // 30 nap
    $active_month = $this->db->query($qq = "SELECT
      sum(ot.me * ot.egysegAr)
    FROM order_termekek as ot
    LEFT OUTER JOIN orders o ON o.ID = ot.orderKey
    WHERE 1=1
    and o.b2b IN (1)
    and o.allapot IN(".$this->db->settings['flagkey_orderstatus_done'].")
    and datediff(now(), o.idopont) < 30
    ")->fetchColumn();
    $stat['day30'] = ($active_month) ? $active_month : 0;

    // 90 nap
    $day90 = $this->db->query($qq = "SELECT
      sum(ot.me * ot.egysegAr)
    FROM order_termekek as ot
    LEFT OUTER JOIN orders o ON o.ID = ot.orderKey
    WHERE 1=1
    and o.b2b IN (1)
    and o.allapot IN(".$this->db->settings['flagkey_orderstatus_done'].")
    and datediff(now(), o.idopont) < 90
    ")->fetchColumn();
    $stat['day90'] = ($day90) ? $day90 : 0;

    // 365 nap
    $day365 = $this->db->query($qq = "SELECT
      sum(ot.me * ot.egysegAr)
    FROM order_termekek as ot
    LEFT OUTER JOIN orders o ON o.ID = ot.orderKey
    WHERE 1=1
    and o.b2b IN (1)
    and o.allapot IN(".$this->db->settings['flagkey_orderstatus_done'].")
    and datediff(now(), o.idopont) < 365
    ")->fetchColumn();
    $stat['day365'] = ($day365) ? $day365 : 0;

    return $stat;
  }

  public function ordertypes()
  {
    $states = array();
    $types = $this->db->query($qq = "SELECT
      o.allapot,
      oa.nev as allapot_nev,
      oa.szin as color
    FROM orders as o
    LEFT OUTER JOIN order_allapot as oa ON oa.ID = o.allapot
    WHERE 1=1
    and o.b2b IN(1)
    and o.allapot NOT IN(".$this->db->settings['flagkey_orderstatus_done'].", ".$this->db->settings['flagkey_orderstatus_delete'].")
    ")->fetchAll(\PDO::FETCH_ASSOC);

    foreach ( $types as $t ) {
      $states[$t['allapot']]['count']++;

      if (!isset($states[$t['allapot']]['name'])) {
        $states[$t['allapot']]['name'] = $t['allapot_nev'];
        $states[$t['allapot']]['color'] = $t['color'];
      }

    }

    return $states;
  }

  public function usersLogins()
  {
    $stat = array();

    return $stat;
  }

  public function userToplist( $day = 90 )
  {
    $stat = array();

    return $stat;
  }

  public function orders( $month = 6 )
  {
    $stat = array();

    for ($i=$month; $i >= 0 ; $i--) {
      $past = date('Y-m', strtotime('-'.$i.' months'));

      $cn = $this->db->query($qq = "SELECT
        count(o.ID)
      FROM orders as o
      WHERE 1=1
      and o.b2b IN (0)
      and o.allapot IN(".$this->db->settings['flagkey_orderstatus_done'].")
      and o.idopont LIKE '".$past."%'
      ")->fetchColumn();
      $b2b = $this->db->query($qq = "SELECT
        count(o.ID)
      FROM orders as o
      WHERE 1=1
      and o.b2b IN (1)
      and o.allapot IN(".$this->db->settings['flagkey_orderstatus_done'].")
      and o.idopont LIKE '".$past."%'
      ")->fetchColumn();
      $stat[$past] = array(
        'normal' => $cn,
        'b2b' => $b2b
      );
    }

    return $stat;
  }
}
?>
