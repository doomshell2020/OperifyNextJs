<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class EnquiresTable extends Table {

    public $name = 'Enquires';
				
public function initialize(array $config)
    {
       
	 $this->table('enquire');
     $this->primaryKey('id');
        	
	}

}
?>
