<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class SwaggerDoc extends Controller
{
    public function index()
    {
        // Serve static swagger.json file
        $swaggerFile = FCPATH . 'swagger.json';
        
        if (!file_exists($swaggerFile)) {
            return $this->response
                ->setStatusCode(404)
                ->setContentType('application/json')
                ->setBody(json_encode([
                    'error' => 'Swagger documentation not found',
                    'message' => 'File swagger.json tidak ditemukan'
                ]));
        }
        
        $swaggerJson = file_get_contents($swaggerFile);
        
        return $this->response
            ->setContentType('application/json')
            ->setBody($swaggerJson);
    }

    public function ui()
    {
        // Serve Swagger UI
        return view('swagger_ui');
    }
}
