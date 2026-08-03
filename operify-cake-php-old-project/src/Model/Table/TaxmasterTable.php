<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class TaxmasterTable extends Table
{
	public $name = 'Taxmaster';
	public function initialize(array $config)
	{
		$this->table('st_taxmaster');
		$this->primaryKey('id');
	}

	
}

?>
