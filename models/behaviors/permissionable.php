<?php
class PermissionableBehavior extends ModelBehavior {
	var $cache = array();
/**
 * Before find, check for model permissions and merge them into query conditions.
 * 
 */
	function beforeFind($model, $queryData) {
		$queryData = array_merge(array('permissions' => true), $queryData);
		
		if ($queryData['permissions']) {
			$permissions = $model->getPermissions();
			if (!empty($permissions) && is_array($permissions)) {
				$queryData['conditions']['AND'] = array_merge((array)$queryData['conditions']['AND'], $permissions);
			}
		}
		return $queryData;
	}
/**
 * Fetch model permissions and store them in cache.
 * 
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