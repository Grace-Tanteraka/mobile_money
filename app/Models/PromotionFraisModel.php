<?php

namespace App\Models;

use CodeIgniter\Model;

class PromotionFraisModel extends Model
{
    protected $table            = 'promotion_frais';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    public function getPromotionValue(){
        return $this->where('id', 1)->first();
    }
}