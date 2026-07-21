<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\TransactionsModel;
use App\Models\OperationModel;
use App\Models\FraisModel;
use App\Models\PrefixeModel;
use App\Models\ComissionModel;
use App\Models\PromotionFraisModel;

class ClientController extends BaseController
{
    protected $session;
    protected $clientModel;
    protected $transactionsModel;
    protected $operationModel;
    protected $fraisModel;
    protected $prefixeModel;
    protected $comissionModel;
    protected $promotionFraisModel;

    public function __construct()
    {
        $this->session = session();
        $this->clientModel = new ClientModel();
        $this->transactionsModel = new TransactionsModel();
        $this->operationModel = new OperationModel();
        $this->fraisModel = new FraisModel();
        $this->prefixeModel = new PrefixeModel();
        $this->comissionModel = new ComissionModel();
        $this->promotionFraisModel = new PromotionFraisModel();
    }

    protected function checkAuth()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'client') {
            return redirect()->to(site_url('login'))->with('error', 'Vous devez être connecté en tant que client.');
        }
        return null;
    }

    public function index()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $clientId = $this->session->get('id');
        $client = $this->clientModel->find($clientId);

        $data = [
            'clientName' => $this->session->get('name'),
            'client' => $client,
            'totalEnvoye' => $this->transactionsModel->getSommeEnvoyeeParClient($clientId),
            'totalRecu' => $this->transactionsModel->getSommeRecueParClient($clientId),
            'totalRetrait' => $this->transactionsModel->getSommeRetraitParClient($clientId),
            'lastOperation' => $this->transactionsModel->getLast3Transaction($clientId),
            'solde' => $client['solde'] ?? 0
        ];

        return view('client/dashboard', $data);
    }

    // --- DÉPÔT ---
    public function depot()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        return view('client/depot');
    }

    public function faire_depot()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $montant = (float) $this->request->getPost('montant');
        $clientId = $this->session->get('id');

        if ($montant < 100) {
            return redirect()->back()->with('error', 'Le montant minimum est de 100.');
        }

        $operationId = 1;

        // Adaptation selon ta méthode du FraisModel (calculerFrais ou calculFrais)
        $fraisData = $this->fraisModel->calculFrais($operationId, $montant);

        $frais = $fraisData['montant_frais'] ?? $fraisData['montant'] ?? 0;

        // Début de la transaction manuelle stricte
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $client = $this->clientModel->find($clientId);

            // Créditer : solde + (montant - frais)
            $nouveauSolde = $client['solde'] + ($montant - $frais);
            //$this->clientModel->update($clientId, ['solde' => $nouveauSolde]);
            $db->table('client')->where('id', $clientId)->update(['solde' => $nouveauSolde]);

            $transactionData = [
                'montant'      => $montant,
                'frais'        => $frais,
                'date'         => date('Y-m-d H:i:s'),
                'operation_id' => $operationId,
                'client_hote'  => $clientId,
                'client_cible' => null
            ];
            $db->table('transactions')->insert($transactionData);

            if ($db->transStatus() === false) {
                throw new \Exception("Échec de l'insertion SQL de la transaction.");
            }

            $db->transCommit();
            return redirect()->to(site_url('client/dashboard'))->with('success', 'Dépôt effectué avec succès.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Erreur lors du dépôt : ' . $e->getMessage());
        }
    }

    // --- TRANSFERT ---
    public function transfert()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        return view('client/transfert');
    }

    public function processTransfert()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) {
            return $authCheck;
        }

        $clientId   = $this->session->get('id');
        $clientHote = $this->clientModel->find($clientId);

        if (!$clientHote) {
            return redirect()->back()->with('error', 'Session invalide ou client introuvable.');
        }

        $montantEntre   = (float) $this->request->getPost('montant');
        $typeEnvoi      = $this->request->getPost('type_envoi');
        $inclureRetrait = $this->request->getPost('inclure_frais_retrait') === '1';

        if ($montantEntre < 100) {
            return redirect()->back()->with('error', 'Le montant minimum est de 100 Ar.');
        }

        $opHote      = $this->prefixeModel->getOperateurParNumero($clientHote['telephone']);
        $operationId = 3; // ID 3 = Transfert

        // Connexion unique DB pour gérer l'ensemble des transactions
        $db = \Config\Database::connect();

        // =========================================================================
        // CAS 1 : ENVOI MULTIPLE (Même opérateur uniquement)
        // =========================================================================
        if ($typeEnvoi === 'multiple') {
            $telephonesInput = $this->request->getPost('telephone');
            $telephones      = array_filter(array_map('trim', explode(',', (string) $telephonesInput)));

            if (count($telephones) < 2) {
                return redirect()->back()->with('error', 'Veuillez saisir au moins 2 numéros pour un envoi multiple.');
            }

            $clientsCibles = [];
            foreach ($telephones as $tel) {
                if ($tel === $clientHote['telephone']) {
                    return redirect()->back()->with('error', "Vous ne pouvez pas inclure votre propre numéro ($tel).");
                }

                $opCible = $this->prefixeModel->getOperateurParNumero($tel);
                if (!$opCible || $opCible['id'] !== $opHote['id']) {
                    return redirect()->back()->with('error', "L'envoi multiple est strictement réservé au même réseau (" . $opHote['nom'] . "). Le numéro $tel pose problème.");
                }

                $cible = $this->clientModel->where('telephone', $tel)->first();
                if (!$cible) {
                    return redirect()->back()->with('error', "Le numéro $tel n'existe pas.");
                }

                $clientsCibles[] = $cible;
            }

            // Division du montant
            $nbCibles           = count($clientsCibles);
            $montantParPersonne = $montantEntre / $nbCibles;

            // Calcul des frais par destinataire
            $fraisData        = $this->fraisModel->calculerFrais($operationId, $montantParPersonne);
            $fraisParPersonne = $fraisData ? (float) ($fraisData['montant_frais'] ?? $fraisData['montant'] ?? 0) : 0.0;

            $costUnitaire = $montantParPersonne + $fraisParPersonne;
            $totalGeneral = $costUnitaire * $nbCibles;

            if ($totalGeneral > (float) $clientHote['solde']) {
                return redirect()->back()->with('error', 'Solde insuffisant pour ce transfert multiple. Total requis : ' . number_format($totalGeneral, 2, ',', ' ') . ' Ar');
            }

            // --- Début de la transaction manuelle $db ---
            $db->transBegin();

            try {
                foreach ($clientsCibles as $cible) {
                    // 1. Insertion transaction
                    $db->table('transactions')->insert([
                        'montant'          => $montantParPersonne,
                        'frais'            => $fraisParPersonne,
                        'valeur_commision' => 0.00,
                        'date'             => date('Y-m-d H:i:s'),
                        'operation_id'     => $operationId,
                        'client_hote'      => $clientId,
                        'client_cible'     => $cible['id']
                    ]);

                    // 2. Débit Hôte
                    $db->query("UPDATE client SET solde = solde - ? WHERE id = ?", [$costUnitaire, $clientId]);
                    $epargneclient_clible = $cible['epargne'];
                    $montantParPersonne = ($montantParPersonne * $epargneclient_clible) / 100;
                    // 3. Crédit Cible
                    $db->query("UPDATE client SET solde = solde + ? WHERE id = ?", [$montantParPersonne, $cible['id']]);
                }

                if ($db->transStatus() === false) {
                    throw new \Exception("Une erreur est survenue lors du traitement du transfert multiple.");
                }

                $db->transCommit();
                return redirect()->to(base_url('client/dashboard'))->with('success', "Envoi multiple de " . number_format($montantEntre, 2, ',', ' ') . " Ar divisé vers $nbCibles personnes réussi !");
            } catch (\Exception $e) {
                $db->transRollback();
                return redirect()->back()->with('error', 'Erreur lors du transfert multiple : ' . $e->getMessage());
            }
        }

        // =========================================================================
        // CAS 2 : ENVOI UNIQUE
        // =========================================================================
        $telephone   = $this->request->getPost('telephone');
        $clientCible = $this->clientModel->where('telephone', $telephone)->first();

        if (!$clientCible) {
            return redirect()->back()->with('error', 'Numéro de téléphone introuvable.');
        }

        if ($clientCible['id'] == $clientId) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas effectuer un transfert vers vous-même.');
        }

        $opCible          = $this->prefixeModel->getOperateurParNumero($clientCible['telephone']);
        $estMemeOperateur = ($opHote && $opCible && $opHote['id'] === $opCible['id']);

        // 1. Frais de retrait inclus (Uniquement sur le MÊME réseau)
        $fraisRetraitInclus = 0.00;
        if ($inclureRetrait && $estMemeOperateur) {
            $fraisRetraitData   = $this->fraisModel->calculerFrais(2, $montantEntre);
            $fraisRetraitInclus = $fraisRetraitData ? (float) ($fraisRetraitData['montant_frais'] ?? $fraisRetraitData['montant'] ?? 0) : 0.00;
        }

        $montantAEnvoyer = $montantEntre + $fraisRetraitInclus;

        // 2. Frais de transfert standards
        $fraisTransfertData = $this->fraisModel->calculerFrais($operationId, $montantAEnvoyer);
        $frais              = $fraisTransfertData ? (float) ($fraisTransfertData['montant_frais'] ?? $fraisTransfertData['montant'] ?? 0) : 0.00;

        if ($estMemeOperateur) {
            $promotion = $this->promotionFraisModel->getPromotionValue();
            $promotion_value = $promotion ? (float) ($promotion['valeur'] ?? 0) : 0.00;
            $poucentage = $frais * $promotion_value / 100;
            $frais = $frais - $poucentage;
        }

        // 3. Commission Inter-Opérateurs
        $commissionValue = 0.00;
        if (!$estMemeOperateur) {
            $regleCom = $this->comissionModel->where('operateur_source_id', $opHote['id'])
                ->where('operateur_cible_id', $opCible['id'])
                ->first();

            $pourcentage     = $regleCom ? (float) $regleCom['taux_pourcentage'] : 5.00;
            $commissionValue = ($montantAEnvoyer * $pourcentage) / 100;
        }

        // Total prélevé sur l'expéditeur
        $totalADebiter = $montantAEnvoyer + $frais + $commissionValue;

        if ($totalADebiter > (float) $clientHote['solde']) {
            return redirect()->back()->with('error', 'Solde insuffisant. Requis avec frais et commission : ' . number_format($totalADebiter, 2, ',', ' ') . ' Ar');
        }

        // --- Début de la transaction manuelle $db ---
        $db->transBegin();

        try {
            // 1. Enregistrement de la transaction
            $transactionData = [
                'montant'          => $montantAEnvoyer,
                'frais'            => $frais,
                'valeur_commision' => $commissionValue,
                'date'             => date('Y-m-d H:i:s'),
                'operation_id'     => $operationId,
                'client_hote'      => $clientId,
                'client_cible'     => $clientCible['id']
            ];
            $db->table('transactions')->insert($transactionData);

            // 2. Débit expéditeur
            $db->query("UPDATE client SET solde = solde - ? WHERE id = ?", [$totalADebiter, $clientId]);

            // 3. Crédit destinataire
            $epargneclient_clible = $clientCible['epargne'];
            $montantAEnvoyer = ($montantAEnvoyer * $epargneclient_clible) / 100;
            $db->query("UPDATE client SET solde = solde + ? WHERE id = ?", [$montantAEnvoyer, $clientCible['id']]);

            if ($db->transStatus() === false) {
                throw new \Exception("Échec du traitement de la transaction en base de données.");
            }

            $db->transCommit();
            return redirect()->to(base_url('client/dashboard'))->with('success', 'Transfert effectué avec succès.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Erreur lors du transfert : ' . $e->getMessage());
        }
    }

    public function update_eparge()
    {
        $epargne = (float) $this->request->getPost('epargne');
        $clientId = (int)$this->session->get('id');
        $db = \Config\Database::connect();
        $db->query("UPDATE client SET epargne = ? WHERE id = ?", [$epargne, $clientId]);
        return redirect()->to(base_url('client/dashboard'))->with('success', 'epargne modifier');
    }

    public function showepargne()
    {
        return view('client/epargne');
    }
    /* public function processTransfert()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        // Récupère soit 'client_cible_num' soit 'telephone' selon ton formulaire
        $telephone = $this->request->getPost('client_cible_num') ?? $this->request->getPost('telephone');
        $montant = (float) $this->request->getPost('montant');
        $clientId = $this->session->get('id');

        if ($montant < 100) {
            return redirect()->back()->with('error', 'Le montant minimum est de 100.');
        }

        // Si findByTelephone existe sur ton modèle, on l'utilise, sinon requete classique
        $clientCible = method_exists($this->clientModel, 'findByTelephone')
            ? $this->clientModel->findByTelephone($telephone)
            : $this->clientModel->where('telephone', $telephone)->first();

        if (!$clientCible) {
            return redirect()->back()->with('error', 'Numéro de téléphone introuvable.');
        }

        if ($clientCible['id'] == $clientId) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas effectuer un transfert à vous-même.');
        }

        $clientHote = $this->clientModel->find($clientId);

        $operationId = 3; // ID Transfert
        $fraisData = method_exists($this->fraisModel, 'calculerFrais')
            ? $this->fraisModel->calculerFrais($operationId, $montant)
            : $this->fraisModel->calculFrais($operationId, $montant);

        $frais = $fraisData['montant_frais'] ?? $fraisData['montant'] ?? 0;

        // Validation du solde
        if ($clientHote['solde'] < $montant) {
            return redirect()->back()->with('error', 'Solde insuffisant.');
        }

        $commissionValue = 0.00;
        if (!$estMemeOperateur) {
            $regleCom = $this->comissionModel->where('operateur_source_id', $opHote['id'])
                ->where('operateur_cible_id', $opCible['id'])
                ->first();

            $pourcentage     = $regleCom ? (float) $regleCom['taux_pourcentage'] : 5.00;
            $commissionValue = ($montantAEnvoyer * $pourcentage) / 100;
        }

        // Total prélevé sur l'expéditeur
        $totalADebiter = $montantAEnvoyer + $frais + $commissionValue;

        if ($totalADebiter > (float) $clientHote['solde']) {
            return redirect()->back()->with('error', 'Solde insuffisant. Requis avec frais et commission : ' . number_format($totalADebiter, 2, ',', ' ') . ' Ar');
        }


        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // Débiter l'expéditeur du montant total
            $nouveauSoldeHote = $clientHote['solde'] - $montant;
            $this->clientModel->update($clientId, ['solde' => $nouveauSoldeHote]);

            // Créditer le destinataire (montant - frais)
            $nouveauSoldeCible = $clientCible['solde'] + ($montant - $frais);
            $this->clientModel->update($clientCible['id'], ['solde' => $nouveauSoldeCible]);

            $transactionData = [
                'montant'      => $montant,
                'frais'        => $frais,
                'date'         => date('Y-m-d H:i:s'),
                'operation_id' => $operationId,
                'client_hote'  => $clientId,
                'client_cible' => $clientCible['id']
            ];
            $this->transactionsModel->insert($transactionData);

            if ($db->transStatus() === false) {
                throw new \Exception("Échec de la validation SQL du transfert.");
            }

            $db->transCommit();
            return redirect()->to(site_url('client/dashboard'))->with('success', 'Transfert effectué avec succès.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Erreur lors du transfert : ' . $e->getMessage());
        }
    } */

    // --- RETRAIT ---
    public function retrait()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        return view('client/retrait');
    }

    public function processRetrait()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $montant  = (float) $this->request->getPost('montant');
        $clientId = $this->session->get('id');

        if ($montant < 100) {
            return redirect()->back()->with('error', 'Le montant minimum est de 100 Ar.');
        }

        $client = $this->clientModel->find($clientId);
        if (!$client) {
            return redirect()->back()->with('error', 'Client introuvable.');
        }

        $operationId = 2; // ID 2 = Retrait

        $fraisData = method_exists($this->fraisModel, 'calculerFrais')
            ? $this->fraisModel->calculerFrais($operationId, $montant)
            : $this->fraisModel->calculFrais($operationId, $montant);

        $frais = (float) ($fraisData['montant_frais'] ?? $fraisData['montant'] ?? 0);
        $totalADebiter = $montant + $frais;

        // Vérification du solde (Montant + Frais)
        if ((float)$client['solde'] < $totalADebiter) {
            return redirect()->back()->with('error', 'Votre solde est insuffisant pour couvrir le retrait et ses frais.');
        }

        // Connexion unique DB pour la transaction
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // 1. Débit du solde du client (Montant + Frais)
            $db->query("UPDATE client SET solde = solde - ? WHERE id = ?", [$totalADebiter, $clientId]);

            // 2. Insertion de l'historique de transaction
            $transactionData = [
                'montant'          => $montant,
                'frais'            => $frais,
                'valeur_commision' => 0.00,
                'date'             => date('Y-m-d H:i:s'),
                'operation_id'     => $operationId,
                'client_hote'      => $clientId,
                'client_cible'     => null
            ];
            $db->table('transactions')->insert($transactionData);

            if ($db->transStatus() === false) {
                throw new \Exception("Échec SQL lors du traitement du retrait.");
            }

            $db->transCommit();
            return redirect()->to(site_url('client/dashboard'))->with('success', 'Retrait effectué avec succès.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Erreur lors du retrait : ' . $e->getMessage());
        }
    }

    public function historique()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $clientId = $this->session->get('id');

        $transactions = $this->transactionsModel->getHistoricClient($clientId);

        return view('client/historique', ['transactions' => $transactions]);
    }
}
