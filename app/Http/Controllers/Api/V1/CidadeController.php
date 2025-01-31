<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CidadeService;
use App\Helpers\ControllerHelper;
use Illuminate\Log\Logger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CidadeController extends Controller
{
    public function __construct(
        private CidadeService $cidadeService,
        private Logger $logger,
        private ControllerHelper $helper
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $cidades = $this->cidadeService->listAll($request->query('nome'));
            return $this->helper->successJsonResponse('Lista de cidades recuperada com sucesso', $cidades);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao listar cidades', [
                'erro' => $e->getMessage(),
                'query' => $request->query()
            ]);
            return $this->helper->errorJsonResponse('Erro interno ao listar cidades', null, 500);
        }
    }
}
