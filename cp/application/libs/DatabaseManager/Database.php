<?
namespace DatabaseManager;

/**
 * ÚJ
* class Database
* @package DatabaseManager
* @version 1.1
*/
class Database
{
	public $db = null;
	// adatbázis hoszt
	private $db_host 	= DB_HOST;
	// adatbázis
	private $db_name 	= DB_NAME;
	// adatbázis felhasználó
	private $db_user 	= DB_USER;
	// adatbázis jelszó
	private $db_pw 		= DB_PW;

	public $settings 	= array();

	public function __construct(){
		try{
			$this->db = new \PDO('mysql:host=' . $this->db_host . ';dbname=' . $this->db_name, $this->db_user , $this->db_pw );
			$this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			//echo '-DBOPEN-';
			$this->query("set names utf8");
		}catch(\PDOException $e){
			die($e->getMessage());
		}

		// Settings
		$settings = $this->query("SELECT * FROM beallitasok")->fetchAll(\PDO::FETCH_ASSOC);
		foreach ($settings as $s) {
			$this->settings[$s['bKulcs']] = $s['bErtek'];
		}
		$this->settings['domain'] = 'http://www.' . rtrim(str_replace(array('http://','www.'),array('',''),$this->settings['page_url']), '/').'/';
	}

	public function query( $qry )
	{
		$q = null;
		try
		{
			$q = $this->db->query( $qry );
		}
		catch(\PDOException $e) {
			$this->printPDOErrorMsg($e, $qry);
		}

		return $q;
	}

	public function printPDOErrorMsg(\PDOException $e, $qrystr, $debug = false)
	{
		$err_at = $e->getTrace();
		$error_id = $this->error_referer_id('DB.QUERY', $e->getCode());
		error_log($error_id.' -- $db->query() EXCEPTION: '.$e->getMessage() . ' @ ' .$err_at[1][file].':'.$err_at[1][line].' | Query String: '.$qrystr);
		if($debug){
			echo '<pre>';
			print_r($err_at);
			echo '</pre>';
		}
		die('Hiba történt az oldalon! <br><br>Referencia kód: '.$error_id.'<br><br><a href="http://arena.hu">arena.hu</a>');
	}

	private function error_referer_id( $type = 'db', $code )
	{
		return $type.'.'.$code.'.'.md5(microtime());
	}

	public function lastInsertId()
	{
		return $this->db->lastInsertId();
	}

	public function update ($table, $arg, $whr = ''){
		$q = "UPDATE $table SET ";
		$sm = '';

		foreach($arg as $ak => $av){
			$val = (is_null($av)) ? 'NULL' : "'".$av."'";

			$sm .= '`'.$ak.'` = '.$val.', ';
		}
		$sm = rtrim($sm,', ');
		$q .= $sm;
		if($whr != ""){
			$q .= " WHERE ".stripslashes($whr);
		}
		$q .= ';';
		//echo $q;
		$this->query($q);
		return true;
	}

	/**
	 * Tömeges beszúrás adatbázis táblába
	 * @param string $table DB Tábla
	 * @param array $head DB Tábla rekordok fejrész azonosítói
	 * @param array $data Beszúrandó adatok, a $head rendje szerint
	 * @param array $arg Paraméterek:
	 * 						- boolean debug Tesztelés végett, ha true, akkor a query nem fut le, de a return kimegy
	 * 						- int steplimit (50) Beállítható, hogy hány adat után indítson új query INSERT-et.
	 *
	 * @return string A Query szövege
	 */
	public function multi_insert( $table, $head = false, $data = false, $arg = array() ){

		$query 	= null;
		$debug_str = null;
		$header	= null;
		$value 	= null;
		$debug 	= ( !$arg[debug] ) ? false : true;

		if( $table == '' ) return false;
		if( !$head || !is_array( $head ) ) return false;
		if( !$data || !is_array( $data ) ) return false;

		foreach( $head as $h ){
			$header[] 	= $h;
		}

		$total_step = 0;
		$steplimit = ($arg['steplimit']) ?: 50;
		$step = 0;
		$step_rows = array();
		$step_breaks = 0;

		foreach( $data as $dh => $dv ){
			if ($steplimit <= $step) {
				$step_breaks++;
				$step = 0;
			}

			$v = null;

			$v = '(';
				foreach ( $dv as $vd ) {

					// IF NULL
					if( is_null( $vd ) ){
						$v .= 'NULL';
					}else if( is_bool( $vd ) ){
					// IF BOOLEAN
						if( $vd ){
							$v .= 1;
						}else{
							$v .= 0;
						}
					}else if( is_numeric( $vd ) ){
					// IF NUMBER
							$v .= "'".$vd."'";
					}else if( is_string( $vd ) ){
					// IF STRING
						$v .= "'".$vd."'";
					}

					$v .= ', ';
				}
				$v = rtrim( $v, ', ');
			$v .= ')';

			$step_rows[$step_breaks][] = $v;
			$step++;
			$total_step++;
		}

		$wk_step = 0;
		while ( $step_breaks >= 0 ) {
			$query = ' INSERT INTO '.$table.'(' . implode( ', ', $header ) . ') VALUES '. implode( ", ", $step_rows[$wk_step] ) ;

			if( $arg['duplicate_keys'] ) {
				$query .= " ON DUPLICATE KEY UPDATE ";
				$keys = '';
				foreach($arg['duplicate_keys'] as $key){
					$keys .= $key . " = VALUES(".$key."), ";
				}
				$keys = rtrim($keys,", ");
				$query .= $keys;
			}

			if( !$debug ){
				$this->query( $query );
			} else {
				$debug_str .= $query;
			}
			unset($query);
			$step_breaks--;
			$wk_step++;
		}

		return $debug_str;
	}

