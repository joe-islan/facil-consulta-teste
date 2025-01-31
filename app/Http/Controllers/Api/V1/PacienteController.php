<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ControllerHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Paciente\StorePacienteRequest;
use App\Http\Requests\Paciente\UpdatePacienteRequest;
use App\Services\PacienteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Log\Logger;

class PacienteController extends Controller
{
    public function __construct(
        private PacienteService $pacienteService,
        private Logger $logger,
        private ControllerHelper $helper,
    ) {
        $this->middleware('auth:api');
    }

    /**
     * @OA\Get(
     *     path="/api/v1/pacientes",
     *     summary="Lista todos os pacientes",
     *     description="Retorna a lista de pacientes cadastrados no sistema.",
     *     tags={"Pacientes"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de pacientes recuperada com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lista de pacientes recuperada com sucesso"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        try {
            $pacientes = $this->pacienteService->listAll();

            return $this->helper->successJsonResponse('Lista de pacientes recuperada com sucesso', $pacientes);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao listar pacientes', ['erro' => $e->getMessage()]);

            return $this->helper->errorJsonResponse('Erro interno ao listar pacientes', null, 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/pacientes",
     *     summary="Cadastra um novo paciente",
     *     description="Cria um novo cadastro de paciente no sistema.",
     *     tags={"Pacientes"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"nome", "idade"},
     *
     *             @OA\Property(property="nome", type="string", example="Maria Santos"),
     *             @OA\Property(property="idade", type="integer", example=30)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Paciente cadastrado com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Paciente cadastrado com sucesso"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function store(StorePacienteRequest $request): JsonResponse
    {
        try {
            $paciente = $this->pacienteService->create($request->validated());

            return $this->helper->successJsonResponse('Paciente cadastrado com sucesso', $paciente, 201);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao cadastrar paciente', ['erro' => $e->getMessage(), 'dados' => $request->all()]);

            return $this->helper->errorJsonResponse('Erro interno ao cadastrar paciente', null, 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/pacientes/{id}",
     *     summary="Atualiza um paciente",
     *     description="Atualiza os dados de um paciente específico.",
     *     tags={"Pacientes"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do paciente",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Paciente atualizado com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Paciente atualizado com sucesso"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function update(UpdatePacienteRequest $request, int $id): JsonResponse
    {
        try {
            $paciente = $this->pacienteService->update($id, $request->validated());

            return $this->helper->successJsonResponse('Paciente atualizado com sucesso', $paciente);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao atualizar paciente', ['erro' => $e->getMessage(), 'dados' => $request->all()]);

            return $this->helper->errorJsonResponse('Erro interno ao atualizar paciente', null, 500);
        }
    }
}
