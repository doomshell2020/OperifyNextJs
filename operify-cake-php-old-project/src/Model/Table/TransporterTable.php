<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class TransporterTable extends Table {

    public $name = 'Transporter';
    
    public function initialize(array $config)
    {
			  $this->table('transporter');
              $this->primarykey('id');
              
              $this->belongsTo('Vendors', [
                'foreignKey' => 'transport_id',
                'joinType' => 'LEFT',
              ]);
	  }
	

}
?>
