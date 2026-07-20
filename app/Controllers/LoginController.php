<?php

namespace App\Controllers;
use App\Models\ClienModel;
use App\Models\AdminModel;

class LoginController extends BaseController
{

    protected $clientModel;
    protected $AdminModel;

    public function __construct()
    {
        $this->clientModel = new clientModel();
        $this->AdminModel = new AdminModel();
    }


    public function loginAuth()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $numero = $this->request->getPost('telephone');
        $clientModel = new clientModel();
        $user = $clientModel->where('telephone', $numero)->first();

        $admin = new AdminModel();
        $user2 = $admin->where("email", $email);


        if ($user2) {
            if ($email != null && $password != null && password_verify($password, $user['password'])) {

                $sessionData = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => "admin",       // ex: 'admin' ou 'user'
                    'isLoggedIn' => true,                // Le fameux drapeau !
                ];

                session()->set($sessionData);

                if ($user['role'] === 'admin') {
                    return redirect()->to('/admin/dashboard');
                } else {
                    return redirect()->to('/dashboard');
                }

            } else {
                return redirect()->to('/login')->with('error', 'Mot de passe incorrect.');
            }
        } else {
            return redirect()->to('/login')->with('error', 'email introuvable.');
        }


        if ($user) {
            if ($numero != null &&  $email == null && $password == null) {
                $sessionData = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'numero'=> $user['tel'],
                    'role' => "client",       // ex: 'admin' ou 'user'
                    'isLoggedIn' => true,                // Le fameux drapeau !
                ];
                session()->set($sessionData);
                if ($user['role'] === 'cleint') {
                    return redirect()->to('client/dashboard');
                } else {
                    return redirect()->to('/login');
                }
            }
        } else {
            return redirect()->to('/login')->with('error', 'numero introuvable.');
        }
    }
}