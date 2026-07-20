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
            return redirect()->to(base_url('login'))->with('error', 'Vous devez être connecté en tant que client.');
        }
        return null;
    }

    public function index()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) {
            return $authCheck;
        }

        $clientId = $this->session->get('id');
        $client = $this->clientModel->find($clientId);

        $data = [
            'clientName' => $this->session->get('name'),
            'client' => $client,
            'solde' => $client['solde'] ?? 0
        ];

        return view('client/dashboard', $data);
    }

    public function depot()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) {
            return $authCheck;
        }

        $clientId = $this->session->get('id');
        $client = $this->clientModel->find($clientId);

        return view('client/depot', ['client' => $client]);
    }

    public function faire_depot()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) {
            return $authCheck;
        }

        $montant = $this->request->getPost('montant');
        $clientId = $this->session->get('id');

        // Validation
        if ($montant <= 0) {
            return redirect()->back()->with('error', 'Le montant doit être positif.');
        }

        // Récupérer l'opération de dépôt (id=1 par exemple)
        $operationId = 1; // À adapter selon votre base
        $operation = $this->operationModel->find($operationId);

        if (!$operation) {
            return redirect()->back()->with('error', 'Opération de dépôt non trouvée.');
        }

        // Calculer les frais
        $fraisData = $this->fraisModel->calculFrais($operationId, $montant);
        $frais = $fraisData['montant'] ?? 0;

        // Démarrer la transaction SQL
        $this->clientModel->transStart();

        try {
            // Mise à jour du solde client
            $client = $this->clientModel->find($clientId);
            $nouveauSolde = $client['solde'] + $montant;
            $this->clientModel->update($clientId, ['solde' => $nouveauSolde]);

            // Insertion de la transaction
            $transactionData = [
                'montant' => $montant,
                'frais' => $frais,
                'date' => date('Y-m-d H:i:s'),
                'operation_id' => $operationId,
                'client_hote' => $clientId,
                'client_cible' => null
            ];
            $this->transactionsModel->insert($transactionData);

            // Valider la transaction
            $this->clientModel->transComplete();

            return redirect()->to(base_url('client/dashboard'))->with('success', 'Dépôt effectué avec succès.');
        } catch (\Exception $e) {
            // Annuler la transaction en cas d'erreur
            $this->clientModel->transRollback();
            return redirect()->back()->with('error', 'Erreur lors du dépôt : ' . $e->getMessage());
        }
    }

    public function historique()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) {
            return $authCheck;
        }

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

    public function transfert()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) {
            return $authCheck;
        }

        return view('client/transfert');
    }

    public function processTransfert()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) {
            return $authCheck;
        }

        $telephone = $this->request->getPost('telephone');
        $montant = $this->request->getPost('montant');
        $clientId = $this->session->get('id');

        // Validation
        if ($montant <= 0) {
            return redirect()->back()->with('error', 'Le montant doit être positif.');
        }

        // Récupérer le client destinataire
        $clientCible = $this->clientModel->where('telephone', $telephone)->first();
        if (!$clientCible) {
            return redirect()->back()->with('error', 'Numéro de téléphone introuvable.');
        }

        if ($clientCible['id'] == $clientId) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas vous transférer à vous-même.');
        }

        // Vérifier le solde
        $clientHote = $this->clientModel->find($clientId);
        if ($clientHote['solde'] < $montant) {
            return redirect()->back()->with('error', 'Solde insuffisant.');
        }

        // Récupérer l'opération de transfert (id=2 par exemple)
        $operationId = 2; // À adapter
        $fraisData = $this->fraisModel->calculFrais($operationId, $montant);
        $frais = $fraisData['montant'] ?? 0;

        // Démarrer la transaction SQL
        $this->clientModel->transStart();

        try {
            // Déduire du solde de l'expéditeur
            $nouveauSoldeHote = $clientHote['solde'] - $montant;
            $this->clientModel->update($clientId, ['solde' => $nouveauSoldeHote]);

            // Ajouter au solde du destinataire
            $nouveauSoldeCible = $clientCible['solde'] + $montant;
            $this->clientModel->update($clientCible['id'], ['solde' => $nouveauSoldeCible]);

            // Insertion de la transaction
            $transactionData = [
                'montant' => $montant,
                'frais' => $frais,
                'date' => date('Y-m-d H:i:s'),
                'operation_id' => $operationId,
                'client_hote' => $clientId,
                'client_cible' => $clientCible['id']
            ];
            $this->transactionsModel->insert($transactionData);

            $this->clientModel->transComplete();

            return redirect()->to(base_url('client/dashboard'))->with('success', 'Transfert effectué avec succès.');
        } catch (\Exception $e) {
            $this->clientModel->transRollback();
            return redirect()->back()->with('error', 'Erreur lors du transfert : ' . $e->getMessage());
        }
    }

    public function retrait()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) {
            return $authCheck;
        }

        return view('client/retrait');
    }

    public function processRetrait()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) {
            return $authCheck;
        }

        $montant = $this->request->getPost('montant');
        $clientId = $this->session->get('id');

        if ($montant <= 0) {
            return redirect()->back()->with('error', 'Le montant doit être positif.');
        }

        $client = $this->clientModel->find($clientId);
        if ($client['solde'] < $montant) {
            return redirect()->back()->with('error', 'Solde insuffisant.');
        }

        $operationId = 3; // Retrait
        $fraisData = $this->fraisModel->calculFrais($operationId, $montant);
        $frais = $fraisData['montant'] ?? 0;

        $this->clientModel->transStart();

        try {
            $nouveauSolde = $client['solde'] - $montant;
            $this->clientModel->update($clientId, ['solde' => $nouveauSolde]);

            $transactionData = [
                'montant' => $montant,
                'frais' => $frais,
                'date' => date('Y-m-d H:i:s'),
                'operation_id' => $operationId,
                'client_hote' => $clientId,
                'client_cible' => null
            ];
            $this->transactionsModel->insert($transactionData);

            $this->clientModel->transComplete();

            return redirect()->to(base_url('client/dashboard'))->with('success', 'Retrait effectué avec succès.');
        } catch (\Exception $e) {
            $this->clientModel->transRollback();
            return redirect()->back()->with('error', 'Erreur lors du retrait : ' . $e->getMessage());
        }
    }
}