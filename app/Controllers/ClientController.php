<?php

namespace App\Controllers;
use App\Models\ClientModel;

class ClientController extends BaseController
{
    public function listeClient()
    {
        $clientModel = new ClientModel();
        $clients = $clientModel->findAllClientWithOperator();
        return view('Administrateur/client/liste_client', ['clients' => $clients]);
    }

    public function voirTransactionsClient($clientId)
    {
        $transactionsModel = new \App\Models\TransactionsModel();
        $transactions = $transactionsModel->findByClientIdWithOperation($clientId);
        return view('Administrateur/client/transactions_client', ['transactions' => $transactions]);
    }
}