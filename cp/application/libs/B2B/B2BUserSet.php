<?php
namespace B2B;

use B2B\B2BUser;

class B2BUserSet extends B2BFactory
{
  public $count = 0;
  protected $dataset = array();
  private $cstep = 0;

  public function __construct( $db = null )
  {
    parent::__construct($db);
    unset($db);

    return $this;
  }

  public function addUser(B2BUser $user)
  {
    $this->count++;
    $this->dataset[] = $user;
  }

  public function walk()
  {
    if($this->cstep >= $this->count) {
      return false;
    }
    $this->cstep++;
    return true;
  }
  
  public function item()
  {
    $c = $this->cstep - 1;
    return $this->dataset[$c];
  }
}
?>
