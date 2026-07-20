<?php

namespace App\Controllers;

class AdminDashboard extends BaseController
{
    protected $session;

    public function __construct()
    {
        $this->session = session();
    }

    /**
     * Vérifier que l'utilisateur est connecté et a le rôle admin
     */
    protected function checkAuth()
    {
        if (!$this->session->get('is_logged_in') || $this->session->get('user_role') !== 'admin') {
            return redirect()->to(route_to('auth_login'));
        }

        return null;
    }

    /**
     * Récupérer les statistiques administrateur depuis la base.
     */
    protected function getStats(): array
    {
        $db = \Config\Database::connect();

        return [
            'employees_active' => (int) $db->table('employes')->where('actif', 1)->countAllResults(),
            'pending_requests' => (int) $db->table('conger')->where('id_status', 1)->countAllResults(),
            'approved_requests' => (int) $db->table('conger')->where('id_status', 2)->countAllResults(),
            'departments_count' => (int) $db->table('departements')->countAllResults(),
            'types_count' => (int) $db->table('TypeConger')->countAllResults(),
        ];
    }

    /**
     * Dashboard administrateur
     */
    public function dashboard()
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $db = \Config\Database::connect();
        $stats = $this->getStats();
        $departments = $db->table('departements')->countAllResults();

        // ==========================================
        // TRAITEMENT DES DONNÉES GRAPHIQUES (NEW)
        // ==========================================
        $currentYear = date('Y');
        
        // 1. Récupération des congés approuvés par mois (SQLite compatible)
        $monthQuery = $db->query("
            SELECT CAST(strftime('%m', date_debut) AS INTEGER) as mois, COUNT(*) as total 
            FROM conger 
            WHERE CAST(strftime('%Y', date_debut) AS INTEGER) = ? AND id_status = 2
            GROUP BY CAST(strftime('%m', date_debut) AS INTEGER)
        ", [$currentYear])->getResultArray();

        $congesParMois = array_fill(1, 12, 0);
        foreach ($monthQuery as $row) {
            $congesParMois[(int)$row['mois']] = (int)$row['total'];
        }

        // 2. Récupération des congés approuvés par jour de la semaine (SQLite compatible)
        $dayQuery = $db->query("
            SELECT CAST(strftime('%w', date_debut) AS INTEGER) as jour_semaine, COUNT(*) as total 
            FROM conger 
            WHERE id_status = 2
            GROUP BY CAST(strftime('%w', date_debut) AS INTEGER)
        ")->getResultArray();

        $congesParJour = array_fill(0, 7, 0);
        foreach ($dayQuery as $row) {
            // strftime('%w'): 0=Dimanche, 1=Lundi, etc.
            // On veut: 0=Lundi, 1=Mardi, ..., 6=Dimanche
            $jourSql = (int)$row['jour_semaine'];
            $jourIndex = ($jourSql + 6) % 7; // Convertir: Dimanche(0) -> 6, Lundi(1) -> 0, etc.
            $congesParJour[$jourIndex] = (int)$row['total'];
        }
        // ==========================================

        $data = [
            'user_nom' => trim((string) ($this->session->get('user_prenom') ?? '') . ' ' . (string) ($this->session->get('user_nom') ?? 'Administrateur')),
            'user_role' => 'Admin système',
            'page' => 'dashboard',
            'employees_active' => $stats['employees_active'],
            'pending_count' => $stats['pending_requests'],
            'approved_count' => $stats['approved_requests'],
            'departments_count' => $departments,
            'types_count' => $stats['types_count'],
            
            // Injection des données pour Chart.js
            'donnees_mois'  => array_values($congesParMois),
            'donnees_jours' => array_values($congesParJour),
        ];

        return view('admin/dashboard', $data);
    }

    /**
     * Gestion des employés
     */
    public function employes()
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $db = \Config\Database::connect();
        $stats = $this->getStats();
        $employes = $db->table('employes')
            ->select('employes.*, departements.nom as departement_nom')
            ->join('departements', 'departements.id = employes.departement_id', 'left')
            ->orderBy('employes.id', 'DESC')
            ->get()
            ->getResultArray();

        $rhAccounts = $db->table('rh')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        $departements = $db->table('departements')
            ->orderBy('nom', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'user_nom' => trim((string) ($this->session->get('user_prenom') ?? '') . ' ' . (string) ($this->session->get('user_nom') ?? 'Administrateur')),
            'user_role' => 'Admin système',
            'page' => 'employes',
            'pending_count' => $stats['pending_requests'],
            'employes' => $employes,
            'rh_accounts' => $rhAccounts,
            'departements' => $departements,
        ];

        return view('admin/employes', $data);
    }

