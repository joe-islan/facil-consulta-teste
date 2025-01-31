<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ControllerHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Log\Logger;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService,
        private Logger $logger,
        private ControllerHelper $helper,
    ) {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/register",
     *     summary="Registra um novo usuário",
     *     tags={"Autenticação"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *
     *             @OA\Property(property="name", type="string", example="João Silva"),
     *             @OA\Property(property="email", type="string", format="email", example="joao@exemplo.com"),
     *             @OA\Property(property="password", type="string", format="password", example="senha123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="senha123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Usuário registrado com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Usuário cadastrado com sucesso"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="João Silva"),
     *                     @OA\Property(property="email", type="string", example="joao@exemplo.com")
     *                 ),
     *                 @OA\Property(property="authorization", type="object",
     *                     @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *                     @OA\Property(property="type", type="string", example="bearer")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *     )
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $data = $this->authService->register($request->validated());
            $this->logger->info('Novo usuário registrado', ['user' => $data['user']]);

            return $this->helper->successJsonResponse('Usuário cadastrado com sucesso', [
                'user' => $data['user'],
                'authorization' => [
                    'token' => $data['token'],
                    'type' => 'bearer',
                ],
            ], 201);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao registrar usuário', [
                'erro' => $e->getMessage(),
                'dados' => $request->all(),
            ]);

            return $this->helper->errorJsonResponse('Erro interno ao cadastrar usuário', null, 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     summary="Realiza login do usuário",
     *     tags={"Autenticação"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email","password"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="joao@exemplo.com"),
     *             @OA\Property(property="password", type="string", format="password", example="senha123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Login realizado com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login realizado com sucesso"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *                 @OA\Property(property="token_type", type="string", example="bearer"),
     *                 @OA\Property(property="expires_in", type="integer", example=3600)
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
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $tokenData = $this->authService->login($request->validated());

            if (!$tokenData) {
                return $this->helper->errorJsonResponse('Não autorizado', null, 401);
            }

            return $this->helper->successJsonResponse('Login realizado com sucesso', $tokenData);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao realizar login', [
                'erro' => $e->getMessage(),
                'dados' => $request->all(),
            ]);

            return $this->helper->errorJsonResponse('Erro interno ao realizar login', null, 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/user",
     *     summary="Retorna os dados do usuário autenticado",
     *     tags={"Autenticação"},
     *     security={{ "bearerAuth": {} }},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Dados do usuário recuperados com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Usuário autenticado"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="João Silva"),
     *                 @OA\Property(property="email", type="string", example="joao@exemplo.com")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */
    public function getAuthenticatedUser(): JsonResponse
    {
        return $this->helper->successJsonResponse('Usuário autenticado', $this->authService->getUser());
    }

    /**
     * @OA\Post(
     *     path="/api/v1/logout",
     *     summary="Realiza logout do usuário",
     *     tags={"Autenticação"},
     *     security={{ "bearerAuth": {} }},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Logout realizado com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Logout realizado com sucesso")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */
    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return $this->helper->successJsonResponse('Logout realizado com sucesso');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/refresh",
     *     summary="Atualiza o token JWT",
     *     tags={"Autenticação"},
     *     security={{ "bearerAuth": {} }},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Token atualizado com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Token atualizado"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *                 @OA\Property(property="token_type", type="string", example="bearer"),
     *                 @OA\Property(property="expires_in", type="integer", example=3600)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */
    public function refresh(): JsonResponse
    {
        return $this->helper->successJsonResponse('Token atualizado', $this->authService->refresh());
    }
}
