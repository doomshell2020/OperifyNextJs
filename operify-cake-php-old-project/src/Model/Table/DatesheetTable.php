<?php

	namespace App\Model\Table;

	use Cake\ORM\Table;
	use Cake\Validation\Validator;

	/**
	* Creating Model for Library cup board shelf module
	*/
	class DatesheetTable extends Table
	{
		public $name = 'Datesheet';

		//---------------------------------------------------------
		public function initialize(array $config)
		{
			$this->table('date_sheet');
        	$this->primaryKey('id');

        	$this->belongsTo(
        		'Classes',
        		['foreignKey' => 'class_id', 'joinType' => 'INNER']
        	);
        }
        
    }

    ?>