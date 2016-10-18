<?php
namespace B2B;

class B2BAuth extends B2BFactory
{
  public function __construct( $db = null )
  {
    parent::__construct($db);
    unset($db);
  }

}

?>
