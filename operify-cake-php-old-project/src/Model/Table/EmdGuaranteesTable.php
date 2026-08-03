<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class EmdGuaranteesTable extends Table
{
	public $name = 'EmdGuarantees';
	public function initialize(array $config)
	{
		$this->table('emd_guarantees');
		$this->primaryKey('id');

		
	}

	
}

?>
