# 📁 Dossier Projet — TechMada RH Interne
**Système de gestion des congés — CI4 (CodeIgniter 4)**

---

## 📌 Présentation générale

TechMada est une entreprise disposant d'un **système RH interne** permettant à ses employés de soumettre des demandes de congé en ligne. Le responsable RH valide ou refuse ces demandes. Le solde de congés se met à jour automatiquement. Un administrateur supervise l'ensemble via un tableau de bord.

**Durée cible :** 4h en binôme

**Stack technique :** CodeIgniter 4 (CI4), PHP, MySQL

---

## 👥 Rôles & Fonctionnalités

### 🧑 Employé
> Rôle par défaut à l'inscription — `role: employe`

| Priorité | Fonctionnalité |
|----------|---------------|
| ✅ Obligatoire | Connexion / Déconnexion |
| ✅ Obligatoire | Soumettre une demande de congé (type, dates, motif) |
| ✅ Obligatoire | Consulter ses propres demandes et leurs statuts |
| ✅ Obligatoire | Voir son solde de congés restant par type |
| ⭕ Bonus | Annuler une demande encore en attente |
| ⭕ Bonus | Modifier son profil (nom, mot de passe) |

---

### 👩‍💼 Responsable RH
> Valide les demandes de son équipe — `role: rh`

| Priorité | Fonctionnalité |
|----------|---------------|
| ✅ Obligatoire | Voir toutes les demandes en attente |
| ✅ Obligatoire | Approuver ou refuser une demande (avec commentaire optionnel) |
| ✅ Obligatoire | Mise à jour automatique du solde à l'approbation |
| ⭕ Bonus | Filtrer les demandes par département ou statut |
| ⭕ Bonus | Voir le solde de chaque employé |

---

### 🔧 Administrateur
> Gestion complète du système — `role: admin`

| Priorité | Fonctionnalité |
|----------|---------------|
| ✅ Obligatoire | CRUD employés (créer, éditer, désactiver) |
| ✅ Obligatoire | CRUD départements et types de congé |
| ✅ Obligatoire | Tableau de bord : absences du mois en cours |
| ⭕ Bonus | Initialiser / ajuster le solde annuel d'un employé |
| ⭕ Bonus | Voir l'historique complet de toutes les demandes |

---

## 🔄 Workflow d'une demande de congé

```
Employé soumet  →  [ en_attente ]  →  ✅ approuvée  →  solde déduit
                                    →  ❌ refusée   →  solde intact
```

> ⚠️ **Logique métier clé** : Le solde est déduit **uniquement** à l'approbation, pas à la soumission.
> Si la demande est annulée ou refusée **après** approbation, le solde est **recrédité**.

---

## 🗄️ Schéma de base de données (5 tables)

### `employes`
| Champ | Type | Contrainte |
|-------|------|-----------|
| id | PK | Auto-incrément |
| nom | VARCHAR | |
| prenom | VARCHAR | |
| email | VARCHAR | UNIQUE |
| password | VARCHAR | hashé |
| role | ENUM | employe / rh / admin |
| departement_id | FK | → departements |
| date_embauche | DATE | |
| actif | TINYINT | 0 ou 1 |

### `departements`
| Champ | Type |
|-------|------|
| id | PK |
| nom | VARCHAR |
| description | TEXT |

### `types_conge`
| Champ | Type |
|-------|------|
| id | PK |
| libelle | VARCHAR |
| jours_annuels | INT |
| deductible | TINYINT (0/1) |

### `soldes`
| Champ | Type | Note |
|-------|------|------|
| id | PK | |
| employe_id | FK | → employes |
| type_conge_id | FK | → types_conge |
| annee | INT | |
| jours_attribues | INT | |
| jours_pris | INT | |
| **restant** | *calculé* | `= attribués − pris` (jamais stocké) |

