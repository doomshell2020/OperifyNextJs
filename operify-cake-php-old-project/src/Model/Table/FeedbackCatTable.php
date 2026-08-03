<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class FeedbackCatTable extends Table {

    public $name = 'feedback_categories';
    
	public function initialize(array $config)
    {
		$this->table('feedback_categories');
        $this->primaryKey('id');
	}

}
?>
