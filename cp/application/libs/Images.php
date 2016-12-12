<?
class Images{
	/*
		$arg
			+ src[String]				: A forrása a feltöltött fájloknak ($_FILE[?])
			- required[0|1] 			: Kötelező a kép feltöltése, vagy sem. (1 = kötelező)
			- text[String]				: A feltöltött mező referencia neve hibaüzenetben.
			+ upDir[String]				: A képek mentésének helye.
			- noRoot[true|false-] 		: true -> nem sorolja be almappákba
			- noThumbImg[true|false-] 	: true -> nem készít bélyegképeket
			- noWaterMark[true|false-] 	: true -> nem készít vízjelet a képre
			- fileName[String]  		: true Str -> A kép neve ez lesz.
			- maxFileSize[Int] 			: Maximálisan engedélyezett fájlméret képenként.
	        - imgLimit[Int]             : Max. ennyi fájlt tölt fel.

	*/
	private static $allowed_extension = array( 'image/jpeg', 'image/png' );
	public static function upload($arg = array()){
		$si 	= $arg[src];
		$file 	= true;
		$dir 	= $arg[upDir];
		$src 	= $_FILES[$si];
		$noDir  = false;

		if($arg[mod] == "add"){ // Kiegészítő
			$dir = $arg[tDir];
		}


			if($dir == null || $dir == ""){
				$dir = substr(IMG,1)."termekek/";
				$noDir = true;
			}

			if(!file_exists($dir)){
				if($arg[mod] != "add"){
					throw new Exception('#Hiba: a megadott mappa nem létezik, ahova menteni kívánja a termék képeit. <em>('.$dir.')</em>');
				}else if($arg[mod] == "add"){
					$dir = substr(IMG,1)."termekek/";
					$noDir = true;
				}
			}

			if($src[size][0] == 0){
				if($arg[mod] != "add"){
					if($arg[required] == 1){
						throw new Exception('Nincs feldolgozandó fájl: <strong>'.$arg[text].'</strong>');
					}else{
						$file = false;
					}
				}
			}else{
			// Ellenőrzése
				$te         = -1;
                $errOfType  = '';
                $fn         = 0;

				foreach($src[type] as $ftt){ $te++; $fn++;
					// Fájlformátum ellenőrzés
					if( !in_array( $ftt, self::$allowed_extension) ){
						$errOfType  .= "A feltöltendő képek közt található olyan forrás, ami nem <strong>jpg, png</strong> formátumú!<br /><br /><strong>".$src[name][$te]."</strong><br />";
					}

					// Fájlméret ellenőrzés - ADD: 2013/07
					if($arg[maxFileSize]){
						$fileKb = ceil($src[size][$te] / 1024);
						if($fileKb > $arg[maxFileSize]){
							$errOfType  .= "A feltöltendő képek közt található olyan forrás, aminek a fájlmérete (".$fileKb.") nagyobb a megengedettnél (".$arg[maxFileSize]." KB)!<br /><br /><strong>".$src[name][$te]."</strong><br />";
						}
					}
				}


                // Darabszám
                if($arg[imgLimit] > 0){
                    if($fn > $arg[imgLimit]){
                        $errOfType  .= "Nem tölthet fel ennyi képet. Ön legfeljebb már csak <strong>".$arg[imgLimit]." db</strong> képet tölthet fel. <br/>";
                    }
                }

				if($errOfType != ''){ throw new Exception($errOfType); }

			// Minden szükséges adat megvan
				if($arg[mod] != "add" || $noDir){
					#Random mappa név
						if(!$arg[noRoot]){ $dh 	= self::sHash(7); }
					# Feltötendő képek mappája
						$updir 	= $dir.$dh.'/';

					if(!file_exists($updir)){
						mkdir($updir,0777);
						chmod($updir,0777);
					}
				}else{
					$updir = $dir;
				}

				$p          = 0;
                $allFiles   = array();

				foreach($src[tmp_name] as $tmp){
					usleep(020000); // 0.2 mp várakozás
               		$file_type 	= '.jpg';
					$mt = explode(" ",str_replace(".","",microtime()));
					$fls = (!$arg[fileName]) ? self::sHash(7) : $arg[fileName].$mt[0];

					switch ( $src[type][$p] ) {
						case 'image/jpeg':
							$file_type 	= '.jpg';
						break;
						case 'image/png':
							$file_type 	= '.png';
						break;
					}

					$fln = $fls.$file_type;
					if($p == 0){$ffile = $fln;}
					move_uploaded_file($tmp,$updir.$fln);

					// Bélyegképek
					if(!$arg[noThumbImg]){
						self::makeThumbnail($updir.$fln,$updir, $fls, 'thb150_', 150, $file_type);
						self::makeThumbnail($updir.$fln,$updir, $fls, 'thb75_', 75, $file_type);
					}

					// Vízjelezés
					if(!$arg[noWaterMark]){
						$kep = $updir.$fln;
						if ( defined('WATERMARK_IMG') ) {
							self::makeWatermarkedImage(WATERMARK_IMG,$kep,'középen');
						}
					}

					$p++;
                    $allFiles[] = $updir.$fln;
				}
				$file = true;
			}


			if($file){
				$back = array(
					"dir" 	=> $updir,
					"file" 	=> $updir.$ffile,
                    "allUploadedFiles" => $allFiles
				);
				return $back;
			}else{ return false; }
	}

