<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class SizemanagerTable extends Table
{
	public $name = 'Sizemanager';
	public function initialize(array $config)
	{
		$this->table('st_sizemanager');
		$this->primaryKey('id');
	}

	
}

?>
