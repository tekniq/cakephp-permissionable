<?php
class PreLoaderComponent extends Object {
	var $models = array();
	
	function initialize(&$controller, $settings = array()) {
		$this->controller =& $controller;		
		$this->_set($settings);
	}
	
	function startup(&$controller) {
		if (!is_array($this->models)) {
			$this->models = (array)$this->models;
		}		
		$controller->loadModel('Permissionable.PermissionLoader');
		$controller->PermissionLoader->cachePermissions($this->models);
	}
}