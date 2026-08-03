<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class SubjectskillTable extends Table {

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->Table('subject_skill');

        $this->DisplayField('name');
        $this->PrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsto('ExamSubjects', [
			'foreignKey' => 'subject_id',
			'joinType' => 'Left'
		]);
        // $this->belongsTo('Itemcategory', [
		// 	'foreignKey' => 'category_id',
		// 	'joinType' => 'Left'
		// ]);
    }

}
?>