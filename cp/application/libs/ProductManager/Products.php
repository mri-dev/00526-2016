<?
namespace ProductManager;

/**
* class Products
* @package ProductManager
* @version 1.0
*/
class Products
{
	const TAG_IMG_NOPRODUCT = 'no-product-img.png';
	const B2BFLAGID_IN_TRUE = '0,1';
	const B2BFLAGID_IN_FALSE = '0';

	private $db = null;
	private $user = null;
	private $products = null;
	private $products_number = 0;
	private $product_limit_per_page = 50;
	private $max_page = 1;
	private $current_page = 1;
	private $avaiable_sizes = array();
	private $qry_str = null;
	private $selected_sizes = array();

	public function __construct( $arg = array() ) {
		$this->db = $arg[db];
		$this->user = $arg[user];

		return $this;
	}

	public function create(Product $product)
	{

		$uploadedProductId = 0;

		$szallitasID 	= $product->getTransportTimeId();
		$keszletID 		= $product->getStatusId();

		// Kötelező mezők ellenőrzése
		if( !$product->getName() ) throw new \Exception('Termék nevének megadása kötelező!');
		if(	!$product->getManufacturerId() ) throw new \Exception('Márka kiválasztása kötelező!');
		if( !$product->getTransportTimeId() ) throw new \Exception('Szállítási időt kötelező kiválasztani!');
		if( !$product->getStatusId() ) throw new \Exception('Állapotot kötelező kiválasztani!');
		if( !$product->getCategoryList() ) throw new \Exception('Termék kategória kiválasztása kötelező!');
		if( !$product->getPrice() ) throw new \Exception('Termék árát kötelező megadni!');

		if( true ){
			$cikkszam 		= $product->getItemNumber();
			$marka 			= $product->getManufacturerId();
			$lathato 		= $product->isVisible();
			$pickpackszallitas 	= ( !$product->isAllowToPickPackPont() ) ? 0 : 1;
			$akcios 		= ( !$product->isDiscounted() ) ? 0 : 1;
			$netto_ar 		= $product->getPrice( 'netto' );
			$brutto_ar 		= $product->getPrice( 'brutto' );
			$akcios_n_ar 	= ( $product->isDiscounted() ) ? $product->getPrice( 'netto', 'akcios' ) : 0;
			$akcios_b_ar 	= ( $product->isDiscounted() ) ? $product->getPrice( 'brutto', 'akcios' ) : 0;
			$argep 	  		= ( !$product->isListedInArgep() ) ? 0 : 1;
			$arukereso 		= ( !$product->isListedInArukereso()) ? 0 : 1;
			$ujdonsag 		= ( !$product->isNewest()) ? 0 : 1;

			$nev 			= addslashes( $product->getName() );
			$raktar_keszlet = $product->getStockNumber();
			$leiras 		= addslashes( $product->getDescription() );
			$rovid_leiras	= addslashes( $product->getShortDescription() );
			$garancia 		= ( $product->getGuarantee() ) ?: NULL;
			$link_list		= $product->getLinks();
			$szin 			= $product->getVariable('szin');
			$meret 			= $product->getVariable('meret');
			$kapcsolatok 	= $product->getVariable('connects');
			$linkek 		= '';

			// Csatolt hivatkozások előkészítése
			if( $link_list ) {
				foreach( $link_list as $lnev => $url ){
					if( $lnev != '' && $url != '' ){
						$linkek .= trim($lnev)."==>".trim($url)."||";
					}
				}
				$linkek = rtrim( $linkek, '||' );
			}

			$this->db->insert(
				'shop_termekek',
				array(
					'cikkszam' => $cikkszam,
					'marka' => $marka,
					'nev' => $nev,
					'leiras' => $leiras,
					'rovid_leiras' => $rovid_leiras,
					'netto_ar' => $netto_ar,
					'brutto_ar' => $brutto_ar,
					'lathato' => $lathato,
					'pickpackszallitas' => $pickpackszallitas,
					'akcios' => $akcios,
					'akcios_netto_ar' => $akcios_n_ar,
					'akcios_brutto_ar' => $akcios_b_ar,
					'szallitasID' => $szallitasID,
					'keszletID' => $keszletID,
					'ujdonsag' => $ujdonsag,
					'argep' => $argep,
					'arukereso' => $arukereso,
					'garancia_honap' => $garancia,
					'linkek' => $linkek,
					'raktar_keszlet' => $raktar_keszlet,
					'szin' => $szin,
					'meret' => $meret
				)
			);

			$uploadedProductId = $this->lastInsertId();

			// Termékek összekőtése
			if( is_array($kapcsolatok) && count($kapcsolatok) > 0 ) {
				foreach ($kapcsolatok as $tid) {
					$this->connectProducts( $uploadedProductId, $tid );
				}
			}

			// Képfeltöltés
			if($_FILES[img][name][0] != ''){
				$dir 	= 'p'.$uploadedProductId;
				$idir 	= 'src/products/'.$dir;

				// Termékmappa létrehozás / Permission
				if( !file_exists($idir) ){
					mkdir( $idir, 0777, true );
				}

				// Feltöltése
				$mt 		= explode(" ",str_replace(".","",microtime()));
				$imgName 	= \PortalManager\Formater::makeSafeUrl( $this->getManufacturName($marka).'-'.$nev.'__'.date('YmdHis').$mt[0] );
				$img 		= \Images::upload(array(
					'src' => 'img',
					'upDir' => $idir,
					'noRoot' => true,
					'fileName' => $imgName,
					'maxFileSize' => 1024
				));

				/*
				$upDir 		= str_replace(array('../img/'),array(''),$img[dir]);
				$upProfil 	= str_replace(array('../img/'),array(''),$img[file]);
				*/

				$upDir 		= $img[dir];
				$upProfil 	= $img[file];

				$this->db->update(
					'shop_termekek',
					array(
						'kep_mappa' => $upDir,
						'profil_kep' => $upProfil
					),
					"ID = $uploadedProductId"
				);

				foreach( $img['allUploadedFiles'] as $kep ){
					$this->addImageToProduct( $uploadedProductId, $kep );
				}
			}


			// Kategóriákba sorolás
			if ( $product->getCategoryList() ) {
			 	$this->doCategoryConnect( $uploadedProductId, $product->getCategoryList() );
			}

			// Regiszterek
			if($pickpackszallitas == 1)
				setcookie("cr_pickpackszallitas","on",time()+3600*30,"/termekek");
			else
				setcookie("cr_pickpackszallitas",false,time()-3600,"/termekek");


			if($akcios == 1)
				setcookie("cr_akcios","on",time()+3600*30,"/termekek");
			else
				setcookie("cr_akcios",false,time()-3600,"/termekek");

			if($szuper_akcios == 1)
				setcookie("cr_szuper_akcios","on",time()+3600*30,"/termekek");
			else
				setcookie("cr_szuper_akcios",false,time()-3600,"/termekek");

			if($ujdonsag == 1)
				setcookie("cr_ujdonsag","on",time()+3600*30,"/termekek");
			else
				setcookie("cr_ujdonsag",false,time()-3600,"/termekek");

			if($argep == 1)
				setcookie("cr_argep","on",time()+3600*30,"/termekek");
			else
				setcookie("cr_argep",false,time()-3600,"/termekek");

			if($arukereso == 1)
				setcookie("cr_arukereso","on",time()+3600*30,"/termekek");
			else
				setcookie("cr_arukereso",false,time()-3600,"/termekek");

			if($szallitasID)
				setcookie("cr_szallitasID",$szallitasID,time()+3600*30,"/termekek");
			else
				setcookie("cr_szallitasID",false,time()-3600,"/termekek");

			if($keszletID)
				setcookie("cr_keszletID",$keszletID,time()+3600*30,"/termekek");
			else
				setcookie("cr_keszletID",false,time()-3600,"/termekek");

			return 'Termék sikeresen létrehozva! ';
		}

		return $product;
	}

