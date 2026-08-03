<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class PeriodicalMasterDetailsTable extends Table {

    public $name = 'PeriodicalMasterDetails';
    
    public function initialize(array $config)
    {
			  $this->table('library_periodical_details');
        $this->primaryKey('id');
		 $this->belongsTo('PeriodicalMaster', [
            'foreignKey' => 'periodic_id',
            'joinType' => 'INNER',
        ]);
	}
	
	

 

}
?>
