<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Creating Model for Shift module
 */
class SeoTable extends Table
{
	public $name = 'Seo';
	//---------------------------------------------------------
	public function initialize(array $config)
	{
		$this->table('seos');
		$this->primaryKey('id');
	}

	//---------------------------------------------------------
	
}

?>