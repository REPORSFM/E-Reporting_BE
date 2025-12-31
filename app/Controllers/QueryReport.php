<?php

namespace App\Controllers;

use App\Models\QueryReportModel;
use CodeIgniter\RESTful\ResourceController;
use OpenApi\Attributes as OA;

class QueryReport extends ResourceController
{
    protected $modelName = 'App\Models\QueryReportModel';
    protected $format    = 'json';

    /**
     * Helper untuk membuat response
     */
    private function apiResponse($data, string $message = 'OK', int $code = 200)
    {
        return $this->respond([
            'response' => $data,
            'metadata' => [
                'message' => $message,
                'code'    => $code
            ]
        ], $code);
    }

    /**
     * POST /api/queryreport/create
     * Membuat report baru
     * 
     * @OA\Post(
     *     path="/api/queryreport/create",
     *     tags={"Query Report"},
     *     summary="Membuat query report baru",
     *     description="Endpoint untuk membuat query report baru. Hanya query SELECT yang diperbolehkan.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"namaReport","querySql"},
     *             @OA\Property(property="namaReport", type="string", example="Laporan Penjualan"),
     *             @OA\Property(property="querySql", type="string", example="SELECT * FROM sales WHERE date = :tanggal"),
     *             @OA\Property(property="catatan", type="string", example="Catatan untuk laporan ini"),
     *             @OA\Property(property="idOrganisasi", type="string", example="ORG001"),
     *             @OA\Property(
     *                 property="parameter",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="name", type="string", example="tanggal"),
     *                     @OA\Property(property="type", type="string", example="date"),
     *                     @OA\Property(property="label", type="string", example="Tanggal")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Report berhasil dibuat",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="response",
     *                 type="object",
     *                 @OA\Property(property="kode", type="string", example="QR20231126001"),
     *                 @OA\Property(property="message", type="string", example="Report berhasil dibuat")
     *             ),
     *             @OA\Property(
     *                 property="metadata",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="OK"),
     *                 @OA\Property(property="code", type="integer", example=200)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Input tidak valid",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function create()
    {
        $json = $this->request->getJSON(true);

        // Validasi input
        if (empty($json['namaReport']) || empty($json['querySql'])) {
            return $this->apiResponse(null, 'Nama Report dan Query SQL harus diisi', 400);
        }

        // Validasi hanya SELECT query yang diperbolehkan
        $querySql = trim($json['querySql']);
        if (stripos($querySql, 'SELECT') !== 0) {
            return $this->apiResponse(null, 'Hanya query SELECT yang diperbolehkan', 400);
        }

        try {
            $model = new QueryReportModel();
            $session = session();
            $userId = $session->get('userId') ?? 'SYSTEM'; // Dari session login
            
            $result = $model->createReport($json, $userId);
            
            return $this->apiResponse([
                'kode'    => $result['kode'],
                'message' => 'Report berhasil dibuat'
            ], 'OK', 200);

        } catch (\Exception $e) {
            log_message('error', 'Error creating report: ' . $e->getMessage());
            return $this->apiResponse(null, 'Gagal membuat report', 500);
        }
    }

    /**
     * POST /api/queryreport/getall
     * Get semua report dengan filter optional
     * 
     * @OA\Post(
     *     path="/api/queryreport/getall",
     *     tags={"Query Report"},
     *     summary="Mendapatkan semua query report",
     *     description="Endpoint untuk mendapatkan semua query report dengan filter optional (idOrganisasi, stAktif, search).",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="idOrganisasi", type="string", example="ORG001"),
     *             @OA\Property(property="stAktif", type="integer", example=1),
     *             @OA\Property(property="search", type="string", example="penjualan")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mendapatkan data report",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="response",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="kode", type="string", example="QR20231126001"),
     *                     @OA\Property(property="namaReport", type="string", example="Laporan Penjualan"),
     *                     @OA\Property(property="querySql", type="string", example="SELECT * FROM sales"),
     *                     @OA\Property(property="catatan", type="string", example="Catatan report"),
     *                     @OA\Property(property="idOrganisasi", type="string", example="ORG001"),
     *                     @OA\Property(property="stAktif", type="integer", example=1),
     *                     @OA\Property(property="parameter", type="array", @OA\Items(type="object")),
     *                     @OA\Property(property="createdAt", type="string", example="2023-11-26 10:00:00"),
     *                     @OA\Property(property="createdBy", type="string", example="admin"),
     *                     @OA\Property(property="updatedAt", type="string", example="2023-11-26 10:00:00"),
     *                     @OA\Property(property="updatedBy", type="string", example="admin")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="metadata",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="OK"),
     *                 @OA\Property(property="code", type="integer", example=200)
     *             )
     *         )
     *     )
     * )
     */
    public function getAll()
    {
        $json = $this->request->getJSON(true);

        $idOrganisasi = $json['idOrganisasi'] ?? null;
        $stAktif = isset($json['stAktif']) ? (int)$json['stAktif'] : null;
        $search = $json['search'] ?? null;

        try {
            $model = new QueryReportModel();
            $reports = $model->getAllReports($idOrganisasi, $stAktif, $search);

            // Parse parameter JSON ke array
            foreach ($reports as &$report) {
                if (!empty($report['Parameter'])) {
                    $report['parameter'] = json_decode($report['Parameter'], true);
                    unset($report['Parameter']);
                } else {
                    $report['parameter'] = [];
                    unset($report['Parameter']);
                }

                // Rename fields untuk konsistensi dengan API spec
                $report['kode'] = $report['Kode'];
                $report['namaReport'] = $report['NamaReport'];
                $report['querySql'] = $report['QuerySql'];
                $report['catatan'] = $report['Catatan'];
                $report['idOrganisasi'] = $report['IdOrganisasi'];
                $report['stAktif'] = (int)$report['StAktif'];
                $report['createdAt'] = $report['CreatedAt'];
                $report['createdBy'] = $report['CreatedBy'];
                $report['updatedAt'] = $report['UpdatedAt'];
                $report['updatedBy'] = $report['UpdatedBy'];

                unset($report['Kode'], $report['NamaReport'], $report['QuerySql'], 
                      $report['Catatan'], $report['IdOrganisasi'], $report['StAktif'], 
                      $report['CreatedAt'], $report['CreatedBy'], $report['UpdatedAt'], 
                      $report['UpdatedBy']);
            }

            $message = empty($reports) ? 'Data tidak ditemukan' : 'OK';
            return $this->apiResponse($reports, $message, 200);

        } catch (\Exception $e) {
            log_message('error', 'Error getting all reports: ' . $e->getMessage());
            return $this->apiResponse(null, 'Gagal mengambil data report', 500);
        }
    }

