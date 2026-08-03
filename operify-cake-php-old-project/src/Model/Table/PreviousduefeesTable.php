<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class PreviousduefeesTable extends Table {

    public $name = 'Previousduefees';
    
        			  public function initialize(array $config)
    {
       
       
       
			  $this->table('previousduefees');
        $this->primaryKey('id');
			 $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);
        	 
       $this->belongsTo('Banks', [
            'foreignKey' => 'bank_id',
            'joinType' => 'INNER',
        ]);
      
	}
				
}
?>
