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
 git clone https://github.com/seu-usuario/seu-repositorio.git
 cd seu-repositorio
```

### 🔧 Configurar Variáveis de Ambiente
```sh
 cp .env.example .env
```
Ajuste o arquivo `.env` conforme necessário.

### 🚀 Subir os Containers e Configurar o Projeto
```sh
 make install
```
Esse comando executa:
- **Sobe os containers do Laravel Sail**
- **Executa as migrações e seeds do banco de dados**
- **Gera a chave JWT necessária para autenticação**
- **Cria o link de armazenamento necessário**

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

## 📄 Licença
Este projeto está sob a licença MIT - veja o arquivo [LICENSE](LICENSE) para mais detalhes.

