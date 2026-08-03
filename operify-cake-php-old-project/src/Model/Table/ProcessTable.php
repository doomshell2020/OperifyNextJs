<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class ProcessTable extends Table {

    public $name = 'Process';
    
    public function initialize(array $config)
    {
              $this->table('process');
              $this->primaryKey('id');
	}
	

}
?>
