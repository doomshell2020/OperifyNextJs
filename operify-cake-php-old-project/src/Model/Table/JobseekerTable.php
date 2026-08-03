<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class JobseekerTable extends Table {

    public $name = 'Jobseeker';
	
	

    public function initialize(array $config)
    {
			  $this->table('st_Jobseeker');
        
			 
	}

}
?>