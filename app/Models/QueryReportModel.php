<?php

namespace App\Models;

use CodeIgniter\Model;

class QueryReportModel extends Model
{
    protected $table            = 'TQueryReport';
    protected $primaryKey       = 'Kode';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'Kode',
        'NamaReport',
        'QuerySql',
        'Parameter',
        'Catatan',
        'IdOrganisasi',
        'StAktif',
        'CreatedAt',
        'CreatedBy',
        'UpdatedAt',
        'UpdatedBy'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'CreatedAt';
    protected $updatedField  = 'UpdatedAt';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Generate kode report dengan format REP + timestamp + random
     */
    public function generateKode(): string
    {
        $timestamp = date('YmdHis');
        $random = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
        return 'REP' . $timestamp . $random;
    }

    /**
     * Get all reports dengan filter optional
     */
    public function getAllReports(?string $idOrganisasi = null, ?int $stAktif = null, ?string $search = null): array
    {
        $builder = $this->builder();

        if ($idOrganisasi !== null) {
            $builder->where('IdOrganisasi', $idOrganisasi);
        }

        if ($stAktif !== null) {
            $builder->where('StAktif', $stAktif);
        }

        if ($search !== null && $search !== '') {
            $builder->like('NamaReport', $search);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get report by kode
     */
    public function getByKode(string $kode): ?array
    {
        $result = $this->where('Kode', $kode)->first();
        return $result ?: null;
    }

    /**
     * Get reports by organisasi
     */
    public function getByOrganisasi(string $idOrganisasi, ?int $stAktif = null): array
    {
        $builder = $this->builder();
        $builder->where('IdOrganisasi', $idOrganisasi);

        if ($stAktif !== null) {
            $builder->where('StAktif', $stAktif);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Create new report
     */
    public function createReport(array $data, string $userId): array
    {
        $kode = $this->generateKode();
        
        $insertData = [
            'Kode'          => $kode,
            'NamaReport'    => $data['namaReport'],
            'QuerySql'      => $data['querySql'],
            'Parameter'     => isset($data['parameter']) ? json_encode($data['parameter']) : null,
            'Catatan'       => $data['catatan'] ?? null,
            'IdOrganisasi'  => $data['idOrganisasi'] ?? null,
            'StAktif'       => 1,
            'CreatedAt'     => date('Y-m-d H:i:s'),
            'CreatedBy'     => $userId,
        ];

        $this->insert($insertData);
        
        return ['kode' => $kode];
    }

    /**
     * Update report
     */
    public function updateReport(string $kode, array $data, string $userId): bool
    {
        $updateData = [
            'UpdatedAt' => date('Y-m-d H:i:s'),
            'UpdatedBy' => $userId,
        ];

        if (isset($data['namaReport'])) {
            $updateData['NamaReport'] = $data['namaReport'];
        }

        if (isset($data['querySql'])) {
            $updateData['QuerySql'] = $data['querySql'];
        }

        if (isset($data['parameter'])) {
            $updateData['Parameter'] = json_encode($data['parameter']);
        }

        if (isset($data['catatan'])) {
            $updateData['Catatan'] = $data['catatan'];
        }

        if (isset($data['stAktif'])) {
            $updateData['StAktif'] = $data['stAktif'];
        }

        return $this->update($kode, $updateData);
    }

    /**
     * Soft delete report (set StAktif = 0)
     */
    public function softDeleteReport(string $kode, string $userId): bool
    {
        return $this->update($kode, [
            'StAktif'   => 0,
            'UpdatedAt' => date('Y-m-d H:i:s'),
            'UpdatedBy' => $userId,
        ]);
    }

    /**
     * Execute report query dengan parameter
     */
    public function executeReport(string $querySql, array $paramValues): array
    {
        // Replace named parameters dengan actual values
        $finalQuery = $querySql;
        $bindings = [];

        foreach ($paramValues as $paramName => $paramValue) {
            // Replace :paramName dengan ?
            $finalQuery = preg_replace('/:'.$paramName.'\b/', '?', $finalQuery);
            $bindings[] = $paramValue;
        }

        // Execute query
        $query = $this->db->query($finalQuery, $bindings);
        return $query->getResultArray();
    }
}
