<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class SyllabusmasterTable extends Table {

    
	
	

    public function initialize()
    {
		$this->table('syllabus_master');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');	 
	}

}
?>