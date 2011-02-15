<?php
class PermissionableBehavior extends ModelBehavior {
	var $cache = array();
	var $models = array();
/**
 * Add model to a list of models using this behavior.
 * 
 * @param object $model Model instance
 * @return void
 */	
	function setup(&$model) {
		if (!array_key_exists($model->name, $this->models)) {
			$this->models[$model->name] =& $model;
		}
	}
/**
 * Cache permissions for all models that have been setup for this behavior.
 * 
 * @param object $model Model instance
 * @param array Array of models for which to cache permissions
 * @return void
 */
	function cachePermissions($model, $modelNames = null) {
		if (empty($modelNames)) {
			$modelNames = array_keys($this->models);
		}
		foreach ($this->models as $model) {
			if (!in_array($model->name, $modelNames)) {
				continue;
			}
			$model->getPermissions();
		}
	}
/**
 * Before find, check for model permissions and merge them into query conditions.
 * 
 * @param object $model Model instance
 * @return array $queryData Array of modified query data
 * @return boolean true
 */
	function beforeFind($model, $queryData) {
		$queryData = array_merge(array('permissions' => true), $queryData);
		
		if ($queryData['permissions']) {
			$permissions = $model->getPermissions();
			if (!empty($permissions) && is_array($permissions)) {
				$queryData['conditions'] = array_merge_recursive($queryData['conditions'], array('AND' => $permissions));
			}
		}
		return $queryData;
	}
/**
 * Fetch model permissions and store them in cache.
 * 
 * @param object $model Model instance
 * @return mixed Array of conditions or false if unsuccessful
 */	
	function getPermissions($model) {
		$user = $model->getUser();
		if (empty($user)) {
			return false;
		}		
		if (!array_key_exists($model->alias, $this->cache)) {
			$this->cache[$model->alias] = $model->permissions($user['Contact']);
		}		
		return $this->cache[$model->alias];
	}
/**
 * Generic permissions method to be overriden by models.
 * 
 */
	function permissions() {
		return false;
	}
/**
 * Generic user accessor method to be overridden by models.
 * 
 */	
	function getUser() {
		return false;
	}
}