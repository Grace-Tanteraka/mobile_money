<?php

namespace App\Controllers;

class AdminstrateurController extends BaseController{
    protected $session;

    public function __construct()
    {
        $this->session = session();
    }

    public function index()
    {
        $admin = $this->session->get('name');
        // Passer les données à la vue
        return view('Administrateur/dashbord', ['admin' => $admin]);
    }
}