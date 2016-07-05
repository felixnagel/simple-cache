<?php 

namespace LuckyNail\SimpleCache;

class IncludeTracker extends Text{
	private $_sId;
	public function __construct($sFolderPath, $sId){
		parent::__construct($sFolderPath, false);
		$this->_sId = $sId;
	}
	public function start(){
		register_shutdown_function(function(){
			$aNewFiles = get_included_files();
			$sOldFiles = $this->read($this->_sId);
			if($sOldFiles !== false){
				$aOldFiles = json_decode($sOldFiles);
				$aNewFiles = array_merge($aOldFiles, $aNewFiles);
			}
			$aNewFiles = array_values(array_unique($aNewFiles));
			$this->write($this->_sId, json_encode($aNewFiles));
		});
	}
	public function get(){
		return json_decode($this->read($this->_sId));
	}
	public function has_been_included_yet($sFilepath){
		return in_array($sFilepath, $this->get());
	}
}
