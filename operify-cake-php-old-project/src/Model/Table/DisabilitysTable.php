<?php

	namespace App\Model\Table;

	use Cake\ORM\Table;
	use Cake\Validation\Validator;

	class DisabilitysTable extends Table
	{
		public $name = 'Disabilitys';

		
		public function initialize(array $config)
		{
			$this->table('disabilitys');
        	$this->primaryKey('id');
		}

public function validationDefault(Validator $validator)
		{



		}

	}

?>
