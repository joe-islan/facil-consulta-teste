.PHONY: help install sail artisan test cs-fixer phpstan phpmd shell restart down prune logs

# Define o interpretador padrão
SHELL := /bin/bash

# Exibe os comandos disponíveis
help:
	@echo "Usage: make [comando]"
	@echo ""
	@echo "Comandos disponíveis:"
	@echo "  install       Instala dependências e configura ambiente"
	@echo "  sail         Executa comandos usando Laravel Sail"
	@echo "  artisan      Executa comandos Artisan dentro do container"
	@echo "  test         Roda os testes automatizados"
	@echo "  cs-fixer     Corrige o código conforme padrões do PHP CS Fixer"
	@echo "  phpstan      Roda análise estática de código com PHPStan"
	@echo "  phpmd        Analisa código com PHP Mess Detector (PHPMD)"
	@echo "  shell        Entra no container com bash"
	@echo "  restart      Reinicia os containers"
	@echo "  down         Para e remove os containers"
	@echo "  prune        Remove imagens e volumes não utilizados"
	@echo "  logs         Exibe os logs do container Laravel"

# ✅ Instala dependências e configura ambiente
install:
	./vendor/bin/sail up -d
	./vendor/bin/sail artisan migrate --seed
	./vendor/bin/sail artisan storage:link
	./vendor/bin/sail artisan jwt:secret --force
	./vendor/bin/sail artisan optimize

# 🚀 Atalho para rodar qualquer comando no Sail
sail:
	./vendor/bin/sail $(CMD)

# 🚀 Executa comandos Artisan dentro do container
artisan:
	./vendor/bin/sail artisan $(CMD)

# ✅ Roda os testes
test:
	./vendor/bin/sail artisan test

# 🛠 Correção de código com PHP CS Fixer
cs-fixer:
	./vendor/bin/sail php -d PHP_CS_FIXER_IGNORE_ENV=1 ./vendor/bin/php-cs-fixer fix

# 📌 Análise estática com PHPStan
phpstan:
	./vendor/bin/sail php ./vendor/bin/phpstan analyse --memory-limit=500M

# 🚨 Análise de qualidade do código com PHPMD
phpmd:
	./vendor/bin/sail php ./vendor/bin/phpmd app text phpmd.xml

# 🐚 Abre um shell dentro do container
shell:
	./vendor/bin/sail shell

# 🔄 Reinicia os containers
restart:
	./vendor/bin/sail down && ./vendor/bin/sail up -d

# ⏹ Para os containers
down:
	./vendor/bin/sail down

# 🧹 Remove imagens e volumes não utilizados
prune:
	./vendor/bin/sail down --rmi all --volumes --remove-orphans

# 📜 Exibe logs do Laravel
logs:
	./vendor/bin/sail logs -f
