<?php

namespace App\Controllers;

use App\Models\Transfert;

class TransfertController extends BaseController
{
    protected Transfert $transfertModel;

    public function __construct()
    {
        $this->transfertModel = new Transfert();
    }

    private function resolveCurrentClient(): ?array
    {
        $numeroClient = session()->get('numero_telephone');

        if (!$numeroClient) {
            return null;
        }

        $clientId = session()->get('client_id');
        $clientNom = session()->get('client_nom') ?? '';

        if (!$clientId) {
            $clientModel = new \App\Models\Client();
            $client = $clientModel->findByNumeroTelephone($numeroClient);

            if (!$client) {
                return null;
            }

            $clientId = (int) $client['id'];
            $clientNom = $client['nom'] ?? $clientNom;
        }

        if (!is_int($clientId) && !ctype_digit((string) $clientId)) {
            return null;
        }

        return [
            'client_id'      => (int) $clientId,
            'numero_client'  => $numeroClient,
            'client_nom'     => $clientNom,
        ];
    }

    private function normaliserNumeroTelephone(?string $numero): string
    {
        return preg_replace('/\D+/', '', (string) $numero) ?? '';
    }

    private function numeroTelephoneValide(string $numero): bool
    {
        return strlen($numero) >= 10 && strlen($numero) <= 15;
    }

    // ------------------------------------------------------------
    // Affiche le formulaire de transfert
    // ------------------------------------------------------------
    public function index()
    {
        $client = $this->resolveCurrentClient();

        if (!$client) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        return view('Transferts/formulaire', [
            'numero_client' => $client['numero_client'],
            'client_nom'    => $client['client_nom'],
        ]);
    }

    // ------------------------------------------------------------
    // Tableau de bord du client connecté
    // ------------------------------------------------------------
    public function dashboard()
    {
        $client = $this->resolveCurrentClient();

        if (!$client) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        $operations = $this->transfertModel->getHistoriqueGlobal($client['client_id'], 10);
        $recentOperations = array_slice($operations, 0, 3);
        $solde = $this->transfertModel->getSolde($client['client_id']);

        return view('dashboard/index', [
            'numero_client'     => $client['numero_client'],
            'client_id'         => $client['client_id'],
            'client_nom'        => $client['client_nom'],
            'solde'             => $solde,
            'dashboard'         => [
                'total_operations' => count($operations),
                'recent_operations' => $recentOperations,
            ],
        ]);
    }

    // ------------------------------------------------------------
    // Traite la soumission du formulaire de transfert
    // ------------------------------------------------------------
    public function transferer()
    {
        $numeroSource = $this->normaliserNumeroTelephone((string) session()->get('numero_telephone'));

        if (!$numeroSource) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        if (!$this->validate([
            'montant' => 'required|numeric|greater_than[0]',
        ])) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $numeroDestination = $this->normaliserNumeroTelephone($this->request->getPost('numero_destination'));

        if (!$this->numeroTelephoneValide($numeroDestination)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Veuillez saisir un numéro destinataire valide.');
        }

        $montant            = (float) $this->request->getPost('montant');
        $inclureFraisRetrait = $this->request->getPost('inclure_frais_retrait') === '1';

        $resultat = $this->transfertModel->effectuerTransfert(
            $numeroSource,
            $numeroDestination,
            $montant,
            $inclureFraisRetrait
        );

        if (!$resultat['success']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $resultat['message']);
        }

        $message = $resultat['message'] . ' Référence : ' . $resultat['reference'];
        $message .= ' — Frais : ' . $resultat['frais'] . ' Ar';

        if (isset($resultat['commission_supplementaire']) && (float) $resultat['commission_supplementaire'] > 0) {
            $message .= ' dont commission opérateur destinataire : ' . $resultat['commission_supplementaire'] . ' Ar';
        }

        if (isset($resultat['frais_retrait']) && (float) $resultat['frais_retrait'] > 0) {
            $message .= ' — Frais de retrait : ' . $resultat['frais_retrait'] . ' Ar';
        }

