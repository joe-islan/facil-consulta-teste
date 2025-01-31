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

    public function getAuthenticatedUser(): JsonResponse
    {
        return $this->helper->successJsonResponse('Usuário autenticado', $this->authService->getUser());
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return $this->helper->successJsonResponse('Logout realizado com sucesso');
    }

    public function refresh(): JsonResponse
    {
        return $this->helper->successJsonResponse('Token atualizado', $this->authService->refresh());
    }
}
