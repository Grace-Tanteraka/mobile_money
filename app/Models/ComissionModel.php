<?php

namespace App\Models;

use CodeIgniter\Model;

class ComissionModel extends Model
{
    protected $table            = 'comission';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    
    protected $allowedFields    = [
        'Nom',
        'id_transaction',
        'commission'
    ];

    protected $useTimestamps    = false;

    // Règles de validation de base
    protected $validationRules  = [
        'Nom'            => 'required|min_length[2]|max_length[50]',
        'id_transaction' => 'permit_empty|is_natural_no_zero',
        'commission'     => 'required|decimal'
    ];

    protected $validationMessages = [
        'Nom' => [
            'required' => 'Le nom de la commission est obligatoire.'
        ],
        'commission' => [
            'required' => 'Le montant de la commission est obligatoire.',
            'decimal'  => 'La commission doit être un montant valide.'
        ]
    ];

    public function getCommissionsAvecTransaction()
    {
        return $this->select('comission.*, transactions.montant AS montant_transaction, transactions.date')
                    ->join('transactions', 'transactions.id = comission.id_transaction', 'left')
                    ->findAll();
    }

    public function getCommissionById(int $id): ?array
    {
        return $this->where('id', $id)->first();
    }

    public function getCommissionByTransactionId(int $transactionId): ?array
    {
        return $this->where('id_transaction', $transactionId)->first();
    }
    public function getTotalCommissions()
    {
        $result = $this->selectSum('commission', 'total')->first();
        return $result['total'] ?? 0.00;
    }
}