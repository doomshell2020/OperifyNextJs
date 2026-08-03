<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class VisitorsTable extends Table {

    public $name = 'Visitors';
	
	

    public function initialize(array $config)
    {
			  $this->table('visitors');
        $this->primaryKey('id');
			 
	}

}
?>