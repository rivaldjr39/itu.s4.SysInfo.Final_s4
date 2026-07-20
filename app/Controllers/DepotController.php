<?php
namespace App\Controllers;
use App\Models\Depot;
use App\Models\Client;
class DepotController extends BaseController
{
    protected Depot $depotModel;

    public function __construct()
    {
        $this->depotModel = new Depot();
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
            $clientModel = new Client();
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

    public function index()
    {
        $client = $this->resolveCurrentClient();

        if (!$client) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        $solde = $this->depotModel->getSolde($client['client_id']);

        return view('depot/depot', [
            'client' => $client,
            'solde'  => $solde,
        ]);
    }

    public function deposer()
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
                ->with('error', 'Veuillez saisir un montant de dépôt valide.');
        }

        $montant = (float) $this->request->getPost('montant');
        $resultat = $this->depotModel->effectuerDepot($client['client_id'], $montant);

        if (!$resultat['success']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $resultat['message']);
        }

        return redirect()->to('/depot')
            ->with('success', $resultat['message'] . ' Référence : ' . $resultat['reference'] . ' — Frais : ' . $resultat['frais'] . ' Ar');
    }

    public function calculerFraisApi()
    {
        $montant = (float) $this->request->getGet('montant');

        if ($montant <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Montant invalide.',
            ])->setStatusCode(422);
        }

        $bareme = $this->depotModel->getBaremeFrais($montant);

        if (!$bareme) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Aucun barème de frais pour ce montant.',
            ])->setStatusCode(404);
        }

        $frais = $this->depotModel->calculerFrais($bareme, $montant);

        return $this->response->setJSON([
            'success'       => true,
            'frais'         => $frais,
            'montant_total' => $montant + $frais,
        ]);
    }
}