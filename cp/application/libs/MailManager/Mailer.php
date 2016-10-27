<?
namespace MailManager;

use MailManager\PHPMailer;
use PortalManager\Template;

/**
* class Mailer
*/
class Mailer extends PHPMailer
{
	private $mode = 'php';
	private $recepiens = array();
	private $from_email = 'mail@example.com';
	private $from_name = 'Test Mailer';
	private $subject = 'Teszt';
	private $msg = 'Teszt';

	function __construct( $from_name, $from_email, $sender_mode )
	{
		parent::__construct();

		$this->from_name 	= $from_name;
		$this->from_email 	= $from_email;
		$this->mode 		= $sender_mode;

		return $this;
	}

	public function add( $email )
	{
		$this->recepiens[] = $email;
		return $this;
	}

	public function setSubject( $subject )
	{
		$this->subject = $subject;
	}

	public function setMsg( $msg )
	{
		$this->msg = $msg;
		return $this;
	}

	public function sendMail()
	{
		date_default_timezone_set('Europe/Budapest');						
		
		if ( $this->mode == 'php' ) {
					
		} else if( $this->mode == 'smtp' ) {
			$this->isSMTP();                    // Set mailer to use SMTP
			//$this->Host 		= '';
			$this->SMTPDebug 	= ($arg[debug]) ? $arg[debug] : 0;

			$this->SMTPAuth 	= true;         // Enable SMTP authentication
			$this->SMTPSecure 	= SMTP_MODE;    // Enable encryption, 'ssl' also accepted
			$this->Host 		= SMTP_HOST;  	// Specify main and backup server
			$this->Port 		= SMTP_PORT;
			$this->Username 	= SMTP_USER;    // SMTP username
			$this->Password 	= SMTP_PW;      // SMTP password			
		}

		// Küldő adatai
		$this->setFrom( $this->from_email, $this->from_name );

		// Válasz adatok
		$this->addReplyTo( $this->from_email, $this->from_name );
		
		$inserted 			= array();
		$err 				= array();
		$ret 				= array(); 

		if ( count($this->recepiens) > 0 ) {

			foreach( $this->recepiens as $r ){
				$this->addAddress($r);  	
				$this->WordWrap = 150;   // Set word wrap to 50 characters
				$this->isHTML(true);     // Set email format to HTML
				
				/*
				$msg = Helper::emailPatern(array(
					'UZENET' 	=> $arg[msg],
					'ALAIRAS' 	=> $arg[alairas],
					'TEMA_NEV' 	=> $arg[tema],
					'NEWS' 		=> $news,
					'EMAIL' 	=> $r
				));
				*/
					
				$this->Subject = $this->subject;
				$this->Body    = $this->msg;
				$this->AltBody = $this->html2text( $this->msg );
				
				if (!$this->send()) {
			       	$emsg 	=  "Kiküldés sikertelen: (" . str_replace("@", "&#64;", $r) . ') ' . $this->ErrorInfo . '<br />';
					$err[] 	= array('mail' => $r, 'msg' => $emsg);
			        break;
			    }else{
			        $inserted[] = $r;
			    }

				$this->clearAddresses();
				$this->clearAttachments();
			}

		}

		$ret['failed'] 	= $err;
		$ret['success'] = $inserted;

		return $ret;
	}
}
?>