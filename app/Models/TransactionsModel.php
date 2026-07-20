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
        'montant',
        'operation_id',
        'date',
        'frais',
        'client_hote',
        'client_cible'
    ];

    protected $validationRules = [
        'client_id' => 'required|integer',
        'montant' => 'required|decimal|greater_than[100]',
        'operation_id' => 'required|integer',
        'date' => 'required|valid_date',
        'frais' => 'required|decimal',
        'client_hote' => 'required|integer',
        'client_cible' => 'permit_empty|integer'
    ];


    public function findByClientIdWithOperation(int $clientId): array
    {
        return $this->select('transactions.*, operation.libelle as operation_nom, client.nom as client_cible_nom')
            ->join('operation', 'transactions.operation_id = operation.id')
            ->join('client', 'transactions.client_cible = client.id', 'left')
            ->groupStart()
                ->where('transactions.client_hote', $clientId)
                ->orWhere('transactions.client_cible', $clientId)
            ->findAll();
    }

    public function saveTransaction(array $data): bool
    {
        return $this->insert($data) !== false;
    }
}
