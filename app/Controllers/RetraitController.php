<?php

namespace App\Controllers;

use App\Models\Retrait;

class RetraitController extends BaseController
{
    protected Retrait $retraitModel;

    public function __construct()
    {
        $this->retraitModel = new Retrait();
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

        return [
            'client_id'      => (int) $clientId,
            'numero_client'  => $numeroClient,
            'client_nom'     => $clientNom,
        ];
    }

    // Affiche le formulaire de retrait
    public function index()
    {
        $client = $this->resolveCurrentClient();

        if (!$client) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        return view('Retraits/formulaire', [
            'numero_client' => $client['numero_client'],
            'client_nom'    => $client['client_nom'],
        ]);
    }

    // Traite la soumission du formulaire de retrait
    public function retirer()
    {
        $client = $this->resolveCurrentClient();

        if (!$client) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        $rules = [
            'montant' => 'required|numeric|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $montant = (float) $this->request->getPost('montant');

        $resultat = $this->retraitModel->effectuerRetrait(
            $client['numero_client'],
            $montant
        );

        if (!$resultat['success']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $resultat['message']);
        }

        return redirect()->to('/retrait')
            ->with('success', $resultat['message'] . ' Référence : ' . $resultat['reference'] . ' — Frais : ' . $resultat['frais'] . ' Ar');
    }

    // Affiche l'historique des retraits
    public function historique()
    {
        $client = $this->resolveCurrentClient();

        if (!$client) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        $historique = $this->retraitModel->getHistoriqueRetraits($client['client_id']);

        return view('Retraits/retrait', [
            'historique'     => $historique,
            'client_id'      => $client['client_id'],
            'numero_client'  => $client['numero_client'],
            'client_nom'     => $client['client_nom'],
        ]);
    }

    // Calcule les frais pour un montant de retrait donné (API)
    public function calculerFraisApi()
    {
        $montant = (float) $this->request->getGet('montant');

        if ($montant <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Montant invalide.',
            ])->setStatusCode(422);
        }

        $bareme = $this->retraitModel->getBaremeFrais($montant);

        if (!$bareme) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Aucun barème de frais de retrait pour ce montant.',
            ])->setStatusCode(404);
        }

        $frais = $this->retraitModel->calculerFrais($bareme, $montant);

        return $this->response->setJSON([
            'success'       => true,
            'frais'         => $frais,
            'montant_total' => $montant + $frais,
        ]);
    }
}
