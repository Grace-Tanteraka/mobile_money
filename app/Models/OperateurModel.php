<?php

namespace App\Models;

use CodeIgniter\Model;

class OperateurModel extends Model
{
    protected $table            = 'operateur';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'nom'
    ];

    protected $useTimestamps    = false;

    // Règles de validation
    protected $validationRules  = [
        'nom' => 'required|min_length[2]|max_length[50]'
    ];

    protected $validationMessages = [
        'nom' => [
            'required' => 'Le nom de l\'opérateur est obligatoire.'
        ]
    ];

    /**
     * Récupérer tous les opérateurs avec leurs préfixes associés
     */
    public function getOperateursAvecPrefixes(): array
    {
        $operateurs = $this->findAll();
        $prefixeModel = new PrefixeModel();

        foreach ($operateurs as &$op) {
            $op['prefixes'] = $prefixeModel->where('operateur_id', $op['id'])->findAll();
        }

        return $operateurs;
    }
}