### `conges`
| Champ | Type |
|-------|------|
| id | PK |
| employe_id | FK → employes |
| type_conge_id | FK → types_conge |
| date_debut | DATE |
| date_fin | DATE |
| nb_jours | INT |
| motif | TEXT |
| statut | ENUM : en_attente / approuvee / refusee / annulee |
| commentaire_rh | TEXT |
| created_at | DATETIME |
| traite_par | FK → employes |

---

## 📐 Logique métier — Calcul du solde

> La table `soldes` stocke les jours attribués et les jours pris.
> Le restant est **toujours calculé**, jamais stocké.

```sql
nb_jours_restant = jours_attribues - jours_pris

-- À l'approbation :
UPDATE soldes SET jours_pris = jours_pris + $nb_jours
  WHERE employe_id = ? AND type_conge_id = ? AND annee = ?

-- Si refus après approbation (annulation) :
UPDATE soldes SET jours_pris = jours_pris - $nb_jours
```

> ⚠️ Toujours vérifier que `jours_pris + nb_jours_demandés ≤ jours_attribués` avant d'approuver.
> Retourner une erreur si le solde est insuffisant.

---

## ⚙️ Directives techniques CI4

### 🔐 Authentification & Rôles
- Session CI4 native, `password_hash()` obligatoire
- Filtre `AuthFilter` sur toutes les routes protégées
- 3 groupes de routes : `/employe`, `/rh`, `/admin`
- Vérification du rôle dans chaque controller (pas dans le filtre)
- CSRF activé sur tous les formulaires POST

### 🗂️ Modèles & Données
- 1 Model CI4 par table avec règles de validation
- Migrations dans l'ordre : `departements` → `types_conge` → `employes` → `soldes` → `conges`
- Calcul `nb_jours` côté PHP : `date_diff()` ou Carbon
- Query Builder uniquement, pas de SQL brut
- Seeder : 1 admin, 2 employés, 3 types de congé, soldes initialisés

### 🧭 Routing & Structure
- Pattern PRG : `POST` → redirect après toute écriture
- Flashdata CI4 pour tous les messages succès/erreur
- Layout partagé `layout/app.php` + sidebar selon rôle
- Vues séparées : `employe/`, `rh/`, `admin/`
- Aucun JavaScript métier — tout côté serveur

### 📅 Calcul des jours
- Calculer les jours ouvrables uniquement (exclure week-ends)
- Bloquer si `date_debut ≥ date_fin`
- Bloquer si solde insuffisant (message flash explicite)
- Bloquer les chevauchements : pas 2 demandes actives aux mêmes dates
- *Simplification TD* : compter tous les jours calendaires si jours ouvrables trop complexe

---

## ⏱️ Découpage du temps — 4h

| Durée | Étape |
|-------|-------|
| 20 min | **Setup & BDD** — migrations + seeder + routes squelette |
| 40 min | **Authentification** — login, session, filtre 3 rôles |
| 60 min | **Espace employé** — soumettre, lister, solde, annuler |
| 50 min | **Espace RH** — approuver/refuser + MAJ solde |
| 30 min | **Back-office admin** — CRUD employés + dashboard |
| 20 min | **Finition** — template, flashdata, README |

---

## ✅ Livrables attendus des binômes

- [ ] Code source complet (structure CI4 standard)
- [ ] Migrations + Seeder fonctionnels — `php spark migrate && php spark db:seed` suffit
- [ ] Les 4 fonctionnalités obligatoires **employé** opérationnelles
- [ ] Les 3 fonctionnalités obligatoires **RH** opérationnelles (dont MAJ solde)
- [ ] Les 3 fonctionnalités obligatoires **admin** opérationnelles
- [ ] `README.md` : instructions + compte admin + compte employé de test

---

## 📝 Notes

- **Ligne en gras** = fonctionnalité obligatoire
- **Ligne normale** = bonus si le temps le permet
- Le calcul du solde restant (jamais stocké) est le **cœur du projet**