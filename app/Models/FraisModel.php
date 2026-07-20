<?php

namespace App\Models;

use CodeIgniter\Model;

class FraisModel extends Model
{
    protected $table = 'frais';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;

    public function calculFrais(int $operation_id, float $montant){
        return $this->where('operation_id', $operation_id)
                ->andWhere('montant_inf >=', $montant)
                ->andWhere('montant_sup <=', $montant)
            ->first();
    }
}