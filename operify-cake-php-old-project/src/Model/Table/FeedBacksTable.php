<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class FeedBacksTable extends Table
{

    public $name = 'feedbacks';

    public function initialize(array $config)
    {
        $this->table('feedbacks');
        $this->primaryKey('id');
        $this->belongsTo('FeedbackCat', [
            'foreignKey' => 'feedback_cat_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Students', [
            'foreignKey' => 'stud_id',
            'joinType' => 'INNER',
        ]);
    }

}
