<?
namespace FileManager;

/**
* Class FileChecker
* @package FileManager
* @version 1.0
*/
class FileChecker{
	private $check_path = null;
	/**
	 * Alapértelmezett forrás meghatározása
	 * @param string $path Mappa vagy fájl elérhetősége
	 */	
	function __construct($path)
	{
		if (isset($path) && $path !== '' && is_string($path)) {

			$this->check_path = $path;

		}
	}

	/**
	 * A cél létezik vagy sem
	 * @return boolean 
	 */
	public function exists(){
		if( file_exists($this->check_path) ) {
			return true;
		}

		return false;
	}

	/**
	 * A cél írható vagy sem
	 * @return boolean 
	 */
	public function writable(){
		if( is_writable($this->check_path) ) {
			return true;
		}

		return false;
	}

	/**
	 * A cél olvashatóság vizsgálata
	 * @return boolean 
	 */
	public function readable(){
		if( is_readable($this->check_path)) {
			return true;
		}

		return false;
	}

	/**
	 * A cél vizsgálata mint mappa
	 * @return boolean 
	 */
	public function isDir(){
		if ( is_dir($this->check_path) ) {
			return true;
		}

		return false;
	}

	/**
	 * A cél vizsgálata mint fájl
	 * @return boolean 
	 */
	public function isFile(){
		if ( is_file($this->check_path) ) {
			return true;
		}

		return false;
	}
}
?>