	private static function sHash($n = 7){
		return substr(md5(microtime()),0,$n);
	}

	static function makeWatermarkedImage($wmk, $file, $pos){
		if($wmk != ""){
			$fln = basename($file);
			$ext = explode(".",$fln);
			if($ext[1] == "jpg"){
				// Eredeti kép
				$kep 			= imagecreatefromjpeg($file);
				list($kx,$ky) 	= getimagesize($file);

				// Vízjel
				$wm 			= imagecreatefrompng($wmk);
				list($wmw,$wmh) = getimagesize($wmk);
				$wmpos 			= $pos;

				switch($wmpos){
					case 'bal-fent';
						$x = 5;
						$y = 5;
					break;
					case 'bal-lent';
						$x = 5;
						$y = $ky - $wmh -5;
					break;
					case 'jobb-fent';
						$x = $kx - $wmw -5;
						$y = 5;
					break;
					case 'jobb-lent';
						$x = $kx - $wmw -5;
						$y = $ky - $wmh -5;
					break;
					case 'középen';
						$x = ($kx / 2) - ($wmw / 2);
						$y = ($ky / 2) - ($wmh / 2);
					break;
				}

				imagecopy($kep,$wm,$x,$y,0,0,$wmw,$wmh);
				imagejpeg($kep,$file,100);
				imagedestroy($kep);
			}
		}
	}

	private static function makeThumbnail($src, $dir, $name, $pref, $maxWidth, $type){
		// Alap műveletek
			# Forrás fájl másolása
			copy($src,$dir.$pref.$name.$type);
			# Forrás kép elérése
			$src = $dir.$pref.$name.$type;
			# Virtuálos kép létrehozás
			switch ($type) {
				case '.jpg':
					$wi = imagecreatefromjpeg($src);
				break;
				case '.png':
					$wi = imagecreatefrompng($src);
				break;
			}

			# Kép méreteinek beolvasása
			list($iw,$ih) 	= getimagesize($src);

		// Méretarányos méretcsökkentés
		$dHeight = floor($ih * ($maxWidth / $iw));

		switch ($type) {
			case '.jpg':

			break;
			case '.png':
				/*imagealphablending($wi, false);
				imagesavealpha($wi, true); 	*/
			break;
		}

		// Kép módosító
  		$vi = imagecreatetruecolor($maxWidth, $dHeight);

  		switch ($type) {
			case '.jpg':
				imagejpeg($vi,$dir.$pref.$name.$type,85);
			break;
			case '.png':
				imagealphablending($vi, false);
				imagesavealpha($vi,true);
				$transparent = imagecolorallocatealpha($vi, 255, 255, 255, 127);
				imagefilledrectangle($vi, 0, 0, $maxWidth, $dHeight, $transparent);
			break;
		}

  		imagecopyresampled($vi, $wi, 0, 0, 0, 0, $maxWidth, $dHeight, $iw, $ih);

		// Módosítások érvényesítése / Output
		switch ($type) {
			case '.jpg':
				imagejpeg($vi,$dir.$pref.$name.$type,85);
			break;
			case '.png':
				imagepng($vi,$dir.$pref.$name.$type);
			break;
		}

		// Temponális változók eltávolítása
		imagedestroy($vi);
	}

