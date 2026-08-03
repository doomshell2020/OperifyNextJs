<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class GalleryTable extends Table
{
     
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->Table('gallery');

        $this->DisplayField('name');
        $this->PrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->Hasmany('Gallerydetails', [
            'foreignKey' => 'gallery_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Classes', [
            'foreignKey' => 'class_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Sections', [
            'foreignKey' => 'section_id',
            'joinType' => 'INNER',
        ]);
        
    }

}