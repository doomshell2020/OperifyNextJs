<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class DocumentsTable extends Table {

    public $name = 'Documents';
	




	
			  public function initialize(array $config)
    {
       
       
       
			 $this->primaryKey('id');
			 $this->belongsTo('Documentcategory', [
            'foreignKey' => 'doccat_id',
            'joinType' => 'INNER',
        ]);
        
         $this->belongsTo('Students', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        
         $this->belongsTo('DropOutStudent', [
            'foreignKey' => false,
            'conditions' => array(
                 'Documents.user_id = DropOutStudent.s_id'
             ),
                           'propertyName' => 'student' 
        ]);
        
	}
	
	
	}
?>
