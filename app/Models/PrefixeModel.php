<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeModel extends Model
{
    protected $table            = 'prefixe';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'code',
        'operateur_id'
    ];

    protected $useTimestamps    = false;

    // Règles de validation
    protected $validationRules  = [
        'code'         => 'required|exact_length[3]|is_unique[prefixe.code,id,{id}]',
        'operateur_id' => 'required|is_natural_no_zero'
    ];

    protected $validationMessages = [
        'code' => [
            'required'     => 'Le code préfixe est obligatoire.',
            'exact_length' => 'Le préfixe doit faire exactement 3 chiffres (ex: 032).',
            'is_unique'    => 'Ce préfixe est déjà attribué.'
        ]
    ];

    /**
     * Obtenir les préfixes avec le nom de l'opérateur
     */
    public function getPrefixesAvecOperateur(): array
    {
        return $this->select('prefixe.*, operateur.nom AS operateur_nom')
                    ->join('operateur', 'operateur.id = prefixe.operateur_id')
                    ->findAll();
    }

    /**
     * Trouver l'opérateur associé à un numéro de téléphone (ex: "0321234567")
     */
    public function getOperateurParNumero(string $numero): ?array
    {
        // On extrait les 3 premiers chiffres du numéro
        $code = substr($numero, 0, 3);

        return $this->select('operateur.*')
                    ->join('operateur', 'operateur.id = prefixe.operateur_id')
                    ->where('prefixe.code', $code)
                    ->first();
    }
}