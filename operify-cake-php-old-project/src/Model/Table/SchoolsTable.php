<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Auth\DefaultPasswordHasher;

class SchoolsTable extends Table {

    public $name = 'Schools'; 

    public function initialize(array $config)
    {
		$this->table('schools');
        $this->primaryKey('id');
		
	}
	

}
?>
