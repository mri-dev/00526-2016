<?php
namespace B2B;

use B2B\B2BUser;
use MailManager\Mailer;
use PortalManager\Template;

class B2BAuth extends B2BFactory
{
  const SESSION_URL = 'b2b/?validateAuthSession=';
  const VALIDETOSEC = 60;

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

    // Active check
    $is_active = $user->Active();

    if (!$is_active) {
      throw new \Exception("Az Ön fiókja jelenleg inaktíválva van. Vegy fel velünk a kapcsolatot.");
    }

    $user->get($valided_user_id);

    $login_session_url = $this->createLoginSession($user);

    $this->sendAuthMail($user, $login_session_url);

    return $user;
  }

  public function loginBySession( $session = false )
  {
    if (!$session) {
      throw new \Exception("Hiányzó azonosító kulcs. Bejelentkezés sikertelen.");
    }

    try {
      $s = $this->db->db->prepare("SELECT userID, email, valideto FROM ".self::DB_SESSION." WHERE hashkey = :session;");
      $s->execute(array(
        ':session' => $session
      ));
    } catch (\PDOException $e) {
      $this->db->printPDOErrorMsg($e, $q, true);
    }

    if ( $s->rowCount() == 0 ) {
      throw new \Exception("Sikertelen azonosítás. Az Ön által használt bejelentkező URL hibás.");
    }

    $now = time();
    $session_data = $s->fetch(\PDO::FETCH_ASSOC);

    if ($now > $session_data['valideto']) {
      throw new \Exception("Az Ön által használt bejelentkező URL időkerete lejárt. Indítsa el a bejelentkezési folyamatot újra.");
    }

    // Bejelentkeztetés
    $_SESSION['b2buserid'] = $session_data['userID'];

    // hashkey törlése
    $this->db->query("DELETE FROM ".self::DB_SESSION." WHERE hashkey = '$session';");

    // log login
    try {
      $s = $this->db->db->prepare("UPDATE ".self::DB_USERS." SET utoljara_belepett = now() WHERE ID = :uid;");
      $s->execute(array(
        ':uid' => $session_data['userID']
      ));
    } catch (\PDOException $e) {
      $this->db->printPDOErrorMsg($e, $q, true);
    }

    return true;
  }

  private function createLoginSession( \B2B\B2BUser $user )
  {
    if( !$user ) return false;
    $id = $user->ID();

    // Aktuális session törlése
    $this->db->query("DELETE FROM ".self::DB_SESSION." WHERE userID = ".$id.";");

    // Összes lejárt session törlése
    $this->db->query("DELETE FROM ".self::DB_SESSION." WHERE valideto < UNIX_TIMESTAMP();");

    $email = $user->Email();
    $randomkey  = uniqid();
    $hashkey    = md5($randomkey.'.'.$id.'.'.$email.'.'.$user->PasswordHash());
    $valideto   = time() + (self::VALIDETOSEC * 60);

    try {
      $s = $this->db->db->prepare("INSERT INTO ".self::DB_SESSION."(userID, email, hashkey, randomkey, valideto) VALUES(:id, :email, :hashkey, :randomkey, :valideto);");
      $s->execute(array(
        ':id' => $id,
        ':email' => $email,
        ':hashkey' => $hashkey,
        ':randomkey' => $randomkey,
        ':valideto' => $valideto
      ));
    } catch (\PDOException $e) {
      $this->db->printPDOErrorMsg($e, $q, true);
    }

    return self::SESSION_URL . $hashkey;
  }

  public function sendAuthMail( \B2B\B2BUser $user, $login_url )
  {
    // Bejelentkező e-mail kiküldése
		$mail = new Mailer(
      $this->db->settings['page_title'],
      $this->db->settings['email_noreply_address'],
      $this->db->settings['mail_sender_mode']
    );
		$mail->add( $user->Email() );
		$arg = array(
			'settings' 		=> $this->db->settings,
      'loginurl'    => DOMAIN.$login_url,
      'user'        => $user,
      'ervenyes'    => (time() + (self::VALIDETOSEC * 60))
		);
		$mail->setSubject( 'B2B Bejelentkezezés Megerősítése' );

    $mailtemp = new Template( VIEW . 'templates/mail/' );
    $msg = $mailtemp->get( 'b2b_user_auth', $arg );

    //echo $msg;
    //exit;

		$mail->setMsg($msg);
		$re = $mail->sendMail();
  }

}

?>
