<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class CategorywiseTable extends Table {

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->Table('st_categorywise');

        $this->DisplayField('name');
        $this->PrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsto('Additem', [
			'foreignKey' => 'item_name',
			'joinType' => 'Left'
		]);
        $this->belongsTo('Itemcategory', [
			'foreignKey' => 'category_id',
			'joinType' => 'Left'
		]);
       
    }

}
?>
