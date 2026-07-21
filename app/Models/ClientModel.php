<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\AdministrationModel;

class ClientModel extends Model
{
    protected $table = 'client';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'nom',
        'prenom',
        'operateur_id',
        'telephone',
        'solde',
        'epargne'
    ];


    public function verificationLogin(string $email, string $password): ?array
    {
        $rh = $this->where('email', $email)->first();
        if ($rh && password_verify($password, $rh['password'])) {
            return $rh;
        }
        return null;
    }

    public function findAllClientWithOperatorForOneAdministrateur(int $administrateurId): array
    {
        $administrationModel = new AdministrationModel();
        $operateurs = $administrationModel->findOperateurAdministratedByAdministrateurId($administrateurId);
        $operateurIds = array_column($operateurs, 'operateur_id');
        return $this->select('client.*, operateur.nom as operateur_nom')
            ->join('operateur', 'client.operateur_id = operateur.id')
            ->whereIn('client.operateur_id', $operateurIds)
            ->findAll();
    }

    public function findByTelephone(string $telephone): ?array
    {
        return $this->where('telephone', $telephone)->first();
    }

    public function getepargne(int $id_client)
    {
        return $this->where('id', $id_client)->first();
    }
}
