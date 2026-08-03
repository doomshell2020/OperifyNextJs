<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class ProductionsheetitemTable extends Table {

    public $name = 'Productionsheetitem';
    
    public function initialize(array $config)
    {
              $this->table('productionsheet_item');
              $this->primaryKey('id');
      
              $this->belongsTo('Productionsheet', [
                  'foreignKey' => 'productionseet_id',
                  'joinType' => 'INNER',
              ]);
	}
	

}
?>
