<?php 

namespace LuckyNail\SimpleCache;
use LuckyNail\Helper;

class Text{
	private $_sFolderPath;
	private $_iExpiresInDays;

	public function __construct($sFolderPath, $iExpiresInHours = 1){
		$this->_sFolderPath = Helper\Path::to_path_part($sFolderPath);
		$this->_iExpiresInDays = $iExpiresInDays;
		if(!is_dir($sFolderPath)){
			mkdir($sFolderPath, 0644, true);
		}
	}

	public function get_cached_path($sRequestId){
		$sFilePath = $this->_sFolderPath.DIRECTORY_SEPARATOR.$sRequestId;
		if(file_exists($sFilePath)){
	     	$iAgeDays = (time() - filemtime($sFilePath)) / (60*60);
	     	if($iAgeDays >= $this->_iExpiresInDays){
	     		unlink($sFilePath);
	     	}else{
	     		return $sFilePath;
	     	}
	    }
    	return false;
	}

	public function write($sRequestId, $sContent){
		$sFilePath = $this->_sFolderPath.DIRECTORY_SEPARATOR.$sRequestId;
		file_put_contents($sFilePath, $sContent);
		return $sFilePath;
	}
}
