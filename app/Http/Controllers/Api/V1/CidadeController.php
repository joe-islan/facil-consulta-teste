<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ControllerHelper;
use App\Http\Controllers\Controller;
use App\Services\CidadeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;

class CidadeController extends Controller
{
    public function __construct(
        private CidadeService $cidadeService,
        private Logger $logger,
        private ControllerHelper $helper,
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/cidades",
     *     summary="Lista todas as cidades",
     *     description="Retorna a lista de cidades, com opção de filtrar por nome",
     *     tags={"Cidades"},
     *
     *     @OA\Parameter(
     *         name="nome",
     *         in="query",
     *         description="Filtrar cidades por nome",
     *         required=false,
     *
     *         @OA\Schema(
     *             type="string",
     *             example="São Paulo"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de cidades recuperada com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lista de cidades recuperada com sucesso"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="nome", type="string", example="São Paulo"),
     *                     @OA\Property(property="estado", type="string", example="SP"),
     *                     @OA\Property(
     *                         property="medicos",
     *                         type="array",
     *
     *                         @OA\Items(
     *                             type="object",
     *
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="nome", type="string", example="Dr. João Silva"),
     *                             @OA\Property(property="especialidade", type="string", example="Cardiologia")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro interno ao listar cidades"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $cidades = $this->cidadeService->listAll($request->query('nome'));

            return $this->helper->successJsonResponse('Lista de cidades recuperada com sucesso', $cidades);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao listar cidades', [
                'erro' => $e->getMessage(),
                'query' => $request->query(),
            ]);

            return $this->helper->errorJsonResponse('Erro interno ao listar cidades', null, 500);
        }
    }
}