    /**
     * POST /api/queryreport/getbykode
     * Get report by kode
     * 
     * @OA\Post(
     *     path="/api/queryreport/getbykode",
     *     tags={"Query Report"},
     *     summary="Mendapatkan query report berdasarkan kode",
     *     description="Endpoint untuk mendapatkan detail query report berdasarkan kode.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"kode"},
     *             @OA\Property(property="kode", type="string", example="QR20231126001")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mendapatkan data report",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="response",
     *                 type="object",
     *                 @OA\Property(property="kode", type="string", example="QR20231126001"),
     *                 @OA\Property(property="namaReport", type="string", example="Laporan Penjualan"),
     *                 @OA\Property(property="querySql", type="string", example="SELECT * FROM sales"),
     *                 @OA\Property(property="catatan", type="string", example="Catatan report"),
     *                 @OA\Property(property="idOrganisasi", type="string", example="ORG001"),
     *                 @OA\Property(property="stAktif", type="integer", example=1),
     *                 @OA\Property(property="parameter", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="createdAt", type="string", example="2023-11-26 10:00:00"),
     *                 @OA\Property(property="createdBy", type="string", example="admin"),
     *                 @OA\Property(property="updatedAt", type="string", example="2023-11-26 10:00:00"),
     *                 @OA\Property(property="updatedBy", type="string", example="admin")
     *             ),
     *             @OA\Property(
     *                 property="metadata",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="OK"),
     *                 @OA\Property(property="code", type="integer", example=200)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Report tidak ditemukan",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function getByKode()
    {
        $json = $this->request->getJSON(true);

        if (empty($json['kode'])) {
            return $this->apiResponse(null, 'Kode report harus diisi', 400);
        }

        try {
            $model = new QueryReportModel();
            $report = $model->getByKode($json['kode']);

            if (!$report) {
                return $this->apiResponse(null, 'Report tidak ditemukan', 404);
            }

            // Parse parameter JSON ke array
            if (!empty($report['Parameter'])) {
                $report['parameter'] = json_decode($report['Parameter'], true);
            } else {
                $report['parameter'] = [];
            }

            // Format response
            $response = [
                'kode'          => $report['Kode'],
                'namaReport'    => $report['NamaReport'],
                'querySql'      => $report['QuerySql'],
                'parameter'     => $report['parameter'],
                'catatan'       => $report['Catatan'],
                'idOrganisasi'  => $report['IdOrganisasi'],
                'stAktif'       => (int)$report['StAktif'],
                'createdAt'     => $report['CreatedAt'],
                'createdBy'     => $report['CreatedBy'],
                'updatedAt'     => $report['UpdatedAt'],
                'updatedBy'     => $report['UpdatedBy'],
            ];

            return $this->apiResponse($response, 'OK', 200);

        } catch (\Exception $e) {
            log_message('error', 'Error getting report by kode: ' . $e->getMessage());
            return $this->apiResponse(null, 'Gagal mengambil data report', 500);
        }
    }

    /**
     * POST /api/queryreport/getbyorganisasi
     * Get reports by organisasi
     * 
     * @OA\Post(
     *     path="/api/queryreport/getbyorganisasi",
     *     tags={"Query Report"},
     *     summary="Mendapatkan query report berdasarkan organisasi",
     *     description="Endpoint untuk mendapatkan semua query report berdasarkan ID organisasi.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"idOrganisasi"},
     *             @OA\Property(property="idOrganisasi", type="string", example="ORG001"),
     *             @OA\Property(property="stAktif", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mendapatkan data report",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="response",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="kode", type="string"),
     *                     @OA\Property(property="namaReport", type="string"),
     *                     @OA\Property(property="querySql", type="string"),
     *                     @OA\Property(property="catatan", type="string"),
     *                     @OA\Property(property="idOrganisasi", type="string"),
     *                     @OA\Property(property="stAktif", type="integer"),
     *                     @OA\Property(property="parameter", type="array", @OA\Items(type="object"))
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="metadata",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="OK"),
     *                 @OA\Property(property="code", type="integer", example=200)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="ID Organisasi harus diisi",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function getByOrganisasi()
    {
        $json = $this->request->getJSON(true);

        if (empty($json['idOrganisasi'])) {
            return $this->apiResponse(null, 'ID Organisasi harus diisi', 400);
        }

        $stAktif = isset($json['stAktif']) ? (int)$json['stAktif'] : null;

        try {
            $model = new QueryReportModel();
            $reports = $model->getByOrganisasi($json['idOrganisasi'], $stAktif);

            // Parse parameter JSON ke array
            foreach ($reports as &$report) {
                if (!empty($report['Parameter'])) {
                    $report['parameter'] = json_decode($report['Parameter'], true);
                    unset($report['Parameter']);
                } else {
                    $report['parameter'] = [];
                    unset($report['Parameter']);
                }

                // Rename fields untuk konsistensi dengan API spec
                $report['kode'] = $report['Kode'];
                $report['namaReport'] = $report['NamaReport'];
                $report['querySql'] = $report['QuerySql'];
                $report['catatan'] = $report['Catatan'];
                $report['idOrganisasi'] = $report['IdOrganisasi'];
                $report['stAktif'] = (int)$report['StAktif'];
                $report['createdAt'] = $report['CreatedAt'];
                $report['createdBy'] = $report['CreatedBy'];
                $report['updatedAt'] = $report['UpdatedAt'];
                $report['updatedBy'] = $report['UpdatedBy'];

                unset($report['Kode'], $report['NamaReport'], $report['QuerySql'], 
                      $report['Catatan'], $report['IdOrganisasi'], $report['StAktif'], 
                      $report['CreatedAt'], $report['CreatedBy'], $report['UpdatedAt'], 
                      $report['UpdatedBy']);
            }

            $message = empty($reports) ? 'Data tidak ditemukan' : 'OK';
            return $this->apiResponse($reports, $message, 200);

        } catch (\Exception $e) {
            log_message('error', 'Error getting reports by organisasi: ' . $e->getMessage());
            return $this->apiResponse(null, 'Gagal mengambil data report', 500);
        }
    }

    /**
     * PUT /api/queryreport/update
     * Update report
     * 
     * @OA\Put(
     *     path="/api/queryreport/update",
     *     tags={"Query Report"},
     *     summary="Update query report",
     *     description="Endpoint untuk mengupdate query report berdasarkan kode. Field yang tidak diisi tidak akan diupdate.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"kode"},
     *             @OA\Property(property="kode", type="string", example="QR20231126001"),
     *             @OA\Property(property="namaReport", type="string", example="Laporan Penjualan Updated"),
     *             @OA\Property(property="querySql", type="string", example="SELECT * FROM sales WHERE date = :tanggal"),
     *             @OA\Property(property="catatan", type="string", example="Catatan updated"),
     *             @OA\Property(property="idOrganisasi", type="string", example="ORG001"),
     *             @OA\Property(property="stAktif", type="integer", example=1),
     *             @OA\Property(property="parameter", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Report berhasil diupdate",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="response",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Report berhasil diupdate")
     *             ),
     *             @OA\Property(
     *                 property="metadata",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="OK"),
     *                 @OA\Property(property="code", type="integer", example=200)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Report tidak ditemukan",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function update($id = null)
    {
        $json = $this->request->getJSON(true);

        if (empty($json['kode'])) {
            return $this->apiResponse(null, 'Kode report harus diisi', 400);
        }

        // Validasi query jika ada
        if (isset($json['querySql'])) {
            $querySql = trim($json['querySql']);
            if (stripos($querySql, 'SELECT') !== 0) {
                return $this->apiResponse(null, 'Hanya query SELECT yang diperbolehkan', 400);
            }
        }

        try {
            $model = new QueryReportModel();
            
            // Cek apakah report ada
            $existing = $model->getByKode($json['kode']);
            if (!$existing) {
                return $this->apiResponse(null, 'Report tidak ditemukan', 404);
            }

            $session = session();
            $userId = $session->get('userId') ?? 'SYSTEM';
            $updated = $model->updateReport($json['kode'], $json, $userId);

            if ($updated) {
                return $this->apiResponse([
                    'message' => 'Report berhasil diupdate'
                ], 'OK', 200);
            } else {
                return $this->apiResponse(null, 'Tidak ada perubahan data', 200);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error updating report: ' . $e->getMessage());
            return $this->apiResponse(null, 'Gagal mengupdate report', 500);
        }
    }

    /**
     * DELETE /api/queryreport/delete
     * Soft delete report
     * 
     * @OA\Delete(
     *     path="/api/queryreport/delete",
     *     tags={"Query Report"},
     *     summary="Hapus query report (soft delete)",
     *     description="Endpoint untuk menghapus query report (soft delete - set stAktif = 0).",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"kode"},
     *             @OA\Property(property="kode", type="string", example="QR20231126001")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Report berhasil dihapus",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="response",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Report berhasil dihapus")
     *             ),
     *             @OA\Property(
     *                 property="metadata",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="OK"),
     *                 @OA\Property(property="code", type="integer", example=200)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Report tidak ditemukan",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function delete($id = null)
    {
        $json = $this->request->getJSON(true);

        if (empty($json['kode'])) {
            return $this->apiResponse(null, 'Kode report harus diisi', 400);
        }

        try {
            $model = new QueryReportModel();
            
            // Cek apakah report ada
            $existing = $model->getByKode($json['kode']);
            if (!$existing) {
                return $this->apiResponse(null, 'Report tidak ditemukan', 404);
            }

            $session = session();
            $userId = $session->get('userId') ?? 'SYSTEM';
            $deleted = $model->softDeleteReport($json['kode'], $userId);

            if ($deleted) {
                return $this->apiResponse([
                    'message' => 'Report berhasil dihapus'
                ], 'OK', 200);
            } else {
                return $this->apiResponse(null, 'Gagal menghapus report', 500);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error deleting report: ' . $e->getMessage());
            return $this->apiResponse(null, 'Gagal menghapus report', 500);
        }
    }

    /**
     * POST /api/queryreport/execute
     * Execute report dengan parameter
     * 
     * @OA\Post(
     *     path="/api/queryreport/execute",
     *     tags={"Query Report"},
     *     summary="Execute query report",
     *     description="Endpoint untuk mengeksekusi query report dengan parameter yang diperlukan.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"kode"},
     *             @OA\Property(property="kode", type="string", example="QR20231126001"),
     *             @OA\Property(
     *                 property="paramValues",
     *                 type="object",
     *                 @OA\Property(property="tanggal", type="string", example="2023-11-26"),
     *                 @OA\Property(property="status", type="string", example="completed")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Query berhasil dieksekusi",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="response",
     *                 type="object",
     *                 @OA\Property(property="reportId", type="string", example="QR20231126001"),
     *                 @OA\Property(property="nama", type="string", example="Laporan Penjualan"),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(type="object")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="metadata",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="OK"),
     *                 @OA\Property(property="code", type="integer", example=200)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Parameter tidak lengkap atau report tidak aktif",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Report tidak ditemukan",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function execute()
    {
        $json = $this->request->getJSON(true);

        if (empty($json['kode'])) {
            return $this->apiResponse(null, 'Kode report harus diisi', 400);
        }

        try {
            $model = new QueryReportModel();
            
            // Get report
            $report = $model->getByKode($json['kode']);
            if (!$report) {
                return $this->apiResponse(null, 'Report tidak ditemukan', 404);
            }

            // Cek apakah report aktif
            if ($report['StAktif'] != 1) {
                return $this->apiResponse(null, 'Report tidak aktif', 400);
            }

            // Parse parameter definition
            $paramDefinitions = json_decode($report['Parameter'] ?? '[]', true);
            $paramValues = $json['paramValues'] ?? [];

            // Validasi parameter wajib
            foreach ($paramDefinitions as $paramDef) {
                $paramName = $paramDef['name'];
                if (!isset($paramValues[$paramName]) || $paramValues[$paramName] === '') {
                    return $this->apiResponse(null, "Parameter \"$paramName\" harus diisi", 400);
                }
            }

            // Execute query
            $data = $model->executeReport($report['QuerySql'], $paramValues);

            $response = [
                'reportId' => $report['Kode'],
                'nama'     => $report['NamaReport'],
                'data'     => $data
            ];

            return $this->apiResponse($response, 'OK', 200);

        } catch (\Exception $e) {
            log_message('error', 'Error executing report: ' . $e->getMessage());
            return $this->apiResponse(null, 'Error executing query: ' . $e->getMessage(), 500);
        }
    }
}
