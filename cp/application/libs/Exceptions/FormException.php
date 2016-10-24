<?
namespace Exceptions;

class FormException extends \Exception
{
	private $errorArr = array(
		'msg' 		=> '',
    'missing_fields' => array(),
    'miss_count' => 0
	);
	public function __construct($message, $missedFields = false, $code = 0, \Exception $previous = null) {
    parent::__construct($message, $code, $previous);

		if($missedFields){
			$this->errorArr['missing_fields'] = $missedFields;
      $this->errorArr['miss_count'] = count($missedFields, \COUNT_RECURSIVE);
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
