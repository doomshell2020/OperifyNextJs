<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class WordbankTable extends Table {

    public $name = 'Wordbank';
	  public function initialize(array $config)
    {
		$this->table('theme_wordbank');

        $this->primaryKey('id');
        
         $this->belongsTo('Classes', [
           'foreignKey' => 'class_id',
         'joinType' => 'INNER',
           
        ]);
		
	}
	

 
}
?>
