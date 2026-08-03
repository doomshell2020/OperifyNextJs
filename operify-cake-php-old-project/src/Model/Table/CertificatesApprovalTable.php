<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;


class CertificatesApprovalTable extends Table
{

    public $name = 'CertificatesApproval';

    public function initialize(array $config)
    {

        $this->table('certificates_approval');
        $this->primaryKey('id');
        
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
?>