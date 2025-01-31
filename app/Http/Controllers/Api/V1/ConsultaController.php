<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ControllerHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Consulta\StoreConsultaRequest;
use App\Http\Requests\Consulta\UpdateConsultaRequest;
use App\Services\ConsultaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Log\Logger;

class ConsultaController extends Controller
{
    public function __construct(
        private ConsultaService $consultaService,
        private Logger $logger,
        private ControllerHelper $helper,
    ) {
        $this->middleware('auth:api');
    }

    /**
     * @OA\Get(
     *     path="/api/v1/consultas",
     *     summary="Lista todas as consultas",
     *     description="Retorna a lista de todas as consultas agendadas",
     *     tags={"Consultas"},
     *     security={{ "bearerAuth": {} }},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de consultas recuperada com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lista de consultas recuperada com sucesso"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="medico_id", type="integer", example=1),
     *                     @OA\Property(property="paciente_id", type="integer", example=1),
     *                     @OA\Property(property="data_hora", type="string", format="date-time", example="2024-02-01 14:30:00"),
     *                     @OA\Property(property="status", type="string", example="agendada"),
     *                     @OA\Property(
     *                         property="medico",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="nome", type="string", example="Dr. João Silva"),
     *                         @OA\Property(property="especialidade", type="string", example="Cardiologia")
     *                     ),
     *                     @OA\Property(
     *                         property="paciente",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="nome", type="string", example="Maria Santos"),
     *                         @OA\Property(property="telefone", type="string", example="(11) 98765-4321")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        try {
            $consultas = $this->consultaService->listAll();

            return $this->helper->successJsonResponse('Lista de consultas recuperada com sucesso', $consultas);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao listar consultas', ['erro' => $e->getMessage()]);

            return $this->helper->errorJsonResponse('Erro interno ao listar consultas', null, 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/medicos/consulta",
     *     summary="Agenda uma nova consulta",
     *     description="Cria um novo agendamento de consulta",
     *     tags={"Consultas"},
     *     security={{ "bearerAuth": {} }},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"medico_id","paciente_id","data_hora"},
     *
     *             @OA\Property(property="medico_id", type="integer", example=1),
     *             @OA\Property(property="paciente_id", type="integer", example=1),
     *             @OA\Property(property="data_hora", type="string", format="date-time", example="2024-02-01 14:30:00")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Consulta agendada com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Consulta agendada com sucesso"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="medico_id", type="integer", example=1),
     *                 @OA\Property(property="paciente_id", type="integer", example=1),
     *                 @OA\Property(property="data_hora", type="string", format="date-time", example="2024-02-01 14:30:00"),
     *                 @OA\Property(property="status", type="string", example="agendada")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function store(StoreConsultaRequest $request): JsonResponse
    {
        try {
            $consulta = $this->consultaService->create($request->validated());

            return $this->helper->successJsonResponse('Consulta agendada com sucesso', $consulta, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->helper->errorJsonResponse($e->getMessage(), null, 422);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao agendar consulta', ['erro' => $e->getMessage(), 'dados' => $request->all()]);

            return $this->helper->errorJsonResponse('Erro interno ao agendar consulta', null, 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/consultas/{id}",
     *     summary="Atualiza uma consulta existente",
     *     description="Atualiza os dados de uma consulta específica",
     *     tags={"Consultas"},
     *     security={{ "bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da consulta",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data_hora", type="string", format="date-time", example="2024-02-01 14:30:00"),
     *             @OA\Property(property="status", type="string", example="realizada")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Consulta atualizada com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Consulta atualizada com sucesso"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="medico_id", type="integer", example=1),
     *                 @OA\Property(property="paciente_id", type="integer", example=1),
     *                 @OA\Property(property="data_hora", type="string", format="date-time", example="2024-02-01 14:30:00"),
     *                 @OA\Property(property="status", type="string", example="realizada")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Consulta não encontrada"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function update(UpdateConsultaRequest $request, int $id): JsonResponse
    {
        try {
            $consulta = $this->consultaService->update($id, $request->validated());

            return $this->helper->successJsonResponse('Consulta atualizada com sucesso', $consulta);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao atualizar consulta', ['erro' => $e->getMessage(), 'dados' => $request->all()]);

            return $this->helper->errorJsonResponse('Erro interno ao atualizar consulta', null, 500);
        }
    }
}
