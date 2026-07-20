<?php

namespace App\Controllers;

class AdminstrateurController extends BaseController{
    protected $session;

    public function __construct()
    {
        $this->session = session();
    }

  
    protected function checkAuth()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Vous devez être connecté en tant qu\'administrateur pour accéder à cette page.');
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
        // Passer les données à la vue
        return view('Administrateur/dashbord', ['clientName' => $clientName]);
    }
}