<?php

namespace App\Controllers;
use App\Models\ClientModel;

class ClientController extends BaseController
{
    protected $session;

    public function __construct()
    {
        $this->session = session();
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

        $clientName = $this->session->get('name');

        return view('client/dashboard', ['clientName' => $clientName]);
    }

    public function listeClient()
    {
        $clientModel = new ClientModel();
        $clients = $clientModel->findAllClientWithOperatorForOneAdministrateur(1);
        return view('Administrateur/client/liste_client', ['clients' => $clients]);
    }

    public function voirTransactionsClient($clientId)
    {
        $transactionsModel = new \App\Models\TransactionsModel();
        $transactions = $transactionsModel->findByClientIdWithOperation($clientId);
        return view('Administrateur/client/transactions_client', ['transactions' => $transactions]);
    }
}