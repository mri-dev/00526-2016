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

  public function Name()
  {
    $this->data['nev'];
  }
  public function Email()
  {
    $this->data['email'];
  }
}

?>
