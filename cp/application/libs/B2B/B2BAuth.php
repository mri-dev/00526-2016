<?php
namespace B2B;

use B2B\B2BUser;

class B2BAuth extends B2BFactory
{
  public function __construct( $db = null )
  {
    parent::__construct($db);
    unset($db);
  }

  public function login( $email = false, $pass = false )
  {
    if (!$email) { return false; }
    if (!$pass) { return false; }

    $user = new B2BUser($this->db);

    $valided_user_id = $user->checkEmailUsage( $email );

    if (!$valided_user_id) {
      throw new \Exception("Nem létezik ilyen azonosítójú partner.");
    }

    $validated = $user->validateToLogin( $email, $pass );

    if (!$validated) {
      throw new \Exception("Hibás bejelentkezési adatok. Kérjük próbálja meg újra.");
    }

    $user->get($valided_user_id);

    $login_session_url = $this->createLoginSession($valided_user_id);

    $this->sendAuthMail($user, $login_session_url);

    return $user;
  }

  private function createLoginSession( $id )
  {

  }

  public function sendAuthMail( $user, $login_url )
  {

  }

}

?>
