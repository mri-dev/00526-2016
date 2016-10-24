<?
namespace ShopManager;

class OrderException extends \Exception
{
	private $errorArr = array(
		'success' 	=> 0,
		'msg' 		=> ''
	);
	public function __construct($message, $errorArray = false, $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
		
		if($errorArray){
			$this->errorArr = $errorArray;		
		}
		if($message != ''){
			$this->errorArr[msg] = $message;
		}
    }
	
	public function getErrorData(){
		return $this->errorArr;
	}
}
?>