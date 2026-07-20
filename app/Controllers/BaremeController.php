<?php

namespace App\Controllers;

use App\Models\Bareme;

class BaremeController extends BaseController
{
    protected Bareme $baremeModel;

    public function __construct()
    {
        $this->baremeModel = new Bareme();
    }

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

        // Get baremes joined with types_operations using COALESCE(id, rowid) for compatibility
        // Using 'top' as alias to avoid SQLite syntax error with reserved keyword 'to'
        $baremes = $this->baremeModel->db->table('baremes_frais bf')
            ->select('COALESCE(bf.id, bf.rowid) AS id, bf.type_operation_id, bf.montant_min, bf.montant_max, bf.frais_fixe, bf.frais_pourcentage, bf.date_debut, bf.date_fin, top.libelle AS type_operation_libelle, top.code AS type_operation_code')
            ->join('types_operations top', 'COALESCE(top.id, top.rowid) = bf.type_operation_id')
            ->orderBy('top.libelle', 'ASC')
            ->orderBy('bf.montant_min', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/baremes/index', [
            'baremes' => $baremes,
            'title'   => 'Gestion des barèmes de frais',
        ]);
    }

    public function create()
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login')->with('error', 'Accès réservé aux administrateurs.');
        }

        // Fetch types of operations using COALESCE(id, rowid)
        $typesOperations = $this->baremeModel->db->table('types_operations')
            ->select('COALESCE(id, rowid) AS id, code, libelle, actif')
            ->where('actif', true)
            ->get()
            ->getResultArray();

        return view('admin/baremes/create', [
            'types_operations' => $typesOperations,
            'title'            => 'Créer un barème de frais',
        ]);
    }

    public function store()
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login')->with('error', 'Accès réservé aux administrateurs.');
        }

        $rules = [
            'type_operation_id' => 'required|numeric',
            'montant_min'       => 'required|numeric|greater_than_equal_to[0]',
            'montant_max'       => 'required|numeric',
            'frais_fixe'        => 'required|numeric|greater_than_equal_to[0]',
            'frais_pourcentage' => 'required|numeric|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $montantMin = (float) $this->request->getPost('montant_min');
        $montantMax = (float) $this->request->getPost('montant_max');

        if ($montantMax < $montantMin) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['montant_max' => 'Le montant maximum doit être supérieur ou égal au montant minimum.']);
        }

        $dateDebut = $this->request->getPost('date_debut');
        $dateFin = $this->request->getPost('date_fin');

        $data = [
            'type_operation_id' => $this->request->getPost('type_operation_id'),
            'montant_min'       => $montantMin,
            'montant_max'       => $montantMax,
            'frais_fixe'        => $this->request->getPost('frais_fixe'),
            'frais_pourcentage' => $this->request->getPost('frais_pourcentage'),
            'date_debut'        => !empty($dateDebut) ? $dateDebut : date('Y-m-d H:i:s'),
            'date_fin'          => !empty($dateFin) ? $dateFin : null,
        ];

        $this->baremeModel->db->table('baremes_frais')->insert($data);

        return redirect()->to('/admin/baremes')->with('success', 'Le barème a été créé avec succès.');
    }

    public function edit($id)
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login')->with('error', 'Accès réservé aux administrateurs.');
        }

        // Fetch bareme using COALESCE(id, rowid)
        $bareme = $this->baremeModel->db->table('baremes_frais')
            ->select('COALESCE(id, rowid) AS id, type_operation_id, montant_min, montant_max, frais_fixe, frais_pourcentage, date_debut, date_fin')
            ->where('COALESCE(id, rowid)', $id)
            ->get()
            ->getRowArray();

        if (!$bareme) {
            return redirect()->to('/admin/baremes')->with('error', 'Barème introuvable.');
        }

        // Fetch types of operations using COALESCE(id, rowid)
        $typesOperations = $this->baremeModel->db->table('types_operations')
            ->select('COALESCE(id, rowid) AS id, code, libelle, actif')
            ->where('actif', true)
            ->get()
            ->getResultArray();

        return view('admin/baremes/edit', [
            'bareme'           => $bareme,
            'types_operations' => $typesOperations,
            'title'            => 'Modifier un barème de frais',
        ]);
    }

    public function update($id)
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login')->with('error', 'Accès réservé aux administrateurs.');
        }

        $bareme = $this->baremeModel->db->table('baremes_frais')
            ->select('COALESCE(id, rowid) AS id')
            ->where('COALESCE(id, rowid)', $id)
            ->get()
            ->getRowArray();

        if (!$bareme) {
            return redirect()->to('/admin/baremes')->with('error', 'Barème introuvable.');
        }

        $rules = [
            'type_operation_id' => 'required|numeric',
            'montant_min'       => 'required|numeric|greater_than_equal_to[0]',
            'montant_max'       => 'required|numeric',
            'frais_fixe'        => 'required|numeric|greater_than_equal_to[0]',
            'frais_pourcentage' => 'required|numeric|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $montantMin = (float) $this->request->getPost('montant_min');
        $montantMax = (float) $this->request->getPost('montant_max');

        if ($montantMax < $montantMin) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['montant_max' => 'Le montant maximum doit être supérieur ou égal au montant minimum.']);
        }

        $dateDebut = $this->request->getPost('date_debut');
        $dateFin = $this->request->getPost('date_fin');

        $data = [
            'type_operation_id' => $this->request->getPost('type_operation_id'),
            'montant_min'       => $montantMin,
            'montant_max'       => $montantMax,
            'frais_fixe'        => $this->request->getPost('frais_fixe'),
            'frais_pourcentage' => $this->request->getPost('frais_pourcentage'),
            'date_debut'        => !empty($dateDebut) ? $dateDebut : date('Y-m-d H:i:s'),
            'date_fin'          => !empty($dateFin) ? $dateFin : null,
        ];

        $this->baremeModel->db->table('baremes_frais')
            ->where('COALESCE(id, rowid)', $id)
            ->update($data);

        return redirect()->to('/admin/baremes')->with('success', 'Le barème a été mis à jour avec succès.');
    }

    public function delete($id)
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login')->with('error', 'Accès réservé aux administrateurs.');
        }

        $bareme = $this->baremeModel->db->table('baremes_frais')
            ->select('COALESCE(id, rowid) AS id')
            ->where('COALESCE(id, rowid)', $id)
            ->get()
            ->getRowArray();

        if (!$bareme) {
            return redirect()->to('/admin/baremes')->with('error', 'Barème introuvable.');
        }

        $this->baremeModel->db->table('baremes_frais')
            ->where('COALESCE(id, rowid)', $id)
            ->delete();

        return redirect()->to('/admin/baremes')->with('success', 'Le barème a été supprimé avec succès.');
    }
}
