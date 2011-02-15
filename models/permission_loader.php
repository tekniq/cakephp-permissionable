<?php
class PermissionLoader extends PermissionableAppModel {
	var $actsAs = array('Permissionable');
	var $useTable = false;
}