<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class ProductionsheetTable extends Table {

    public $name = 'Productionsheet';
    
    public function initialize(array $config)
    {
              $this->table('production_sheet');
              $this->primaryKey('id');
      
              $this->belongsTo('Machinemaster', [
                  'foreignKey' => 'machines_id',
                  'joinType' => 'INNER',
              ]);
	}
	

}
?>
