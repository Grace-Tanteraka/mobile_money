<?php

namespace App\Controllers;

class ClientController extends BaseController
{
    protected $session;

    public function __construct()
    {
        $this->session = session();
    }

    protected function checkAuth()
    {
        // Corrigé : 'isLoggedIn' au lieu de 'is_logged_in'
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

        // Assure-toi que le dossier dans app/Views s'appelle 'client' (en minuscules)
        return view('client/dashboard', ['clientName' => $clientName]);
    }
}