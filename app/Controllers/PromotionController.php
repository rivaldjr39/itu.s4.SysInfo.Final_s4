<?php

namespace App\Controllers;

class PromotionController extends BaseController
{
    private function checkAdmin()
    {
        $role = session()->get('client_role');
        return ($role && $role === 'ADMIN');
    }

    public function index()
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login')->with('error', 'Accès réservé aux administrateurs.');
        }

        $db = db_connect();

        $promotions = $db->table('promotions_transferts')
            ->select('*')
            ->orderBy('date_debut', 'DESC')
            ->get()
            ->getResultArray();

        return view('admin/promotions/index', [
            'promotions' => $promotions,
            'title'      => 'Gestion des promotions',
        ]);
    }

    public function store()
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login')->with('error', 'Accès réservé aux administrateurs.');
        }

        $rules = [
            'pourcentage_reduction' => 'required|numeric|greater_than[0]|less_than_equal_to[100]',
            'date_debut'            => 'required|valid_date',
            'date_fin'              => 'permit_empty|valid_date',
            'montant_min'           => 'permit_empty|numeric|greater_than_equal_to[0]',
            'montant_max'           => 'permit_empty|numeric|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $db = db_connect();

        $data = [
            'type_operation_id'    => 3, // TRANSFERT
            'pourcentage_reduction' => $this->request->getPost('pourcentage_reduction'),
            'date_debut'           => $this->request->getPost('date_debut'),
            'date_fin'             => $this->request->getPost('date_fin') ?: null,
            'montant_min'          => $this->request->getPost('montant_min') ?: 0,
            'montant_max'          => $this->request->getPost('montant_max') ?: 999999999.99,
            'actif'                => $this->request->getPost('actif') ?? 1,
        ];

        $db->table('promotions_transferts')->insert($data);

        return redirect()->to('/admin/promotions')->with('success', 'Promotion ajoutée avec succès.');
    }

    public function update($id)
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login')->with('error', 'Accès réservé aux administrateurs.');
        }

        $rules = [
            'pourcentage_reduction' => 'required|numeric|greater_than[0]|less_than_equal_to[100]',
            'date_debut'            => 'required|valid_date',
            'date_fin'              => 'permit_empty|valid_date',
            'montant_min'           => 'permit_empty|numeric|greater_than_equal_to[0]',
            'montant_max'           => 'permit_empty|numeric|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $db = db_connect();

        $data = [
            'pourcentage_reduction' => $this->request->getPost('pourcentage_reduction'),
            'date_debut'           => $this->request->getPost('date_debut'),
            'date_fin'             => $this->request->getPost('date_fin') ?: null,
            'montant_min'          => $this->request->getPost('montant_min') ?: 0,
            'montant_max'          => $this->request->getPost('montant_max') ?: 999999999.99,
            'actif'                => $this->request->getPost('actif') ?? 1,
        ];

        $db->table('promotions_transferts')
            ->where('id', $id)
            ->update($data);

        return redirect()->to('/admin/promotions')->with('success', 'Promotion modifiée avec succès.');
    }

    public function delete($id)
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login')->with('error', 'Accès réservé aux administrateurs.');
        }

        $db = db_connect();

        $promo = $db->table('promotions_transferts')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (!$promo) {
            return redirect()->to('/admin/promotions')->with('error', 'Promotion introuvable.');
        }

        $db->table('promotions_transferts')
            ->where('id', $id)
            ->delete();

        return redirect()->to('/admin/promotions')->with('success', 'Promotion supprimée avec succès.');
    }
}