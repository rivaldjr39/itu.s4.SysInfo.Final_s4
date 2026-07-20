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
        
        $numeroClient = session()->get('numero_telephone');
        if (!$numeroClient) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }
        return view('client/transfert_form', [
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

    /*public function historique()
    {
        $numeroClient = session()->get('numero_telephone');

        if (!$numeroClient) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        // Récupérer le client + son compte pour avoir client_id
        $clientModel = new \App\Models\Client();
        $client = $clientModel->where('numero_telephone', $numeroClient)->first();

        if (!$client) {
            return redirect()->to('/login')->with('error', 'Client introuvable.');
        }

        $historique = $this->transfertModel->getHistoriqueTransferts($client['id']);

        return view('client/transfert_historique', [
            'historique' => $historique,
        ]);
    }
    */

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