    /**
     * Gestion des départements
     */
    public function departements()
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $db = \Config\Database::connect();
        $stats = $this->getStats();
        $departements = $db->table('departements')
            ->orderBy('nom', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'user_nom' => trim((string) ($this->session->get('user_prenom') ?? '') . ' ' . (string) ($this->session->get('user_nom') ?? 'Administrateur')),
            'user_role' => 'Admin système',
            'page' => 'departements',
            'pending_count' => $stats['pending_requests'],
            'departements' => $departements,
        ];

        return view('admin/departements', $data);
    }

    /**
     * Gestion des types de congé
     */
    public function typesConge()
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $db = \Config\Database::connect();
        $stats = $this->getStats();
        $typesConge = $db->table('TypeConger')
            ->orderBy('nom', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'user_nom' => trim((string) ($this->session->get('user_prenom') ?? '') . ' ' . (string) ($this->session->get('user_nom') ?? 'Administrateur')),
            'user_role' => 'Admin système',
            'page' => 'types_conge',
            'pending_count' => $stats['pending_requests'],
            'types_conge' => $typesConge,
        ];

        return view('admin/types_conge', $data);
    }

    /**
     * Afficher toutes les demandes
     */
    public function demandesAll()
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $db = \Config\Database::connect();
        $stats = $this->getStats();

        $conges = $db->table('conger')
            ->select('conger.id, conger.employe_id, conger.type_conger_id, conger.date_debut, conger.date_fin, conger.id_status, employes.nom as nom_emp, employes.prenom as prenom_emp, TypeConger.nom as type_nom, Status.nom as status_nom')
            ->join('employes', 'employes.id = conger.employe_id', 'left')
            ->join('TypeConger', 'TypeConger.id = conger.type_conger_id', 'left')
            ->join('Status', 'Status.id = conger.id_status', 'left')
            ->orderBy('conger.id', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'user_nom' => trim((string) ($this->session->get('user_prenom') ?? '') . ' ' . (string) ($this->session->get('user_nom') ?? 'Administrateur')),
            'user_role' => 'Admin système',
            'page' => 'demandes',
            'pending_count' => $stats['pending_requests'],
            'conges' => $conges,
        ];

        return view('admin/demandes', $data);
    }

    /**
     * Stocker un nouvel employé (via formulaire admin)
     */
    public function storeEmploye()
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $post = $this->request->getPost();
        $rules = [
            'nom' => 'required',
            'prenom' => 'required',
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $hashed = password_hash($post['password'], PASSWORD_DEFAULT);

        $exists = $db->table('employes')->where('email', $post['email'])->limit(1)->get()->getRowArray();
        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Cet email existe déjà dans la table employés');
        }

        $db->table('employes')->insert([
            'nom' => $post['nom'],
            'prenom' => $post['prenom'],
            'email' => $post['email'],
            'password' => $hashed,
            'role' => 'employe',
            'departement_id' => $post['departement_id'] ?? null,
            'date_embauche' => $post['date_embauche'] ?? null,
            'actif' => 1,
        ]);

        $newId = $db->insertID();

        $types = $db->table('TypeConger')->get()->getResultArray();
        foreach ($types as $type) {
            $sample = $db->table('Soldes_emp')->where('type_conger_id', $type['id'])->limit(1)->get()->getRowArray();
            if ($sample && isset($sample['jours_attribues'])) {
                $jours = (int) $sample['jours_attribues'];
            } else {
                $name = mb_strtolower($type['nom'] ?? '');
                if (str_contains($name, 'annuel')) {
                    $jours = 30;
                } elseif (str_contains($name, 'maladie')) {
                    $jours = 10;
                } elseif (str_contains($name, 'spécial') || str_contains($name, 'special')) {
                    $jours = 5;
                } else {
                    $jours = 0;
                }
            }

            $db->table('Soldes_emp')->insert([
                'employe_id' => $newId,
                'type_conger_id' => $type['id'],
                'solde' => $jours,
                'jours_attribues' => $jours,
                'jour_prises' => 0,
            ]);
        }

        return redirect()->back()->with('success', 'Employé ajouté et soldes initialisés');
    }

    /**
     * Désactiver ou réactiver un employé.
     */
    public function toggleEmploye($id)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $db = \Config\Database::connect();
        $employe = $db->table('employes')->where('id', $id)->limit(1)->get()->getRowArray();
        if (!$employe) {
            return redirect()->back()->with('error', 'Employé introuvable');
        }

        $db->table('employes')->where('id', $id)->update(['actif' => $employe['actif'] ? 0 : 1]);
        return redirect()->back()->with('success', 'Statut employé mis à jour');
    }

    /**
     * Supprimer/désactiver un employé.
     */
    public function deleteEmploye($id)
    {
        return $this->toggleEmploye($id);
    }

    /**
     * Supprimer définitivement un employé si aucune demande n'existe.
     */
    public function removeEmploye($id)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $db = \Config\Database::connect();

        $used = $db->table('conger')->where('employe_id', $id)->limit(1)->get()->getRowArray();
        if ($used) {
            return redirect()->back()->with('error', 'Impossible de supprimer: des demandes de congé existent pour cet employé');
        }

        $db->table('Soldes_emp')->where('employe_id', $id)->delete();
        $db->table('employes')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Employé supprimé');
    }

    /**
     * Supprimer un compte RH
     */
    public function deleteRh($id)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $db = \Config\Database::connect();
        $exists = $db->table('rh')->where('id', $id)->limit(1)->get()->getRowArray();
        if (!$exists) {
            return redirect()->back()->with('error', 'Compte RH introuvable');
        }

        $db->table('rh')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Compte RH supprimé');
    }

    /**
     * Stocker un nouvel RH (via formulaire admin)
     */
    public function storeRh()
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $post = $this->request->getPost();
        $rules = [
            'username' => 'required',
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $hashed = password_hash($post['password'], PASSWORD_DEFAULT);

        $db->table('rh')->insert([
            'username' => $post['username'],
            'email' => $post['email'],
            'password' => $hashed,
            'role' => 'rh',
        ]);

        return redirect()->back()->with('success', 'RH ajouté');
    }

    /**
     * Ajouter un département.
     */
    public function storeDepartement()
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $rules = [
            'nom' => 'required',
            'description' => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $nom = trim((string) $this->request->getPost('nom'));
        $exists = $db->table('departements')->where('nom', $nom)->limit(1)->get()->getRowArray();
        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Ce département existe déjà');
        }

        $db->table('departements')->insert([
            'nom' => $nom,
            'description' => (string) $this->request->getPost('description'),
            'deductible' => $this->request->getPost('deductible') ? 1 : 0,
        ]);

        return redirect()->back()->with('success', 'Département ajouté');
    }

    /**
     * Supprimer un département s'il n'est pas utilisé.
     */
    public function deleteDepartement($id)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $db = \Config\Database::connect();
        $used = $db->table('employes')->where('departement_id', $id)->limit(1)->get()->getRowArray();
        if ($used) {
            return redirect()->back()->with('error', 'Impossible de supprimer: des employés sont rattachés à ce département');
        }

        $db->table('departements')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Département supprimé');
    }

    /**
     * Ajouter un type de congé.
     */
    public function storeTypeConge()
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        if (!$this->validate(['nom' => 'required'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $nom = trim((string) $this->request->getPost('nom'));
        $exists = $db->table('TypeConger')->where('nom', $nom)->limit(1)->get()->getRowArray();
        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Ce type de congé existe déjà');
        }

        $db->table('TypeConger')->insert([
            'nom' => $nom,
            'description' => (string) $this->request->getPost('description'),
        ]);

        return redirect()->back()->with('success', 'Type de congé ajouté');
    }

    /**
     * Supprimer un type de congé s'il n'est pas utilisé.
     */
    public function deleteTypeConge($id)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $db = \Config\Database::connect();
        $used = $db->table('conger')->where('type_conger_id', $id)->limit(1)->get()->getRowArray();
        if ($used) {
            return redirect()->back()->with('error', 'Impossible de supprimer: des demandes utilisent ce type de congé');
        }

        $db->table('TypeConger')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Type de congé supprimé');
    }
}