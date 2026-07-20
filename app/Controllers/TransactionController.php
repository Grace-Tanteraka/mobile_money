<?php

namespace App\Controllers;

use App\Models\TransactionsModel;
use App\Models\ClientModel;
use App\Models\FraisModel;
use App\Models\OperationModel;

class TransactionController extends BaseController
{
    public function index()
    {
        $operationModel = new \App\Models\OperationModel();

        $data['operations'] = $operationModel->findAll();

        return view('Client/new-transaction', $data);
    }

    public function processTransaction()
    {
        $operation_id = $this->request->getPost('operation');

        // Aiguillage selon le type d'opération
        switch ($operation_id) {
            case 1: // Dépôt
                return $this->executeDepot();
            case 2: // Retrait
                return $this->executeRetrait();
            case 3: // Transfert
                return $this->executeTransfert();
            default:
                return redirect()->back()->with('error', 'Opération non reconnue.');
        }
    }

    private function executeDepot()
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $id_client_hote = 1; // Remplacer par la session réelle
        $montant = (float) $this->request->getPost('montant');

        if ($montant < 100) {
            return redirect()->back()->with('error', 'Le montant minimum est de 100.');
        }

        $operation_id = 1;

        $fraisModel = new FraisModel();
        $frais = $fraisModel->calculerFrais($operation_id, $montant);
        $frais_value = $frais ? (float)$frais['montant_frais'] : 0.0;

        $transactionModel = new TransactionsModel();
        $data = [
            'client_hote'  => $id_client_hote,
            'client_cible' => $id_client_hote,
            'montant'      => $montant,
            'frais'        => $frais_value,
            'operation_id' => $operation_id,
            'date'         => date('Y-m-d H:i:s')
        ];

        $transactionModel->insert($data);

        // Créditer le compte : solde + (montant - frais)
        $db->table('client')
            ->where('id', $id_client_hote)
            ->set('solde', 'solde + ' . ($montant - $frais_value), false)
            ->update();

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Le dépôt a échoué.');
        }

        return redirect()->to('/client/transactions')->with('success', 'Dépôt réussi !');
    }

    private function executeRetrait()
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $id_client_hote = 1; // Remplacer par la session réelle
        $montant = (float) $this->request->getPost('montant');

        // Simulation du solde actuel du client (à remplacer par $client['solde'])
        $solde_actuel = 500;

        if ($montant < 100) {
            return redirect()->back()->with('error', 'Le montant minimum est de 100.');
        }

        $operation_id = 2;

        $fraisModel = new FraisModel();
        $frais = $fraisModel->calculerFrais($operation_id, $montant);
        $frais_value = $frais ? (float)$frais['montant_frais'] : 0.0;

        // Validation du solde : le client doit pouvoir payer le montant + les frais du retrait
        if (($montant + $frais_value) > $solde_actuel) {
            return redirect()->back()->with('error', 'Votre solde est insuffisant pour couvrir le retrait et ses frais.');
        }

        $transactionModel = new TransactionsModel();
        $data = [
            'client_hote'  => $id_client_hote,
            'client_cible' => null,
            'montant'      => $montant,
            'frais'        => $frais_value,
            'operation_id' => $operation_id,
            'date'         => date('Y-m-d H:i:s')
        ];

        $transactionModel->insert($data);

        // Débiter le compte : solde - (montant + frais)
        $db->table('client')
            ->where('id', $id_client_hote)
            ->set('solde', 'solde - ' . ($montant + $frais_value), false)
            ->update();

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Le retrait a échoué.');
        }

        return redirect()->to('/client/transactions')->with('success', 'Retrait réussi !');
    }

    private function executeTransfert()
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $id_client_hote = 1; // Remplacer par la session réelle
        $montant = (float) $this->request->getPost('montant');

        $solde_actuel = 500; // Remplacer par le solde réel

        if ($montant < 100) {
            return redirect()->back()->with('error', 'Le montant minimum est de 100.');
        }

        $ClientModel = new ClientModel();
        $clientCibleNum = $this->request->getPost('client_cible_num');
        $clientCible = $ClientModel->findByTelephone($clientCibleNum);

        if ($clientCible === null) {
            return redirect()->back()->with('error', "Le numéro que vous avez entré n'existe pas.");
        }

        $operation_id = 3;

        $fraisModel = new FraisModel();
        $frais = $fraisModel->calculerFrais($operation_id, $montant);
        $frais_value = $frais ? (float)$frais['montant_frais'] : 0.0;

        if ($montant > $solde_actuel) {
            return redirect()->back()->with('error', 'Votre solde est insuffisant pour effectuer ce transfert.');
        }

        $transactionModel = new TransactionsModel();
        $data = [
            'client_hote'  => $id_client_hote,
            'client_cible' => $clientCible['id'],
            'montant'      => $montant,
            'frais'        => $frais_value,
            'operation_id' => $operation_id,
            'date'         => date('Y-m-d H:i:s')
        ];

        $transactionModel->insert($data);

        // Débit hôte
        $db->table('client')
            ->where('id', $id_client_hote)
            ->set('solde', 'solde - ' . $montant, false)
            ->update();

        // Crédit cible
        $db->table('client')
            ->where('id', $clientCible['id'])
            ->set('solde', 'solde + ' . ($montant - $frais_value), false)
            ->update();

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Le transfert a échoué.');
        }

        return redirect()->to('/client/transactions')->with('success', 'Transfert réussi !');
    }
}
