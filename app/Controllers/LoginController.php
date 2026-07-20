<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\AdminstrateurModel; // Attention à l'orthographe du fichier Model si besoin

class LoginController extends BaseController
{
    protected $clientModel;
    protected $adminModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->adminModel = new AdminstrateurModel();
    }

    public function showLogin()
    {
        return view('login');
    }

    public function loginAuth()
    {
        // On récupère le rôle choisi par l'utilisateur ('admin' ou 'client')
        $role     = $this->request->getPost('role');
        $email    = $this->request->getPost('email');
        $numero   = $this->request->getPost('telephone');
        $password = $this->request->getPost('mdp');

        // 1. TENTATIVE DE CONNEXION ADMIN
        if ($role === 'admin') {
            if (empty($email)) {
                return redirect()->to(site_url('login'))->with('error', 'Veuillez saisir votre email.');
            }

            $admin = $this->adminModel->where('email', $email)->first();

            if ($admin && password_verify($password, $admin['mdp'])) {
                $sessionData = [
                    'id'         => $admin['id'],
                    'name'       => $admin['prenom'] . ' ' . $admin['nom'],
                    'email'      => $admin['email'],
                    'role'       => 'admin',
                    'isLoggedIn' => true,
                ];

                session()->set($sessionData);
                return redirect()->to(site_url('admin/dashboard'));
            }

            return redirect()->to(site_url('login'))->with('error', 'Email ou mot de passe Administrateur incorrect.');
        }

        // 2. TENTATIVE DE CONNEXION CLIENT
        if ($role === 'client') {
            if (empty($numero)) {
                return redirect()->to(site_url('login'))->with('error', 'Veuillez saisir votre numéro de téléphone.');
            }

            $client = $this->clientModel->where('telephone', $numero)->first();

            if ($client) {
                $sessionData = [
                    'id'         => $client['id'],
                    'name'       => $client['prenom'] . ' ' . $client['nom'],
                    'numero'     => $client['telephone'],
                    'role'       => 'client',
                    'isLoggedIn' => true,
                ];

                session()->set($sessionData);
                return redirect()->to(site_url('client/dashboard'));
            }

            return redirect()->to(site_url('login'))->with('error', 'Numéro de téléphone introuvable.');
        }

        return redirect()->to(site_url('login'))->with('error', 'Veuillez sélectionner un type de compte.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('login'));
    }
}
