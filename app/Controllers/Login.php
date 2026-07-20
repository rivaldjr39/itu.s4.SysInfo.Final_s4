<?php

namespace App\Controllers;

use App\Models\Client;

class Login extends BaseController
{
    public function index()
    {
        if (session()->get('numero_telephone')) {
            return redirect()->to('/transfert');
        }

        return view('auth/login');
    }

    public function authenticate()
    {
        $rules = [
            'numero_telephone' => 'required|regex_match[/^[0-9]{10,15}$/]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Veuillez saisir un numéro de téléphone valide.');
        }

        $numeroTelephone = $this->request->getPost('numero_telephone');
        $clientModel = new Client();
        $client = $clientModel->findByNumeroTelephone($numeroTelephone);

        if ($client === null) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Aucun client ne correspond à ce numéro.');
        }

        session()->regenerate(true);
        session()->set([
            'client_id'       => $client['id'],
            'numero_telephone' => $client['numero_telephone'],
            'client_nom'      => $client['nom'] ?? '',
            'client_role'     => $client['role'] ?? 'CLIENT',
        ]);

        return redirect()->to('/transfert')->with('success', 'Connexion réussie.');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('/login')->with('success', 'Vous êtes déconnecté.');
    }
}
