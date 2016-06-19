<?php 

namespace LuckyNail\SimpleCache;
use LuckyNail\Helper;

class Text{
	private $_sFolderPath;
	private $_iExpiresInDays;

	public function __construct($sFolderPath, $iExpiresInDays = 30){
		$this->_sFolderPath = Helper\Path::to_path_part($sFolderPath);
		$this->_iExpiresInDays = $iExpiresInDays;
		if(!is_dir($sFolderPath)){
			mkdir($sFolderPath, 0644, true);
		}
	}

	public function get($sFileId){
		$sFilePath = $this->_sFolderPath.DIRECTORY_SEPARATOR.$sFileId;
		if(file_exists($sFilePath)){
	     	$iAgeDays = (time() - filemtime($sFilePath)) / (60*60*24);
	     	if($iAgeDays >= $this->_iExpiresInDays){
	     		unlink($sFilePath);
	     	}else{
	     		return file_get_contents($sFilePath);
	     	}
	    }

    	return false;
	}

	public function write($sFileId, $sContent){
		$sFilePath = $this->_sFolderPath.DIRECTORY_SEPARATOR.$sFileId;
		file_put_contents($sFilePath, $sContent);
		return $sFilePath;
	}
}
