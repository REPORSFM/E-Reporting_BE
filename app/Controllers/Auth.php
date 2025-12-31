<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;
use OpenApi\Attributes as OA;

class Auth extends ResourceController
{
    protected $format = 'json';

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
     * POST /api/login
     * Login endpoint
     * 
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Authentication"},
     *     summary="Login pengguna",
     *     description="Endpoint untuk login pengguna dengan username dan password. Session akan dibuat setelah login berhasil.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username","password"},
     *             @OA\Property(property="username", type="string", example="admin"),
     *             @OA\Property(property="password", type="string", example="admin123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login berhasil",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="response",
     *                 type="object",
     *                 @OA\Property(property="ID", type="integer", example=1),
     *                 @OA\Property(property="Username", type="string", example="admin"),
     *                 @OA\Property(property="Nama", type="string", example="Administrator"),
     *                 @OA\Property(property="Role", type="string", example="admin"),
     *                 @OA\Property(property="Organisasi", type="string", example="ORG001")
     *             ),
     *             @OA\Property(
     *                 property="metadata",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Ok"),
     *                 @OA\Property(property="code", type="integer", example=200)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Input tidak lengkap",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Username atau password salah",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function login()
    {
        $json = $this->request->getJSON(true);

        // Validasi input
        if (empty($json['username']) || empty($json['password'])) {
            return $this->apiResponse(null, 'Username dan password harus diisi', 400);
        }

        try {
            $userModel = new UserModel();
            $userData = $userModel->authenticate($json['username'], $json['password']);

            if (!$userData) {
                return $this->apiResponse(null, 'Username atau password salah', 401);
            }

            // Set session untuk maintain login state
            $session = session();
            $session->set([
                'userId' => $userData['ID'],
                'username' => $json['username'],
                'isLoggedIn' => true
            ]);

            return $this->apiResponse($userData, 'Ok', 200);

        } catch (\Exception $e) {
            log_message('error', 'Error during login: ' . $e->getMessage());
            return $this->apiResponse(null, 'Terjadi kesalahan saat login', 500);
        }
    }

    /**
     * POST /api/logout
     * Logout endpoint
     * 
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Authentication"},
     *     summary="Logout pengguna",
     *     description="Endpoint untuk logout pengguna. Session akan dihapus.",
     *     @OA\Response(
     *         response=200,
     *         description="Logout berhasil",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="response",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Logout berhasil")
     *             ),
     *             @OA\Property(
     *                 property="metadata",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Ok"),
     *                 @OA\Property(property="code", type="integer", example=200)
     *             )
     *         )
     *     )
     * )
     */
    public function logout()
    {
        $session = session();
        $session->destroy();

        return $this->apiResponse([
            'message' => 'Logout berhasil'
        ], 'Ok', 200);
    }

    /**
     * GET /api/profile
     * Get current user profile
     * 
     * @OA\Get(
     *     path="/api/profile",
     *     tags={"Authentication"},
     *     summary="Get profil pengguna yang sedang login",
     *     description="Endpoint untuk mendapatkan informasi profil pengguna yang sedang login. Memerlukan session aktif.",
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mendapatkan profil",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="response",
     *                 type="object",
     *                 @OA\Property(property="ID", type="integer", example=1),
     *                 @OA\Property(property="Username", type="string", example="admin"),
     *                 @OA\Property(property="Nama", type="string", example="Administrator"),
     *                 @OA\Property(property="Role", type="string", example="admin"),
     *                 @OA\Property(property="Organisasi", type="string", example="ORG001")
     *             ),
     *             @OA\Property(
     *                 property="metadata",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Ok"),
     *                 @OA\Property(property="code", type="integer", example=200)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - belum login",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User tidak ditemukan",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function profile()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return $this->apiResponse(null, 'Unauthorized', 401);
        }

        $userModel = new UserModel();
        $userData = $userModel->getUserById($session->get('userId'));

        if (!$userData) {
            return $this->apiResponse(null, 'User tidak ditemukan', 404);
        }

        return $this->apiResponse($userData, 'Ok', 200);
    }
}
