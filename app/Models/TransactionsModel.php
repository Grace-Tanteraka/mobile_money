<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionsModel extends Model
{
    protected $table = 'transactions';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'client_id',
        'operateur_id',
        'montant',
        'operation_id',
        'date',
        'frais',
        'client_hote',
        'client_cible'
    ];

    public function findByClientIdWithOperation(int $clientId): array
    {
        return $this->select('transactions.*, operation.nom as operation_nom, client.nom as client_cible_nom')
            ->join('operation', 'transactions.operation_id = operation.id')
            ->join('client', 'transactions.client_cible = client.id', 'left')
            ->where('transactions.client_hote', $clientId, 'or')
            ->where('transactions.client_cible', $clientId)
            ->findAll();
    }
}
