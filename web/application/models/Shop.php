<?
class Shop_Model extends Model{

	function __construct(){
		parent::__construct();
	}

	public function getTotalTermekNum(){
		return $this->db->query("SELECT count(ID) FROM shop_termekek WHERE lathato = 1;")->fetchColumn();
	}

	public function listUjdonsagok($arg = array()){
		$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
		$apsz = $akcios_plus_szaz / 100 + 1;

		$q = "SELECT
			t.ID,
			tik.modszerID,
			tik.gyujtoID,
			t.termek_kategoria,
			getTermekAr(t.marka, t.brutto_ar) as brutto_ar,
			t.akcios_brutto_ar,
			t.akcios,
			t.marka,
			TRIM(SUBSTRING_INDEX(m.neve,'::',1)) as markaNev,
			t.ujdonsag,
			t.szuper_akcios,
			t.egyedi_ar,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			t.pickpackszallitas,
			IF(t.akcios,
				IF(t.akcios_egyedi_brutto_ar != 0,
					t.akcios_egyedi_brutto_ar,
					getTermekAr(t.marka, (t.brutto_ar * ".$apsz."))),
				getTermekAr(t.marka, t.brutto_ar)
			) as brutto_ar,
			IF(t.egyedi_ar IS NOT NULL,
				t.egyedi_ar,
				getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))
			) as ar,
			t.nev as termekNev,
			ta.elnevezes as keszlet,
			GROUP_CONCAT(CONCAT('p_',p.parameterID,':',p.ertek)) as paramErtek,
			FULLIMG(t.profil_kep) as profil_kep
		FROM shop_termekek as t
		LEFT OUTER JOIN shop_termek_parameter as p ON p.termekID = t.ID
		LEFT OUTER JOIN shop_termek_in_kategoria as tik ON tik.termekID = t.ID
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		LEFT OUTER JOIN shop_termek_allapotok as ta ON ta.ID = t.keszletID
		WHERE t.ID IS NOT NULL and
			t.lathato = 1 and
			t.ujdonsag IN (1)
		 ";


		 $q .= "GROUP BY t.ID ";
		 $q .= " ORDER BY RAND() ";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		//return false;
		return $ret;
	}

	public function listAkcios($arg = array()){
		$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
		$apsz = $akcios_plus_szaz / 100 + 1;

		$q = "SELECT
			t.ID,
			tik.modszerID,
			tik.gyujtoID,
			t.termek_kategoria,
			IF(t.akcios,
				IF(t.akcios_egyedi_brutto_ar != 0,
					t.akcios_egyedi_brutto_ar,
					getTermekAr(t.marka, (t.brutto_ar * ".$apsz."))),
				getTermekAr(t.marka, t.brutto_ar)) as brutto_ar,
			t.akcios_brutto_ar,
			t.akcios,
			t.marka,
			TRIM(SUBSTRING_INDEX(m.neve,'::',1)) as markaNev,
			t.ujdonsag,
			t.szuper_akcios,
			t.egyedi_ar,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			t.pickpackszallitas,
			IF(t.egyedi_ar IS NOT NULL, t.egyedi_ar, getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))) as ar,
			t.nev as termekNev,
			ta.elnevezes as keszlet,
			GROUP_CONCAT(CONCAT('p_',p.parameterID,':',p.ertek)) as paramErtek,
			FULLIMG(t.profil_kep) as profil_kep
		FROM shop_termekek as t
		LEFT OUTER JOIN shop_termek_parameter as p ON p.termekID = t.ID
		LEFT OUTER JOIN shop_termek_in_kategoria as tik ON tik.termekID = t.ID
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		LEFT OUTER JOIN shop_termek_allapotok as ta ON ta.ID = t.keszletID
		WHERE t.ID IS NOT NULL and
			t.lathato = 1 and
			t.akcios IN (1)
		 ";


		 $q .= "GROUP BY t.ID ";
		 $q .= " ORDER BY RAND() ";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		return $ret;
	}

	public function checkSzuperakciosTermekNum(){
		$num = 0;

		$num = $this->db->query("SELECT count(ID) FROM shop_termekek WHERE lathato = 1 and szuper_akcios = 1 and akcios = 1")->fetch(PDO::FETCH_COLUMN);

		return $num;
	}

	public function listTermekek($arg = array()){
		$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
		$apsz = $akcios_plus_szaz / 100 + 1;

		$filtered 	= false;
		$where 		= '';
		$arg[orderByPriority] = ($arg[parameterOrderByPriority])?true:false;

		$q = "SELECT
			t.ID,
			tik.modszerID,
			tik.gyujtoID,
			t.termek_kategoria,
			t.szuper_akcios_szazalek,
			t.utolso_darab,
			IF(t.akcios,
				IF(t.akcios_egyedi_brutto_ar != 0,
					t.akcios_egyedi_brutto_ar,
					getTermekAr(t.marka, (t.brutto_ar * ".$apsz."))),
				getTermekAr(t.marka, t.brutto_ar)
			) as brutto_ar,
			IF(t.egyedi_ar IS NOT NULL,
				t.egyedi_ar,
				getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))
			) as ar,
			t.akcios_brutto_ar,
			t.akcios,
			t.marka,
			TRIM(SUBSTRING_INDEX(m.neve,'::',1)) as markaNev,
			t.ujdonsag,
			t.szuper_akcios,
			t.egyedi_ar,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			t.pickpackszallitas,
			t.nev as termekNev,
			ta.elnevezes as keszlet,
			GROUP_CONCAT(CONCAT('p_',p.parameterID,':',p.ertek)) as paramErtek,
			FULLIMG(t.profil_kep) as profil_kep
		FROM shop_termekek as t
		LEFT OUTER JOIN shop_termek_parameter as p ON p.termekID = t.ID
		LEFT OUTER JOIN shop_termek_in_kategoria as tik ON tik.termekID = t.ID
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		LEFT OUTER JOIN shop_termek_allapotok as ta ON ta.ID = t.keszletID
		WHERE ";

		$where .= "
			t.ID IS NOT NULL and
			t.lathato = 1";
		$q .= $where;

		if($arg[modszer_kategoria]){
			$w = " and tik.modszerID = ".$arg[modszer_kategoria];
			$q .= $w;
			$where .= $w;
		}
		if($arg[gyujto_kategoria]){
			$w = " and tik.gyujtoID = ".$arg[gyujto_kategoria];
			$q .= $w;
			$where .= $w;
		}
		if($arg[termek_kategoria]){
			$w = " and t.termek_kategoria = ".$arg[termek_kategoria];
			$q .= $w;
			$where .= $w;
		}

		// Szűrők
		if(count($arg[filters]) > 0){
			extract($arg[filters]);
			if($fil_nev){
				 $q .= " and t.nev like '%".$fil_nev[0]."%' ";
			}
			if($fil_marka){
				$imk = implode(',',$fil_marka);
				$q .= " and t.marka IN (".$imk.") ";
			}
			if($fil_ujdonsag){
				 $q .= " and t.ujdonsag = 1 ";
			}
			if($fil_akcios){
				 $q .= " and t.akcios = 1 ";
			}
			if($fil_szuper_akcios){
				 $q .= " and t.szuper_akcios = 1 ";
			}
			if($fil_pickpackpontra_szallithato){
				 $q .= " and t.pickpackszallitas = 1 ";
			}
			if($fil_ar_min){
				 $q .= " and getTermekAr(t.marka,IF(t.egyedi_ar IS NOT NULL,t.egyedi_ar,IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))) >= ".$fil_ar_min[0];
			}
			if($fil_ar_max){
				 $q .= " and getTermekAr(t.marka,IF(t.egyedi_ar IS NOT NULL,t.egyedi_ar,IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))) <= ".$fil_ar_max[0];
			}
		}

		$q .= "
		GROUP BY
			t.ID
		";
		if(count($arg[filters]) > 0){
			$paramFilter = array();
			foreach($arg[filters] as $fk => $fv){
				if($fv) $filtered = true;
				if(strpos($fk,'fil_p_') === 0){
					if($fv)
					$paramFilter[$fk] = $fv;
				}
			}

			//Having
			if(count($paramFilter) > 0){
				$fkq = '';

				foreach($paramFilter as $pmfk => $pmfv){
					$key = str_replace('fil_p_','',$pmfk);
					if(strpos($key,'min') === false && strpos($key,'max') === false){
						$fkq .= " (";
							foreach($pmfv as $pv){
								$fkq .= "FIND_IN_SET('p_".$key.":".$pv."',GROUP_CONCAT(CONCAT('p_',p.parameterID,':',p.ertek))) or ";
							}
						$fkq = rtrim($fkq,' or ');
						$fkq .= ") and ";
					}else{
						$v = $pmfv[0];
						$fkq .= "isInMinMax(t.ID,'".$key."',".$v.") and ";

					}
				}
				$fkq = rtrim($fkq,' and ');
				if($fkq != ''){
					$q .= " HAVING ";
					$q .= $fkq;
				}
			}

		}

		// ORDER
		$order = 't.nev ASC';
		if($arg[order] == '' || $arg[order] == 'abc_asc'){
			$order = 'CONCAT(TRIM(SUBSTRING_INDEX(m.neve,\'::\',1))," ",t.nev) ASC';
		}else if($arg[order] == 'abc_desc'){
			$order = 'CONCAT(TRIM(SUBSTRING_INDEX(m.neve,\'::\',1))," ",t.nev) DESC';
		}else if($arg[order] == 'price_asc'){
			$order = 'ar ASC';
		}else if($arg[order] == 'price_desc'){
			$order = 'ar DESC';
		}else if($arg[order] == 'view_asc'){
			$order = 'getTermekViewStat(t.ID,90) ASC';
		}else if($arg[order] == 'view_desc'){
			$order = 'getTermekViewStat(t.ID,90) DESC';
		}

		if($arg[filters][fil_szuper]){
			$order = "t.szuper_akcios DESC,  ".$order;
		}

		$q .= " ORDER BY ".$order;

		//echo '<pre>'.$q.'</pre>';

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		$re = $ret;
		$re[info][filtered] 	= $filtered;

		$priceInfo = array(
			'min' => 0,
			'max' => 250000
		);
		$markak = array();

		$priceInfo = $this->getPrinceInfo($where, $priceInfo);
		$re[info][price][min] 	= $priceInfo[min];
		$re[info][price][max] 	= $priceInfo[max];
		$re[info][markak] 		= $this->getAwaiableMarkak($where, $markak);

		$r 		= array();
		$tidarr = array();

		foreach($data as $d){
			/*$arInfo 		= $this->getTermekArInfo($d[marka], $d[ar]);
			$d[ar] 			= $arInfo[ar];

			$brar 			= $this->getTermekArInfo($d[marka], $d[brutto_ar]);
			$d[brutto_ar] 	= $brar[ar];

			$akcios_arInfo 	= $this->getTermekArInfo($d[marka], $d[akcios_brutto_ar]);
			$d[akcios_brutto_ar] 	= $akcios_arInfo[ar];*/

			$params 		= $this->getTermekParameter($d[ID], $d[termek_kategoria], $arg);
			$d[params] 		= $params;

			$tidarr[] 	= $d[ID];
			$r[] 		= $d;
		}

		$re[termekIDs] = $tidarr;
		$re[data] = $r;

		return $re;
	}

	public function loadTermekSet($termek_id_str, $onTermekId = false, $arg = array()){
		$re 	= array();

		if( $termek_id_str == '' ){
			return false;
		}

		$q 		= "SELECT ID,nev,getTermekUrl(ID,'".DOMAIN."') as url FROM shop_termekek WHERE ID IN(".$termek_id_str.")";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		$edata 	= array();
		$ex 	= explode(",",$termek_id_str);
		foreach($ex as $e){
			$edata[] = $data[Helper::getFromArrByAssocVal($data,'ID',$e)];
		}

		$data = $edata;

		$itemSetNums 	= count($data);
		$lastPos 		= $itemSetNums-1;

		if($onTermekId && $itemSetNums > 1){
			$currentPosition = Helper::getFromArrByAssocVal($data,'ID',$onTermekId);
			if($currentPosition == 0){
				$prev = $lastPos;
				$next = $currentPosition+1;
			}else if($currentPosition > 0 && $currentPosition < $lastPos){
				$prev = $currentPosition-1;
				$next = $currentPosition+1;
			}else if($currentPosition == $lastPos){
				$prev = $currentPosition-1;
				$next = 0;
			}
		}

		$re[set] 	= $data;
		$re[prev] 	= $prev;
		$re[next] 	= $next;


		return $re;
	}

	public function getAwaiableMarkak($where, $arr){
		$q = "SELECT
			TRIM(SUBSTRING_INDEX(m.neve,'::',1)) as markaNev
		FROM shop_termekek as t
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		LEFT OUTER JOIN shop_termek_in_kategoria as tik ON tik.termekID = t.ID
		WHERE ".$where."
		GROUP BY markaNev";
		$arg[multi] = '1';
		extract($this->db->q($q,$arg));
		foreach($data as $d){
			$arr[] = $d[markaNev];
		}
		return $arr;
	}
	public function getSlideShowItems($arg = array()){
		$q = "SELECT * FROM slideshow WHERE lathato = 1 ORDER BY sorrend ASC";
		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		return $data;
	}
	public function getHasonloTermekek($searchString, $arg = array()){
		$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
		$apsz = $akcios_plus_szaz / 100 + 1;

		$tures 		= 25;
		$isParamSrc = (empty($arg[key_params])) ? false : true;

		$q = "SELECT
			t.ID,
			t.nev,
			t.akcios,
			t.szuper_akcios,
			t.szuper_akcios_szazalek,
			t.utolso_darab,
			IF(t.akcios,
				IF(t.akcios_egyedi_brutto_ar != 0,
					t.akcios_egyedi_brutto_ar,
					getTermekAr(t.marka, (t.brutto_ar * ".$apsz."))),
				getTermekAr(t.marka, t.brutto_ar)
			) as brutto_ar,
			IF(t.egyedi_ar IS NOT NULL,
				t.egyedi_ar,
				getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))
			) as ar,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			FULLIMG(t.profil_kep) as profil_kep,
			TRIM(SUBSTRING_INDEX(m.neve,'::',1)) as markaNev,
			szovegHasonlosag(t.nev,'".$searchString."') as same,
			GROUP_CONCAT(CONCAT('p_',p.parameterID,':',p.ertek)) as paramErtek
		FROM `shop_termekek` as t
		LEFT OUTER JOIN shop_termek_parameter as p ON p.termekID = t.ID
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		WHERE
			t.ID IS NOT NULL and
			t.lathato = 1 ";
		if($arg[excID] != ''){
			$q .= " and t.ID != ".$arg[excID];
		}
		if(!$isParamSrc){
			$q .= "
				and szovegHasonlosag(t.nev,'".$searchString."') < 100 and szovegHasonlosag(nev,'".$searchString."') > $tures
			";
		}


		$q.= " GROUP BY t.ID ";
		//Having
		$paramFilter = $arg[key_params];
		if(count($paramFilter) > 0){
			$fkq = '';

			foreach($paramFilter as $pf){
				$fkq .= " FIND_IN_SET('p_".$pf[parameterID].":".$pf[ertek]."',GROUP_CONCAT(CONCAT('p_',p.parameterID,':',p.ertek))) and ";
			}
			$fkq = rtrim($fkq,' and ');
			if($fkq != ''){
				$q .= " HAVING ";
				$q .= $fkq;
			}
		}

		$q .= "ORDER BY same DESC, t.nev ASC ";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		$keys = array();

		foreach($data as $d){
			$keys[] = $d[ID];
		}
		$ret[keys] = $keys;
		return $ret;
	}
	public function getFreshTermekek($arg = array()){
		$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
		$apsz = $akcios_plus_szaz / 100 + 1;

		$q = "SELECT
			t.ID,
			t.nev,
			t.akcios,
			t.szuper_akcios,
			t.ujdonsag,
			t.szuper_akcios_szazalek,
			t.utolso_darab,
			IF(t.akcios,
				IF(t.akcios_egyedi_brutto_ar != 0,
					t.akcios_egyedi_brutto_ar,
					getTermekAr(t.marka, (t.brutto_ar * ".$apsz."))),
				getTermekAr(t.marka, t.brutto_ar)
			) as brutto_ar,
			IF(t.egyedi_ar IS NOT NULL,
				t.egyedi_ar,
				getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))
			) as ar,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			FULLIMG(t.profil_kep) as profil_kep,
			TRIM(SUBSTRING_INDEX(m.neve,'::',1)) as markaNev
		FROM shop_termekek as t
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		WHERE t.ID IS NOT NULL and
		t.lathato = 1
		ORDER BY t.letrehozva DESC";
		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		$keys = array();

		foreach($data as $d){
			$keys[] = $d[ID];
		}
		$ret[keys] = $keys;
		return $ret;
	}
	public function getMenu($key){
		$q = "SELECT nev,url FROM menu WHERE gyujto = '$key' ORDER BY sorrend ASC;";
		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		return $data;
	}
	public function getSzuperakciosTermekek($arg = array()){
		$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
		$apsz = $akcios_plus_szaz / 100 + 1;
		$q = "SELECT
			t.ID,
			t.nev,
			t.akcios,
			t.szuper_akcios,
			t.ujdonsag,
			t.szuper_akcios_szazalek,
			t.utolso_darab,
			IF(t.akcios,
				IF(t.akcios_egyedi_brutto_ar != 0,
					t.akcios_egyedi_brutto_ar,
					getTermekAr(t.marka, (t.brutto_ar * ".$apsz."))),
				getTermekAr(t.marka, t.brutto_ar)
			) as brutto_ar,
			IF(t.egyedi_ar IS NOT NULL,
				t.egyedi_ar,
				getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))
			) as ar,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			FULLIMG(t.profil_kep) as profil_kep,
			TRIM(SUBSTRING_INDEX(m.neve,'::',1)) as markaNev
		FROM shop_termekek as t
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		WHERE t.ID IS NOT NULL and
		t.lathato = 1 and
		t.szuper_akcios = 1
		ORDER BY getTermekAr(t.marka,IF(t.egyedi_ar IS NOT NULL,t.egyedi_ar,IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))) ASC";
		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		$keys = array();

		foreach($data as $d){
			$keys[] = $d[ID];
		}
		$ret[keys] = $keys;
		return $ret;
	}
	public function getTermekAdat($id, $arg = array()){
		$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
		$apsz = $akcios_plus_szaz / 100 + 1;

		$re = array();
		if($id == '') return false;
		// getTermekAr(t.marka,IF(t.egyedi_ar IS NOT NULL,t.egyedi_ar,IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))) as ar,
		$q = "SELECT
			t.* ,
			m.elorendelheto,
			FULLIMG(t.profil_kep) as profil_kep,
			getTermekUrl(t.ID, '".DOMAIN."') as url,
			IF(t.akcios,
				IF(t.akcios_egyedi_brutto_ar != 0,
					t.akcios_egyedi_brutto_ar,
					getTermekAr(t.marka, (t.brutto_ar * ".$apsz."))),
				getTermekAr(t.marka, t.brutto_ar)
			) as brutto_ar,
			IF(t.egyedi_ar IS NOT NULL,
				t.egyedi_ar,
				getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))
			) as ar,
			TRIM(SUBSTRING_INDEX(m.neve,'::',1)) as markaNev,
			ta.elnevezes as allapotNev,
			ts.elnevezes as szallitasNev,
			tk.neve as kategoriaNev
		FROM shop_termekek as t
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		LEFT OUTER JOIN shop_termek_allapotok as ta ON ta.ID = t.keszletID
		LEFT OUTER JOIN shop_szallitasi_ido as ts ON ts.ID = t.szallitasID
		LEFT OUTER JOIN shop_termek_kategoriak as tk ON tk.ID = t.termek_kategoria
		WHERE
			t.ID = $id
		";

		$data = $this->db->query($q);

		if($data->rowCount() == 0) return false;

		$re[data] = $data->fetch(PDO::FETCH_ASSOC);
		$re[params] = $this->getTermekParameter($id,$re[data][termek_kategoria]);
		$re[key_params] = $this->getKeyParameter($re[params]);
		$re[images] = $this->getAllTermekImg($id);

		return $re;
	}
	function getKeyParameter($params_arry){
		$ret = array();

		if(count($params_arry) == 0) return $ret;

		foreach($params_arry as $p){
			if($p[kulcs] == '1'){
				$ret[] = $p;
			}
		}

		return $ret;
	}
	function getAllTermekImg($termekID){
		$imgs = array();
		if($termekID == '') return $imgs;
		$q = "SELECT FULLIMG(kep) as kep FROM shop_termek_kepek WHERE termekID = $termekID ORDER BY sorrend ASC, kep ASC";
		extract($this->db->q($q,array('multi'=> '1')));

		foreach($data as $i){
			$imgs[] = $i[kep];
		}

		return $imgs;
	}
	public function getAkciosTermekek($arg = array()){
	$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
	$apsz = $akcios_plus_szaz / 100 + 1;

		$q = "SELECT
			t.ID,
			t.nev,
			t.akcios,
			t.szuper_akcios,
			t.ujdonsag,
			t.szuper_akcios_szazalek,
			t.utolso_darab,
			IF(t.akcios,
				IF(t.akcios_egyedi_brutto_ar != 0,
					t.akcios_egyedi_brutto_ar,
					getTermekAr(t.marka, (t.brutto_ar * ".$apsz."))),
				getTermekAr(t.marka, t.brutto_ar)
			) as brutto_ar,
			IF(t.egyedi_ar IS NOT NULL,
				t.egyedi_ar,
				getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))
			) as ar,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			FULLIMG(t.profil_kep) as profil_kep,
			TRIM(SUBSTRING_INDEX(m.neve,'::',1)) as markaNev
		FROM shop_termekek as t
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		WHERE t.ID IS NOT NULL and
		t.lathato = 1 and
		t.akcios = 1 and
		t.szuper_akcios = 0 and
		t.ujdonsag = 0
		ORDER BY getTermekAr(t.marka,IF(t.egyedi_ar IS NOT NULL,t.egyedi_ar,IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))) ASC";
		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		$keys = array();

		foreach($data as $d){
			$keys[] = $d[ID];
		}
		$ret[keys] = $keys;
		return $ret;
	}
	public function getUjdonsagTermekek($arg = array()){
		$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
		$apsz = $akcios_plus_szaz / 100 + 1;

		$q = "SELECT
			t.ID,
			t.nev,
			t.akcios,
			t.szuper_akcios,
			t.ujdonsag,
			t.szuper_akcios_szazalek,
			t.utolso_darab,
			IF(t.akcios,
				IF(t.akcios_egyedi_brutto_ar != 0,
					t.akcios_egyedi_brutto_ar,
					getTermekAr(t.marka, (t.brutto_ar * ".$apsz."))),
				getTermekAr(t.marka, t.brutto_ar)
			) as brutto_ar,
			IF(t.egyedi_ar IS NOT NULL,
				t.egyedi_ar,
				getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))
			) as ar,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			FULLIMG(t.profil_kep) as profil_kep,
			TRIM(SUBSTRING_INDEX(m.neve,'::',1)) as markaNev
		FROM shop_termekek as t
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		WHERE t.ID IS NOT NULL and
		t.lathato = 1 and
		t.ujdonsag = 1
		ORDER BY rand() DESC";
		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		$keys = array();

		foreach($data as $d){
			$keys[] = $d[ID];
		}
		$ret[keys] = $keys;
		return $ret;
	}
	public function getTermekParamList(){
		$r = array();
		$q = "SELECT ID,parameter,mertekegyseg FROM shop_termek_kategoria_parameter";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		foreach($data as $d){
			$r[$d[ID]] = $d;
		}

		return $r;
	}
	public function getMegrendelesAllapotok(){
		$q = "SELECT * FROM order_allapot ORDER BY sorrend ASC";

		extract($this->db->q($q,array('multi'=>'1')));

		$back = array();
		foreach($data as $d){
			$back[$d[ID]] = $d;
		}

		return $back;
	}
	public function kereses($arg = array()){
		$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
		$apsz = $akcios_plus_szaz / 100 + 1;

		$back 	= array();
		$params = $this->getTermekParamList();
		$arg[orderByPriority] = true;

		$q = "SELECT
			t.ID,
			tik.modszerID,
			tik.gyujtoID,
			t.termek_kategoria,
			t.szuper_akcios_szazalek,
			t.utolso_darab,
			IF(t.akcios,
				IF(t.akcios_egyedi_brutto_ar != 0,
					t.akcios_egyedi_brutto_ar,
					getTermekAr(t.marka, (t.brutto_ar * ".$apsz."))),
				getTermekAr(t.marka, t.brutto_ar)
			) as brutto_ar,
			IF(t.egyedi_ar IS NOT NULL,
				t.egyedi_ar,
				getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))
			) as ar,
			t.akcios_brutto_ar,
			t.termek_kategoria,
			t.akcios,
			t.marka,
			TRIM(SUBSTRING_INDEX(m.neve,'::',1)) as markaNev,
			t.ujdonsag,
			t.szuper_akcios,
			t.egyedi_ar,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			t.pickpackszallitas,
			t.nev as termekNev,
			ta.elnevezes as keszlet,
			tk.neve as kategoriaNev,
			getTermekParamString(t.ID,t.termek_kategoria) as params,
			FULLIMG(t.profil_kep) as profil_kep
		FROM shop_termekek as t
		LEFT OUTER JOIN shop_termek_parameter as p ON p.termekID = t.ID
		LEFT OUTER JOIN shop_termek_in_kategoria as tik ON tik.termekID = t.ID
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		LEFT OUTER JOIN shop_termek_allapotok as ta ON ta.ID = t.keszletID
		LEFT OUTER JOIN shop_termek_kategoriak as tk ON tk.ID = t.termek_kategoria
		WHERE
		";
		$q .= "
			t.ID is NOT NULL and
			t.lathato = 1
		";
		if($arg[search] != ''){
			$q .= " and CONCAT(TRIM(SUBSTRING_INDEX(m.neve,'::',1)),' ',t.nev) LIKE '%".$arg[search]."%' ";
		}
		if($arg[inKat][0]){
			$q .= " and t.termek_kategoria IN (".implode(",",$arg[inKat]).") ";
		}
		if($arg[inMarka][0]){
			$q .= " and t.marka IN (".implode(",",$arg[inMarka]).") ";
		}

		if($arg[akcio] == '1'){
			$q .= " and t.akcios = 1 and t.ujdonsag = 0 and t.szuper_akcios = 0";
		}
		if($arg[superakcio] == '1'){
			$q .= " and (t.szuper_akcios = 1 and t.akcios = 1)";
		}
		if($arg[ujdonsag] == '1'){
			$q .= " and t.ujdonsag = 1";
		}

		$q .= " GROUP BY t.ID ";

		// ORDER
		$order = 't.nev ASC';
		if($arg[order] == '' || $arg[order] == 'abc_asc'){
			$order = 't.nev ASC';
		}else if($arg[order] == 'abc_desc'){
			$order = 't.nev DESC';
		}else if($arg[order] == 'price_asc'){
			$order = 'ar ASC';
		}else if($arg[order] == 'price_desc'){
			$order = 'ar DESC';
		}

		$q .= " ORDER BY ".$order;

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));
		$r = array();

		foreach($data as $d){
			$inp = $this->getTermekParameter($d[ID],$d[termek_kategoria],$arg);

			$d[params] = $inp;
			$r[] = $d;
		}

		$ret[data] = $r;
		$back = $ret;

		return $back;
	}
	private function getPrinceInfo($where, $info_arry){
		if($where == '')return $info_arry;
		if(count($info_arry) == 0) return $info_arry;
		$min = 0;
		$max = 0;

		$q = "SELECT getTermekAr(t.marka,IF(t.egyedi_ar IS NOT NULL,t.egyedi_ar,IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))) as ar FROM shop_termekek as t LEFT OUTER JOIN shop_termek_in_kategoria as tik ON tik.termekID = t.ID WHERE ".$where." GROUP BY t.ID";
		extract($this->db->q($q));

		foreach($data as $d){
			$ar = $d[ar];

			if($min == 0) $min = $ar;
			if($max == 0) $max = $ar;

			if($ar < $min) $min = $ar;
			if($ar > $max) $max = $ar;
		}

		$info_arry[min] = $min;
		$info_arry[max] = $max;

		return $info_arry;
	}

	/**
	* Nem előrendelhető termékek eltávolítása a felhasználó kosarából
	*
	* @param integer $mid Felhasználó gép ID-ja. Hívás Helper::getMachineID()
	*
	* @return void
	*/
	public function clearCartForPreorder ($mid)
	{
		if($mid == '') return false;

		$remove = array();

		//Kosár
		$arg 	= array( 'multi' => 1 );
		$q 		= "
		SELECT 				c.termekID,
							m.elorendelheto
		FROM 				shop_kosar as c
		LEFT OUTER JOIN 	shop_termekek as t ON t.ID = c.termekID
		LEFT OUTER JOIN 	shop_markak as m ON m.ID = t.marka
		WHERE  				c.gepID = '$mid'
		";
		extract($this->db->q($q, $arg));

		foreach( $data as $d ){

			if( $d[elorendelheto] == '0' ){
				$remove[] = $d[termekID];
				$this->db->query("DELETE FROM shop_kosar WHERE gepID = '$mid' and termekID = ".$d[termekID]);
			}

		}

		return $remove;
	}

	public function addItemToCart($mid, $termekID){
		if($mid == '')
			throw new Exception('Nem sikerült hozzáadni a terméket a kosárhoz. Frissítse le az oldalt és próbálja újra!');
		if($termekID == '')
			throw new Exception('Termék azonosító hiányzik!');

		$c = $this->db->query("SELECT me FROM shop_kosar WHERE termekID = $termekID and gepID = $mid;")->fetch(PDO::FETCH_ASSOC);

		$this->db->query("UPDATE shop_kosar SET me = me + 1  WHERE termekID = $termekID and gepID = $mid");
	}
	public function clearCart($mid){
		if($mid == '') return false;
		$this->db->query("DELETE FROM shop_kosar WHERE gepID = $mid");
	}
	public function removeItemFromCart($mid, $termekID){
		if($mid == '')
			throw new Exception('Nem sikerült hozzáadni a terméket a kosárhoz. Frissítse le az oldalt és próbálja újra!');
		if($termekID == '')
			throw new Exception('Termék azonosító hiányzik!');

		$c = $this->db->query("SELECT me FROM shop_kosar WHERE termekID = $termekID and gepID = $mid;")->fetch(PDO::FETCH_ASSOC);

		$cn = $c[me];

		if($cn == 1){
			$this->db->query("DELETE FROM shop_kosar WHERE termekID = $termekID and gepID = $mid");
		}else if($cn > 1){
			$this->db->query("UPDATE shop_kosar SET me = me - 1  WHERE termekID = $termekID and gepID = $mid");
		}
	}
	public function addToCart($mid, $termekID, $me){
		if($mid == '')
		throw new Exception('Nem sikerült hozzáadni a terméket a kosárhoz. Frissítse le az oldalt és próbálja újra!');

		$ci = $this->db->query("SELECT ID FROM shop_kosar WHERE termekID = $termekID and gepID = $mid;");

		if($ci->rowCount() == 0){

			//$c = $ci->fetch(PDO::FETCH_ASSOC);

			$this->db->insert('shop_kosar',
				array('termekID','gepID','me'),
				array($termekID,$mid,$me)
			);

		}else{
			$this->db->query("UPDATE shop_kosar SET me = me + $me  WHERE termekID = $termekID and gepID = $mid");
		}

	}
	public function removeFromCart($mid, $termekID){
		if($mid == '')
			throw new Exception('Nem sikerült hozzáadni a terméket a kosárhoz. Frissítse le az oldalt és próbálja újra!');
		if($termekID == '')
			throw new Exception('Termék azonosító hiányzik!');
		$q = "DELETE FROM shop_kosar WHERE termekID = $termekID and gepID = $mid";
		$this->db->query($q);
	}
	public function getSzallitasiModok(){
		$q = "SELECT * FROM shop_szallitasi_mod";
		extract($this->db->q($q,array('multi'=> '1')));
		return $data;
	}
	public function cartInfo($mid){
		$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
		$apsz = $akcios_plus_szaz / 100 + 1;

		$re 		= array();
		$itemNum 	= 0;
		$totalPrice = 0;
		$has_requst_price = 0;

		$q = "SELECT
			c.ID,
			c.termekID,
			c.me,
			c.hozzaadva,
			t.pickpackszallitas,
			t.szuper_akcios,
			t.szuper_akcios_szazalek,
			m.elorendelheto,
			CONCAT(m.neve,' ',t.nev) as termekNev,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			ta.elnevezes as allapot,
			FULLIMG(t.profil_kep) as profil_kep,
			IF(t.egyedi_ar IS NOT NULL, t.egyedi_ar, getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))) as ar,
			(IF(t.egyedi_ar IS NOT NULL, t.egyedi_ar, getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))) * c.me) as sum_ar,
			szid.elnevezes as szallitasIdo
		FROM shop_kosar as c
		LEFT OUTER JOIN shop_termekek AS t ON t.ID = c.termekID
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		LEFT OUTER JOIN shop_termek_allapotok as ta ON ta.ID = t.keszletID
		LEFT OUTER JOIN shop_szallitasi_ido as szid ON szid.ID = t.szallitasID
		WHERE c.gepID = $mid";
		$arg[multi] = '1';
		extract($this->db->q($q, $arg));
		$dt = array();
		foreach($data as $d){

			if( !is_null( $d[szuper_akcios_szazalek] ) ){
				$d[ar] 		= -1;
				$d[sum_ar] 	= -1;
				$itemNum 	+= $d[me];
				$totalPrice += 0;
				$has_requst_price++;
			}else{
				$itemNum 	+= $d[me];
				$totalPrice += $d[sum_ar];
			}

			$dt[] = $d;
		}

		$re[itemNum]			= $itemNum;
		$re[totalPrice]			= $totalPrice;
		$re[totalPriceTxt]		= number_format($totalPrice,0,""," ");
		$re[items] 				= $dt;
		$re[has_request_price] 	= $has_requst_price;

		return $re;
	}

	public function getFilterString($filter_arry){
		$ret = '';

		if(count($filter_arry) > 0){
			$ret .= '?';
			foreach($filter_arry as $fk => $fv){
				if($fv){
					$n = count($fv);

					if($n == 1){
						$d = $fv[0];
					}else{
						$d = implode(urlencode(","),$fv);
					}
					$ret .= $fk.'='.$d.'&amp;';
				}
			}
		}
		$ret = rtrim($ret,'&amp;');

		return $ret;
	}

	public function getFilters($get, $prefix){
		$re = array();

		foreach($get as $gk => $gv){
			if(strpos($gk,$prefix.'_') === 0){
				/*if(strpos($gv,',') !== false){
					$x = false;
					if($gv != ''){
						$x = explode(',',rtrim($gv,','));
					}
					$g = $x;
				}else{
					$g = $gv;
				}*/
				$x = false;
				if($gv != ''){
					$x = explode(',',rtrim($gv,','));
				}
				$g = $x;
				if($g == '') $g = false;
				$re[$gk] = $g;
			}
		}

		return $re;
	}

	function getTermekParameter($termekID, $katID = false, $arg = array()){
		$q = "SELECT
			p.* ,
			pm.parameter as neve,
			pm.mertekegyseg as me,
			pm.kulcs
		FROM shop_termek_parameter as p
		LEFT OUTER JOIN shop_termek_kategoria_parameter as pm ON pm.ID = p.parameterID
		 WHERE p.termekID = $termekID ";
		if($katID){
			$q .= " and katID = $katID ";
		}
		if($arg[orderByPriority]){
			$q .= "
			ORDER BY pm.priority ASC, pm.parameter ASC";
		}else{
			$q .= "
			ORDER BY pm.parameter ASC";
		 }
		extract($this->db->q($q,array('multi'=> '1')));
		$back = array();
		foreach($data as $d){
			$back[$d[parameterID]] = $d;
		}
		return $back;
	}

	private function getTermekAr($markaID, $bruttoAr){
		$ari =  $this->getTermekArInfo($markaID, $bruttoAr);
		return $ari[ar];
	}

	public function getMarkak($arg = array()){
		$rows 	=  'ID, TRIM(SUBSTRING_INDEX(neve,"::",1)) as neve,GROUP_CONCAT(ID) as IDS, arres_mod, fix_arres, brutto';
		$q 		= "SELECT $rows FROM shop_markak GROUP BY TRIM(SUBSTRING_INDEX(neve,'::',1));";

		extract($this->db->q($q,array('multi'=> '1')));

		$back = array();
		foreach($data as $d){
			$back[] = $d;
		}
		return $back;
	}

	public function getParameterHint($modszer = false, $gyujto = false){
		$hints = array();

		$h = "SELECT
			GROUP_CONCAT(CONCAT('p_',p.parameterID,':',p.ertek)) as paramErtek
		FROM `shop_termekek` as t
		LEFT OUTER JOIN shop_termek_parameter as p ON p.termekID = t.ID
		LEFT OUTER JOIN shop_termek_in_kategoria as tik ON tik.termekID = t.ID
		WHERE tik.modszerID = $modszer
		GROUP BY t.ID";

		extract($this->db->q($h,array('multi'=> '1')));
		$box = array();
		foreach($data as $d){
			$exp_one = explode(",",$d[paramErtek]);
			foreach($exp_one as $e){
				$exp_two 	= explode(":",$e);
				$key 		= str_replace('p_','',$exp_two[0]);

				if(!in_array($exp_two[1],$box[$key])){
					$box[$key][] = $exp_two[1];
				}

			}
		}
		$data = $box;

		$hints = $data;

		return $hints;
	}

	private function getTermekArInfo($markaID, $bruttoAr){
		$re 	  = array();
		$re[info] =  array();
		$re[arres] = 0;
		$re[ar]   =  $bruttoAr;

		// Márka adatok
		$marka = $this->db->query("SELECT fix_arres FROM shop_markak WHERE ID = $markaID")->fetch(PDO::FETCH_ASSOC);

		if(!is_null($marka[fix_arres])){
		// Fix árrés
			$re[info] 	= 'FIX : '.$marka[fix_arres].'%';
			$re[arres] 	= $marka[fix_arres];
			$re[ar] 	= round($bruttoAr * ($marka[fix_arres]/100+1));
		}else{
		// Sávos árrés
			$savok = $this->db->query("SELECT ar_min, ar_max, arres FROM shop_marka_arres_savok WHERE markaID = $markaID ORDER BY ar_min ASC")->fetchAll(PDO::FETCH_ASSOC);
			foreach($savok as $s){
				$min = $s[ar_min];
				$max = $s[ar_max];
				$max = (is_null($max)) ? 999999999999999 : $max;

				if($bruttoAr >= $min && $bruttoAr <= $max){
					$re[info] 	= $min.' - '.$max.' : '.$s[arres].'%';
					$re[arres] 	= $s[arres];
					$re[ar] 	= round($bruttoAr * ($s[arres]/100+1));
					break;
				}else{
					$re[info] 	= $min.' - '.$max.' : '.$s[arres].'%';
					$re[arres] 	= $s[arres];
					$re[ar] 	= round($bruttoAr * ($s[arres]/100+1));
				}

			}
		}

		return $re;
	}


	public function getTermekKeys(&$view){
		$get 	= Helper::GET();
		$szurok = array();

		if($get[0] == 'termekek'){
			$view->kategoria->modszer 	= $this->getStringID($get[1]);
			$view->kategoria->gyujto 	= $this->getStringID($get[2]);
			$view->kategoria->termek 	= $this->getStringID($get[3]);

			$view->kategoria->modszer_text 	= $this->getKategoriaStringByID('modszer', $view->kategoria->modszer);
			$view->kategoria->gyujto_text 	= $this->getKategoriaStringByID('gyujto', $view->kategoria->gyujto);
			$view->kategoria->termek_text 	= $this->getKategoriaStringByID('termek', $view->kategoria->termek);

			if($view->kategoria->modszer && ($view->kategoria->gyujto || $view->kategoria->termek))
				$navi =  $view->kategoria->modszer_text;
			if($view->kategoria->modszer && $view->kategoria->gyujto && $view->kategoria->termek)
				$navi .=  ' / '.$view->kategoria->gyujto_text;


			if($view->kategoria->modszer)
			$onKat = $view->kategoria->modszer_text;
			if($view->kategoria->gyujto)
			$onKat = $view->kategoria->gyujto_text;
			if($view->kategoria->termek)
			$onKat = $view->kategoria->termek_text;

			if($view->kategoria->termek){
				$szurok = $this->getParamsForFilter($view->kategoria->termek,array(
					'modszer' 	=> $view->kategoria->modszer,
					'gyujto' 	=> $view->kategoria->gyujto
				));
			}

			$view->szuroParams 				= $szurok;
			$view->markak 					= $this->getMarkak(array('row' => '*'));
			$view->kategoria->navi_text 	= ($navi == '') ? 'Kérjük, hogy válasszon módszert és kategóriákat a pontosabb szűrésért.' : $navi;
			$view->kategoria->ON 			= ($onKat == '') ? 'Termékek' : $onKat;

		}else if($get[0] == 't'){
			$view->kategoria->modszer 	= $_COOKIE[__list_modszer];
			$view->kategoria->gyujto 	= $_COOKIE[__list_gyujto];
			$view->kategoria->termek 	= $_COOKIE[__list_kategoria];

			$view->termekID = Product::getTermekIDFromUrl();
		}

		return $view;
	}
	public function getParamsForFilter($katID = false, $arg = array()){
		$back = array();
		if(!$katID) return $back;
		$modszer 	= ($arg[modszer]) 	? $arg[modszer] : false;
		$gyujto 	= ($arg[gyujto]) 	? $arg[gyujto] : false;

		$q = "SELECT * FROM `shop_termek_kategoria_parameter` WHERE kategoriaID = $katID ORDER BY parameter ASC;";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		$loadParams = $this->getParameterHint($modszer, $gyujto);

		foreach($data as $d){
			$hint 		= $loadParams[$d[ID]];
			$type 		= $this->getParameterType($d, $hint);
			asort($hint);

			$d[hints]   	= $hint;
			$d[type] 		= $type;
			$d[minmax] 		= $this->getParameterMinMax($d[type],$hint);
			$back[] 		= $d;
		}

		return $back;
	}

	private function getParameterMinMax($type, $hints){
		$re 	= array();
		$min 	= 0;
		$max 	= 0;

		if($type == 'szam' || $type == 'tartomany'){
			foreach($hints as $h){
				if($type == 'szam'){
					if($min == 0) $min = $h;
					if($max == 0) $max = $h;

					if($h < $min) $min = $h;
					if($h > $max) $max = $h;
				}else if($type == 'tartomany'){
					$xn 	= explode('-',$h);
					$xmin 	= (int)$xn[0];
					$xmax	= (int)$xn[1];

					if($min == 0) $min = $xmin;
					if($max == 0) $max = $xmax;

					if($xmin < $min) $min = $xmin;
					if($xmax > $max) $max = $xmax;
				}
			}
		}

		$re[min] = $min;
		$re[max] = $max;

		return $re;
	}

	private function getParameterType($parameter, $hints){
		$re = false;

		if($parameter[mertekegyseg] == ''){
			$re = 'szoveg';
		}else{
			$re = 'szoveg';
			foreach($hints as $h){
				if(is_numeric($h)){
					$re = 'szoveg';
					if($parameter[is_range] == 1){
						$re = 'szam';
					}
				}else{
					if(strpos($h,'-') !== false){
						$re = 'tartomany';
					}
				}
			}
		}


		return $re;
	}
	private function getKategoriaStringByID($type, $id){
		$str = false;
		if($id == '') return $str;
		switch($type){
			case 'modszer':
				$str = Product::clear($this->db->query("SELECT neve FROM shop_modszerek WHERE ID = $id")->fetchColumn());
			break;
			case 'gyujto':
				$str = Product::clear($this->db->query("SELECT neve FROM shop_gyujto_kategoriak WHERE ID = $id")->fetchColumn());
			break;
			case 'termek':
				$str = Product::clear($this->db->query("SELECT neve FROM shop_termek_kategoriak WHERE ID = $id")->fetchColumn());
			break;
		}
		return $str;
	}
	private function getStringID($str){
		$x = explode("_-",$str);
		if(count($x) > 1){
			$id = $x[1];
		}else return false;
		return $id;
	}
	public function getTermekKategoriak(){
		$q = "SELECT * FROM shop_termek_kategoriak ORDER BY neve ASC;";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));
		return $data;
	}
	public function getUsableModszerek($arg = array()){
		$selectedModszer = ($arg[shopInfo]->kategoria->modszer) ? $arg[shopInfo]->kategoria->modszer : false;

		$q = "SELECT
		  ik.modszerID,
		  m.neve as modszer,
		  count(ik.id) as tct
		FROM `shop_termek_in_kategoria` as ik
		LEFT OUTER JOIN shop_termekek as t ON t.id = ik.termekID
		LEFT OUTER JOIN shop_modszerek as m ON m.id = ik.modszerID
		WHERE
			t.lathato = 1
		GROUP BY ik.modszerID
		ORDER BY m.neve ASC;";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		$re 	= array();
		$select = array();
		$index 	= 0;

		if($selectedModszer) $index = 1;
		foreach($data as $d){
			$d[gyujtok] 		= array();
			$d[kategoriak] 		= array();

				// Gyűjtők csatolása
				if(count($arg[gyujtok]) > 0){
					foreach($arg[gyujtok] as $gy){
						if($d[modszerID] == $gy[modszerID]){
							$gy[kategoriak] = $this->getUsedGyujtoKategoriak($d[modszerID], $gy[gyujtoID]);
							$d[gyujtok][] = $gy;
						}
					}
				}
				// Elérhető kategóriák csatolása
				if(count($d[gyujtok]) == 0){
					$d[kategoriak] = $this->getUsedKategoriak($d[modszerID]);
				}
			if($selectedModszer == $d[modszerID]){
				$select = $d;
			}else{
				$re[$index] = $d;
			}
			$index++;
		}
		if($selectedModszer && !empty($select)){
			$re[0] = $select;
		}
		ksort($re);
		return $re;
	}
	private function getUsedKategoriak($modszerID = false, $arg = array()){
		$re = array();
		$gyujto = ($arg[gyujtoID]) ? $arg[gyujtoID] : false;

		if(!$modszerID) return $re;

		$q = "SELECT
		  k.ID as kategoriaID,
		  k.neve as kategoriaNev,
		  count(t.id) as tct
		FROM `shop_termek_in_kategoria` as ik
		LEFT OUTER JOIN shop_termekek as t ON t.id = ik.termekID
		LEFT OUTER JOIN shop_termek_kategoriak as k ON k.id = t.termek_kategoria
		WHERE
			ik.modszerID = $modszerID and";
		if($gyujto){
			$q .= " ik.gyujtoID = $gyujto and ";
		}
		$q .= "
			t.lathato = 1 and
			t.termek_kategoria IS NOT NULL
		GROUP BY t.termek_kategoria
		ORDER BY k.neve ASC;";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		$ret = $data;

		return $ret;
	}
	private function getUsedGyujtoKategoriak($modszerID = false, $gyujtoID = false){
		$re = array();
		if(!$modszerID || !$gyujtoID) return $re;

		$q = "SELECT
		  k.ID as kategoriaID,
		  k.neve as kategoriaNev,
		  count(t.id) as tct
		FROM `shop_termek_in_kategoria` as ik
		LEFT OUTER JOIN shop_termekek as t ON t.id = ik.termekID
		LEFT OUTER JOIN shop_termek_kategoriak as k ON k.id = t.termek_kategoria
		WHERE
			ik.modszerID = $modszerID and
			ik.gyujtoID = $gyujtoID and ";
		$q .= "
			t.lathato = 1 and
			t.termek_kategoria IS NOT NULL
		GROUP BY t.termek_kategoria
		ORDER BY k.neve ASC;";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		$ret = $data;
		return $ret;
	}
	public function getUsableGyujtok($arg = array()){
		$q = "SELECT
			tk.*,
			gyk.neve as gyujtoNev,
			count(tk.ID) as tct
		FROM `shop_termek_in_kategoria` as tk
		LEFT OUTER JOIN shop_termekek as t ON tk.termekID = t.ID
		LEFT OUTER JOIN shop_gyujto_kategoriak as gyk ON gyk.ID = tk.gyujtoID
		WHERE
			gyk.neve IS NOT NULL and
			t.lathato = 1
		GROUP BY tk.gyujtoID
		ORDER BY
			gyk.neve ASC";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		return $data;
	}
	public function sendKapcsolatMsg($post){
		extract($post);

		if(!Captcha::verify())throw new Exception('Az ellenőrző kód hibás!');

		if($name == '') throw new Exception('Kérjük, ne felejtse megadni saját nevét!');
		if($email == '') throw new Exception('Kérjük, ne felejtse megadni saját e-mail címét!');
		if($subject == '') throw new Exception('Kérjük, ne felejtse megadni az üzenet témáját!');
		if($msgText == '') throw new Exception('Kérjük, fogalmazza meg, hogy miben segíthetünk Önnek!');
		$t = $this->getTermekAdat($tid);

		$msg = '';

		$msg .= "<strong>Kapcsolat > Üzenet küldés</strong>";
		$msg .= '<div>---</div>';
		$msg .= '<div>Név: <strong>'.$name.'</strong></div>';
		$msg .= '<div>E-mail cím: <strong>'.$email.'</strong></div>';
		$msg .= '<div>Telefonszám: <strong>'.$phone.'</strong></div>';
		$msg .= '<div>Időpont: <strong>'.NOW.'</strong></div>';
		$msg .= '<div><br /></div>';
		$msg .= '<div>Üzenet</div>';
		$msg .= '<div><strong>'.(($msgText == '')?'-':$msgText).'</strong></div>';


		if(false){
			Helper::smtpMail(array(
				'recepiens' => array(EMAIL),
				'msg' 	=> $msg,
				'tema' 	=> 'Kapcsolat',
				'from' 	=> $email,
				'fromName' 	=> $name,
				'sub' 	=> TITLE.': Kapcsolat üzenet'
			));
		}else{
			Helper::smtpMail(array(
				'recepiens' => array(ALERT_EMAIL),
				'msg' 		=> 'Új kapcsolat üzenet érkezett.<br><br><a href="'.ADMIN.'uzenetek">Üzenetek listája</a>',
				'tema' 		=> 'Értesítő',
				'from' 		=> NOREPLY_EMAIL,
				'fromName'	=> TITLE,
				'sub' 		=> 'Értesítő - Új kapcsolat üzenet'
			));
			$this->logMessage(array(
				'felado_nev' 	=> $name,
				'felado_email' 	=> $email,
				'item_id' 		=> '',
				'tipus' 		=> 'contactMsg',
				'uzenet_targy' 	=> 'Kapcsolat üzenet',
				'uzenet' 		=> $msg
			));
		}


		return 'Köszönjük megkeresését. Hamarosan felvesszük Önnel a kapcsolatot!';
	}
	public function requestReCall($post){
		extract($post);
		$msg = '';

		if(!Captcha::verify())throw new Exception('Az ellenőrző kód hibás!');

		if($nev 	== '') throw new Exception('Kérjük, ne felejtse megadni saját nevét!');
		if($phone 	== '') throw new Exception('Kérjük, ne felejtse megadni saját telefonszámát!');

		$t = $this->getTermekAdat($tid);

		if($tid != ''){
			$msg .= '<div>Kapcsolódó termék: <strong><a href="'.$t[data][url].'">'.$t[data][markaNev]." ".$t[data][nev].'</a></strong> ('.$t[data][url].')</div>';
		}else{
			$msg .= "<strong>Kapcsolat > Visszahívás kérése</strong>";
		}
		$msg .= '<div>---</div>';
		$msg .= '<div>Név: <strong>'.$nev.'</strong></div>';
		$msg .= '<div>Telefonszám: <strong>'.$phone.'</strong></div>';
		$msg .= '<div>Kiválasztott időszak visszahíváshoz: <strong>'.(($idoszak == 'egesz_nap')?'8-17 óráig hívható':($idoszak == 'delelott')?'7-12 óráig hívható':'12-17 óráig hívható').'</strong></div>';
		$msg .= '<div>Megjegyzés</div>';
		$msg .= '<div><strong>'.(($comment == '')?'-':$comment).'</strong></div>';

		if(false){
			Helper::smtpMail(array(
				'recepiens' => array(ALERT_EMAIL),
				'msg' 	=> $msg,
				'tema' 	=> 'Értesítő',
				'from' 	=> NOREPLY_EMAIL,
				'sub' 	=> TITLE.': Visszahívás kérése'
			));
		}else{
			Helper::smtpMail(array(
				'recepiens' => array(ALERT_EMAIL),
				'msg' 		=> 'Új visszahívás kérés érkezett<br><br><a href="'.ADMIN.'uzenetek">Üzenetek listája</a>',
				'tema' 		=> 'Értesítő',
				'from' 		=> NOREPLY_EMAIL,
				'fromName'	=> TITLE,
				'sub' 		=> 'Értesítő - Visszahívás kérése'
			));
			$this->logMessage(array(
				'felado_nev' 	=> $nev,
				'felado_email' 	=> $email,
				'item_id' 		=> $tid,
				'tipus' 		=> 'recall',
				'uzenet_targy' 	=> 'Visszahívás',
				'uzenet' 		=> $msg
			));
		}

		return 'Köszönjük megkeresését. Hamarosan felvesszük Önnel a kapcsolatot!';
	}

	public function requestAskTermek($post){
		extract($post);

		$msg = '';

		if(!Captcha::verify())throw new Exception('Az ellenőrző kód hibás!');

		if($nev == '') throw new Exception('Kérjük, ne felejtse megadni saját nevét!');
		if($email == '') throw new Exception('Kérjük, ne felejtse megadni saját e-mail címét!');
		if($ask == '') throw new Exception('Kérjük, ne felejtse feltenni kérdését a termékkel kapcsolatban!');

		$t = $this->getTermekAdat($tid);

		$msg .= '<div>Kapcsolódó termék: <strong><a href="'.$t[data][url].'">'.$t[data][markaNev]." ".$t[data][nev].'</a></strong> ('.$t[data][url].')</div>';
		$msg .= '<div><br /></div>';
		$msg .= '<div>Kérdés:</div>';
		$msg .= '<div><strong>'.(($ask == '')?'-':$ask).'</strong></div>';
		$msg .= '<div><br /></div>';
		$msg .= '<div>Küldte: <strong>'.$nev.'</strong> - '.$email.' @ '.NOW.'</div>';

		if(false){
			Helper::smtpMail(array(
			'recepiens' => array(EMAIL),
			'msg' 		=> $msg,
			'tema' 		=> 'Kérdés termékkel kapcsolatban',
			'from' 		=> $email,
			'fromName'	=> $nev,
			'sub' 		=> 'Kérdés: '.$t[data][markaNev]." ".$t[data][nev]
			));
		}else{
			Helper::smtpMail(array(
				'recepiens' => array(ALERT_EMAIL),
				'msg' 		=> 'Új kérdés érkezett egy termékkel kapcsolatban:<br><a href="'.$t[data][url].'">'.$t[data][markaNev]." ".$t[data][nev].'</a><br><br><a href="'.ADMIN.'uzenetek">Üzenetek listája</a>',
				'tema' 		=> 'Értesítő ',
				'from' 		=> NOREPLY_EMAIL,
				'fromName'	=> TITLE,
				'sub' 		=> 'Értesítő - Kérdés termékről'
			));
			$this->logMessage(array(
				'felado_nev' 	=> $nev,
				'felado_email' 	=> $email,
				'item_id' 		=> $tid,
				'tipus' 		=> 'requestAsk',
				'uzenet_targy' 	=> 'Kérdés egy termékről: '.$t[data][markaNev].' '.$t[data][nev],
				'uzenet' 		=> $msg
			));
		}

		return 'Köszönjük érdeklődését. Hamarosan válaszolni fogunk kérdéseire!';
	}
	const ORDER_COOKIE_KEY_STEP = 'orderStep';
	public function doOrder($post){
		extract($post);
		$errArr = false;
		$gets 	= Helper::GET();
		$step 	= $gets[1];
		$step 	= (!$step) ? 0 : $step;

		$post_str = json_encode($post, JSON_UNESCAPED_UNICODE);

		switch($step){
			case 0:
				$err 		= false;
				$inputErr 	= array();

				if($nev == ''){
					$err 		= 'Alapvető adatok megadása kötelező vagy jelentkezzen be.';
					$inputErr[] = 'nev';
				}

				if($email == ''){
					$err 		= 'Alapvető adatok megadása kötelező vagy jelentkezzen be.';
					$inputErr[] = 'email';
				}


				if($err){
					$errArr[input] = $inputErr;
					throw new OrderException($err, $errArr);
				}else{
					setcookie(self::ORDER_COOKIE_KEY_STEP,$step+1,time()+3600*24,'/');
					Helper::setStoredPOSTData('order_step_'.($step+1),$post_str);
				}
			break;
			case 1:
				$err 		= false;
				$inputErr 	= array();

				if($szam_nev == ''){
					$err 		= 'Alapvető adatok megadása kötelező vagy jelentkezzen be.';
					$inputErr[] = 'szam_nev';
				}
				if($szam_uhsz == ''){
					$err 		= 'Alapvető adatok megadása kötelező vagy jelentkezzen be.';
					$inputErr[] = 'szam_uhsz';
				}
				if($szam_city == ''){
					$err 		= 'Alapvető adatok megadása kötelező vagy jelentkezzen be.';
					$inputErr[] = 'szam_city';
				}
				if($szam_irsz == ''){
					$err 		= 'Alapvető adatok megadása kötelező vagy jelentkezzen be.';
					$inputErr[] = 'szam_irsz';
				}
				if($szall_nev == ''){
					$err 		= 'Alapvető adatok megadása kötelező vagy jelentkezzen be.';
					$inputErr[] = 'szall_nev';
				}
				if($szall_uhsz == ''){
					$err 		= 'Alapvető adatok megadása kötelező vagy jelentkezzen be.';
					$inputErr[] = 'szall_uhsz';
				}
				if($szall_city == ''){
					$err 		= 'Alapvető adatok megadása kötelező vagy jelentkezzen be.';
					$inputErr[] = 'szall_city';
				}
				if($szall_irsz == ''){
					$err 		= 'Alapvető adatok megadása kötelező vagy jelentkezzen be.';
					$inputErr[] = 'szall_irsz';
				}
				if($szall_phone == ''){
					$err 		= 'Alapvető adatok megadása kötelező vagy jelentkezzen be.';
					$inputErr[] = 'szall_phone';
				}

				if($err){
					$errArr[input] = $inputErr;
					throw new OrderException($err, $errArr);
				}else{
					setcookie(self::ORDER_COOKIE_KEY_STEP,$step+1,time()+3600*24,'/');
					Helper::setStoredPOSTData('order_step_'.($step+1),$post_str);
				}
			break;
			case 2:
				$err 		= false;
				$inputErr 	= array();
				if($atvetel == ''){
					$err 		= 'Átvételi mód kiválasztása kötelező!';
					$inputErr[] = 'atvetel';
				}

				if($err){
					$errArr[input] = $inputErr;
					throw new OrderException($err, $errArr);
				}else{
					setcookie(self::ORDER_COOKIE_KEY_STEP,$step+1,time()+3600*24,'/');

					Helper::setStoredPOSTData('order_step_'.($step+1),$post_str);
				}
			break;
			case 3:
				$err 		= false;
				$inputErr 	= array();
				if($fizetes == ''){
					$err 		= 'Fizetési mód kiválasztása kötelező!';
					$inputErr[] = 'fizetes';
				}
				if($err){
					$errArr[input] = $inputErr;
					throw new OrderException($err, $errArr);
				}else{
					setcookie(self::ORDER_COOKIE_KEY_STEP,$step+1,time()+3600*24,'/');
					Helper::setStoredPOSTData('order_step_'.($step+1),$post_str);
				}
			break;
			case 4:
				$err 		= false;
				$inputErr 	= array();

				if($err){
					$errArr[input] = $inputErr;
					throw new OrderException($err, $errArr);
				}else{
					$go 	= true;
					$mid 	= Helper::getMachineID();
					$orderID= 0;
					$uid 	= ($orderUserID == '') ? 'NULL' : $orderUserID;
					$total 	= 0;
					$pppkod = ($ppp_uzlet_done) ? $ppp_uzlet_done : 'NULL';
					$pp_pont= ($pp_selected_point) ? $pp_selected_point : 'NULL';

					$cart = $this->db->query("SELECT
							c.*,
							t.szuper_akcios,
							CONCAT(m.neve,' ',t.nev) as nev,
							m.elorendelheto,
							getTermekUrl(t.ID,'".DOMAIN."') as url,
							IF(t.egyedi_ar IS NOT NULL, t.egyedi_ar, getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))) as ar,
							m.neve as markaNev
						FROM shop_kosar as c
						LEFT OUTER JOIN shop_termekek as t ON t.ID = c.termekID
						LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
						WHERE c.gepID = $mid");
					$cart = $cart->fetchAll(PDO::FETCH_ASSOC);

					if(count($cart) == 0){
						return false;
					}

					$preorders = 0;
					foreach($cart as $d){
						// Flag preorder
						if($post[preOrderItem][$d[termekID]] == 'on'){
							$preorders = 1;
							break;
						}
					}

					// Create new order
					if($go){
						$szamlazasi_keys = Helper::getArrayValueByMatch($post,'szam_');
						$szallitasi_keys = Helper::getArrayValueByMatch($post,'szall_');
						$iq = "INSERT INTO orders(nev,azonosito,email,userID,gepID,szallitasiModID,fizetesiModID,kedvezmeny,szallitasi_koltseg,szamlazasi_keys,szallitasi_keys,pickpackpont_uzlet_kod,elorendeles,comment,postapont) VALUES(
						'$nev',
						nextOrderID(),
						'$email',
						$uid,
						'$mid',
						'$atvetel',
						'$fizetes',
						'$kedvezmeny',
						'$szallitasi_koltseg',
						'".json_encode($szamlazasi_keys,JSON_UNESCAPED_UNICODE)."',
						'".json_encode($szallitasi_keys,JSON_UNESCAPED_UNICODE)."',
						$pppkod,
						$preorders,
						'$comment',
						'$pp_pont'
						);";

						$this->db->query($iq);

						$orderID 	= $this->db->lastInsertId();
						$accessKey	= md5($orderID.'.'.$email);
						$this->db->update('orders',
							array(
								'accessKey' => $accessKey
							),
							"ID = $orderID"
						);

					}
					// Copy items to order items and connect with order parent
					$orderedProducts = array();
					if($go){
						foreach($cart as $d){
							$total += ($d[ar]*$d[me]);

							$preord = ($post[preOrderItem][$d[termekID]] == 'on') ? 1 : 0;

							$orderedProducts[] = $d[nev];

							$this->db->insert('order_termekek',
								array('orderKey','gepID','userID','email','termekID','me','egysegAr','elorendelt'),
								array($orderID, $mid,$uid,$email,$d[termekID],$d[me],$d[ar],$preord)
							);
						}
					}

					// Clear shoping cart by machineID
					if($go){
						$this->db->query("DELETE FROM shop_kosar WHERE gepID = $mid");
					}
					// Alert orders and admin about new order
						$orderData = $this->db->query("SELECT * FROM orders WHERE ID = $orderID")->fetch(PDO::FETCH_ASSOC);

						// Admin alert
						$total 		= 0;
						$param 		= array();
						$msg 		= '';
						$msg 		.= '<div>Név: <strong>'.$nev.'</strong></div>';
						$msg 		.= '<div>E-mail: <strong>'.$email.'</strong></div>';
						$msg 		.= '<div>('.(($uid == '')? 'Nem regisztrált':'Regisztrált').')</div>';
						$msg 		.= '<div>Rendelés azonosító: <strong>'.$orderData[azonosito].'</strong></div>';
						$msg 		.= '<div><br /></div>';
						$msg 		.= '<table width="100%" border="1" style="border-collapse:collapse; border:2px solid #dddddd; background:#ffffff;" cellpadding="10" cellspacing="0">';
						$msg 		.= '<thead>';
							$msg 		.= '<tr>';
								$msg 		.= '<th align="center">Me.</th>';
								$msg 		.= '<th align="center">Termék</th>';
								$msg 		.= '<th align="center">Bruttó e. ár</th>';
								$msg 		.= '<th align="center">Bruttó ár</th>';
								$msg 		.= '<th align="center">Állapot</th>';
							$msg 		.= '</tr>';
						$msg 		.= '</thead>';
						$msg 		.= '<tbody style="color:#888;">';
						foreach($cart as $d){
							$total += ($d[ar]*$d[me]);
							$msg 		.= '<tr>';
								$msg 		.= '<td align="center">'.$d[me].'x</td>';
								$msg 		.= '<td><a href="'.$d[url].'">'.$d[nev].'</a></td>';
								$msg 		.= '<td align="center">'.Helper::cashFormat($d[ar]).' Ft</td>';
								$msg 		.= '<td align="center">'.Helper::cashFormat(($d[ar]*$d[me])).' Ft</td>';
									$msg 		.= '<td align="center"><strong style="color:#CC0000;">Feldolgozás alatt</strong></td>';
							$msg 		.= '</tr>';
						}
							// Összesítő ár
							$msg 		.= '<tr>';
								$msg 		.= '<td colspan="4" align="right">Összesen:</td>';
								$msg 		.= '<td align="center">'.Helper::cashFormat($total).' Ft</td>';
							$msg 		.= '</tr>';
							// Egyéb kiegészítő adatok
							$msg 		.= '<tr>';
								$msg 		.= '<td colspan="4" align="right">Szállítási költség:</td>';
								$msg 		.= '<td align="center">'.Helper::cashFormat($szallitasi_koltseg).' Ft</td>';
							$msg 		.= '</tr>';
							$msg 		.= '<tr>';
								$msg 		.= '<td colspan="4" align="right">Kedvezmény:</td>';
								$msg 		.= '<td align="center">'.(($kedvezmeny > 0) ? '-'.Helper::cashFormat($kedvezmeny):0).' Ft</td>';
							$msg 		.= '</tr>';
							if($szallitasi_koltseg > 0) $total += $szallitasi_koltseg;
							if($kedvezmeny > 0) $total -= $kedvezmeny;
							$msg 		.= '<tr>';
								$msg 		.= '<td colspan="4" align="right"><strong>Végösszeg:</strong></td>';
								$msg 		.= '<td align="center"><strong>'.Helper::cashFormat($total).' Ft</strong></td>';
							$msg 		.= '</tr>';
						$msg 		.= '</tbody>';
						$msg 		.= '</table>';


						// Számlázási adatok
						$msg 		.= '<div><h3>Számlázási adatok</h3></div>';
						$msg 		.= '<table width="100%" border="1" style="border-collapse:collapse; border:2px solid #dddddd; background:#ffffff;" cellpadding="10" cellspacing="0">';
						$msg 		.= '<tbody>';
							$msg 		.= '<tr>';
								$msg 		.= '<td width="150" align="left">Név</td>';
								$msg 		.= '<td align="left"><strong>'.$szamlazasi_keys[nev].'</strong></td>';
							$msg 		.= '</tr>';
							$msg 		.= '<tr>';
								$msg 		.= '<td align="left">Utca, házszám</td>';
								$msg 		.= '<td align="left"><strong>'.$szamlazasi_keys[uhsz].'</strong></td>';
							$msg 		.= '</tr>';
							$msg 		.= '<tr>';
								$msg 		.= '<td align="left">Város</td>';
								$msg 		.= '<td align="left"><strong>'.$szamlazasi_keys[city].'</strong></td>';
							$msg 		.= '</tr>';
							$msg 		.= '<tr>';
								$msg 		.= '<td align="left">Irányítószám</td>';
								$msg 		.= '<td align="left"><strong>'.$szamlazasi_keys[irsz].'</strong></td>';
							$msg 		.= '</tr>';
						$msg 		.= '</tbody>';
						$msg 		.= '</table>';

						// Szállítási adatok
						$msg 		.= '<div><h3>Szállítási adatok</h3></div>';
						$msg 		.= '<table width="100%" border="1" style="border-collapse:collapse; border:2px solid #dddddd; background:#ffffff;" cellpadding="10" cellspacing="0">';
						$msg 		.= '<tbody>';
							$msg 		.= '<tr>';
								$msg 		.= '<td width="150" align="left">Név</td>';
								$msg 		.= '<td align="left"><strong>'.$szallitasi_keys[nev].'</strong></td>';
							$msg 		.= '</tr>';
							$msg 		.= '<tr>';
								$msg 		.= '<td align="left">Utca, házszám</td>';
								$msg 		.= '<td align="left"><strong>'.$szallitasi_keys[uhsz].'</strong></td>';
							$msg 		.= '</tr>';
							$msg 		.= '<tr>';
								$msg 		.= '<td align="left">Város</td>';
								$msg 		.= '<td align="left"><strong>'.$szallitasi_keys[city].'</strong></td>';
							$msg 		.= '</tr>';
							$msg 		.= '<tr>';
								$msg 		.= '<td align="left">Irányítószám</td>';
								$msg 		.= '<td align="left"><strong>'.$szallitasi_keys[irsz].'</strong></td>';
							$msg 		.= '</tr>';
							$msg 		.= '<tr>';
								$msg 		.= '<td align="left">Telefonszám</td>';
								$msg 		.= '<td align="left"><strong>'.$szallitasi_keys[phone].'</strong></td>';
							$msg 		.= '</tr>';
						$msg 		.= '</tbody>';
						$msg 		.= '</table>';


						// Egyéb adatok
						$msg 		.= '<div><h3>Egyéb adatok</h3></div>';
						$msg 		.= '<table width="100%" border="1" style="border-collapse:collapse; border:2px solid #dddddd; background:#ffffff;" cellpadding="10" cellspacing="0">';
						$msg 		.= '<tbody>';
							$msg 		.= '<tr>';
								$msg 		.= '<td width="150" align="left">Átvétel módja</td>';
								$msg 		.= '<td align="left"><strong>'.$this->getSzallitasiModeData($atvetel,'nev').'</strong></td>';
							$msg 		.= '</tr>';
							$msg 		.= '<tr>';
								$msg 		.= '<td align="left">Fizetés módja</td>';
								$msg 		.= '<td align="left"><strong>'.$this->getFizetesiModeData($fizetes,'nev').'</strong>';

								if($atvetel == '2'){
									$msg .= ' ('.$ppp_uzlet_str.')';
								}
								$msg .= 	'</td>';
							$msg 		.= '</tr>';
							$msg 		.= '<tr>';
								$msg 		.= '<td align="left">Megrendelve</td>';
								$msg 		.= '<td align="left"><strong>'.NOW.'</strong></td>';
							$msg 		.= '</tr>';
						$msg 		.= '</tbody>';
						$msg 		.= '</table>';







						$msg 		.= '<div>Megrendelés azonosító: <strong>'.$orderID.'</strong></div>';

						$param[msg] 		= $msg;
						$param[elnevezes] 	= 'Értesítő';

						$remsg = Helper::smtpMail(array(
							'recepiens' => array(ALERT_EMAIL),
							'msg' 	=> $msg,
							'tema' 	=> 'Értesítő',
							'from' 	=> NOREPLY_EMAIL,
							'sub' 	=> 'Új megrendelés'
						));

						// User alert
						$total 		= 0;
						$param 		= array();
						$msg 		= '<h2>Tisztelt '.$nev.'!</h2>';
						$msg 		.= '<div>Köszönjük, hogy a(z) '.TITLE.' webáruházat választotta!</div>';
						$msg 		.= '<div>A rendelés azonosítója: <strong>'.$orderData[azonosito].'</strong></div>';
						$msg 		.= '<div><h3>Megrendelt termékek</h3></div>';

						$msg 		.= '<table width="100%" border="1" style="border-collapse:collapse; border:2px solid #dddddd; background:#ffffff;" cellpadding="10" cellspacing="0">';
						$msg 		.= '<thead>';
							$msg 		.= '<tr>';
								$msg 		.= '<th align="center">Me.</th>';
								$msg 		.= '<th align="center">Termék</th>';
								$msg 		.= '<th align="center">Bruttó e. ár</th>';
								$msg 		.= '<th align="center">Bruttó ár</th>';
								$msg 		.= '<th align="center">Állapot</th>';
							$msg 		.= '</tr>';
						$msg 		.= '</thead>';
						$msg 		.= '<tbody style="color:#888;">';
						foreach($cart as $d){
							$total += ($d[ar]*$d[me]);
							$msg 		.= '<tr>';
								$msg 		.= '<td align="center">'.$d[me].'x</td>';
								$msg 		.= '<td><a href="'.$d[url].'">'.$d[nev].'</a></td>';
								$msg 		.= '<td align="center">'.Helper::cashFormat($d[ar]).' Ft</td>';
								$msg 		.= '<td align="center">'.Helper::cashFormat(($d[ar]*$d[me])).' Ft</td>';
								$msg 		.= '<td align="center"><strong style="color:#CC0000;">Feldolgozás alatt</strong></td>';
							$msg 		.= '</tr>';
						}
							// Összesítő ár
							$msg 		.= '<tr>';
								$msg 		.= '<td colspan="4" align="right">Összesen:</td>';
								$msg 		.= '<td align="center">'.Helper::cashFormat($total).' Ft</td>';
							$msg 		.= '</tr>';
							// Egyéb kiegészítő adatok
							$msg 		.= '<tr>';
								$msg 		.= '<td colspan="4" align="right">Szállítási költség:</td>';
								$msg 		.= '<td align="center">'.Helper::cashFormat($szallitasi_koltseg).' Ft</td>';
							$msg 		.= '</tr>';
							$msg 		.= '<tr>';
								$msg 		.= '<td colspan="4" align="right">Kedvezmény:</td>';
								$msg 		.= '<td align="center">'.(($kedvezmeny > 0) ? '-'.Helper::cashFormat($kedvezmeny):0).' Ft</td>';
							$msg 		.= '</tr>';
							if($szallitasi_koltseg > 0) $total += $szallitasi_koltseg;
							if($kedvezmeny > 0) $total -= $kedvezmeny;
							$msg 		.= '<tr>';
								$msg 		.= '<td colspan="4" align="right"><strong>Végösszeg:</strong></td>';
								$msg 		.= '<td align="center"><strong>'.Helper::cashFormat($total).' Ft</strong></td>';
							$msg 		.= '</tr>';
						$msg 		.= '</tbody>';
						$msg 		.= '</table>';


						// Számlázási adatok
						$msg 		.= '<div><h3>Számlázási adatok</h3></div>';
						$msg 		.= '<table width="100%" border="1" style="border-collapse:collapse; border:2px solid #dddddd; background:#ffffff;" cellpadding="10" cellspacing="0">';
						$msg 		.= '<tbody>';
							$msg 		.= '<tr>';
								$msg 		.= '<td width="150" align="left">Név</td>';
								$msg 		.= '<td align="left"><strong>'.$szamlazasi_keys[nev].'</strong></td>';
							$msg 		.= '</tr>';
							$msg 		.= '<tr>';
								$msg 		.= '<td align="left">Utca, házszám</td>';
								$msg 		.= '<td align="left"><strong>'.$szamlazasi_keys[uhsz].'</strong></td>';
							$msg 		.= '</tr>';
							$msg 		.= '<tr>';
								$msg 		.= '<td align="left">Város</td>';
								$msg 		.= '<td align="left"><strong>'.$szamlazasi_keys[city].'</strong></td>';
							$msg 		.= '</tr>';
							$msg 		.= '<tr>';
								$msg 		.= '<td align="left">Irányítószám</td>';
								$msg 		.= '<td align="left"><strong>'.$szamlazasi_keys[irsz].'</strong></td>';
							$msg 		.= '</tr>';
						$msg 		.= '</tbody>';
						$msg 		.= '</table>';

						// Szállítási adatok
						$msg 		.= '<div><h3>Szállítási adatok</h3></div>';
						$msg 		.= '<table width="100%" border="1" style="border-collapse:collapse; border:2px solid #dddddd; background:#ffffff;" cellpadding="10" cellspacing="0">';
						$msg 		.= '<tbody>';
							$msg 		.= '<tr>';
								$msg 		.= '<td width="150" align="left">Név</td>';
								$msg 		.= '<td align="left"><strong>'.$szallitasi_keys[nev].'</strong></td>';
							$msg 		.= '</tr>';
							$msg 		.= '<tr>';
								$msg 		.= '<td align="left">Utca, házszám</td>';
								$msg 		.= '<td align="left"><strong>'.$szallitasi_keys[uhsz].'</strong></td>';
							$msg 		.= '</tr>';
							$msg 		.= '<tr>';
								$msg 		.= '<td align="left">Város</td>';
								$msg 		.= '<td align="left"><strong>'.$szallitasi_keys[city].'</strong></td>';
							$msg 		.= '</tr>';
							$msg 		.= '<tr>';
								$msg 		.= '<td align="left">Irányítószám</td>';
								$msg 		.= '<td align="left"><strong>'.$szallitasi_keys[irsz].'</strong></td>';
							$msg 		.= '</tr>';
							$msg 		.= '<tr>';
								$msg 		.= '<td align="left">Telefonszám</td>';
								$msg 		.= '<td align="left"><strong>'.$szallitasi_keys[phone].'</strong></td>';
							$msg 		.= '</tr>';
						$msg 		.= '</tbody>';
						$msg 		.= '</table>';


						// Egyéb adatok
						$msg 		.= '<div><h3>Egyéb adatok</h3></div>';
						$msg 		.= '<table width="100%" border="1" style="border-collapse:collapse; border:2px solid #dddddd; background:#ffffff;" cellpadding="10" cellspacing="0">';
						$msg 		.= '<tbody>';
							$msg 		.= '<tr>';
								$msg 		.= '<td width="150" align="left">Átvétel módja</td>';
								$msg 		.= '<td align="left"><strong>'.$this->getSzallitasiModeData($atvetel,'nev').'</strong></td>';
							$msg 		.= '</tr>';
							$msg 		.= '<tr>';
								$msg 		.= '<td align="left">Fizetés módja</td>';
								$msg 		.= '<td align="left"><strong>'.$this->getFizetesiModeData($fizetes,'nev').'</strong>';

								if($atvetel == '2'){
									$msg .= ' ('.$ppp_uzlet_str.')';
								}
								$msg .= 	'</td>';
							$msg 		.= '</tr>';
							$msg 		.= '<tr>';
								$msg 		.= '<td align="left">Megrendelve</td>';
								$msg 		.= '<td align="left"><strong>'.NOW.'</strong></td>';
							$msg 		.= '</tr>';
						$msg 		.= '</tbody>';
						$msg 		.= '</table>';

						// Előreutalás adatok
						if( $this->getFizetesiModeData($fizetes,'nev') == 'Előreutalás' ){

							$msg 		.= '<div><h3>Átutaláshoz szükséges adatok</h3></div>';
							$msg 		.= '<table width="100%" border="1" style="border-collapse:collapse; border:2px solid #dddddd; background:#ffffff;" cellpadding="10" cellspacing="0">';
							$msg 		.= '<tbody>';
								$msg 		.= '<tr>';
									$msg 		.= '<td width="150" align="left">Név</td>';
									$msg 		.= '<td align="left"><strong>GOLDONLINE Kft.</strong></td>';
								$msg 		.= '</tr>';
								$msg 		.= '<tr>';
									$msg 		.= '<td align="left">Számlaszám:</td>';
									$msg 		.= '<td align="left"><strong>11738008-20886343</strong></td>';
								$msg 		.= '</tr>';
								$msg 		.= '<tr>';
									$msg 		.= '<td align="left">Bank:</td>';
									$msg 		.= '<td align="left"><strong>OTP Bank Nyrt.</strong></td>';
								$msg 		.= '</tr>';
							$msg 		.= '</tbody>';
							$msg 		.= '</table>';

						}

						$msg 		.= '<div>Megrendelését nyomon követheti weboldalunkon. Regisztrált tagként, bejelentkezés után a megrendelések menüpont alatt keresse. <br /><br />
						<strong>Ha Ön nem regisztrált, ezen a linken megtekintheti aktuális megrendelését:</strong><br />
						<a href="'.DOMAIN.'order/'.$accessKey.'">'.DOMAIN.'order/'.$accessKey.'</a>
						</div>';

						$param[msg] 		= $msg;
						$param[elnevezes] 	= 'Megrendelés';

						$remsg = Helper::smtpMail(array(
							'recepiens' => array($email),
							'msg' 	=> $msg,
							'tema' 	=> $orderData[azonosito].'<br/>megrendelését fogadtuk',
							'from' 	=> NOREPLY_EMAIL,
							'sub' 	=> 'Megrendelését fogadtuk'
						));

						// Clear cookies
						setcookie('__order_step_1poststr','',time()-3600);
						setcookie('__order_step_2poststr','',time()-3600);
						setcookie('__order_step_3poststr','',time()-3600);
						setcookie('__order_step_4poststr','',time()-3600);
						setcookie('orderStep','',time()-3600);

						setcookie('lastOrderedKey',$accessKey,time()+3600);
				}
			break;
		}

		return $step+1;

	}
	public function getOrderData($key){

		$q = "SELECT o.* FROM orders as o WHERE o.accessKey = '$key'";
		extract($this->db->q($q));

		$data[items] = $this->getOrderItems($data[ID]);

		return $data;
	}
	private function getOrderItems($orderID){
		if($orderID == '') return false;
		$q = "SELECT
			ok.*,
			CONCAT(m.neve,' ',t.nev) as nev,
			(ok.egysegAr * ok.me) as subAr,
			FULLIMG(t.profil_kep) as profil_kep,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			getTermekAr(t.marka,IF(t.egyedi_ar IS NOT NULL,t.egyedi_ar,IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))) as ar,
			otp.nev as allapotNev,
			otp.szin as allapotSzin
		FROM order_termekek as ok
		LEFT OUTER JOIN shop_termekek as t ON t.ID = ok.termekID
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		LEFT OUTER JOIN order_termek_allapot as otp ON ok.allapotID = otp.ID
		WHERE ok.orderKey = $orderID";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		return $data;
	}
	private function getFizetesiModeData($id, $row = false){
		$q = "SELECT * FROM shop_fizetesi_modok WHERE ID = $id";

		extract($this->db->q($q));
		if(!$row){
			return $data;
		}else{
			return $data[$row];
		}

	}
	private function getSzallitasiModeData($id, $row = false){
		$q = "SELECT * FROM shop_szallitasi_mod WHERE ID = $id";

		extract($this->db->q($q));
		if(!$row){
			return $data;
		}else{
			return $data[$row];
		}

	}
	public function getFizetesiModok(){
		$q = "SELECT
			f.*,
			(SELECT GROUP_CONCAT(ID) as IDS FROM `shop_szallitasi_mod` WHERE FIND_IN_SET(f.ID,fizetesi_mod) > 0) as in_szallitas_mod
		FROM shop_fizetesi_modok as f";
		extract($this->db->q($q,array('multi' => '1')));
		$back = array();

		foreach($data as $d){
			$d['in_szallitas_mod'] = explode(',',$d[in_szallitas_mod]);
			$back[] = $d;
		}

		return $back;
	}

	function getArgepTermekek(){
		$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
		$apsz = $akcios_plus_szaz / 100 + 1;


		$back = array();
		$q = $this->db->query("SELECT
			t.ID,
			t.szuper_akcios,
			t.termek_kategoria,
			CONCAT(m.neve,' ',t.nev) as nev,
			IF(t.egyedi_ar IS NOT NULL,
				t.egyedi_ar,
				getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))
			) as ar,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			t.leiras,
			szall.elnevezes as szallitas,
			FULLIMG(t.profil_kep) as kep
		FROM
			shop_termekek as t
		LEFT OUTER JOIN shop_szallitasi_ido as szall ON szall.ID = t.szallitasID
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		WHERE
			t.argep = 1 and
			t.lathato = 1
		ORDER BY
			id DESC;");
		$d = $q->fetchAll(PDO::FETCH_ASSOC);

		foreach($d as $id){
			$id[param] = $this->getTermekParameter($id[ID],$id[termek_kategoria]);
			$back[] = $id;
		}

		return $back;
	}

	function getArukeresoTermekek(){
		$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
		$apsz = $akcios_plus_szaz / 100 + 1;

		$back = array();
		$q = $this->db->query("SELECT
			t.ID,
			t.szuper_akcios,
			m.neve as markaNev,
			t.termek_kategoria,
			t.nev,
			IF(t.egyedi_ar IS NOT NULL,
				t.egyedi_ar,
				getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))
			) as ar,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			t.leiras,
			szall.elnevezes as szallitas,
			FULLIMG(t.profil_kep) as kep
		FROM
			shop_termekek as t
		LEFT OUTER JOIN shop_szallitasi_ido as szall ON szall.ID = t.szallitasID
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		WHERE
			t.arukereso = 1 and
			t.lathato = 1
		ORDER BY
			id DESC;");
		$d = $q->fetchAll(PDO::FETCH_ASSOC);

		foreach($d as $id){
			$id[param] = $this->getTermekParameter($id[ID],$id[termek_kategoria]);
			$back[] = $id;
		}

		return $back;
	}

	public function getKedvezmenyesAr($ar){
		$kedv 	= 0;
		$newAr 	= 0;

		// Kedvezmény sávok
		$sv = "SELECT * FROM torzsvasarloi_kedvezmeny ORDER BY ar_from ASC;";

		extract($this->db->q($sv,array('multi' => '1')));

		foreach($data as $d){
			$from 	= (int)$d[ar_from];
			$to 	= (int)$d[ar_to];
			$k 		= (float)$d[kedvezmeny];

			if($to === 0) $to = 999999999;

			if($ar >= $from && $ar <= $to){
				$kedv = $k;
				break;
			}

		}

		$newAr = round(($ar - ($ar * ($kedv / 100))) / 5 ) * 5;

		return $newAr;
	}

	function logTermekView($termekID){
		$date = date('Y-m-d');
		$c = $this->db->query("SELECT 1 FROM stat_nezettseg_termek WHERE datum = '$date' and termekID = $termekID")->rowCount();

		if($c == 0){
			$this->db->insert("stat_nezettseg_termek",
				array('termekID','datum'),
				array($termekID,$date)
			);
		}else{
			$this->db->query("UPDATE stat_nezettseg_termek SET me = me + 1 WHERE termekID = $termekID and datum = '$date'");
		}
	}

	function logKategoriaView($cat_id = NULL){
		$date 	= date('Y-m-d');

		$cq = "SELECT 1 FROM stat_nezettseg_kategoria WHERE datum = '$date' and kategoriaID = $cat_id";

		$c = $this->db->query($cq)->rowCount();

		if($c == 0){
			$this->db->insert("stat_nezettseg_kategoria",
				array('kategoriaID','datum'),
				array($cat_id, $date)
			);
		}else{
			$this->db->query("UPDATE stat_nezettseg_kategoria SET me = me + 1 WHERE datum = '$date' and kategoriaID = $kategoria");
		}
	}

	function logSearching($searchString){
		$date 	= date('Y-m-d');
		$txt 	= trim($searchString);

		$c = $this->db->query("SELECT 1 FROM stat_kereses WHERE datum = '$date' and szoveg = '$txt'")->rowCount();

		if($c == 0 && trim($txt) != ''){
			$this->db->insert("stat_kereses",
				array('szoveg','datum'),
				array(trim($txt),$date)
			);
		}else{
			$this->db->query("UPDATE stat_kereses SET me = me + 1 WHERE szoveg = '$txt' and datum = '$date'");
		}
	}

	function logLastViewedTermek($termek_id){
		$mid = Helper::getMachineID();

		if($termek_id == '' || $mid == '') return false;

		$list = $this->db->query("SELECT * FROM `shop_utoljaraLatottTermek` WHERE mID = '$mid'  and termekID = '$termek_id' LIMIT 0,10");

		if($list->rowCount() == 0){
			$this->db->insert('shop_utoljaraLatottTermek',
				array('mID','termekID'),
				array($mid, $termek_id)
			);
		}else{
			$this->db->update('shop_utoljaraLatottTermek',
			array(
				'idopont' => NOW
			),
			"mID = '$mid' and termekID = '$termek_id'");
		}
	}

	function getLastViewedTermek($mID, $limit = 5, $arg = array()){
		$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
		$apsz = $akcios_plus_szaz / 100 + 1;

		$q = "SELECT
			v.*,
			CONCAT(m.neve,' ',t.nev) as termekNev,
			FULLIMG(t.profil_kep) as profil_kep,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			t.akcios,
			t.szuper_akcios_szazalek,
			t.utolso_darab,
			IF(t.akcios,
			IF(t.akcios_egyedi_brutto_ar != 0,
				t.akcios_egyedi_brutto_ar,
				getTermekAr(t.marka, (t.brutto_ar * ".$apsz."))),
			getTermekAr(t.marka, t.brutto_ar)) as brutto_ar,
			IF(t.egyedi_ar IS NOT NULL, t.egyedi_ar, getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))) as ar
		FROM `shop_utoljaraLatottTermek` as v
		LEFT OUTER JOIN shop_termekek as t ON t.ID = v.termekID
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		WHERE v.mID = '$mID' and t.ID IS NOT NULL
		ORDER BY v.idopont DESC
		LIMIT 0,$limit";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		return $data;
	}

	function getMostViewedTermekek($limit = 10){
		$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
		$apsz = $akcios_plus_szaz / 100 + 1;

		$q = "SELECT
			t.ID as termekID,
			CONCAT(m.neve,' ',t.nev) as termekNev,
			FULLIMG(t.profil_kep) as profil_kep,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			t.akcios,
			t.szuper_akcios_szazalek,
			t.utolso_darab,
			IF(t.akcios,
			IF(t.akcios_egyedi_brutto_ar != 0,
				t.akcios_egyedi_brutto_ar,
				getTermekAr(t.marka, (t.brutto_ar * ".$apsz."))),
			getTermekAr(t.marka, t.brutto_ar)) as brutto_ar,
			IF(t.egyedi_ar IS NOT NULL, t.egyedi_ar, getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))) as ar,
			(SELECT sum(me) FROM `stat_nezettseg_termek` WHERE termekID = t.ID and datediff(now(),datum) < 60) as v
		FROM `shop_termekek` as t
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		WHERE t.ID IS NOT NULL
		ORDER BY v DESC, t.nev ASC
		LIMIT 0, $limit";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		return $data;
	}

	function orderAlreadyPaidViaPayPal($accessKey){
		$check = $this->db->query("SELECT paypal_fizetve FROM orders WHERE accessKey = '$accessKey'")->fetch(PDO::FETCH_COLUMN);

		if($check == 1){
			return true;
		}else{
			return false;
		}
	}

	function setOrderPaidByPayPal($accessKey){
		// Log
			$this->db->update("orders",
				array(
					'paypal_fizetve' => 1,
					'allapot' => 10
				),
			"accessKey = '$accessKey'"
			);

		// Admin alert
		$order = $this->getOrderData($accessKey);

		$vegosszeg 	= 0;
		$param 		= array();
		$msg 		= '';
		$msg 		.= '<div><strong>'.$order[azonosito].' megrendelés kifizetésre került PayPal-on keresztül!</strong></div>';
		$msg 		.= '<br /><br />';
		$msg 		.= '<div>Megrendelés: <a href="http://cp.goldfishing.hu/megrendelesek/?ID='.$order[ID].'">http://cp.goldfishing.hu/megrendelesek/?ID='.$order[ID].'</a></div>';

		foreach($order[items] as $d):
			$vegosszeg += $d[subAr];
		endforeach;

		if($order[szallitasi_koltseg] > 0) $vegosszeg += $order[szallitasi_koltseg];
		if($order[kedvezmeny] > 0) $vegosszeg -= $order[kedvezmeny];

		$msg 		.= '<div><h3>Információk</h3></div>';
		$msg 		.= '<table width="100%" border="1" style="border-collapse:collapse; border:2px solid #dddddd; background:#ffffff;" cellpadding="10" cellspacing="0">';
		$msg 		.= '<tbody>';
			$msg 		.= '<tr>';
				$msg 		.= '<td width="180" align="left">Megrendelés összege</td>';
				$msg 		.= '<td align="left"><strong>'.Helper::cashFormat($vegosszeg).' Ft</strong></td>';
			$msg 		.= '</tr>';
			$msg 		.= '<tr>';
				$msg 		.= '<td width="150" align="left">Tranzakció ideje</td>';
				$msg 		.= '<td align="left"><strong>'.NOW.'</strong></td>';
			$msg 		.= '</tr>';
		$msg 		.= '</tbody>';
		$msg 		.= '</table>';
		$msg 		.= '<br /><br />';
		$msg 		.= 'A megrendelés állapota automatikusan <strong>"PayPal visszaigazolásra vár"</strong> állapotra lett állítva!';

		$msg 		.= '<br /><br />';
		$msg 		.= '<span style="color:#CC0000;">A tranzakció tájékoztató jellegű. A befizetést a PayPal fiókjában is ellenőrizni kell, hogy a befizetés megtörtént-e.</span>';

		$param[msg] 		= $msg;
		$param[elnevezes] 	= 'Értesítő';

		$remsg = Helper::smtpMail(array(
			'recepiens' => array(ALERT_EMAIL),
			'msg' 	=> $msg,
			'tema' 	=> 'Értesítő',
			'from' 	=> NOREPLY_EMAIL,
			'sub' 	=> 'PayPal befizetés: '.$order[azonosito].' megrendelés'
		));
	}

	public $messageType = array('requestAsk','recall','contactMsg','contactRecall');
	function logMessage($opts = array()){
		$item_id 		= 'NULL';

		extract($opts);

		if($felado_nev == '' || $uzenet_targy == '' || $uzenet == '' || $tipus == '') return false;

		if(!in_array($tipus,$this->messageType)) return false;

		$this->db->insert("uzenetek",
			array('item_id', 'felado_email', 'felado_nev', 'uzenet_targy', 'uzenet', 'tipus'),
			array($item_id, $felado_email, $felado_nev, $uzenet_targy, $uzenet, $tipus)
		);
	}
	public function __destruct()
	{
		$this->db = null;
	}
}

class OrderException extends Exception{
	private $errorArr = array(
		'success' 	=> 0,
		'msg' 		=> ''
	);
	public function __construct($message, $errorArray = false, $code = 0, Exception $previous = null) {
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
