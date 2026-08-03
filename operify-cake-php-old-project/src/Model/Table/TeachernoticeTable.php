<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class TeachernoticeTable extends Table {

    public $name = 'Teachernotice';
    
    	  public function initialize(array $config)
    {
       
       
       
			  $this->table('teachernotice');
        $this->primaryKey('id');
        
    }
    
    
	
	

 

}
?>
