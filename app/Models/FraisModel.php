<?php

namespace App\Models;

use CodeIgniter\Model;

class FraisModel extends Model
{
    protected $table = 'frais';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;

    public function calculerFrais($operation_id, $montant)
    {
        return $this->where('operation_id', $operation_id)
            ->where('montant_inf <=', $montant) // montant est supérieur ou égal au minimum
            ->where('montant_sup >=', $montant) // montant est inférieur ou égal au maximum
            ->first();
    }

    public function calculFrais($operation_id, $montant)
    {
        return $this->calculerFrais($operation_id, $montant);
    }
}
