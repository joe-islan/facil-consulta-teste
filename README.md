# 📌 API para Gestão de Consultas Médicas

Este repositório contém a implementação de uma API RESTful para gestão de consultas médicas, desenvolvida em **Laravel 10** e utilizando **Laravel Sail** para um ambiente Dockerizado.

---

## 🚀 Tecnologias Utilizadas

- **Laravel 10** (Framework PHP)
- **MySQL 8** (Banco de Dados)
- **Laravel Sail** (Ambiente Docker)
- **JWT Auth** (Autenticação via Token JWT)
- **PHP CS Fixer, PHPStan e PHPMD** (Ferramentas de Qualidade de Código)
- **PHP 8.3**

---

## 📦 Instalação e Configuração do Projeto

### 🔧 Pré-requisitos
Antes de começar, certifique-se de ter instalado:
- **Docker** e **Docker Compose**
- **Make** (caso queira utilizar atalhos para os comandos do Laravel Sail)

### 📥 Clonar o Repositório
```sh
 git clone https://github.com/joe-islan/facil-consulta-teste.git
 cd facil-consulta-teste
```

### 🚀 Subir os Containers e Configurar o Projeto

1. Instalar as dependências via Composer

Se você já tem o Composer instalado localmente:
```sh
 composer install
```
Caso prefira usar o Laravel Sail, primeiro copie o arquivo .env:

```sh
cp .env.example .env
```
Inicie o container e instale as dependências:

```sh
./vendor/bin/sail up -d
./vendor/bin/sail composer install
```

#### Configurar variáveis de ambiente (.env)
Abra o arquivo .env e configure as variáveis do banco de dados:

DB_CONNECTION

DB_HOST

DB_PORT

DB_DATABASE

DB_USERNAME

DB_PASSWORD

#### Gerar chave da aplicação:

```sh
./vendor/bin/sail artisan key:generate
 ```
Gerar chave JWT para autenticação:

```sh
./vendor/bin/sail artisan jwt:secret --force
 ```
Executar migrações e seeds:
```sh
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
```

Se quiser executar os teste, rode o banco de teste e Execute as migrações e seeds:
```sh
./vendor/bin/sail artisan migrate --env=testing --seed
```


---

## 🎯 Endpoints da API

### 🔐 **Autenticação**
| Método | Rota            | Descrição                         |
|--------|----------------|---------------------------------|
| POST   | `/api/v1/login`    | Login e geração de token JWT  |
| POST   | `/api/v1/register` | Cadastro de usuário          |
| POST   | `/api/v1/logout`   | Logout                        |
| POST   | `/api/v1/refresh`  | Refresh do Token JWT         |
| GET    | `/api/v1/user`     | Retorna usuário autenticado  |

### 📍 **Cidades e Médicos**
| Método | Rota                                | Descrição                              |
|--------|-------------------------------------|--------------------------------------|
| GET    | `/api/v1/cidades`                  | Listar cidades                      |
| GET    | `/api/v1/medicos`                  | Listar médicos                      |
| GET    | `/api/v1/cidades/{id}/medicos`     | Médicos de uma cidade específica   |
| POST   | `/api/v1/medicos`                  | Cadastrar médico (requer login)     |
| GET    | `/api/v1/medicos/{id}/pacientes`   | Listar pacientes do médico (protegido) |

### 🏥 **Pacientes e Consultas**
| Método | Rota                          | Descrição                             |
|--------|--------------------------------|--------------------------------------|
| GET    | `/api/v1/pacientes`           | Listar pacientes (protegido)        |
| POST   | `/api/v1/pacientes`           | Cadastrar paciente (protegido)      |
| PUT    | `/api/v1/pacientes/{id}`      | Atualizar paciente (protegido)      |
| GET    | `/api/v1/consultas`           | Listar consultas (protegido)        |
| POST   | `/api/v1/medicos/consulta`    | Agendar consulta (protegido)        |
| PUT    | `/api/v1/consultas/{id}`      | Atualizar consulta (protegido)      |

---

## ✅ Testes Automatizados
Para rodar os testes:
```sh
 make test
```

---

## 🛠 Qualidade de Código

### 📌 **Corrigir Código com PHP CS Fixer**
```sh
 make cs-fixer
```

### 📌 **Análise Estática com PHPStan**
```sh
 make phpstan
```

### 📌 **Análise de Qualidade com PHPMD**
```sh
 make phpmd
```

---

## 🔄 Outras Comandos Úteis

### 📌 Reiniciar Containers
```sh
 make restart
```

### 📌 Acessar o Shell do Container
```sh
 make shell
```

### 📌 Ver Logs da Aplicação
```sh
 make logs
```

### 📌 Derrubar os Containers
```sh
 make down
```

### 📌 Remover Containers e Volumes não utilizados
```sh
 make prune
```

---

## 🚀 Contribuição
1. **Fork** este repositório
2. Crie uma **branch** com sua feature (`git checkout -b minha-feature`)
3. Commit suas alterações (`git commit -m 'Minha feature'`)
4. Push para a branch (`git push origin minha-feature`)
5. Abra um **Pull Request**

---

## Considerações Técnicas

### 🔍 Padronização dos nomes de métodos
Para manter consistência no projeto, foi adotada a seguinte convenção:

Arquivos, rotas e classes permanecem em português, pois representam entidades do domínio (ex.: MedicoController, MedicoService).
Métodos seguem um padrão em inglês, garantindo legibilidade e alinhamento com boas práticas.

---

## 📄 Documentação da API
A documentação da API está disponível em [Swagger UI](http://localhost/api/documentation).


