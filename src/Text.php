<?php 

namespace LuckyNail\SimpleCache;
use LuckyNail\Helper;

class Text{
	protected $_sFolderPath;
	protected $_iExpiresInHours;
	public function __construct($sFolderPath, $iExpiresInHours = 0){
		$this->_sFolderPath = $sFolderPath;
		$this->_iExpiresInHours = $iExpiresInHours;
		if(!is_dir($sFolderPath)){
			mkdir($sFolderPath, 0755, true);
		}
	}
	public function is_cached($sRequestId){
		$sFilePath = $this->_sFolderPath.DIRECTORY_SEPARATOR.$sRequestId;
		if(!file_exists($sFilePath)){
    		return false;
		}
	    if($this->_iExpiresInHours === false){
 			return true;
	   	}
	   	$iFiletime = filemtime($sFilePath);
	    $iAgeHours = (time() - $iFiletime) / 3600;
	    if($iAgeHours >= $this->_iExpiresInHours){
	    	unlink($sFilePath);
	    	return false;
     	}else{
     		return true;
     	}
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
