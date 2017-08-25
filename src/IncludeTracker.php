<?php 
namespace LuckyNail\SimpleCache;
class IncludeTracker extends Text{
	private $_sId;
	private $_aExcludedPaths = [];
	public function __construct($sFolderPath, $sId){
		parent::__construct($sFolderPath, false);
		$this->_sId = $sId;
	}
	public function set_replacements($aReplacements){
		$this->_aReplacements = $aReplacements;
	}
	public function exclude_paths($aPaths){
		if(!is_array($aPaths)){
			$aPaths = [$aPaths];
		}
		$this->_aExcludedPaths = array_values(array_unique($aPaths));
	}
	private function _filter_excluded_paths($aPaths){
		$aResult = [];
		foreach($aPaths as $sPath){
			foreach($this->_aExcludedPaths as $sExcludedPath){
				if(strpos($sPath, $sExcludedPath) !== 0){
					$aResult[] = $sPath;
				}
			}
		}
		return $aResult;
	}
	private function _execute_path_replacements($aPaths){
		$aResult = [];
		foreach($aPaths as $iKey => $sPath){
			foreach($this->_aReplacements as $sSearch => $sReplacement){
				if(strpos($sPath, $sSearch) !== false){
					$aResult[$iKey] = str_replace($sSearch, $sReplacement, $sPath);
					break;
				}
			}
		}
		return $aResult;
	}
	public function start(){
		register_shutdown_function(function(){
			$aNewFiles = get_included_files();
			if($this->_aExcludedPaths){
				$aNewFiles = $this->_filter_excluded_paths($aNewFiles);
			}
			if($this->_aReplacements){
				$aNewFiles = $this->_execute_path_replacements($aNewFiles);
			}
			$sOldFiles = $this->read($this->_sId);
			if($sOldFiles !== false){
				$aOldFiles = json_decode($sOldFiles);
				if(!is_array($aOldFiles)){
					$aOldFiles = ['__err' => 'cached data was no array at '.date().'.'];
				}
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
