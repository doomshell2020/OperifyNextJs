<?php

	namespace App\Model\Table;

	use Cake\ORM\Table;
	use Cake\Validation\Validator;

	/**
	* Creating Model for Library cup board shelf module
	*/
	class DemorequestTable extends Table
	{
		public $name = 'Demorequest';

		//---------------------------------------------------------
		public function initialize(array $config)
		{
			$this->table('demo_request');
        	$this->primaryKey('id');

        
        }
        
    }

    ?>