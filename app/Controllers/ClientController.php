<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\TransactionsModel;
use App\Models\OperationModel;
use App\Models\FraisModel;

class ClientController extends BaseController
{
    protected $session;
    protected $clientModel;
    protected $transactionsModel;
    protected $operationModel;
    protected $fraisModel;

    public function __construct()
    {
        $this->session = session();
        $this->clientModel = new ClientModel();
        $this->transactionsModel = new TransactionsModel();
        $this->operationModel = new OperationModel();
        $this->fraisModel = new FraisModel();
    }

    protected function checkAuth()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'client') {
            return redirect()->to(site_url('login'))->with('error', 'Vous devez être connecté en tant que client.');
        }
        return null;
    }

    public function index()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $clientId = $this->session->get('id');
        $client = $this->clientModel->find($clientId);

        $data = [
            'clientName' => $this->session->get('name'),
            'client' => $client,
            'solde' => $client['solde'] ?? 0
        ];

        return view('client/dashboard', $data);
    }

    // --- DÉPÔT ---
    public function depot()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        return view('client/depot');
    }

    public function faire_depot()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $montant = (float) $this->request->getPost('montant');
        $clientId = $this->session->get('id');

        if ($montant < 100) {
            return redirect()->back()->with('error', 'Le montant minimum est de 100.');
        }

        $operationId = 1; 
        
        // Adaptation selon ta méthode du FraisModel (calculerFrais ou calculFrais)
        $fraisData = method_exists($this->fraisModel, 'calculerFrais') 
            ? $this->fraisModel->calculerFrais($operationId, $montant)
            : $this->fraisModel->calculFrais($operationId, $montant);
            
        $frais = $fraisData['montant_frais'] ?? $fraisData['montant'] ?? 0;

        // Début de la transaction manuelle stricte
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $client = $this->clientModel->find($clientId);
            
            // Créditer : solde + (montant - frais)
            $nouveauSolde = $client['solde'] + ($montant - $frais);
            $this->clientModel->update($clientId, ['solde' => $nouveauSolde]);

            $transactionData = [
                'montant'      => $montant,
                'frais'        => $frais,
                'date'         => date('Y-m-d H:i:s'),
                'operation_id' => $operationId,
                'client_hote'  => $clientId,
                'client_cible' => null
            ];
            $this->transactionsModel->insert($transactionData);

            if ($db->transStatus() === false) {
                throw new \Exception("Échec de l'insertion SQL de la transaction.");
            }

            $db->transCommit();
            return redirect()->to(site_url('client/dashboard'))->with('success', 'Dépôt effectué avec succès.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Erreur lors du dépôt : ' . $e->getMessage());
        }
    }   

    // --- TRANSFERT ---
    public function transfert()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        return view('client/transfert');
    }

    public function processTransfert()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        // Récupère soit 'client_cible_num' soit 'telephone' selon ton formulaire
        $telephone = $this->request->getPost('client_cible_num') ?? $this->request->getPost('telephone');
        $montant = (float) $this->request->getPost('montant');
        $clientId = $this->session->get('id');

        if ($montant < 100) {
            return redirect()->back()->with('error', 'Le montant minimum est de 100.');
        }

        // Si findByTelephone existe sur ton modèle, on l'utilise, sinon requete classique
        $clientCible = method_exists($this->clientModel, 'findByTelephone')
            ? $this->clientModel->findByTelephone($telephone)
            : $this->clientModel->where('telephone', $telephone)->first();

        if (!$clientCible) {
            return redirect()->back()->with('error', 'Numéro de téléphone introuvable.');
        }

        if ($clientCible['id'] == $clientId) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas effectuer un transfert à vous-même.');
        }

        $clientHote = $this->clientModel->find($clientId);
        
        $operationId = 3; // ID Transfert
        $fraisData = method_exists($this->fraisModel, 'calculerFrais') 
            ? $this->fraisModel->calculerFrais($operationId, $montant)
            : $this->fraisModel->calculFrais($operationId, $montant);
            
        $frais = $fraisData['montant_frais'] ?? $fraisData['montant'] ?? 0;

        // Validation du solde
        if ($clientHote['solde'] < $montant) {
            return redirect()->back()->with('error', 'Solde insuffisant.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // Débiter l'expéditeur du montant total
            $nouveauSoldeHote = $clientHote['solde'] - $montant;
            $this->clientModel->update($clientId, ['solde' => $nouveauSoldeHote]);

            // Créditer le destinataire (montant - frais)
            $nouveauSoldeCible = $clientCible['solde'] + ($montant - $frais);
            $this->clientModel->update($clientCible['id'], ['solde' => $nouveauSoldeCible]);

            $transactionData = [
                'montant'      => $montant,
                'frais'        => $frais,
                'date'         => date('Y-m-d H:i:s'),
                'operation_id' => $operationId,
                'client_hote'  => $clientId,
                'client_cible' => $clientCible['id']
            ];
            $this->transactionsModel->insert($transactionData);

            if ($db->transStatus() === false) {
                throw new \Exception("Échec de la validation SQL du transfert.");
            }

            $db->transCommit();
            return redirect()->to(site_url('client/dashboard'))->with('success', 'Transfert effectué avec succès.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Erreur lors du transfert : ' . $e->getMessage());
        }
    }

    // --- RETRAIT ---
    public function retrait()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        return view('client/retrait');
    }

    public function processRetrait()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $montant = (float) $this->request->getPost('montant');
        $clientId = $this->session->get('id');

        if ($montant < 100) {
            return redirect()->back()->with('error', 'Le montant minimum est de 100.');
        }

        $client = $this->clientModel->find($clientId);
        $operationId = 2; // ID Retrait

        $fraisData = method_exists($this->fraisModel, 'calculerFrais') 
            ? $this->fraisModel->calculerFrais($operationId, $montant)
            : $this->fraisModel->calculFrais($operationId, $montant);
            
        $frais = $fraisData['montant_frais'] ?? $fraisData['montant'] ?? 0;

        // Tester le solde (Montant + Frais dus pour le retrait)
        if ($client['solde'] < ($montant + $frais)) {
            return redirect()->back()->with('error', 'Votre solde est insuffisant pour couvrir le retrait et ses frais.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // Débiter : solde - (montant + frais)
            $nouveauSolde = $client['solde'] - ($montant + $frais);
            $this->clientModel->update($clientId, ['solde' => $nouveauSolde]);

            $transactionData = [
                'montant'      => $montant,
                'frais'        => $frais,
                'date'         => date('Y-m-d H:i:s'),
                'operation_id' => $operationId,
                'client_hote'  => $clientId,
                'client_cible' => null
            ];
            $this->transactionsModel->insert($transactionData);

            if ($db->transStatus() === false) {
                throw new \Exception("Échec SQL lors du traitement du retrait.");
            }

            $db->transCommit();
            return redirect()->to(site_url('client/dashboard'))->with('success', 'Retrait effectué avec succès.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Erreur lors du retrait : ' . $e->getMessage());
        }
    }

    public function historique()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $clientId = $this->session->get('id');

        $transactions = $this->transactionsModel
            ->select('transactions.*, operation.libelle as operation_nom, 
                     c1.nom as client_hote_nom, c1.prenom as client_hote_prenom,
                     c2.nom as client_cible_nom, c2.prenom as client_cible_prenom')
            ->join('operation', 'transactions.operation_id = operation.id')
            ->join('client as c1', 'transactions.client_hote = c1.id')
            ->join('client as c2', 'transactions.client_cible = c2.id', 'left')
            ->groupStart()
                ->where('transactions.client_hote', $clientId)
                ->orWhere('transactions.client_cible', $clientId)
            ->groupEnd()
            ->orderBy('transactions.date', 'DESC')
            ->findAll();

        return view('client/historique', ['transactions' => $transactions]);
    }
}