	public function save(Product $product)
	{
		if ( !$product->getId() ) {
			 throw new \Exception('Mentés sikertelen! Nem találjuk a termék azonosítóját!');
		}

		$szallitasID 	= $product->getTransportTimeId();
		$keszletID 		= $product->getStatusId();

		// Kötelező mezők ellenőrzése
		if( !$product->getName() ) throw new \Exception('Termék nevének megadása kötelező!');
		if(	!$product->getManufacturerId() ) throw new \Exception('Márka kiválasztása kötelező!');
		if( !$product->getTransportTimeId() ) throw new \Exception('Szállítási időt kötelező kiválasztani!');
		if( !$product->getStatusId() ) throw new \Exception('Állapotot kötelező kiválasztani!');
		if( !$product->getCategoryList() ) throw new \Exception('Termék kategória kiválasztása kötelező!');

		if( true ){
			$cikkszam 		= $product->getItemNumber();
			$marka 			= $product->getManufacturerId();
			$lathato 		= $product->isVisible();
			$pickpackszallitas 	= ( !$product->isAllowToPickPackPont() ) ? 0 : 1;
			$akcios 		= ( !$product->isDiscounted() ) ? 0 : 1;
			$netto_ar 		= $product->getPrice( 'netto' );
			$brutto_ar 		= $product->getPrice( 'brutto' );
			$akcios_n_ar 	= ( $product->isDiscounted() ) ? $product->getPrice( 'netto', 'akcios' ) : 0;
			$akcios_b_ar 	= ( $product->isDiscounted() ) ? $product->getPrice( 'brutto', 'akcios' ) : 0;
			$argep 	  		= ( !$product->isListedInArgep() ) ? 0 : 1;
			$arukereso 		= ( !$product->isListedInArukereso()) ? 0 : 1;
			$ujdonsag 		= ( !$product->isNewest()) ? 0 : 1;

			$nev 			= addslashes( $product->getName() );
			$raktar_keszlet = $product->getStockNumber();
			$leiras 		= addslashes( $product->getDescription() );
			$rovid_leiras	= addslashes( $product->getShortDescription() );
			$garancia 		= ( $product->getGuarantee() ) ?: NULL;
			$link_list		= $product->getLinks();
			$linkek 		= '';
			$kulcsszavak	= (!$product->getVariable('kulcsszavak')) ? NULL : $product->getVariable('kulcsszavak');
			$raktar_articleid	= (!$product->getVariable('raktar_articleid')) ? NULL : $product->getVariable('raktar_articleid');
			$raktar_variantid	= (!$product->getVariable('raktar_variantid')) ? NULL : $product->getVariable('raktar_variantid');
			$raktar_supplierid	= (!$product->getVariable('raktar_supplierid')) ? NULL : $product->getVariable('raktar_supplierid');
			$raktar_number	= (!$product->getVariable('raktar_number')) ? NULL : $product->getVariable('raktar_number');


			// Csatolt hivatkozások előkészítése
			if( $link_list ) {
				foreach( $link_list as $lnev => $url ){
					if( $lnev != '' && $url != '' ){
						$linkek .= trim($lnev)."==>".trim($url)."||";
					}
				}
				$linkek = rtrim( $linkek, '||' );
			}

			$meret = ( !$product->getVariable( 'meret' ) ) ? NULL : $product->getVariable( 'meret' );
			$szin = ( !$product->getVariable( 'szin' ) ) ? NULL : $product->getVariable( 'szin' );

			$this->db->update(
				'shop_termekek',
				array(
					'cikkszam' => $cikkszam,
					'marka' => $marka,
					'nev' => $nev,
					'leiras' => $leiras,
					'rovid_leiras' => $rovid_leiras,
					'lathato' => $lathato,
					'pickpackszallitas' => $pickpackszallitas,
					'akcios' => $akcios,
					'szallitasID' => $szallitasID,
					'keszletID' => $keszletID,
					'ujdonsag' => $ujdonsag,
					'argep' => $argep,
					'arukereso' => $arukereso,
					'garancia_honap' => $garancia,
					'kulcsszavak' => $kulcsszavak,
					'linkek' => $linkek,
					'raktar_keszlet' => $raktar_keszlet,
					'meret' => $meret,
					'szin' => $szin,
					'fotermek' => ($product->isMainProduct() ? 1 : 0),
					'raktar_articleid' => $raktar_articleid,
					'raktar_variantid' => $raktar_variantid,
					'raktar_supplierid' => $raktar_supplierid,
					'raktar_number' => $raktar_number
				),
				sprintf("ID = %d", $product->getId())
			);

			// Kategóriákba sorolás
			if ( $product->getCategoryList() ) {
			 	$this->doCategoryConnect( $product->getId(), $product->getCategoryList() );
			}

			// Árváltozások módosítása
				// Sima ár változtatása
				if( $product->getVariable('ar_by') ) {
	 				if( $product->getVariable('ar_by') == 'netto' ){
						$brutto_ar = $netto_ar * 1.27;
					}else if( $product->getVariable('ar_by') == 'brutto' ){
						$netto_ar = $brutto_ar/1.27;
					}else{
						$donot_ar = true;
					}
				}

				// Akciós ár változtatás
				if( $product->getVariable('akcios_ar_by') ) {
					if( $product->getVariable('akcios_ar_by') == 'netto' ){
						$akcios_b_ar = $akcios_n_ar * 1.27;
					}else if( $product->getVariable('akcios_ar_by') == 'brutto' ){
						$akcios_n_ar = $akcios_b_ar/1.27;
					}else{
						$donot_akcios_ar = true;
					}
				}

				if(!$donot_ar){
					$this->db->update(
						'shop_termekek',
						array(
							'netto_ar' 	=> $netto_ar,
							'brutto_ar' => $brutto_ar
						),
						sprintf("ID = %d", $product->getId())
					);
				}

				if(!$donot_akcios_ar){
					if ( $akcios_n_ar == 0 || $akcios_b_ar == 0 ) {
						$this->db->update(
							'shop_termekek',
							array(
								'akcios' => 0
							),
							sprintf("ID = %d", $product->getId())
						);
					}

					$this->db->update(
						'shop_termekek',
						array(
							'akcios_netto_ar' 	=> $akcios_n_ar,
							'akcios_brutto_ar' => $akcios_b_ar
						),
						sprintf("ID = %d", $product->getId())
					);
				};

			return 'Termék adatok sikeresen módosítva! ';
		}

		return $product;
	}

