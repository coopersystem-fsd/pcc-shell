<?php
/**
 * WP Google Maps Pro Import / Export API: Backup class
 *
 * Leverages the Import/Export modules for some functionality
 *
 * @package WPGMapsPro\ImportExport
 * @since 8.1.9
 */

namespace WPGMZA;

class Backup {
	const CORE_DIR = "wp-google-maps";
	const BACKUP_DIR = "backups";
	
	const BACKUP_BASE_NAME = "wpgmza_backup";
	const BACKUP_BASE_STAMP = "Y-m-d-H-i";

	const FLAG_TYPE_MANUAL = "M";
	const FLAG_TYPE_PRE_IMPORT = "PI";
	const FLAG_TYPE_POST_UPDATE = "PU";

	const MAX_AUTO_BACKUPS = 3;

	public function __construct() {
		$this->isReady = false;

		$this->loadDependencies();
		$this->prepareStorage();
	}

	public function createBackup($filename = false, $flag = "M"){
		if(empty($filename) || strpos($filename, '.') !== FALSE){
			/*
			 * No filename, or filename contained a file suffix
			 *
			 * Use the default base name
			*/
			$filename = self::BACKUP_BASE_NAME;
		}

		if($this->isFlagAutomatedType($flag)){
			$this->removeOldBackupsOfType($flag);
		}

		$backupDate = date(self::BACKUP_BASE_STAMP);
		$filename = "{$filename}__{$backupDate}--{$flag}.json";

		$export = new Export();
		$json = $export->get_json();

		$storageDir = $this->getStorageDir();

		if(file_exists($storageDir)){
			$storagePath = implode('/', array($storageDir, $filename));
			if(!file_exists($storagePath)){
				try{
					@file_put_contents($storagePath, $json);
					return true;
				} catch (\Exception $ex){
					/* Do nothing, fail gracefully */
				} catch (\Error $err){
					/* Do nothing, fail gracefully */
				}
			}
		}
		return false;
	}

	public function getBackupFiles(){
		$backupDir = $this->getStorageDir();
		$files = \list_files($backupDir);

		if(!empty($files)){
			$backups = array();

			foreach ($files as $filename) {
				$backups[] = $this->decodeFileName($filename);
			}

			return array_reverse($backups);
		}
		return false;
	}

	public function deleteBackup($filename){
		if(strpos($filename, '.json') === FALSE){
			$filename .= ".json";
		}

		$files = $this->getBackupFiles();
		if(!empty($files)){
			foreach ($files as $file) {
				if($file['filename'] === $filename){
					if(file_exists($file['dir'])){
						wp_delete_file($file['dir']);
						return true;
					}
				}
			}
		}
		return false;
	}

	private function removeOldBackupsOfType($type){
		$filesOfType = array();

		$files = $this->getBackupFiles();
		if(!empty($files)){
			foreach ($files as $file) {
				if($file['flag'] === $type){
					$filesOfType[] = $file;
				}
			}
		}

		if(count($filesOfType) >= self::MAX_AUTO_BACKUPS){
			if(!empty($filesOfType[count($filesOfType) - 1])){
				$fileToDelete = $filesOfType[count($filesOfType) - 1];

				if(file_exists($fileToDelete['dir'])){
					wp_delete_file($fileToDelete['dir']);
				}
			}
		}
	}

	private function decodeFileName($filename){
		$filename = str_replace($this->getStorageDir() . "/", "", $filename);

		$decoded = array(
			"filename" => $filename,
			"dir" => $this->getStorageDir() . "/" . $filename,
			"url" => $this->getStorageDir(true) . "/" . $filename,
		);

		if(strpos($filename, "__") !== FALSE){
			$filename = str_replace(".json", "", $filename);
			
			$splitIndex = strpos($filename, "__");
			$rootName = substr($filename, 0, $splitIndex);
			$split = substr($filename, $splitIndex);

			if(!empty($rootName)){
				$rootName = str_replace("_", " ", $rootName);

				if(strpos($rootName, "wpgmza") !== FALSE){
					$rootName = str_replace("wpgmza", "WPGMZA", $rootName);
				}
				$decoded["pretty"] = ucwords($rootName);
			}

			$flag = self::FLAG_TYPE_MANUAL;
			if(!empty($split)){
				$split = str_replace("__", "", $split);
				if(strpos($split, "--") !== FALSE){
					$flag = substr($split, strpos($split, "--"));
					$flag = str_replace("--", "", $flag);
				}

				$decoded['flag'] = $flag;
				$decoded['flag_alias'] = $this->getFlagAlias($flag);

				$split = explode("-", $split);

				if(!empty($split)){
					$dateMap = array(
						'year',
						'month',
						'day',
						'hour',
						'min'
					);

					$decoded['date'] = array();
					foreach ($split as $key => $value) {
						if(!empty($dateMap[$key])){
							$decoded['date'][$dateMap[$key]] = intval($value);
						}
					}
				}

				if($this->isFlagAutomatedType($decoded['flag'])){
					$decoded["pretty"] .= " (Automated)";
				}
			}
		}

		return $decoded;
	}

	private function isFlagAutomatedType($flag = "M"){
		return $flag !== self::FLAG_TYPE_MANUAL;
	}

	private function getFlagAlias($flag = "M"){
		$type = "Unknown";
		switch ($flag) {
			case self::FLAG_TYPE_MANUAL:
				$type = "Manual";
				break;
			case self::FLAG_TYPE_POST_UPDATE:
				$type = "Post-Update";
				break;
			case self::FLAG_TYPE_PRE_IMPORT:
				$type = "Pre-Import";
				break;
		}

		return $type;
	}

	private function loadDependencies(){
		$path = plugin_dir_path( __FILE__ );
		require_once( $path . 'class.export.php' );		
	}

	private function prepareStorage(){
        $backupDir = $this->getStorageDir();
        if(!file_exists($backupDir)){
        	wp_mkdir_p($backupDir);
        }

        $isReady = true;
	}

	private function getStorageDir($asUrl = false){
		$uploadDir = wp_upload_dir();
		if (!empty($uploadDir['basedir'])){
			if($asUrl && !empty($uploadDir['baseurl'])){
    			$uploadDir = $uploadDir['baseurl'];
			} else {
    			$uploadDir = $uploadDir['basedir'];
			}

    		return implode("/", 
    			array(
    				$uploadDir,
    				self::CORE_DIR,
    				self::BACKUP_DIR
    			)
    		);
    	}
    	return false;
	}
}