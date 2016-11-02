<?php
  if( !defined('B2BLOGGED') ){
    $this->render('b2b/auth');
  } else {
    $this->render('b2b/home');
  }
?>
