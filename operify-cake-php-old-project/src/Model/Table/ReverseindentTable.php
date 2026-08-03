<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class ReverseindentTable extends Table {

    public $name = 'Reverseindent';
    
    public function initialize(array $config)
    {
			  $this->table('reverseindent');
	}
	

}
?>
