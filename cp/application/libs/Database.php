<?
class Database{
	public $db = null;
	public function __construct(){
		
		try{
			$this->db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PW);
			$this->query("set names utf8");
			
			// Functions 
				// Full IMG
				$f = "
					DROP FUNCTION IF EXISTS FULLIMG;
					DELIMITER $$
					CREATE FUNCTION FULLIMG(img TEXT)
					  RETURNS TEXT
					  LANGUAGE SQL
					BEGIN
					  RETURN CONCAT('http://img.goldfishing.hu/',IF(img IS NULL,'products/noimg.png',img));
					END;
					$$
					DELIMITER ;";
				// Termék ár számoló
				$f .= "
				DROP FUNCTION IF EXISTS getTermekAr;
				DELIMITER $$
				CREATE FUNCTION getTermekAr(marka INT, ar FLOAT)
				  RETURNS FLOAT
				BEGIN
				  DECLARE felh_ar FLOAT DEFAULT ar;
				  DECLARE a FLOAT DEFAULT 0;
				  
				  SELECT fix_arres INTO a FROM `shop_markak` WHERE ID = marka;
				  
				  IF 
					a IS NOT NULL 
				  THEN
					SET felh_ar = (ar * ((a/100)+1));
				  ELSE
					SELECT arres INTO a FROM `shop_marka_arres_savok` WHERE markaID = marka and (ar >= ar_min and ar <= IF(ar_max IS NULL, 999999999999,ar_max));
					SET felh_ar = (ar * ((a/100)+1));
				  END IF;
				  
				  SET felh_ar = ROUND(felh_ar);
				  
				  RETURN felh_ar;
				END;
				$$
				DELIMITER ;
				";
				// EXPLODE ALternatív
				$f .= "
				DROP FUNCTION IF EXISTS GET_SPLITSTR;
				DELIMITER $$
				CREATE FUNCTION GET_SPLITSTR(str VARCHAR(255), delim VARCHAR(12), pos INT)
				RETURNS VARCHAR(255)
				BEGIN
					RETURN REPLACE(SUBSTRING(SUBSTRING_INDEX(str, delim, pos),
				       LENGTH(SUBSTRING_INDEX(str, delim, pos-1)) + 1),
				       delim, '');
				END;
				$$
				DELIMITER ;";
				
				$f .= "
				BEGIN
					DECLARE re BOOLEAN DEFAULT FALSE;
					DECLARE paramID VARCHAR(7);
					DECLARE pType VARCHAR(3);
					DECLARE pErtek VARCHAR(20);
					DECLARE minErtek INT;
					DECLARE maxErtek INT;
					
					SET paramID = GET_SPLITSTR(keyString,'_',1);
					SET pType 	= GET_SPLITSTR(keyString,'_',2);
					
					SELECT ertek INTO pErtek FROM `shop_termek_parameter` WHERE termekID = inTermekID and parameterID = paramID;
					
					SET minErtek = GET_SPLITSTR(pErtek,'-',1);
					SET maxErtek = GET_SPLITSTR(pErtek,'-',2);
					
					IF pType = 'min' THEN
						IF val <= minErtek THEN
							SET re = TRUE;
						ELSE
							SET re = FALSE;
						END IF;
					ELSEIF pType = 'max' THEN
						IF val >= maxErtek THEN
							SET re = TRUE;
						ELSE
							SET re = FALSE;
						END IF;
					END IF;
					
					RETURN re;
				END";
				
				$f .= "DROP FUNCTION IF EXISTS getTermekUrl;
DELIMITER $$
CREATE FUNCTION getTermekUrl(INtermekID INT, url VARCHAR(50))
RETURNS VARCHAR(200)
BEGIN
	DECLARE termekURL VARCHAR(200) DEFAULT NULL;
	DECLARE termekNev VARCHAR(200);
	DECLARE termekMarka VARCHAR(15);
	DECLARE termekID VARCHAR(11);
	
	SELECT t.ID,t.nev,TRIM(SUBSTRING_INDEX(m.neve,'::',1)) as markaNev INTO termekID,termekNev,termekMarka FROM `shop_termekek` as t LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka WHERE t.ID = INtermekID;
	
	SET termekNev = LOWER(termekNev);
			
	SET termekNev = REPLACE(termekNev,' ','-');
	SET termekNev = REPLACE(termekNev,',','');
	SET termekNev = REPLACE(termekNev,'á','a');
	SET termekNev = REPLACE(termekNev,'é','e');
	SET termekNev = REPLACE(termekNev,'í','i');
	SET termekNev = REPLACE(termekNev,'ú','u');
	SET termekNev = REPLACE(termekNev,'ü','u');
	SET termekNev = REPLACE(termekNev,'ű','u');
	SET termekNev = REPLACE(termekNev,'ö','o');
	SET termekNev = REPLACE(termekNev,'ő','o');
	SET termekNev = REPLACE(termekNev,'ó','o');
	SET termekNev = REPLACE(termekNev,'(','');
	SET termekNev = REPLACE(termekNev,')','');
	SET termekNev = REPLACE(termekNev,'\"','');
	SET termekNev = REPLACE(termekNev,'=','');
	SET termekNev = REPLACE(termekNev,'/','');
	SET termekNev = REPLACE(termekNev,'\\','');
	SET termekNev = REPLACE(termekNev,'?','');
	SET termekNev = REPLACE(termekNev,'&','');
	SET termekNev = REPLACE(termekNev,'!','');
	
	SET termekMarka = LOWER(termekMarka);
			
	SET termekMarka = REPLACE(termekMarka,' ','-');
	SET termekMarka = REPLACE(termekMarka,',','');
	SET termekMarka = REPLACE(termekMarka,'á','a');
	SET termekMarka = REPLACE(termekMarka,'é','e');
	SET termekMarka = REPLACE(termekMarka,'í','i');
	SET termekMarka = REPLACE(termekMarka,'ú','u');
	SET termekMarka = REPLACE(termekMarka,'ü','u');
	SET termekMarka = REPLACE(termekMarka,'ű','u');
	SET termekMarka = REPLACE(termekMarka,'ö','o');
	SET termekMarka = REPLACE(termekMarka,'ő','o');
	SET termekMarka = REPLACE(termekMarka,'ó','o');
	SET termekMarka = REPLACE(termekMarka,'(','');
	SET termekMarka = REPLACE(termekMarka,')','');
	SET termekMarka = REPLACE(termekMarka,'\"','');
	SET termekMarka = REPLACE(termekMarka,'=','');
	SET termekMarka = REPLACE(termekMarka,'/','');
	SET termekMarka = REPLACE(termekMarka,'\\','');
	SET termekMarka = REPLACE(termekMarka,'?','');
	SET termekMarka = REPLACE(termekMarka,'&','');
	SET termekMarka = REPLACE(termekMarka,'!','');
	
	SET termekURL = CONCAT(url,'/termek/',termekNev,'_-',termekID);
	
	RETURN termekURL;
END;
$$
DELIMITER ;";

			$f .= "DROP FUNCTION IF EXISTS getTermekParamString;
				DELIMITER $$
				CREATE FUNCTION getTermekParamString(INtermekID INT, INtermekKatID INT)
				RETURNS text
				BEGIN
					DECLARE paramStr text;
					SELECT GROUP_CONCAT(CONCAT(parameterID,':',ertek)) as paramString INTO paramStr FROM `shop_termek_parameter` as p LEFT OUTER JOIN shop_termek_kategoria_parameter as pk ON pk.ID = p.parameterID where p.termekID = INtermekID and p.katID = INtermekKatID ORDER BY pk.parameter ASC;
					RETURN paramStr;
				END;
				$$
				DELIMITER ;";
				// Szöveg hasonlóság százalék kimutató
				$f .= "DROP FUNCTION IF EXISTS szovegHasonlosag;
						DELIMITER $$
						CREATE FUNCTION szovegHasonlosag(mit text, mivel text)
							RETURNS FLOAT
						BEGIN
							DECLARE ret BOOLEAN DEFAULT FALSE;
							SET @mit_len 	= LENGTH(TRIM(REPLACE(mit,' ','')));
							SET @mivel_len 	= LENGTH(TRIM(REPLACE(mivel,' ','')));
							SET @mivel_count 	= 0;
							SET @mivel_index 	= 0;
							SET @egyezes 		= 0;
							SET @szazalek		= 0.0;
							SET @quit 			= FALSE;
							
							WHILE @mivel_index <= @mivel_len DO
								IF 
									SUBSTRING(TRIM(mivel), @mivel_index, 1) = SUBSTRING(TRIM(mit), @mivel_index, 1) AND @quit = FALSE
								THEN
									SET @egyezes = @egyezes + 1;
								ELSE
									SET @quit = TRUE;
								END IF;
								SET @mivel_index = @mivel_index + 1;
							END WHILE;
							SET @egyezes = @egyezes - 1;
							
							SET @szazalek = @egyezes / (@mit_len/100);
								
							RETURN @szazalek;
						END
						$$
						DELIMITER ;";
				$f .= "DROP FUNCTION IF EXISTS nextOrderID;
					DELIMITER $$
					CREATE FUNCTION nextOrderID()
					  RETURNS VARCHAR(15)
					BEGIN
					  DECLARE orderPrefix VARCHAR(5);
					  DECLARE newOrderId VARCHAR(15);
					  DECLARE mainKey VARCHAR(15);
					  DECLARE cYear VARCHAR(5);
					  DECLARE cMonth VARCHAR(5);
					  DECLARE prevKey INT DEFAULT 0;
					  DECLARE prevKeyStr VARCHAR(5) DEFAULT '0000';
					  
					  SET orderPrefix = 'ARENA';
					  SET cYear 	= SUBSTR(YEAR(NOW()),3);
					  SET cMonth 	= MONTH(NOW());
					  
					  IF LENGTH(cMonth) <= 1 THEN
						SET cMonth = CONCAT('0',cMonth);  
					  END IF;
					  
					  SET mainKey = CONCAT(orderPrefix,cYear,cMonth);
					  
					  SELECT REPLACE(azonosito,mainKey,'') INTO prevKey FROM `orders` WHERE azonosito LIKE CONCAT(mainKey,'%') ORDER BY idopont DESC LIMIT 0,1;
					  
					  SET prevKey = prevKey + 1;
					  
					  IF LENGTH(prevKey) = 1 THEN
						SET prevKeyStr = CONCAT('000',prevKey);
					  ELSEIF LENGTH(prevKey) = 2 THEN
						SET prevKeyStr = CONCAT('00',prevKey);
					  ELSEIF LENGTH(prevKey) = 3 THEN
						SET prevKeyStr = CONCAT('0',prevKey);
					  ELSE SET prevKeyStr = '0001';
					  END IF;
					  
					  SET newOrderId = CONCAT(mainKey,prevKeyStr);
					  RETURN newOrderId;
					END
					$$
					DELIMITER ;";
					
				$f .= "
				DROP FUNCTION IF EXISTS getTermekViewStat;
				DELIMITER $$
				CREATE FUNCTION `getTermekViewStat`(`INtermekID` INT, `INdateDist` INT(3)) RETURNS int(6)
					NO SQL
				BEGIN
					DECLARE viewNum INT;
					SELECT sum(me) INTO viewNum FROM `stat_nezettseg_termek` WHERE termekID = INTermekID and datediff(now(), datum) < INdateDist;
					RETURN viewNum;
				END
				$$
				DELIMITER ;";
				
				$f .= "
				DROP FUNCTION IF EXISTS isInMinMax;
				DELIMITER $$
				CREATE FUNCTION `isInMinMax`(`inTermekID` INT, `keyString` VARCHAR(25), `val` INT) RETURNS tinyint(1)
				BEGIN
					DECLARE re BOOLEAN DEFAULT FALSE;
					DECLARE paramID VARCHAR(7);
					DECLARE pType VARCHAR(3);
					DECLARE pErtek VARCHAR(20);
					DECLARE minErtek INT;
					DECLARE maxErtek INT;
					
					SET paramID = GET_SPLITSTR(keyString,'_',1);
					SET pType 	= GET_SPLITSTR(keyString,'_',2);
					
					SELECT ertek INTO pErtek FROM `shop_termek_parameter` WHERE termekID = inTermekID and parameterID = paramID;
					
					SET minErtek = GET_SPLITSTR(pErtek,'-',1);
					SET maxErtek = GET_SPLITSTR(pErtek,'-',2);
					
					IF minErtek != '' AND maxErtek != '' THEN
						IF pType = 'min' THEN
							IF val <= minErtek THEN
								SET re = TRUE;
							ELSE
								SET re = FALSE;
							END IF;
						ELSEIF pType = 'max' THEN
							IF val >= maxErtek THEN
								SET re = TRUE;
							ELSE
								SET re = FALSE;
							END IF;
						END IF;
					ELSE
						IF pType = 'min' THEN
							IF pErtek >= val THEN
								SET re = TRUE;
							ELSE
								SET re = FALSE;
							END IF;
						ELSEIF pType = 'max' THEN
							IF pErtek <= val THEN
								SET re = TRUE;
							ELSE
								SET re = FALSE;
							END IF;
						END IF;
					END IF;
					
					RETURN re;
				END 
				$$
				DELIMITER ;";
				
			
		}catch(PDOException $e){
			die($e->getMessage());
		}
	}

	public function query($qry)
	{
		return $this->db->query($qry);
	}
	
	public  function insert($table, $fields, $values){
		// Kivételkezelés használata
		$this->db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		
		
		$q = $this->db->prepare("INSERT INTO $table(".implode($fields,', ').") VALUES(:".implode($fields,', :').")");
			
		$binds = array();
		foreach($values as $vk => $v){
			$binds[':'.$fields[$vk]] = (is_null($v)) ? null : stripslashes($v);
		}
						
		// Execute
		try{
			$q->execute($binds);
			return true;
		}catch(PDOException $e){
			throw new Exception($e->getMessage());
		}
	}
	
	public function select($table, $select = false, $where = '', $fetchAll = false){
		$ret = array();
		$q  = "SELECT ";
		if(!$select){
			$q .= " * ";
		}else{
			$q .= rtrim(implode(',', $select),',');
		}
		$q .= " FROM $table ";
		if($where != ''){
			$where = stripslashes($where);
			$q .= ' WHERE '.$where;	
		}
		$d = $this->query($q);
		
		if($fetchAll){
			$ret = $d->fetchAll(PDO::FETCH_ASSOC);
		}else if(is_array($select) && count($select) == 1){
			$ret = $d->fetchColumn();
		}else{
			$ret = $d->fetch(PDO::FETCH_ASSOC);
		}
		
		return $ret;
	}
	
	public function update($table, $arg, $whr = ''){
		$q = "UPDATE $table SET ";
		$sm = '';
		
		foreach($arg as $ak => $av){
			$val = (is_null($av)) ? 'NULL' : (is_string($av)) ? "'".$av."'" : $av ;
			$sm .= '`'.$ak.'` = '.$val.', ';
		}
		$sm = rtrim($sm,', ');
		$q .= $sm;
		if($whr != ""){
			$q .= " WHERE ".stripslashes($whr);
		}
		$q .= ';';


		$this->query($q);
		return true;
	}
	
	public function delete($table, $whr){
		$this->db->query("DELETE FROM $table WHERE $whr;");
	}

	public function q($query, $arg = array()){
		$back 		= array();
		$pages 		= array();
		$total_num 	= 0;
		$return_str = ($arg[ret_str]) ? $arg[ret_str] : 'ret';
		$current_page = Helper::getLastParam();
		$get 		= count(Helper::GET());
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
        //echo $query;
		
		$q = $this->query($query);
		
		if(!$q){
			error_log($query);
			//$back[$return_str][info][query][error] = $q->errorInfo();
		}
		
		if($q->rowCount() == 1 && !$arg[multi]){
			$data = $q->fetch(PDO::FETCH_ASSOC);	
		}else if($q->rowCount() > 1 || $arg[multi]){
			$data = $q->fetchAll(PDO::FETCH_ASSOC);	
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
		$pages[current] = 1;
		$back[$return_str][info][pages] 		= $pages;
		
		$back[$return_str][data] 	= $data;
		$back[data] 				= $data;
		return $back;
	}

	public function crud($type, $data = array()){
		$back = false;
		switch($type){
			default:
				throw new Exception(__('Nincs ilyen művelet végrehajtó').': '.$type);
			break;
			case 'insert':
				$rows 	= $data[rows];
				$rows 	= explode(',', $rows);
				$values = $data[values];
				$values = explode('::', $values);
				
				if(empty($data)) throw new Exception(__('Művelet nem hajtódott végre. Nincs elküldött feldolgozandó adat!'));
				if(empty($data[table])) throw new Exception(__('Művelet nem hajtódott végre. Nincs kiválasztva cél táblázat!'));
				if(empty($rows)) throw new Exception(__('Művelet nem hajtódott végre. Nincs elküldött rekordkulcs azonosító!'));
				
				$back = $this->insert($data[table], $rows, $values);
			break;
			
			case 'update':
				$udata 	= $data[data];
				$udata 	= explode('::', $udata);
				$xdata 	= array();
					foreach($udata as $ud){
						$cdt = explode('=',$ud);
						$xdata[trim($cdt[0])] = trim($cdt[1]);
					} 				
				if(empty($data)) throw new Exception(__('Művelet nem hajtódott végre. Nincs elküldött feldolgozandó adat!'));
				if(empty($data[table])) throw new Exception(__('Művelet nem hajtódott végre. Nincs kiválasztva cél táblázat!'));
				if(empty($xdata)) throw new Exception(__('Művelet nem hajtódott végre. Nincsennek megadva a cserélendő rekordok!'));
				
				$back = $this->update($data[table],$xdata,$data[where]);
			break;
			
			case 'delete':
			break;
			
			case 'select':
				if(empty($data[table])) throw new Exception(__('Művelet nem hajtódott végre. Nincs kiválasztva cél táblázat!'));
				if($data[rows] == '') throw new Exception(__('Művelet nem hajtódott végre. Nincs kiválasztva visszatérő rekord!'));
				$rows = explode(',',$data[rows]);
				
				$loop = false;
				if($data[loop] || count($rows) > 1) $loop = true;
				
				
				$back = $this->select($data[table],$rows,$data[where], $loop);
			break;
		}
		return $back;
	}

	function __destruct(){
		$this->db = null;
	}
}
?>