	function image_create_transparent($width, $height) {
	  $res = imagecreatetruecolor($width, $height);
	  $transparency = imagecolorallocatealpha($res, 0, 0, 0, 127);
	  imagealphablending($res, FALSE);
	  imagefilledrectangle($res, 0, 0, $width, $height, $transparency);
	  imagealphablending($res, TRUE);
	  imagesavealpha($res, TRUE);
	  return $res;
	}

	public static function getThumbImg($type, $src){
		$ct 	= explode("/",$src);
		$max 	= count($ct);
		$im 	= $ct[$max-1];
		$root 	= str_replace($im,"",$src);
		if($im == 'noimg.png'){
			$thmb 	= '/'.$root.$im;
		}else{
			$thmb 	= '/'.$root.'thb'.$type.'_'.$im;
		}
		$thmb = ltrim($thmb,'/');
		return $thmb;
	}

    public static function showThumbImg($src, $w = 100){
        $url = '/services/image/thumbnail/'.base64_encode($src).'/'.$w.'/';

        return $url;
    }

    public static function thumbImg($img, $arg = array()){
    	session_cache_limiter('none');

		if(empty($img)) return false;

		$file 		= $_SERVER['SCRIPT_FILENAME'];
		$timestamp 	= filemtime($_SERVER['SCRIPT_FILENAME']);

	  	$gmt_mtime = gmdate('r', $timestamp);

	    header('ETag: "'.md5($timestamp.$file).'"');
	    header('Last-Modified: '.$gmt_mtime);
	    header('Cache-Control: public');

	    if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) || isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
	        if ($_SERVER['HTTP_IF_MODIFIED_SINCE'] == $gmt_mtime || str_replace('"', '', stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) == md5($timestamp.$file)) {
	            header('HTTP/1.1 304 Not Modified');
	            exit();
	        }
	    }

		        // if no destination file was given then display a png
        header('Pragma: public');
        header('Cache-Control: max-age='.$cacheTime.', public');
        header('Expires: '. gmdate(DATE_RFC1123, time() + ($cacheTime)));
        //header('Last-Modified: '.gmdate(DATE_RFC1123,filemtime( $img )));
       	header('Content-Type: image/png');

        $square_size    = ($arg[s]) ? $arg[s] : 100;
        $cacheTime      = 60*60*24*365;

        if(substr($img,0,4) != 'http')
            if(strpos($img,IMG) === false){
                //$img = substr(IMG,1).$img;
            }
        //echo $img;
        // get width and height of original image

        $imagedata          = @getimagesize($img);
        //print_r($imagedata);
        $original_width     = $imagedata[0];
        $original_height    = $imagedata[1];

        if($original_width > $original_height){
            $new_height = $square_size;
            $new_width = $new_height*($original_width/$original_height);
        }
        if($original_height > $original_width){
            $new_width = $square_size;
            $new_height = $new_width*($original_height/$original_width);
        }
        if($original_height == $original_width){
            $new_width = $square_size;
            $new_height = $square_size;
        }

        $new_width  = round($new_width);
        $new_height = round($new_height);

        // load the image
        if(substr_count(strtolower($img), ".jpg") or substr_count(strtolower($img), ".jpeg")){
            $original_image = @imagecreatefromjpeg($img);
        }
        if(substr_count(strtolower($img), ".gif")){
            $original_image = @imagecreatefromgif($img);
        }
        if(substr_count(strtolower($img), ".png")){
            $original_image = @imagecreatefrompng($img);
        }

        $smaller_image 	= @imagecreatetruecolor($new_width, $new_height);
        $square_image 	= @imagecreatetruecolor($square_size, $square_size);

		if($original_image) @imagecopyresampled($smaller_image, $original_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);

        if($new_width>$new_height){
            $difference = $new_width-$new_height;
            $half_difference =  round($difference/2);
            @imagecopyresampled($square_image, $smaller_image, 0-$half_difference+1, 0, 0, 0, $square_size+$difference, $square_size, $new_width, $new_height);
        }
        if($new_height>$new_width){
            $difference = $new_height-$new_width;
            $half_difference =  round($difference/2);
            @imagecopyresampled($square_image, $smaller_image, 0, 0-$half_difference+1, 0, 0, $square_size, $square_size+$difference, $new_width, $new_height);
        }
        if($new_height == $new_width){
            @imagecopyresampled($square_image, $smaller_image, 0, 0, 0, 0, $square_size, $square_size, $new_width, $new_height);
        }
        @imagepng($square_image,NULL,9);

        // save the smaller image FILE if destination file given
        /*
        if(substr_count(strtolower($destination_file), ".jpg")){
            imagejpeg($square_image,$destination_file,100);
        }
        if(substr_count(strtolower($destination_file), ".gif")){
            imagegif($square_image,$destination_file);
        }
        if(substr_count(strtolower($destination_file), ".png")){
            imagepng($square_image,$destination_file,9);
        }
        */

      	if($original_image) @imagedestroy($original_image);
        if($smaller_image) @imagedestroy($smaller_image);
        if($square_image) @imagedestroy($square_image);
    }

	public static function full($file){
		$file = str_replace(array('../img/'),array(IMG),$file);
		return $file;
	}

	public static function getAllImg($path,$wh = null){
		if($path != "" && is_dir($path)){
			$data 	= new DirectoryIterator($path);
			$files 	= array();

			foreach ($data as $fl) {
	        	if($fl->isFile()) {
					$fln = $fl->getFilename();
					if($wh == null){
	            		$files[] = $path.$fln;
					}else if($wh != null && $wh != "big"){
						if(strpos($fln,$wh) !== false){
							$files[] = $path.$fln;
						}
					}else{
						if(strpos($fln,"thb75_") !== 0 && strpos($fln,"thb150_") !== 0){
							$files[] = $path.$fln;
						}
					}
	        	}
	    	}
			return $files;
		} return array();
	}


    public static function getParentFolderName($file){

    }

	/* ADD: 2013/07 */
	public static function dellAllFolderImgs($folder){
		$allImg = self::getAllImg($folder);

		foreach($allImg as $img){
			if(file_exists($img)){
				unlink($img);
			}
		}
	}

	/* ADD: 2013/07 */
	public static function dellSubImgs($url){
		$url 	= ltrim($url,'/');
		$curl 	= explode('/',$url);
		$nurl 	= count($curl);

		$file 	= $curl[$nurl-1];
		$root 	= str_replace($file,'',$url);

		$dellfile = array();
		$dellfile[] = $root.$file;

		if(file_exists($root.'thb150_'.$file))
			$dellfile[] = $root.'thb150_'.$file;

		if(file_exists($root.'thb75_'.$file))
			$dellfile[] = $root.'thb75_'.$file;

		foreach($dellfile as $df){
			unlink($df);
		}
	}
}
?>
