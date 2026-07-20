<?php

namespace App\Models;

use CodeIgniter\Model;

class AdministrationModel extends Model
{
    protected $table = 'administration';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'administrateur_id',
        'operateur_id'
    ];

    public function findOperateurAdministratedByAdministrateurId(int $administrateurId): ?array
    {
        return $this->select('administration.operateur_id, operateur.nom as operateur_nom')
            ->join('operateur', 'administration.operateur_id = operateur.id')
            ->where('administrateur_id', $administrateurId)->findAll();
    }
}