	public function multi_insert_new( $table, $head = false, $data = false, $arg = array() ){

		$query 	= null;
		$debug_str = null;
		$header	= null;
		$value 	= null;
		$debug 	= ( !$arg[debug] ) ? false : true;

		if( $table == '' ) return false;
		if( !$head || !is_array( $head ) ) return false;
		if( !$data || !is_array( $data ) ) return false;

		foreach( $head as $h ){
			$header[] 	= $h;
		}

		$total_step = 0;
		$steplimit = ($arg['steplimit']) ?: 50;
		$step = 0;
		$step_rows = array();
		$step_breaks = 0;

		foreach( $data as $dh => $dv ){
			if ($steplimit <= $step) {
				$step_breaks++;
				$step = 0;
			}

			$v = null;

			$v = '(';
				foreach ( $dv as $vd ) {

					// IF NULL
					if( is_null( $vd ) ){
						$v .= 'NULL';
					}else if( is_bool( $vd ) ){
					// IF BOOLEAN
						if( $vd ){
							$v .= 1;
						}else{
							$v .= 0;
						}
					}else if( is_numeric( $vd ) ){
					// IF NUMBER
							$v .= "'".$vd."'";
					}else if( is_string( $vd ) ){
					// IF STRING
						$v .= "'".$vd."'";
					}

					$v .= ', ';
				}
				$v = rtrim( $v, ', ');
			$v .= ')';

			$step_rows[$step_breaks][] = $v;
			$step++;
			$total_step++;
		}

		$wk_step = 0;
		while ( $step_breaks >= 0 ) {
			$query = ' INSERT INTO '.$table.'(' . implode( ', ', $header ) . ') VALUES '. implode( ", ", $step_rows[$wk_step] );
			if( $arg['duplicate_keys'] ) {
				$query .= " ON DUPLICATE KEY UPDATE ";
				$keys = '';
				foreach($arg['duplicate_keys'] as $key){
					$keys .= $key . " = VALUES(".$key."), ";
				}
				$keys = rtrim($keys,", ");
				$query .= $keys;
			}
			$query .= ";";
			echo $query.'<br><br>';
			if( !$debug ){
				//$this->query( $query );
			} else {
				$debug_str .= $query;
			}
			unset($query);
			$step_breaks--;
			$wk_step++;
		}

		return $debug_str;
	}

	public function insert( $table, $post ) {
		$fields = array();
		$values = array();

		// Kivételkezelés használata
		$this->db->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);

		foreach ($post as $fd => $v ) {
			$fields[] = $fd;
			$values[] = $v;
		}


		$q = $this->db->prepare("INSERT INTO $table(".implode($fields,', ').") VALUES(:".implode($fields,', :').")");

		$binds = array();
		foreach($values as $vk => $v){
			$binds[':'.$fields[$vk]] = (is_null($v)) ? null : stripslashes($v);
		}

		// Execute
		try{
			$q->execute($binds);
			return true;
		}catch(\PDOException $e){
			throw new \Exception($e->getMessage());
		}
	}

	public function q($query, $arg = array()){
		$query = trim($query);
		$back 		= array();
		$pages 		= array();
		$total_num 	= 0;
		$return_str = ($arg[ret_str]) ? $arg[ret_str] : 'ret';
		$current_page = ($arg['page']) ? $arg['page'] : \Helper::getLastParam();
		$get 		= count(\Helper::GET());
		if($get <= 2) $current_page = 1;
			$pages[current] = (is_numeric($current_page) && $current_page > 0) ? $current_page : 1;
		$limit 		= 50;
		$data 		= array();
		//////////////////////
		$query = preg_replace('/^SELECT/i', 'SELECT SQL_CALC_FOUND_ROWS ', $query);


		// LIMIT
		if($arg[limit]){
			$query = rtrim($query,";");
			$limit = (is_numeric($arg[limit]) && $arg[limit] > 0 && $arg[limit] != '') ? $arg[limit] : $limit;
			$l_min = 0;
			$l_min = $pages[current] * $limit - $limit;
			$query .= " LIMIT $l_min, $limit";
			$query .= ";";
		}

       	//echo $query.'<br><br>';

		$q = $this->query($query);

		if(!$q){
			error_log($query);
			//$back[$return_str][info][query][error] = $q->errorInfo();
		}

		if($q->rowCount() == 1 && !$arg[multi]){
			$data = $q->fetch(\PDO::FETCH_ASSOC);
		}else if($q->rowCount() > 1 || $arg[multi]){
			$data = $q->fetchAll(\PDO::FETCH_ASSOC);
		}

		$total_num 	=  $this->query("SELECT FOUND_ROWS();")->fetchColumn();
		$return_num = $q->rowCount();

		///
			$pages[max] 	= ($total_num == 0) ? 0 : ceil($total_num / $limit);
			$pages[limit] 	= ($arg[limit]) ? $limit : false;

		$back[$return_str][info][input][arg] 	= $arg;
		$back[$return_str][info][query][str] 	= $query;
		$back[$return_str][info][total_num] 	= (int)$total_num;
		$back[$return_str][info][return_num] 	= (int)$return_num;
		$back[$return_str][info][pages] 		= $pages;

		$back[$return_str][data] 	= $data;
		$back[data] 				= $data;
		return $back;
	}

	public function __destruct(){
		//echo '-DBCLOSE-';
		$this->db = null;
	}
}
?>
