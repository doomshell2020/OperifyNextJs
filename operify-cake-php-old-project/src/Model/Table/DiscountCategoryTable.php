<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class DiscountCategoryTable extends Table {

    public $name = 'DiscountCategory';
    
    public function initialize(array $config)
    {
			  $this->table('discountcategory');
        $this->primaryKey('id');
		
	}
	
	

 

}
?>
