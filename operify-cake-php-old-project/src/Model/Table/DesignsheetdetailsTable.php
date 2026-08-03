<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class DesignsheetdetailsTable extends Table {

    public $name = 'Designsheetdetails';
    
    public function initialize(array $config)
    {
			  $this->table('designsheetdetails');
       
	}
	

}
?>