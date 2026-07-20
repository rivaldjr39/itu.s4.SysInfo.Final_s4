<?php

namespace App\Controllers;

class CommissionController extends BaseController
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

        $commissions = $db->table('configurations_commissions cc')
            ->select("
                COALESCE(cc.id, cc.rowid) AS id,
                cc.operateur_id,
                cc.type_operation_id,
                cc.autre_operateur,
                cc.commission_pourcentage,
                cc.date_debut,
                cc.date_fin,
                op.nom AS operateur_nom,
                top.libelle AS type_operation_libelle,
                top.code AS type_operation_code
            ")
            ->join('operateurs op', 'COALESCE(op.id, op.rowid) = cc.operateur_id')
            ->join('types_operations top', 'COALESCE(top.id, top.rowid) = cc.type_operation_id')
            ->orderBy('op.nom', 'ASC')
            ->orderBy('top.libelle', 'ASC')
            ->get()
            ->getResultArray();

        $operateurs = $db->table('operateurs')
            ->select('COALESCE(id, rowid) AS id, nom')
            ->orderBy('nom', 'ASC')
            ->get()
            ->getResultArray();

        $typesOperations = $db->table('types_operations')
            ->select('COALESCE(id, rowid) AS id, code, libelle')
            ->where('actif', 1)
            ->orderBy('libelle', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/commissions/index', [
            'commissions'      => $commissions,
            'operateurs'       => $operateurs,
            'types_operations' => $typesOperations,
            'title'            => 'Gestion des commissions',
        ]);
    }

    public function store()
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login')->with('error', 'Accès réservé aux administrateurs.');
        }

        $rules = [
            'operateur_id'         => 'required|numeric',
            'type_operation_id'    => 'required|numeric',
            'commission_pourcentage' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'autre_operateur'      => 'permit_empty|in_list[0,1]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $db = db_connect();

        $db->table('configurations_commissions')->insert([
            'operateur_id'          => $this->request->getPost('operateur_id'),
            'type_operation_id'     => $this->request->getPost('type_operation_id'),
            'commission_pourcentage' => $this->request->getPost('commission_pourcentage'),
            'autre_operateur'       => $this->request->getPost('autre_operateur') ?? 0,
            'date_debut'            => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/commissions')->with('success', 'Commission ajoutée avec succès.');
    }

    public function update($id)
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login')->with('error', 'Accès réservé aux administrateurs.');
        }

        $rules = [
            'operateur_id'         => 'required|numeric',
            'type_operation_id'    => 'required|numeric',
            'commission_pourcentage' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'autre_operateur'      => 'permit_empty|in_list[0,1]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $db = db_connect();

        $db->table('configurations_commissions')
            ->where('COALESCE(id, rowid)', $id)
            ->update([
                'operateur_id'          => $this->request->getPost('operateur_id'),
                'type_operation_id'     => $this->request->getPost('type_operation_id'),
                'commission_pourcentage' => $this->request->getPost('commission_pourcentage'),
                'autre_operateur'       => $this->request->getPost('autre_operateur') ?? 0,
            ]);

        return redirect()->to('/admin/commissions')->with('success', 'Commission modifiée avec succès.');
    }

    public function delete($id)
    {
        if (!$this->checkAdmin()) {
            return redirect()->to('/login')->with('error', 'Accès réservé aux administrateurs.');
        }

        $db = db_connect();

        $commission = $db->table('configurations_commissions')
            ->select('COALESCE(id, rowid) AS id')
            ->where('COALESCE(id, rowid)', $id)
            ->get()
            ->getRowArray();

        if (!$commission) {
            return redirect()->to('/admin/commissions')->with('error', 'Commission introuvable.');
        }

        $db->table('configurations_commissions')
            ->where('COALESCE(id, rowid)', $id)
            ->delete();

        return redirect()->to('/admin/commissions')->with('success', 'Commission supprimée avec succès.');
    }
}