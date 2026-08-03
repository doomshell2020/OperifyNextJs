<?php

	namespace App\Model\Table;

	use Cake\ORM\Table;
	use Cake\Validation\Validator;

	/**
	* Creating Model for Library Fine module
	*/
	class FineTable extends Table
	{
		public $name = 'Fine';

		//---------------------------------------------------------
		public function initialize(array $config)
		{
			$this->table('library_fines');
        	$this->primaryKey('id');
		}

		//---------------------------------------------------------
		public function validationDefault(Validator $validator)
		{
			$validator = new Validator();

			$validator
				->requirePresence('holder_type_id')
				->notEmpty('holder_type_id', 'Holder Type cannot be blank.')

				->requirePresence('holder_name')
				->notEmpty('holder_name', 'Holder Name cannot be blank.')

				->requirePresence('fine_type')
				->notEmpty('fine_type', 'Fine Type cannot be blank.')

				->requirePresence('asn_no')
				->notEmpty('asn_no', 'ASN_No cannot be blank.')

				->requirePresence('amount')
				->notEmpty('amount', 'Amount cannot be blank.');

			return $validator;
		}
	}

?>