	public function connectProducts( $id1, $id2 )
	{
		$c1 = $this->db->query("SELECT 1 FROM shop_termek_kapcsolatok WHERE termek_from = $id1 and termek_to = $id2;");
		if ( $c1->rowCount() == 0 ) {
			$this->db->insert(
				"shop_termek_kapcsolatok",
				array(
					'termek_from' => $id1,
					'termek_to' => $id2
				)
			);
		}

		$c2 = $this->db->query("SELECT 1 FROM shop_termek_kapcsolatok WHERE termek_to = $id1 and termek_from = $id2;");
		if ( $c2->rowCount() == 0 ) {
			$this->db->insert(
				"shop_termek_kapcsolatok",
				array(
					'termek_to' => $id1,
					'termek_from' => $id2
				)
			);
		}
	}

	public function disconnectProducts( $id1, $id2 )
	{
		$this->db->query("DELETE FROM shop_termek_kapcsolatok WHERE (termek_from = $id1 and termek_to = $id2) or (termek_to = $id1 and termek_from = $id2)");
	}



	public function prepareList( $arg = array() )
	{
		$this->products = array();
		$this->products_number = 0;

		if ( $arg['limit'] ) {
			if( $arg['limit'] > 0 ) {
				$this->product_limit_per_page = ( is_numeric($this->product_limit_per_page) && $this->product_limit_per_page > 0) ? (int)$arg['limit'] : $this->product_limit_per_page;
			} else if( $arg['limit'] == -1 ){
				$this->product_limit_per_page = 999999999999;
			}
		}

		$admin_listing = ( $arg['admin'] ) ? true : false;

		/*==========  Lekérdezés  ==========*/
		$qry = "
		SELECT SQL_CALC_FOUND_ROWS
			p.ID as product_id,
			p.nev as product_nev,
			p.cikkszam,
			p.kulcsszavak,
			p.pickpackszallitas,
			p.akcios,
			p.ujdonsag,
			p.netto_ar,
			p.brutto_ar,
			p.akcios_netto_ar,
			p.akcios_brutto_ar,
			p.egyedi_ar,
			p.marka as marka_id,
			p.szallitasID,
			p.keszletID,
			p.raktar_keszlet,
			p.profil_kep,
			p.kep_mappa,
			p.lathato,
			p.szin,
			p.csoport_kategoria,
			p.meret,
			IF(p.egyedi_ar IS NOT NULL,
				p.egyedi_ar,
				getTermekAr(p.marka, IF(p.akcios,p.akcios_brutto_ar,p.brutto_ar))
			) as ar,
			(SELECT GROUP_CONCAT(t.meret) FROM shop_termek_kapcsolatok as tk LEFT OUTER JOIN shop_termekek as t ON t.ID = tk.termek_to WHERE tk.termek_from = p.ID) as sizes,
			p.fotermek,
			(SELECT GROUP_CONCAT(kategoria_id) FROM shop_termek_in_kategoria WHERE termekID = p.ID ) as in_cat,
			(SELECT neve FROM shop_termek_kategoriak WHERE ID = p.alapertelmezett_kategoria ) as alap_kategoria
		FROM					shop_termekek as p
		WHERE 				1 = 1 ";

		$whr = '';
		$size_whr = '';
		$add = '';

		if ( $arg['b2b'] === true ) {
			$add = " and p.b2b IN(".self::B2BFLAGID_IN_TRUE.") ";
			$whr .= $add;
			$size_whr .= $add;
		}
		else
		{
			$add = " and p.b2b IN(".self::B2BFLAGID_IN_TRUE.") ";
			$whr .= $add;
			$size_whr .= $add;
		}

		if (!$admin_listing) {
			$add = " and p.lathato = 1 and p.profil_kep IS NOT NULL ";
			$whr .= $add;
			$size_whr .= $add;

			if(!empty($arg['meret']) && $arg['meret'][0] != ''){
				$add = " and p.meret IN ('".trim(implode("','",$arg['meret']))."') ";
				$whr .= $add;
			} else {
				/* $add = " and p.fotermek = 1 ";
				$whr .= $add;*/
			}

		}

		// WHERE
		if ( $arg['in_cat'] ) {
			$add = " and FIND_IN_SET(".$arg['in_cat'].",(SELECT GROUP_CONCAT(kategoria_id) FROM shop_termek_in_kategoria WHERE termekID = p.ID )) ";
			$whr .= $add;
			$size_whr .= $add;
		}

		if ( $arg['csoport_kategoria'] ) {
			$add = " and p.csoport_kategoria = '{$arg[csoport_kategoria]}' ";
			$whr .= $add;
			$size_whr .= $add;
		}

		// Keresés
		if ( $arg['search'] && is_array($arg['search']) && !empty($arg['search']) ) {
			$add = " and (";
				foreach ($arg['search'] as $src ) {
					$add .= "(p.nev LIKE '%".$src."%' or p.kulcsszavak LIKE '%".$src."%' or p.rovid_leiras LIKE '%".$src."%') and ";
				}
				$add = rtrim($add," and ");
			$add .= ") ";

			$whr .= $add;
			$size_whr .= $add;
		}

		if ( !empty($arg['meret']) && $arg['meret'][0] != '' ) {
			$this->selected_sizes = $arg['meret'];

			$add = " and (";
			foreach ( $arg['meret'] as $size ) {
				if( $size == "" ) continue;

				$add .= "(FIND_IN_SET('".$size."' ,(SELECT GROUP_CONCAT(t.meret) FROM shop_termek_kapcsolatok as tk LEFT OUTER JOIN shop_termekek as t ON t.ID = tk.termek_to WHERE tk.termek_from = p.ID)) or '$size' = p.meret) or";
			}
			$add = rtrim($add," or");
			$add .= ")";
			$whr .= $add;
		}

		// Excepts
		if( $arg['except'] ) {
			foreach ($arg['except'] as $key => $value) {
				if( is_array($value) ) {
					$value_set = false;

					if( count($value) > 0 ) {
						$value_set = implode(',', $value);
					}

					if($value_set){
						$add = " and p.".$key." NOT IN (".$value_set.") ";
						$whr .= $add;
						$size_whr .= $add;
					}
				} else {
					$add = " and p.".$key." != '".$value."' ";
					$whr .= $add;
					$size_whr .= $add;
				}
			}
		}

		if(count($arg['filters']) > 0){
			foreach($arg['filters'] as $key => $v){
				switch($key)
				{
					case 'ID':
						if( is_array($v) ) {
							$value_set = false;

							if( count($v) > 0 ) {
								$value_set = implode(',', $v);
							}

							if($value_set){
								$add = " and p.".$key." IN (".$value_set.") ";
								$whr .= $add;
								$size_whr .= $add;
							}
						} else {
							$add = " and p.".$key." LIKE '".$v."%' ";
							$whr .= $add;
							$size_whr .= $add;

						}

					break;
					case 'cikkszam':
						$add = " and p.".$key." LIKE '".$v."%' ";
						$whr .= $add;
						$size_whr .= $add;
					break;
					case 'nev':
						$add = " and p.".$key." LIKE '%".$v."%' ";
						$whr .= $add;
						$size_whr .= $add;
					break;
					default:
						if( is_array($v) ) {
							$value_set = false;

							if( count($v) > 0 ) {
								$value_set = implode(',', $v);
							}

							if($value_set){
								$add = " and p.".$key." IN (".$value_set.") ";
								$whr .= $add;
								$size_whr .= $add;
							}
						} else {
							$add = " and ".$key." = '".$v."' ";
							$whr .= $add;
							$size_whr .= $add;
						}

					break;
				}

			}
		}
		$qry .= $whr;

		// GROUP BY
		if (!$admin_listing ) {
			if( !empty($arg['meret']) ) {
				$qry .= "GROUP BY p.raktar_articleid";
			} else {
				$qry .= "GROUP BY p.raktar_articleid";
			}
		}

		// ORDER

		if( $arg['order'] ) {
			$qry .= " ORDER BY ".$arg['order']['by']." ".$arg['order']['how'];
		} else {
			$qry .= " ORDER BY nev ASC, fotermek DESC, ID DESC ";
		}

		// LIMIT
		$current_page = ($arg['page'] ?: 1);
		$start_item = $current_page * $this->product_limit_per_page - $this->product_limit_per_page;
		$qry .= " LIMIT ".$start_item.",".$this->product_limit_per_page.";";

		//echo $qry;

		$this->qry_str = $qry;

		$get = $this->db->query( $qry );

		$data =  $get->fetchAll(\PDO::FETCH_ASSOC);

		$this->products_number = $this->db->query("SELECT FOUND_ROWS();")->fetch(\PDO::FETCH_COLUMN);

		$this->max_page = ceil($this->products_number / $this->product_limit_per_page);
		$this->current_page = $current_page;

		// Get sizes
		$sqry = "
		SELECT 		p.meret
		FROM 		shop_termekek as p
		WHERE 		p.ID IS NOT NULL
		";
		$sqry .= $size_whr;
		/* $sqry .= " ORDER BY
		IF(
			p.meret RLIKE '[a-z]',
			IF(	p.meret RLIKE '[1-9]{1}[a-z]',
				-1,
				-3
			), -10) ASC,
		CAST(p.meret as unsigned) ASC ";*/
		$sqry .= " ORDER BY
		(CASE
			WHEN p.meret RLIKE '^[0-9]{1,3}$' THEN 0
			WHEN p.meret RLIKE '^XXS$' THEN 1
			WHEN p.meret RLIKE '^XS$' THEN 2
			WHEN p.meret RLIKE '^S$' THEN 3
			WHEN p.meret RLIKE '^M$' THEN 4
			WHEN p.meret RLIKE '^L$' THEN 5
			WHEN p.meret RLIKE '^XL$' THEN 6
			WHEN p.meret RLIKE '^XXL$' THEN 7
			WHEN p.meret RLIKE '^3XL$' THEN 8
			WHEN p.meret RLIKE '^4XL$' THEN 9
			WHEN p.meret RLIKE '^5XL$' THEN 10
			ELSE 100
		END) ASC,
		CAST(p.meret as unsigned) ASC";

		$s_qry_data = $this->db->query( $sqry )->fetchAll(\PDO::FETCH_ASSOC);

		foreach ( $s_qry_data as $s ) {
			if($s['meret'] == '') continue;
			if (!in_array($s['meret'], $this->avaiable_sizes)) {
				$this->avaiable_sizes[] = $s['meret'];
			}
		}

		$bdata = array();

		foreach($data as $d){
			$brutto_ar 			= $d['brutto_ar'];
			$akcios_brutto_ar 	= $d['akcios_brutto_ar'];

			$kep = $d['profil_kep'];
			$d['profil_kep'] 		=  \PortalManager\Formater::productImage( $kep, false, self::TAG_IMG_NOPRODUCT );
			$d['profil_kep_small'] 	=  \PortalManager\Formater::productImage( $kep, 75, self::TAG_IMG_NOPRODUCT );

			$arInfo 		= $this->getProductPriceCalculate( $d['marka_id'], $brutto_ar );
			$akcios_arInfo 	= $this->getProductPriceCalculate( $d['marka_id'], $akcios_brutto_ar );

			if( $d['akcios'] == '1') {
				$arInfo['ar'] = $arInfo['ar'];
			}

			$arInfo['ar'] 			= ($this->settings['round_price_5'] == '1') ? round($arInfo['ar'] / 5) * 5 : $arInfo['ar'] ;
			$akcios_arInfo['ar'] 	= ($this->settings['round_price_5'] == '1') ? round($akcios_arInfo['ar'] / 5) * 5 : $akcios_arInfo['ar'] ;

			// Kategória lista, ahol szerepel a termék
			$in_cat = $this->getCategoriesWhereProductIn( $d['product_id'] );

			$d['link'] 				= DOMAIN.'termek/'.\PortalManager\Formater::makeSafeUrl( $d['product_nev'], '_-'.$d['product_id'] );
			$d['hasonlo_termek_ids']= $this->getProductRelatives( $d['product_id'] );
			$d['inKatList'] 		= $in_cat;
			$d['ar'] 				= $arInfo['ar'];
			$d['akcios_fogy_ar']	= $akcios_arInfo['ar'];
			$d['arres_szazalek'] 	= $arInfo['arres'];

			$bdata[]	 			= $d;
		}

		$this->products = $bdata;

		return $this;
	}

