<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table = 'administrateur';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'nom',
        'prenom',
        'email',
        'mdp'
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