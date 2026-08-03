<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class CompanymasterTable extends Table
{
	public $name = 'Companymaster';
	public function initialize(array $config)
	{
		$this->table('st_companymaster');
		$this->primaryKey('id');
	}

	
}

?>