	public function getList()
	{
		return $this->products;
	}

	public function hasItems()
	{
		return ($this->products_number == 0) ? false : true;
	}

	private function getProductPriceCalculate($markaID, $bruttoAr){
		$re 	  = array();
		$re[info] =  array();
		$re[arres] = 0;
		$re[ar]   =  $bruttoAr;

		if(empty($markaID)) return false;

		// Márka adatok
		$marka = $this->db->query("SELECT fix_arres FROM shop_markak WHERE ID = $markaID")->fetch(\PDO::FETCH_ASSOC);

		if( !is_null($marka['fix_arres']) ) {
			// Fix árrés
			$re[info] 	= 'FIX : '.$marka[fix_arres].'%';
			$re[arres] 	= $marka[fix_arres];
			$re[ar] 	= round($bruttoAr * ($marka[fix_arres]/100+1));
		} else {
			// Sávos árrés
			$savok = $this->db->query("SELECT ar_min, ar_max, arres FROM shop_marka_arres_savok WHERE markaID = $markaID ORDER BY ar_min ASC")->fetchAll(\PDO::FETCH_ASSOC);

			foreach( $savok as $s ){
				$min = $s[ar_min];
				$max = $s[ar_max];
				$max = (is_null($max)) ? 999999999999999 : $max;

				if( $bruttoAr >= $min && $bruttoAr <= $max ) {
					$re[info] 	= $min.' - '.$max.' : '.$s[arres].'%';
					$re[arres] 	= $s[arres];
					$re[ar] 	= round($bruttoAr * ($s[arres]/100+1));
					break;
				} else {
					$re[info] 	= $min.' - '.$max.' : '.$s[arres].'%';
					$re[arres] 	= $s[arres];
					$re[ar] 	= round($bruttoAr * ($s[arres]/100+1));
				}

			}
		}

		// Kedvezményes ár csökkentés
		if( $this->user && $this->user[kedvezmeny] > 0 ) {
			\PortalManager\Formater::discountPrice( $re[ar], $this->user[kedvezmeny] );
		}

		return $re;
	}

