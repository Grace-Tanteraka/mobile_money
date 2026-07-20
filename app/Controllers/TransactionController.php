<?php

namespace App\Controllers;

use App\Models\TransactionsModel;
use App\Models\ClientModel;
use App\Models\FraisModel;
use App\Models\OperationModel;
use App\Models\ComissionModel;
use App\Models\OperateurModel;
use App\Models\PrefixeModel;
class TransactionController extends BaseController
{
    public function index()
    {
        $operationModel = new \App\Models\OperationModel();
        $clientModel = new \App\Models\ClientModel();
        $comissionModel = new \App\Models\ComissionModel();
        $OperateurModel = new \App\Models\OperateurModel();
        $PrefixeModel = new \App\Models\PrefixeModel();
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
        $frais_value = $frais ? (float) $frais['montant_frais'] : 0.0;

        $transactionModel = new TransactionsModel();
        $data = [
            'client_hote' => $id_client_hote,
            'client_cible' => $id_client_hote,
            'montant' => $montant,
            'frais' => $frais_value,
            'operation_id' => $operation_id,
            'date' => date('Y-m-d H:i:s')
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
        $frais_value = $frais ? (float) $frais['montant_frais'] : 0.0;

        // Validation du solde : le client doit pouvoir payer le montant + les frais du retrait
        if (($montant + $frais_value) > $solde_actuel) {
            return redirect()->back()->with('error', 'Votre solde est insuffisant pour couvrir le retrait et ses frais.');
        }

        $transactionModel = new TransactionsModel();
        $data = [
            'client_hote' => $id_client_hote,
            'client_cible' => null,
            'montant' => $montant,
            'frais' => $frais_value,
            'operation_id' => $operation_id,
            'date' => date('Y-m-d H:i:s')
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

   /* private function executeTransfert()
    {
        $db = \Config\Database::connect();
        $db->transStart();

        //$id_client_hote = 1; // Remplacer par la session réelle
        $nuemro_hote = $this->session->get('numero');
        $clientModel1 = new ClientModel();
        $id_client_hote = $this->$clientModel1->findByTelephone($nuemro_hote)['id'];
        $client1 = $clientModel1->findByTelephone($nuemro_hote);
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
        $frais_value = $frais ? (float) $frais['montant_frais'] : 0.0;

        if ($montant > $solde_actuel) {
            return redirect()->back()->with('error', 'Votre solde est insuffisant pour effectuer ce transfert.');
        }

        /*Comparer les prefixe pour les comission
        // 1. Identifier l'opérateur de l'expéditeur et du destinataire
        $prefixeModel = new PrefixeModel();
        $opHote = $prefixeModel->getOperateurParNumero($client1['telephone']);
        $opCible = $prefixeModel->getOperateurParNumero($clientCible['telephone']);

        $commission_value = 0.00;

        // 2. Si c'est un transfert vers un AUTRE opérateur
        if ($opHote && $opCible && $opHote['id'] !== $opCible['id']) {

            // Rechercher la règle de commission correspondante dans la BDD
            $comissionModel = new ComissionModel();
            $regleCom = $comissionModel->where('operateur_source_id', $opHote['id'])
                ->where('operateur_cible_id', $opCible['id'])
                ->first();

            if ($regleCom) {
                $pourcentage = (float) $regleCom['taux_pourcentage']; // Ex: 10
                $commission_value = ($montant * $pourcentage) / 100;  // Calcul des 10% du montant
            } else {
                // Taux par défaut si pas de règle spécifique trouvée
                $commission_value = ($montant * 5) / 100; // 5% par défaut
            }
        }

        // 3. Calcul du total à débiter chez l'expéditeur
        $totalADebiter = $montant + $frais_value + $commission_value;

        $transactionModel = new TransactionsModel();
        $data = [
            'client_hote' => $id_client_hote,
            'client_cible' => $clientCible['id'],
            'montant' => $montant,
            'frais' => $frais_value,
            'operation_id' => $operation_id,
            'valeur_commision' => $commission_value,
            'date' => date('Y-m-d H:i:s')
        ];

        $transactionModel->insert($data);

        // Débit hôte
        $db->table('client')
            ->where('id', $id_client_hote)
            ->set('solde', 'solde - ' . ($montant - $frais_value - $commission_value), false)
            ->update();
        
        // Crédit cible
        $db->table('client')
            ->where('id', $clientCible['id'])
            ->set('solde', 'solde + ' . ($montant), false)
            ->update();

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Le transfert a échoué.');
        }

        return redirect()->to('/client/transactions')->with('success', 'Transfert réussi !');
    }*/
        private function executeTransfert()
{
    $db = \Config\Database::connect();
    $db->transStart();

    // 1. Récupération du client connecté (Hôte)
    $numero_hote  = $this->session->get('numero');
    $clientModel  = new ClientModel();
    $client1      = $clientModel->findByTelephone($numero_hote);

    if (!$client1) {
        return redirect()->back()->with('error', 'Session invalide.');
    }

    $id_client_hote = $client1['id'];
    $montant        = (float) $this->request->getPost('montant');

    if ($montant < 100) {
        return redirect()->back()->with('error', 'Le montant minimum est de 100.');
    }

    // 2. Récupération du destinataire (Cible)
    $clientCibleNum = $this->request->getPost('client_cible_num');
    $clientCible    = $clientModel->findByTelephone($clientCibleNum);

    if ($clientCible === null) {
        return redirect()->back()->with('error', "Le numéro que vous avez entré n'existe pas.");
    }

    if ($id_client_hote === $clientCible['id']) {
        return redirect()->back()->with('error', "Vous ne pouvez pas effectuer un transfert vers votre propre numéro.");
    }

    $operation_id = 3;

    // 3. Calcul des frais de transfert
    $fraisModel  = new FraisModel();
    $frais       = $fraisModel->calculerFrais($operation_id, $montant);
    $frais_value = $frais ? (float) $frais['montant_frais'] : 0.0;

    // 4. Calcul de la commission Inter-Opérateurs
    $prefixeModel     = new PrefixeModel();
    $opHote           = $prefixeModel->getOperateurParNumero($client1['telephone']);
    $opCible          = $prefixeModel->getOperateurParNumero($clientCible['telephone']);
    $commission_value = 0.00;

    if ($opHote && $opCible && $opHote['id'] !== $opCible['id']) {
        $comissionModel = new ComissionModel();
        $regleCom       = $comissionModel->where('operateur_source_id', $opHote['id'])
                                         ->where('operateur_cible_id', $opCible['id'])
                                         ->first();

        if ($regleCom) {
            $pourcentage      = (float) $regleCom['taux_pourcentage'];
            $commission_value = ($montant * $pourcentage) / 100;
        } else {
            $commission_value = ($montant * 5) / 100; // 5% par défaut
        }
    }

    // 5. Calcul du total et vérification du vrai solde
    $totalADebiter = $montant + $frais_value + $commission_value;
    $solde_actuel  = (float) $client1['solde'];

    if ($totalADebiter > $solde_actuel) {
        return redirect()->back()->with('error', 'Solde insuffisant. Requis avec frais : ' . $totalADebiter . ' Ar');
    }

    // 6. Enregistrement de la transaction
    $transactionModel = new TransactionsModel();
    $data = [
        'client_hote'      => $id_client_hote,
        'client_cible'     => $clientCible['id'],
        'montant'          => $montant,
        'frais'            => $frais_value,
        'operation_id'     => $operation_id,
        'valeur_commision' => $commission_value,
        'date'             => date('Y-m-d H:i:s')
    ];

    $transactionModel->insert($data);

    // 7. Mise à jour des soldes
    // Débit hôte (Montant + Frais + Commission)
    $db->table('client')
        ->where('id', $id_client_hote)
        ->set('solde', 'solde - ' . $totalADebiter, false)
        ->update();

    // Crédit cible (Montant net)
    $db->table('client')
        ->where('id', $clientCible['id'])
        ->set('solde', 'solde + ' . $montant, false)
        ->update(); 
        
    $db->transComplete();

    if ($db->transStatus() === false) {
        return redirect()->back()->with('error', 'Le transfert a échoué.');
    }

    return redirect()->to('/client/transactions')->with('success', 'Transfert réussi !');
}
}