        return redirect()->to('/transfert')
            ->with('success', $message);
    }

    // ------------------------------------------------------------
    // Historique des transferts du client connecté
    // ------------------------------------------------------------
    public function historique()
    {
        $client = $this->resolveCurrentClient();

        if (!$client) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        $historique = $this->transfertModel->getHistoriqueTransferts($client['client_id']);

        return view('Transferts/transfert', [
            'historique'     => $historique,
            'client_id'      => $client['client_id'],
            'numero_client'  => $client['numero_client'],
            'client_nom'     => $client['client_nom'],
        ]);
    }

    // ------------------------------------------------------------
    // Calcule les frais pour un montant donné, sans effectuer le transfert
    // Appelé en JS pendant la saisie (GET /transfert/calculer-frais?montant=...)
    // ------------------------------------------------------------
    public function calculerFraisApi()
    {
        $montant = (float) $this->request->getGet('montant');
        $numeroDestination = $this->normaliserNumeroTelephone((string) $this->request->getGet('numero_destination'));
        $numeroSource = $this->normaliserNumeroTelephone((string) session()->get('numero_telephone'));
        $inclureFraisRetrait = $this->request->getGet('inclure_frais_retrait') === '1';

        if ($montant <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Montant invalide.',
            ])->setStatusCode(422);
        }

        // Récupérer l'opérateur source pour vérifier si c'est notre opérateur
        $compteSource = null;
        $operateurSourceId = null;
        
        if ($numeroSource) {
            $compteSource = $this->transfertModel->getCompteParNumero($numeroSource);
            if ($compteSource) {
                $operateurSourceId = (int) ($compteSource['operateur_id'] ?? 0);
            }
        }

        // Si pas de compte source connecté, retourner une erreur
        if (!$compteSource) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Veuillez vous connecter pour calculer les frais.',
            ])->setStatusCode(401);
        }

        // Récupérer le barème de frais (seulement pour notre opérateur)
        $bareme = $this->transfertModel->getBaremeFrais($montant, $operateurSourceId);

        // Initialiser les frais par défaut
        $fraisDetails = [
            'inter_operateur' => false,
            'frais_base' => 0.0,
            'commission_supplementaire' => 0.0,
            'frais_total' => 0.0,
            'frais_retrait' => 0.0,
        ];

        if ($numeroDestination !== '') {
            if (!$this->numeroTelephoneValide($numeroDestination)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Numéro destinataire invalide.',
                ])->setStatusCode(422);
            }

            $compteDestination = $this->transfertModel->getCompteParNumero($numeroDestination);

            if (!$compteSource || !$compteDestination) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Numéro source ou destinataire introuvable.',
                ])->setStatusCode(404);
            }

            $operateurDestinationId = (int) ($compteDestination['operateur_id'] ?? 0);
            $estInterOperateur = $operateurSourceId !== $operateurDestinationId;

            // Si c'est notre opérateur, calculer les frais normaux
            if ($bareme) {
                $fraisDetails = $this->transfertModel->calculerFraisTransfert(
                    $bareme,
                    $montant,
                    $operateurSourceId,
                    $operateurDestinationId
                );
            } else {
                // Si ce n'est pas notre opérateur, seulement la commission inter-opérateur
                $fraisDetails['inter_operateur'] = $estInterOperateur;
                if ($estInterOperateur) {
                    $commission = round(
                        $montant * $this->transfertModel->getCommissionInterOperateur($operateurDestinationId) / 100,
                        2
                    );
                    $fraisDetails['commission_supplementaire'] = $commission;
                    $fraisDetails['frais_total'] = $commission;
                }
            }

            // Calculer les frais de retrait si l'option est activée et si c'est notre opérateur
            if ($inclureFraisRetrait && $bareme) {
                $fraisRetrait = $this->transfertModel->calculerFraisRetrait($montant, $operateurSourceId);
                $fraisDetails['frais_retrait'] = $fraisRetrait;
                $fraisDetails['frais_total'] += $fraisRetrait;
            }
        } elseif ($bareme) {
            // Pas de destination, juste calculer les frais de base
            $fraisDetails['frais_base'] = $this->transfertModel->calculerFrais($bareme, $montant);
            $fraisDetails['frais_total'] = $fraisDetails['frais_base'];
        } else {
            // Pas de barème (autre opérateur) et pas de destination
            // Retourner les frais à 0 mais avec succès
            $fraisDetails = [
                'inter_operateur' => false,
                'frais_base' => 0.0,
                'commission_supplementaire' => 0.0,
                'frais_total' => 0.0,
                'frais_retrait' => 0.0,
            ];
        }

        $promotionReduction = $fraisDetails['promotion_reduction'] ?? 0;
        $promotionMessage = $fraisDetails['promotion_message'] ?? null;

        return $this->response->setJSON([
            'success'                   => true,
            'inter_operateur'           => (bool) $fraisDetails['inter_operateur'],
            'frais_base'                => $fraisDetails['frais_base'],
            'commission_supplementaire' => $fraisDetails['commission_supplementaire'],
            'frais_retrait'             => $fraisDetails['frais_retrait'],
            'frais'                     => $fraisDetails['frais_total'],
            'montant_total'             => $montant + $fraisDetails['frais_total'],
            'inclure_frais_retrait'     => $inclureFraisRetrait,
            'promotion_reduction'       => $promotionReduction,
            'promotion_message'         => $promotionMessage,
        ]);
    }

    // ------------------------------------------------------------
    // Affiche le formulaire d'envoi multiple
    // ------------------------------------------------------------
    public function multiple()
    {
        $client = $this->resolveCurrentClient();

        if (!$client) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        return view('Transferts/multiple', [
            'numero_client' => $client['numero_client'],
            'client_nom'    => $client['client_nom'],
        ]);
    }

    // ------------------------------------------------------------
    // Traite l'envoi multiple vers plusieurs numéros
    // ------------------------------------------------------------
    public function transfererMultiple()
    {
        $numeroSource = session()->get('numero_telephone');

        if (!$numeroSource) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        $numeros = $this->request->getPost('numeros');
        $montantTotal = (float) $this->request->getPost('montant_total');

        if (!$numeros || !is_array($numeros) || count($numeros) < 2) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Veuillez spécifier au moins deux destinataires.');
        }

        // Nettoyer les numéros vides
        $numeros = array_values(array_filter(array_map('trim', $numeros)));

        if (count($numeros) < 2) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Veuillez spécifier au moins deux destinataires.');
        }

        if ($montantTotal <= 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Montant total invalide.');
        }

        $resultat = $this->transfertModel->effectuerTransfertsMultiple(
            $numeroSource,
            $numeros,
            $montantTotal
        );

        if (!$resultat['success']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $resultat['message']);
        }

        // Construire le message détaillé
        $message = $resultat['message'] . ' — ' . number_format($resultat['montant_par_personne'], 0, ',', ' ') . ' Ar chacun.';
        $message .= ' Frais total : ' . number_format($resultat['frais_total'], 0, ',', ' ') . ' Ar.';

        return redirect()->to('/transfert/multiple')
            ->with('success', $message);
    }

    // ------------------------------------------------------------
    // Situation des gains via les frais (retrait + transfert)
    // Accessible uniquement aux administrateurs
    // ------------------------------------------------------------
    public function gainsFrais()
    {
        $role = session()->get('client_role');
        if (!$role || $role !== 'ADMIN') {
            return redirect()->to('/dashboard')->with('error', 'Accès réservé aux administrateurs.');
        }

        $dateDebut = $this->request->getGet('date_debut');
        $dateFin   = $this->request->getGet('date_fin');

        $stats = $this->transfertModel->getStatsFrais(
            !empty($dateDebut) ? $dateDebut : null,
            !empty($dateFin)   ? $dateFin   : null
        );

        return view('admin/gains_frais', [
            'stats'          => $stats,
            'title'          => 'Situation des gains — Frais perçus',
            'date_debut'     => $dateDebut,
            'date_fin'       => $dateFin,
        ]);
    }

    // ------------------------------------------------------------
    // Version API (JSON) — utile si le front est en AJAX/SPA
    // ------------------------------------------------------------
    public function transfererApi()
    {
        $numeroSource = $this->normaliserNumeroTelephone($this->request->getPost('numero_source'));
        $numeroDestination = $this->normaliserNumeroTelephone($this->request->getPost('numero_destination'));
        $montant = (float) $this->request->getPost('montant');
        $inclureFraisRetrait = $this->request->getPost('inclure_frais_retrait') === '1';

        if (!$this->validate([
            'montant' => 'required|numeric|greater_than[0]',
        ])) {
            return $this->response->setJSON([
                'success' => false,
                'errors'  => $this->validator->getErrors(),
            ])->setStatusCode(422);
        }

        if (!$this->numeroTelephoneValide($numeroSource) || !$this->numeroTelephoneValide($numeroDestination)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Numéro source ou destinataire invalide.',
            ])->setStatusCode(422);
        }

        $resultat = $this->transfertModel->effectuerTransfert(
            $numeroSource,
            $numeroDestination,
            $montant,
            $inclureFraisRetrait
        );

        $statusCode = $resultat['success'] ? 200 : 400;

        return $this->response->setJSON($resultat)->setStatusCode($statusCode);
    }
}