<?php

namespace App\Models;

use CodeIgniter\Model;

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
        'solde'
    ];
    

    public function verificationLogin(string $email, string $password): ?array
    {
        $rh = $this->where('email', $email)->first();
        if ($rh && password_verify($password, $rh['password'])) {
            return $rh;
        }
        return null;
    }

}