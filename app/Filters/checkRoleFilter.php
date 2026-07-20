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
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        // 2. Si aucun rôle n'est spécifié dans les arguments de la route, on laisse passer
        if (empty($arguments)) {
            return;
        }

        // 3. Récupération du rôle utilisateur
        $roleUtilisateur = session()->get('role');

        // 4. Vérification : si le rôle de l'utilisateur n'est pas dans la liste des rôles autorisés ($arguments)
        if (!in_array($roleUtilisateur, $arguments)) {
            
            // Redirection intelligente selon le rôle de l'utilisateur pour éviter les boucles
            if ($roleUtilisateur === 'admin') {
                return redirect()->to('/admin/dashboard')->with('error', 'Accès non autorisé.');
            } 
            
            if ($roleUtilisateur === 'client') {
                return redirect()->to('/client/dashboard')->with('error', 'Accès non autorisé.');
            }

            // Si le rôle est inconnu, retour au login
            return redirect()->to('/login')->with('error', 'Accès refusé.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Rien à faire après l'exécution de la requête
    }
}