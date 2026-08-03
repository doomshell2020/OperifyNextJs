<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class InspectionGrnDetailsTable extends Table {

    public $name = 'InspectionGrnDetails';
	
	public function initialize(array $config)
    {       
        $this->table('grn_inspection_details');
		$this->primaryKey('id');

        $this->belongsTo('Additem', [
			'foreignKey' => 'item_id',
			'joinType' => 'INNER'
		]);

        $this->belongsTo('Taxmaster', [
			'foreignKey' => 'tax_id',
			'joinType' => 'Left'
		]);
          
          
    }

  

}
?>
