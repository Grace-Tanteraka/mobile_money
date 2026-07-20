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
        // On récupère les deux noms de champs possibles au cas où
        $email    = $this->request->getPost('identifiant') ?? $this->request->getPost('email');
        $numero   = $this->request->getPost('telephone');
        $password = $this->request->getPost('mdp');

        // 1. TENTATIVE DE CONNEXION ADMIN (Par Email)
        if (!empty($email)) {
            $admin = $this->adminModel->where('email', $email)->first();

            // Corrigé : $admin['mdp'] au lieu de $admin['password'] (nom dans ton schéma SQLite)
            if ($admin && password_verify($password, $admin['mdp'])) {
                $sessionData = [
                    'id'         => $admin['id'],
                    'name'       => $admin['prenom'] . ' ' . $admin['nom'],
                    'email'      => $admin['email'],
                    'role'       => 'admin',
                    'isLoggedIn' => true,
                ];

                session()->set($sessionData);
                return redirect()->to(base_url('admin/dashboard'));
            }

            return redirect()->to(base_url('login'))->with('error', 'Email ou mot de passe Administrateur incorrect.');
        }

        // 2. TENTATIVE DE CONNEXION CLIENT (Par Téléphone)
        if (!empty($numero)) {
            $client = $this->clientModel->where('telephone', $numero)->first();

            // ✅ CORRECTION CRITIQUE : On vérifie SI $client existe AVANT d'accéder à $client['id']
            if ($client) {
                
                // Si plus tard tu ajoutes un mot de passe client, décommente cette ligne :
                // if (password_verify($password, $client['mdp'])) { ... }

                $sessionData = [
                    'id'         => $client['id'],
                    'name'       => $client['prenom'] . ' ' . $client['nom'],
                    'numero'     => $client['telephone'],
                    'role'       => 'client',
                    'isLoggedIn' => true,
                ];

                session()->set($sessionData);
                return redirect()->to(base_url('client/dashboard'));
            }

            return redirect()->to(base_url('login'))->with('error', 'Numéro de téléphone introuvable.');
        }

        // 3. SI AUCUN CHAMP N'A ÉTÉ REMPLI
        return redirect()->to(base_url('login'))->with('error', 'Veuillez saisir vos identifiants.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'));
    }
}