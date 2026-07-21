<?php

namespace App\Controllers;

use App\Models\TransactionsModel;
use App\Models\OperationModel;
use App\Models\OperateurModel;
use App\Models\ClientModel;
use App\Models\AdminstrateurModel;
use App\Models\PromotionFraisModel;

class AdminController extends BaseController
{
    protected $session;
    protected $transactionsModel;
    protected $operationModel;
    protected $operateurModel;
    protected $clientModel;
    protected $administrateurModel;
    protected $promotionFraisModel;

    public function __construct()
    {
        $this->session = session();
        $this->transactionsModel = new TransactionsModel();
        $this->operationModel = new OperationModel();
        $this->operateurModel = new OperateurModel();
        $this->clientModel = new ClientModel();
        $this->administrateurModel = new AdminstrateurModel();
        $this->promotionFraisModel = new PromotionFraisModel();
    }

    protected function checkAuth()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Vous devez être connecté en tant qu\'administrateur.');
        }
        return null;
    }

    public function index()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) {
            return $authCheck;
        }

        // Calcul des gains totaux
        $totalGains = $this->transactionsModel->selectSum('frais')->first();

        // Gains par type d'opération
        $gainsParOperation = $this->transactionsModel
            ->select('operation.libelle as operation, SUM(transactions.frais) as total')
            ->join('operation', 'transactions.operation_id = operation.id')
            ->groupBy('operation.id, operation.libelle')
            ->findAll();

        // Gains par opérateur
        $gainsParOperateur = $this->transactionsModel
            ->select('operateur.nom as operateur, SUM(transactions.frais) as total')
            ->join('client', 'transactions.client_hote = client.id')
            ->join('operateur', 'client.operateur_id = operateur.id')
            ->groupBy('operateur.id, operateur.nom')
            ->findAll();

        // Statistiques générales
        $totalTransactions = $this->transactionsModel->countAll();
        $totalClients = $this->clientModel->countAll();
        $totalOperations = $this->operationModel->countAll();

        $data = [
            'adminName' => $this->session->get('name'),
            'totalGains' => $totalGains['frais'] ?? 0,
            'gainsParOperation' => $gainsParOperation,
            'gainsParOperateur' => $gainsParOperateur,
            'totalTransactions' => $totalTransactions,
            'totalClients' => $totalClients,
            'totalOperations' => $totalOperations
        ];

        return view('admin/dashboard', $data);
    }

    public function listeClient()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) {
            return $authCheck;
        }

        $adminId = session()->get('id');

        $clients = $this->clientModel->findAllClientWithOperatorForOneAdministrateur($adminId);

        return view('admin/liste_client', [
            'clients' => $clients,
            'pager' => $this->clientModel->pager,
        ]);
    }

    public function voirTransactionsClient($clientId)
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) {
            return $authCheck;
        }

        $transactions = $this->transactionsModel->getHistoricClient($clientId);

        $client = $this->clientModel->find($clientId);

        return view('admin/transactions_client', [
            'client' => $client,
            'transactions' => $transactions
        ]);
    }

    public function situationGains()
    {
        $transactionsModel = $this->transactionsModel;
        $data = [
            'gainGlobal'     => $transactionsModel->getGainTotalGlobal(),
            'detailGains'    => $transactionsModel->getGainsInclusionInterOp(),
            'gainsOps'       => $transactionsModel->getGainsParOperation(),
            'gainsOperateurs' => $transactionsModel->getGainsParOperateur(),
            'reconciliation' => $transactionsModel->getMontantsAEnvoyerParOperateur()
        ];

        return view('admin/dashboard', $data);
    }

    public function showUpdatePromotionForm(){
        $promotion = $this->promotionFraisModel->getPromotionValue();

        $data = [
            'promotion' => $promotion
        ];

        return view('admin/promotion', $data);
    }
}
