<?php

namespace App\Controllers;
use App\Models\FraisModel;

class FraisController extends BaseController
{
    public function calculFraisApi()
    {
        $operation_id = $this->request->getPost('operation_id');
        $montant = $this->request->getPost('montant');

        $fraisModel = new FraisModel();
        $frais = $fraisModel->calculFrais($operation_id, $montant);

        if ($frais) {
            return $this->response->setJSON(['frais' => $frais['montant_frais']]);
        } else {
            return $this->response->setJSON(['frais' => 0]);
        }
    }
}