	/**
	 * Termék kategóriákba való becsatolása/kicsatolás
	 * A program megvizsgálja a már becsatolt kategóriákat a terméknél. Ha a listában nem szerepel egy kategória ID, ami a már becsatoltak közt van, akkor azt eltávolítja.
	 *
	 * @param  int $product_id
	 * @param  array $cat_list   kategória ID lista
	 * @return boolean
	 */
	public function doCategoryConnect( $product_id, $cat_list )
	{
		if ( !$cat_list || empty( $cat_list ) ) return false;

		// Kategória ID-k, ahova már be lett csatolva a termék
		$already_connected = $this->getProductInCategory( $product_id );

		foreach ( $cat_list as $i => $cat_id ) {
			//Ha még nem lett becsatolva egy kategóriába
			if (!in_array( $cat_id, $already_connected )) {
				$this->addProductToCategory( $product_id, $cat_id );
			} else {
				if ( ($key = array_search($cat_id, $already_connected)) !== false ) {
				    unset( $already_connected[$key] );
				}
			}
		}

		// Termék kiszedése azokból a kategóriákból, amelyikeknél kiszedtük a pipát
		$this->removeProductFromCategories( $product_id, $already_connected );

		return true;
	}

	/**
	 * Termék eltávolítás kategóriákból
	 * @param  int $product_id
	 * @param  array $category_list kategória ID lista, ahonnan ki szeretnénk szedni a termékeket
	 * @return void
	 */
	public function removeProductFromCategories( $product_id, $category_list = array() )
	{
		if( empty($category_list) ) return false;

		$katliststr = implode(',', $category_list);

		$dq = "DELETE FROM shop_termek_in_kategoria WHERE termekID = $product_id and kategoria_id IN ($katliststr)";

		$this->db->query( $dq );
	}

