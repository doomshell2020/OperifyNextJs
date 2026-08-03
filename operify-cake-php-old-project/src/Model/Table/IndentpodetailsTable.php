<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class IndentpodetailsTable extends Table {

    public $name = 'Indentpodetails';
    
    public function initialize(array $config)
    {
			  $this->table('indentpodetails');
	}
	

}
?>
