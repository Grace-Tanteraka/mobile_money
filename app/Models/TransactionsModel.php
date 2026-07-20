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

    public function getGainTotalGlobal()
    {
        $builder = $this->builder();
        $builder->select('SUM(frais + valeur_commision) AS total_gain');
        $query = $builder->get()->getRowArray();
        return (float) ($query['total_gain'] ?? 0);
    }

   
    public function getGainsInclusionInterOp()
    {
        $db = \Config\Database::connect();
        
        $sql = "
            SELECT 
                SUM(frais) AS total_frais_standards,
                SUM(valeur_commision) AS total_commissions_interop,
                SUM(frais + valeur_commision) AS total_general
            FROM transactions
        ";

        return $db->query($sql)->getRowArray();
    }

 
    public function getGainsParOperation()
    {
        return $this->select('operation.libelle AS operation, SUM(transactions.frais + transactions.valeur_commision) AS gain_total, COUNT(transactions.id) AS nb_transactions')
                    ->join('operation', 'operation.id = transactions.operation_id')
                    ->groupBy('operation.id')
                    ->findAll();
    }

    public function getGainsParOperateur()
    {
        return $this->select('operateur.nom AS operateur, SUM(transactions.frais + transactions.valeur_commision) AS gain_total, COUNT(transactions.id) AS nb_transactions')
                    ->join('client AS client_hote', 'client_hote.id = transactions.client_hote')
                    ->join('operateur', 'operateur.id = client_hote.operateur_id')
                    ->groupBy('operateur.id')
                    ->findAll();
    }
    public function getMontantsAEnvoyerParOperateur()
    {
        $db = \Config\Database::connect();

        $sql = "
            SELECT 
                op.nom AS operateur_destinataire,
                COUNT(t.id) AS nb_transferts,
                SUM(t.montant) AS total_brut_envoye,
                SUM(t.valeur_commision) AS total_commissions_gagnees
            FROM transactions t
            JOIN client c ON t.client_cible = c.id
            JOIN operateur op ON c.operateur_id = op.id
            WHERE t.operation_id = 3 -- 3 = Transfert
            GROUP BY op.id, op.nom
        ";

        return $db->query($sql)->getResultArray();
    }
}
