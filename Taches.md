# Projet final SI (simulation operateur)

## Taches pour la version 1
| Tanteraka                                                                         | Steeven
|:----------------------------------------------------------------------------------|:---------------------------------------------------------------------------------
| initialisation                                                                    | script-base
| gestion clients cotee admin : (backoffice)                                        | login admin : (backoffice)
| - liste des clients (paginée),                                                    | - validation et hashage de mdp + creation session + filter
| - voir ses transactions                                                           | - seeder client, admin, ...
| - seeder transaction, frais, operation                                            | - Situation gain via les différents frais (dashboard : chart js)
| cotee client : (frontoffice)                                                      | cotee client : (frontoffice)
| - faire retrait (transaction + solde + calcul frais + verification solde)         | - login + filter + validation
| - faire transfert (meme que retrait)                                              | - faire depot
|                                                                                   | - voir historique
| design (backoffice)                                                               | design (frontoffice)


## Taches pour la version 2
| Tanteraka                                                                         | Steeven
|:----------------------------------------------------------------------------------|:---------------------------------------------------------------------------------
| transfert avance (frontoffice) :                                                  | situation gains avancee (backoffice) :
| - transfert multiple (envoi a plusieurs destinataires)                            | - calcul des commissions inter-operateurs
| - gestion de l'option inclure frais de retrait                                    | - suivi du gain global et des frais d'inclusion
| - verification de solde globale (montants + frais)                                | - repartition des gains par type d'operation
|                                                                                   | - repartition des gains par operateur
| validation & securite (frontoffice) :                                             | dashboard admin & visualisation (backoffice) :
| - validation dynamiques des numeros selon l'operateur (032, 033, 034, 037, 038)   | - integration des graphiques Chart.js (Doughnut / Bar)
| - contraintes de formatage et decoupage des destinataires (mode unique/multiple)  | - calcul des montants a reconcilier / envoyer par operateur
|                                                                                   | - route dediee admin/situation-gains et vues associees
| optimisation design & UX (frontoffice)                                            | optimisation design & UX (backoffice)
