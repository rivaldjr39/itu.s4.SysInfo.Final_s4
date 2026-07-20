<?php

namespace App\Controllers;

class PrefixeController extends BaseController
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
        $prefixes = $db->table('prefixes p')
            ->select('COALESCE(p.id, p.rowid) AS id, p.prefixe, p.actif, p.date_creation, op.nom AS operateur_nom, op.id AS operateur_id')
            ->join('operateurs op', 'COALESCE(op.id, op.rowid) = p.id_operateur')
            ->orderBy('op.nom', 'ASC')
            ->orderBy('p.prefixe', 'ASC')
            ->get()
            ->getResultArray();

        $operateurs = $db->table('operateurs')
            ->select('COALESCE(id, rowid) AS id, nom')
            ->orderBy('nom', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/prefixes/index', [
            'prefixes'   => $prefixes,
            'operateurs' => $operateurs,
            'title'      => 'Gestion des préfixes',
        ]);
    }

    public function store()
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login')->with('error', 'Accès réservé aux administrateurs.');
        }

        $rules = [
            'prefixe'        => 'required|min_length[2]|max_length[5]',
            'id_operateur'   => 'required|numeric',
            'actif'          => 'permit_empty|in_list[0,1]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $db = db_connect();

        // Vérifier unicité
        $existing = $db->table('prefixes')
            ->where('prefixe', $this->request->getPost('prefixe'))
            ->get()
            ->getRowArray();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ce préfixe existe déjà.');
        }

        $db->table('prefixes')->insert([
            'prefixe'      => $this->request->getPost('prefixe'),
            'id_operateur' => $this->request->getPost('id_operateur'),
            'actif'        => $this->request->getPost('actif') ?? 1,
        ]);

        return redirect()->to('/admin/prefixes')->with('success', 'Préfixe ajouté avec succès.');
    }

    public function update($id)
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login')->with('error', 'Accès réservé aux administrateurs.');
        }

        $rules = [
            'prefixe'        => 'required|min_length[2]|max_length[5]',
            'id_operateur'   => 'required|numeric',
            'actif'          => 'permit_empty|in_list[0,1]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $db = db_connect();

        $db->table('prefixes')
            ->where('COALESCE(id, rowid)', $id)
            ->update([
                'prefixe'      => $this->request->getPost('prefixe'),
                'id_operateur' => $this->request->getPost('id_operateur'),
                'actif'        => $this->request->getPost('actif') ?? 1,
            ]);

        return redirect()->to('/admin/prefixes')->with('success', 'Préfixe modifié avec succès.');
    }

    public function delete($id)
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login')->with('error', 'Accès réservé aux administrateurs.');
        }

        $db = db_connect();

        $prefixe = $db->table('prefixes')
            ->select('COALESCE(id, rowid) AS id')
            ->where('COALESCE(id, rowid)', $id)
            ->get()
            ->getRowArray();

        if (!$prefixe) {
            return redirect()->to('/admin/prefixes')->with('error', 'Préfixe introuvable.');
        }

        $db->table('prefixes')
            ->where('COALESCE(id, rowid)', $id)
            ->delete();

        return redirect()->to('/admin/prefixes')->with('success', 'Préfixe supprimé avec succès.');
    }

    // API : retourne les préfixes d'un opérateur (pour JS)
    public function getByOperateurApi($operateurId)
    {
        $db = db_connect();
        $prefixes = $db->table('prefixes')
            ->select('COALESCE(id, rowid) AS id, prefixe')
            ->where('id_operateur', $operateurId)
            ->where('actif', 1)
            ->orderBy('prefixe', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON($prefixes);
    }
}