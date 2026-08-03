<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class DesignationsTable extends Table {

    public $name = 'Designations';
	
	

      public function initialize(array $config)
    {
		
		 $this->table('designations');
        $this->primaryKey('id');
    $this->belongsTo('Departments', [
           'foreignKey' => 'depart_id',
         'joinType' => 'INNER',
           
        ]);
	 
	}

}
?>
