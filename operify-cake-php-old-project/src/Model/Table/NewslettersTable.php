<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Creating Model for Shift module
 */
class NewslettersTable extends Table
{
	public $name = 'Newsletters';

	//---------------------------------------------------------
	public function initialize(array $config)
	{
		$this->table('letters');
		$this->primaryKey('id');
	}

	//---------------------------------------------------------

}

?>
