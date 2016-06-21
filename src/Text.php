<?php 

namespace LuckyNail\SimpleCache;
use LuckyNail\Helper;

class Text{
	private $_sFolderPath;
	private $_iExpiresInHours;

	public function __construct($sFolderPath, $iExpiresInHours = 0){
		$this->_sFolderPath = $sFolderPath;
		$this->_iExpiresInHours = $iExpiresInHours;
		if(!is_dir($sFolderPath)){
			mkdir($sFolderPath, 0755, true);
		}
	}

	public function is_cached($sRequestId){
		$sFilePath = $this->_sFolderPath.DIRECTORY_SEPARATOR.$sRequestId;
		if(file_exists($sFilePath)){
	     	$iAgeHours = (time() - filemtime($sFilePath)) / (60*60);
	     	if($iAgeHours >= $this->_iExpiresInHours){
	     		unlink($sFilePath);
	     	}else{
	     		return true;
	     	}
	    }
    	return false;
	}

	public function read($sRequestId){
		if($this->is_cached($sRequestId)){
			$sFilePath = $this->_sFolderPath.DIRECTORY_SEPARATOR.$sRequestId;
			return file_get_contents($sFilePath);
		}else{
			return false;
		}
	}

	public function write($sRequestId, $sContent){
		$sFilePath = $this->_sFolderPath.DIRECTORY_SEPARATOR.$sRequestId;
		file_put_contents($sFilePath, $sContent);
		return $sFilePath;
	}
}