	/**
	 * Termék becsatolása egy adott kategóriába
	 * @param int $product_id
	 * @param int $cat_id
	 */
	public function addProductToCategory( $product_id, $cat_id )
	{
		$this->db->insert(
			'shop_termek_in_kategoria',
			array(
				'kategoria_id' => $cat_id,
				'termekID' => $product_id,
			)
		);
	}
	/**
	 * Egy adott termék kategória ID listája, ahova be van csatolva
	 * @param  int $product_id
	 * @return array             Kategória ID lista, ahova be lett csatolva a termék
	 */
	public function getProductInCategory( $product_id, $get_names = false )
	{
		if ( !$product_id ) return false;

		$cat_ids = array();

		$qry = $this->db->query( sprintf( "
			SELECT 				k.kategoria_id,
								IF(kat.szulo_id IS NULL, kat.neve, CONCAT(p.neve, ' / ', kat.neve)) as neve,
								kat.hashkey,
								kat.oldal_hashkeys
			FROM 				shop_termek_in_kategoria as k
			LEFT OUTER JOIN 	shop_termek_kategoriak as kat ON kat.ID = k.kategoria_id
			LEFT OUTER JOIN 	shop_termek_kategoriak as p ON p.ID = kat.szulo_id
			WHERE 				k.termekID = %d
			ORDER BY 			p.sorrend ASC, kat.sorrend ASC", $product_id ) );

		if( $qry->rowCount() == 0 ) return $cat_ids;

		$data = $qry->fetchAll(\PDO::FETCH_ASSOC);

		$page_hashkeys = '';
		foreach ( $data as $v ) {
			if ($get_names) {
				$cat_ids['id'][] = $v['kategoria_id'];
				$cat_ids['name'][] = $v['neve'];
				$cat_ids['hashkey'][] = $v['hashkey'];

				if( $v['oldal_hashkeys'] ) {
					$page_hashkeys .= ','.$v['oldal_hashkeys'];
				}

			} else {
				$cat_ids[] = $v['kategoria_id'];
			}

		}

		$cat_ids['page_hashkeys'] = ltrim( $page_hashkeys, ',' );

		return $cat_ids;
	}

	public function getCategoriesWhereProductIn( $product_id )
	{
		if ( !$product_id ) return false;

		$cat_ids = array();

		$qry = $this->db->query( sprintf( "
			SELECT  			c.kategoria_id as id,
								IF(cat.szulo_id IS NOT NULL, CONCAT(pc.neve,' / ', cat.neve), cat.neve) as neve
			FROM 				shop_termek_in_kategoria as c
			LEFT OUTER JOIN 	shop_termek_kategoriak as cat ON cat.ID = c.kategoria_id
			LEFT OUTER JOIN 	shop_termek_kategoriak as pc ON pc.ID = cat.szulo_id
			WHERE 				c.termekID = %d
			ORDER BY			cat.deep ASC, cat.sorrend ASC", $product_id ) );

		if( $qry->rowCount() == 0 ) return $cat_ids;

		$data = $qry->fetchAll(\PDO::FETCH_ASSOC);


		return $data;
	}

	/**
	 * Márka neve, ID alapján
	 * @param  int $id márka id
	 * @return string
	 */
	public function getManufacturName( $id ) {
		$nev = '';
		if($id == '') return $nev;

		$q = $this->db->query("SELECT neve FROM shop_markak WHERE ID = $id")->fetch(\PDO::FETCH_COLUMN);
		$nev = $q;

		return $nev;
	}
	/**
	 * Termékkép mentése adatbázisba
	 * @param int  $termekID
	 * @param string  $kep 	kép elérhetősége
	 * @param int $sorrend  kép sorrend
	 */
	public function addImageToProduct( $termekID, $kep, $sorrend = 0 ) {
		$this->db->insert(
			'shop_termek_kepek',
			array(
				'termekID' => $termekID,
				'kep' => $kep,
				'sorrend' => $sorrend
			)
		);
	}

	public function removeImageFromProduct( $termekID )
	{
		if ( !$termekID ) {
			return false;
		}

		$this->db->query("DELETE FROM shop_termek_kepek WHERE termekID = $termekID;");
	}

	public function setProfilImageToProduct( $termekID, $kep )
	{
		if ( !$termekID ) {
			return false;
		}

		$this->db->query("UPDATE shop_termekek SET profil_kep = '$kep' WHERE ID = $termekID;");
	}

	public function getAllProductImages()
	{
		$imgs = array();
		$data = $this->db->query("SELECT kep FROM shop_termek_kepek;")->fetchAll(\PDO::FETCH_ASSOC);

		foreach ($data as $kep ) {
			$img = $kep['kep'];

			$img = preg_replace('/\/{2,}/', '/', $img);

			if ( !in_array($img, $imgs)) {
				$imgs[] = $img;
			}

 		}

		return $imgs;
	}

	public function getProductImages( $product_id )
	{
		$imgs = array();

		if ( !$product_id ) {
			return $imgs;
		}

		$data = $this->db->query("SELECT kep FROM shop_termek_kepek WHERE termekID = $product_id ORDER BY sorrend ASC;")->fetchAll(\PDO::FETCH_ASSOC);

		foreach ($data as $kep ) {
			$img = $kep['kep'];

			$img = preg_replace('/\/{2,}/', '/', $img);

			$imgs[] = $kep['kep'];
 		}

		return $imgs;
	}

	public function getProductRelatives( $product_id )
	{
		$set = array();

		/*$data = $this->db->query("
			SELECT			r.termek_to,

							t.nev,
							t.fotermek,
							t.szin,
							t.profil_kep,
							t.meret
			FROM 			shop_termek_kapcsolatok as r
			LEFT OUTER JOIN shop_termekek as t ON t.ID = r.termek_to
			WHERE 			r.termek_from = $product_id and t.ID IS NOT NULL
			ORDER BY 		t.meret ASC;")->fetchAll(\PDO::FETCH_ASSOC);
		*/

		$q .= "
		SELECT			t.ID as termek_to,
						t.nev,
						t.fotermek,
						t.szin,
						t.profil_kep,
						t.meret
		FROM 			shop_termekek as t
		WHERE 			t.lathato = 1 and profil_kep IS NOT NULL and t.raktar_articleid = (SELECT raktar_articleid FROM shop_termekek WHERE ID = $product_id)
		";

		$q .= " ORDER BY
		(CASE
			WHEN t.meret RLIKE '^[0-9]{1,3}$' THEN 0
			WHEN t.meret RLIKE '^XXS$' THEN 1
			WHEN t.meret RLIKE '^XS$' THEN 2
			WHEN t.meret RLIKE '^S$' THEN 3
			WHEN t.meret RLIKE '^M$' THEN 4
			WHEN t.meret RLIKE '^L$' THEN 5
			WHEN t.meret RLIKE '^XL$' THEN 6
			WHEN t.meret RLIKE '^XXL$' THEN 7
			WHEN t.meret RLIKE '^3XL$' THEN 8
			WHEN t.meret RLIKE '^4XL$' THEN 9
			WHEN t.meret RLIKE '^5XL$' THEN 10
			ELSE 100
		END) ASC,
		CAST(t.meret as unsigned) ASC";

		$data = $this->db->query( $q )->fetchAll(\PDO::FETCH_ASSOC);

		$color_set = array();

		foreach ($data as $id ) {
			if( (!empty($this->selected_sizes) && in_array($id['meret'], $this->selected_sizes ) ) || empty($this->selected_sizes)){
				$id['link'] = DOMAIN.'termek/'.\PortalManager\Formater::makeSafeUrl($id['nev'],'_-'.$id['termek_to']);
				$set['ids'][] = $id['termek_to'];
				$set['set'][$id['termek_to']] = $id;

				// Color stack
				if (!array_key_exists( $id['szin'], $color_set )) {
					if( !$id[szin] ) continue;
					$color_set[$id['szin']] = array(
						'img' 	=> \PortalManager\Formater::productImage($id['profil_kep'],false, self::TAG_IMG_NOPRODUCT),
						'sizes' => 1,
						'link' 	=> DOMAIN.'termek/'.\PortalManager\Formater::makeSafeUrl($id['nev'],'_-'.$id['termek_to'])
					);
				} else {
					if( !$id[szin] ) continue;
					$color_set[$id['szin']]['sizes'] = (int)$color_set[$id['szin']]['sizes']+1;
				}
				$color_set[$id['szin']]['size_stack'] .= $id['meret'].', ';
				$color_set[$id['szin']]['size_set'][] = array(
					'size' => $id['meret'],
					'link' => DOMAIN.'termek/'.\PortalManager\Formater::makeSafeUrl($id['nev'],'_-'.$id['termek_to'])
				);
			}

 		}

 		$set['colors'] = $color_set;

		return $set;
	}
/**
	 * Termék adatainak lekérése ID alapján
	 * @param  int $product_id
	 * @param  array  $opt        Opcionális paraméterek:
	 *                            string rows vesszővel elválasztva, visszaadja a kívánt rekordokat
	 * @return array
	 */
	public function get( $product_id, array $opt = array() ) {
		if( $product_id === '' || !isset( $product_id ) || $product_id == '' ) return false;

		$row = "t.*";

		if (isset($opt['rows'])) {
			$row = rtrim( $opt['rows'], ',' );
		}

		$whr = "
			SELECT 			$row,
							k.neve as kategoriaNev,
							ta.elnevezes as keszletNev
			FROM 			shop_termekek as t
			LEFT OUTER JOIN shop_termek_kategoriak as k ON k.ID = t.alapertelmezett_kategoria
			LEFT OUTER JOIN shop_termek_allapotok as ta ON ta.ID = t.keszletID
			WHERE 			t.ID = $product_id
		";

		if ( $opt['b2b'] === true ) {
			$whr .= " and t.b2b IN(".self::B2BFLAGID_IN_TRUE.") ";
		} else {
			$whr .= " and t.b2b IN(".self::B2BFLAGID_IN_FALSE.") ";
		}

		$q = $this->db->query($whr);

		$data = $q->fetch(\PDO::FETCH_ASSOC);


		$brutto_ar 			= $data['brutto_ar'];
		$akcios_brutto_ar 	= $data['akcios_brutto_ar'];

		$kep = $data['profil_kep'];
		$data['profil_kep'] 		=  \PortalManager\Formater::productImage( $kep, false, self::TAG_IMG_NOPRODUCT );
		$data['profil_kep_small'] 	=  \PortalManager\Formater::productImage( $kep, 75, self::TAG_IMG_NOPRODUCT );

		$arInfo 		= $this->getProductPriceCalculate( $data['marka'], $brutto_ar );
		$akcios_arInfo 	= $this->getProductPriceCalculate( $data['marka'], $akcios_brutto_ar );

		if( $d['akcios'] == '1') {
			$arInfo['ar'] = $arInfo['ar'];
		}

		$arInfo['ar'] 			= ($this->settings['round_price_5'] == '1') ? round($arInfo['ar'] / 5) * 5 : $arInfo['ar'] ;
		$akcios_arInfo['ar'] 	= ($this->settings['round_price_5'] == '1') ? round($akcios_arInfo['ar'] / 5) * 5 : $akcios_arInfo['ar'] ;

		$data['rovid_leiras'] 		= $this->addLinkToString( $data, $data['rovid_leiras'] );
		$data['ar'] 				= $arInfo['ar'];
		$data['akcios_fogy_ar']		= $akcios_arInfo['ar'];
		$data['arres_szazalek'] 	= $arInfo['arres'];
		$data['hasonlo_termek_ids'] = $this->getProductRelatives( $product_id );
		$in_kat = $this->getProductInCategory( $product_id, true );
		$data['in_cat_ids'] 		= $in_kat['id'];
		$data['in_cat_names'] 		= $in_kat['name'];
		$data['in_cat_hashkey']		= $in_kat['hashkey'];
		$data['in_cat_page_hashkeys']= $in_kat['page_hashkeys'];
		$data['images'] 			= $this->getProductImages( $product_id );

		$keszlet_info = 			$data['keszletNev'];
		switch( $keszlet_info ) {
			case 'Raktáron':
				$keszlet_info = '<span style="color:green;">'.$keszlet_info.'</span>';
			break;
			case 'Elfogyott':
				$keszlet_info = '<span style="color:red;">'.$keszlet_info.'</span>';
			break;
			case 'Rendelhető':
				$keszlet_info = '<span style="color:orange;">'.$keszlet_info.'</span>';
			break;
			default:
			break;
		}
		$data['keszlet_info'] 		= $keszlet_info;


		// Linkek
		$data['link_lista']			= $this->getProductLinksFromStr( $data['linkek'] );
		// Csatolt link hivatkozások
		$this->getProductLinksFromCategoryHashkeys( $data['in_cat_page_hashkeys'], $data['link_lista'] );

		return $data;
	}

	/**
	 * Egy termék összes elérhető adatának lekérése termék ID alapján
	 * @param  int $product_id
	 * @return boolean|array
	 */
	public function getAllData( $product_id ){
		if( $product_id === '' || !isset( $product_id ) ) return false;

		$q = $this->db->query("
		SELECT 				t.*, t.ID as termekID
		FROM 				shop_termekek as t
		WHERE 				t.ID = $product_id");

		return $q->fetch(\PDO::FETCH_ASSOC);
	}

	public function getProductLinksFromStr( $linkstr )
	{
		$links = array();

		$each = explode("||", $linkstr);

		if ( $each[0] == '' ) {
			return $links;
		}

		foreach ($each as $value) {
			$link = explode("==>", $value);
			$links[] = array(
				'title' => $link[0],
				'link' => $link[1]
			);
		}

		return $links;
	}

	/**
	 * Oldal hivatkozások termékhez csatolt hashkey-ekből
	 * */
	public function getProductLinksFromHashkeys( $productID, &$linkset )
	{
		$hashkeys = explode( ",", $this->db->query("SELECT link_hashkeys FROM shop_termekek WHERE ID = $productID and link_hashkeys IS NOT NULL;")->fetchColumn() );

		// Linkek begyűjtése
		foreach ( $hashkeys as $link_key ) {
			$eleres = $this->db->query("SELECT eleres FROM oldalak WHERE hashkey = '$link_key' and hashkey IS NOT NULL and lathato = 1;")->fetchColumn();

			$title = $this->hashkeyRewrite( $link_key );

			if( $eleres ){
				if( $title == '' ) continue;
				$linkset[] = array(
					'title' => $title,
					'link' => '/p/'.$eleres
				);
			}
		}
	}

	/**
	 * Oldal hivatkozások kategóriához csatolt hashkey-ekből
	 * */
	public function getProductLinksFromCategoryHashkeys( $hashkeys, &$linkset )
	{
		$title_in = array();
		$hashkeys = explode( ",", $hashkeys );
		// Linkek begyűjtése
		foreach ( $hashkeys as $link_key ) {
			$eleres = $this->db->query("SELECT eleres FROM oldalak WHERE hashkey = '$link_key' and hashkey IS NOT NULL and lathato = 1;")->fetchColumn();

			$title = $this->hashkeyRewrite( $link_key );

			if( $eleres ){
				if( $title == '' ) continue;
				if( !in_array( $title, $title_in) ) {
					$title_in[] = $title;
					$linkset[] = array(
						'title' => $title,
						'link' => '/p/'.$eleres
					);
				}
			}
		}

		unset($title_in);
	}

	/**
	 * Hashkey feliratának módosítása karakterek alapján
	 * */
	private function hashkeyRewrite( $link_key)
	{
		$title = $link_key;

		if( strpos( $link_key, 'WS_MERET_' ) === 0 ) {
			$title = 'Mérettáblázat';
		} else if( strpos( $link_key, 'WS_ANYAG_' ) === 0 ) {
			$title = 'Anyagösszetétel';
		} else if( strpos( $link_key, 'WS_EGYEB_' ) === 0 ) {
			// Egyéb linkek
			if( strpos( $link_key, 'USZOHOSSZUSAG' ) !== false ) {
				$title = 'Úszóhosszúság';
			}
		}

		return $title;
	}

	/**
	*  Felmenő kategóriák ID-jának lekérdezése
	**/
	public function getCategorySet( $cat_id )
	{
		$set = array();

		$walk = true;

		while( $walk ) {
			$q = $this->db->query("SELECT ID, szulo_id, neve, hashkey FROM shop_termek_kategoriak WHERE ID = $cat_id;");
			$qd = $q->fetch(\PDO::FETCH_ASSOC);

			if( is_null( $qd['szulo_id'] ) ) {
				$set[] = array(
					'ID' => $qd['ID'],
					'nev' => $qd['neve'],
					'hash' => $qd['hashkey']
				);

				$walk = false;
			} else {
				$set[] = array(
					'ID' => $qd['ID'],
					'nev' => $qd['neve'],
					'hash' => $qd['hashkey']
				);
				$cat_id = $qd['szulo_id'];
			}
		}




		return $set;
	}

	public function addLinkToString( $product, $text )
	{
		// Formázás eltávolítás, kivétel a sortörés
		$text = strip_tags( $text, '<br>' );

		$gender = '';

		if(
			strpos( strtolower($product['csoport_kategoria']), 'férfi' ) !== false
		) {
			$gender = 'férfi';
		} else if(
			strpos( strtolower($product['csoport_kategoria']), 'női' ) !== false
		) {
			$gender = 'női';
		} else if(
			strpos( strtolower($product['csoport_kategoria']), 'lányka' ) !== false ||
			strpos( strtolower($product['csoport_kategoria']), 'lány' ) !== false) {
			$gender = 'lány';
		} else if(
			strpos( strtolower($product['csoport_kategoria']), 'fiú' ) !== false) {
			$gender = 'fiú';
		}

		/**
		 * Hivatkozások
		**/
		$rep = array(
			'waterfeel x-life eco' => 'WATERFEELXLIFEECO',
			'waterfeel x-life' => 'WATERFEELXLIFE',
			'max life' => 'MAXLIFE',
			'max-life' => 'MAX-LIFE',
			'powerskin carbon pro' => 'POWERSKINCARBONPRO',
			'powerskin carbon flex' => 'POWERSKINCARBONFLEX',
			'powerskin carbon air' => 'POWERSKINCARBONAIR',
			'powerskin carbon st' => 'POWERSKICARBONST',
			'bodylift' => 'BODYLIFT',
			'sensitive' => 'SENSITIVE',
		);

		$replaces = array(
			'waterfeel x-life eco' => $this->getPageByKeywords( array( 'waterfeel x-life-eco', $gender ), \Helper::makeSafeUrl( 'waterfeel x-life-eco', '' ) ),
			'waterfeel x-life' => $this->getPageByKeywords( array( 'waterfeel x-life', $gender ), \Helper::makeSafeUrl( 'waterfeel x-life', '' ) ),
			'powerskin carbon pro' => $this->getPageByKeywords( array( 'powerskin carbon pro', $gender ), \Helper::makeSafeUrl( 'powerskin carbon pro', '' ) ),
			'powerskin carbon flex' => $this->getPageByKeywords( array( 'powerskin carbon flex', $gender ), \Helper::makeSafeUrl( 'powerskin carbon flex', '' ) ),
			'powerskin carbon air' => $this->getPageByKeywords( array( 'powerskin carbon air', $gender ), \Helper::makeSafeUrl( 'powerskin carbon air', '' ) ),
			'powerskin carbon st' => $this->getPageByKeywords( array( 'powerskin carbon st', $gender ), \Helper::makeSafeUrl( 'powerskin carbon st', '' ) ),
			'max life' => $this->getPageByKeywords( array( 'max life', $gender ), \Helper::makeSafeUrl( 'max life', '' ) ),
			'max-life' => $this->getPageByKeywords( array( 'max life', $gender ), \Helper::makeSafeUrl( 'max life', '' ) ),
			'bodylift' => $this->getPageByKeywords( array( 'bodylift' ), \Helper::makeSafeUrl( 'bodylift', '' ) ),
			'sensitive' => $this->getPageByKeywords( array( 'sensitive', $gender ), \Helper::makeSafeUrl( 'sensitive', '' ) ),
		);

		foreach ( $replaces as $search => $link ) {
			$reptext =  $rep[$search];
			$text = preg_replace( '/('.$search.')/i', $reptext, $text );
		}

		foreach ( $replaces as $search => $link ) {
			$reptext =  $rep[$search];
			$text = str_replace( $reptext, '<a href="'.$link[url].'" title="'.$link[title].'" target="_blank">'.ucwords($search).'</a>', $text );
		}


		return $text;
	}

	public function getPageByKeywords( $keyword_set = array(), $anchor = '' )
	{
		$q = "SELECT eleres, cim FROM oldalak WHERE ID IS NOT NULL and hashkey_keywords IS NOT NULL and lathato = 1";

		$src = ' and (';
		foreach ( $keyword_set as $key ) {
			if( $key != '') {
				$src .= " hashkey_keywords LIKE '%$key%' and ";
			}
		}
		$src = rtrim($src, ' and ');
		$src .= ')';

		$q .= $src;

		$qq = $this->db->query( $q )->fetch(\PDO::FETCH_ASSOC);

		if( $qq[eleres] == '' ) return false;

		return array(
			'url' => '/p/'.$qq[eleres] . ( $anchor != '' ? '#'.$anchor : ''),
			'title' => $qq[cim]
		);
	}

	/*===============================
	=            GETTERS            =
	===============================*/

	public function getItemNumbers()
	{
		return $this->products_number;
	}

	public function getSelectedSizes()
	{
		return $this->selected_sizes;
	}

	public function getAvaiableSizes()
	{
		return $this->avaiable_sizes;
	}

	public function getMaxPage()
	{
		return $this->max_page;
	}

	public function getCurrentPage()
	{
		return $this->current_page;
	}

	public function getQueryString()
	{
		return $this->qry_str;
	}

	/*-----  End of GETTERS  ------*/

	public function __destruct()
	{
		$this->db = null;
		$this->user = null;
		$this->products = null;
		$this->products_number = 0;
		$this->product_limit_per_page = 50;
		$this->max_page = 1;
		$this->current_page = 1;
		$this->avaiable_sizes = array();
		$this->qry_str = null;
		$this->selected_sizes = array();
	}
}
?>
