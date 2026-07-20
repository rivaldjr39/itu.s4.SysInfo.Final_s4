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
        $numeroSource = session()->get('numero_telephone');

        if (!$numeroSource) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        $rules = [
            'numero_destination' => 'required|min_length[10]|max_length[15]',
            'montant'            => 'required|numeric|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $numeroDestination = $this->request->getPost('numero_destination');
        $montant            = (float) $this->request->getPost('montant');

        $resultat = $this->transfertModel->effectuerTransfert(
            $numeroSource,
            $numeroDestination,
            $montant
        );

        if (!$resultat['success']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $resultat['message']);
        }

        return redirect()->to('/transfert')
            ->with('success', $resultat['message'] . ' Référence : ' . $resultat['reference'] . ' — Frais : ' . $resultat['frais'] . ' Ar');
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

        if ($montant <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Montant invalide.',
            ])->setStatusCode(422);
        }

        $bareme = $this->transfertModel->getBaremeFrais($montant);

        if (!$bareme) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Aucun barème de frais pour ce montant.',
            ])->setStatusCode(404);
        }

        $frais = $this->transfertModel->calculerFrais($bareme, $montant);

        return $this->response->setJSON([
            'success'       => true,
            'frais'         => $frais,
            'montant_total' => $montant + $frais,
        ]);
    }

    // ------------------------------------------------------------
    // Version API (JSON) — utile si le front est en AJAX/SPA
    // ------------------------------------------------------------
    public function transfererApi()
    {
        $numeroSource = $this->request->getPost('numero_source');
        $numeroDestination = $this->request->getPost('numero_destination');
        $montant = (float) $this->request->getPost('montant');

        $rules = [
            'numero_source'      => 'required|min_length[10]|max_length[15]',
            'numero_destination' => 'required|min_length[10]|max_length[15]',
            'montant'            => 'required|numeric|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors'  => $this->validator->getErrors(),
            ])->setStatusCode(422);
        }

        $resultat = $this->transfertModel->effectuerTransfert(
            $numeroSource,
            $numeroDestination,
            $montant
        );

        $statusCode = $resultat['success'] ? 200 : 400;

        return $this->response->setJSON($resultat)->setStatusCode($statusCode);
    }
}