<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CheckRoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Vérification de la connexion
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // 2. Si aucun rôle n'est spécifié dans la route, on laisse passer
        if (empty($arguments)) {
            return;
        }

        // 3. Récupération du rôle requis (argument passé depuis la route)
        $roleRequis = $arguments[0];

        // 4. Récupération du rôle de l'utilisateur (stocké en session)
        $roleUtilisateur = session()->get('role'); // ex: 'admin', 'user'

        // 5. Blocage si le rôle ne correspond pas
        if ($roleUtilisateur !== $roleRequis) {
            // Redirection avec un message d'erreur (ou vers une erreur 403)
            return redirect()->to('/dashboard')->with('error', 'Accès interdit !');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Laisser vide pour ce cas d'usage
    }
}