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

    // ------------------------------------------------------------
    // Affiche le formulaire de transfert
    // ------------------------------------------------------------
    public function index()
    {
        // Le numéro du client connecté est censé être en session
        // (posé au moment du "login" automatique par numéro de téléphone)
        $numeroClient = session()->get('numero_telephone');

        if (!$numeroClient) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        return view('Transferts/formulaire', [
            'numero_client' => $numeroClient,
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
        $numeroClient = session()->get('numero_telephone');
        $clientId = session()->get('client_id');

        if (!$numeroClient) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        if (!$clientId) {
            // Récupérer le client + son compte pour avoir client_id
            $clientModel = new \App\Models\Client();
            $client = $clientModel->findByNumeroTelephone($numeroClient);

            if (!$client) {
                return redirect()->to('/login')->with('error', 'Client introuvable.');
            }

            $clientId = (int) $client['id'];
        }

        if (!is_int($clientId) && !ctype_digit((string) $clientId)) {
            return redirect()->to('/login')->with('error', 'Session client invalide.');
        }

        $clientId = (int) $clientId;

        $historique = $this->transfertModel->getHistoriqueTransferts($clientId);

        return view('Transferts/transfert', [
            'historique'     => $historique,
            'client_id'      => $clientId,
            'numero_client'  => $numeroClient,
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