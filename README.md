# API de Reserva de Salões

API REST desenvolvida em Laravel para gestão de reservas de salões/salas.

## Sobre o Projeto

Este projeto permite gerir salas e realizar reservas com verificação de conflitos de horário.  
Foi desenvolvido com foco em organização, validação e segurança.

### Funcionalidades

- Autenticação de utilizadores (registo, login e logout)
- Gestão de salas (criar, listar, atualizar, ativar e desativar)
- Sistema de reservas com verificação de disponibilidade
- Cancelamento de reservas
- Controlo de acesso com Policies
- Validação de dados com Form Requests
- Respostas formatadas com API Resources

## Tecnologias

- PHP 8.3+
- Laravel 13
- Laravel Sanctum (autenticação)
- MySQL
- Pest (testes)

## Estrutura do Projeto

O projeto segue uma organização clara:

- **Controllers** → Lógica de controlo das rotas
- **Form Requests** → Validação de dados de entrada
- **Models** → Representação das entidades (User, Room, Reserve)
- **Resources** → Formatação das respostas da API
- **Policies** → Controlo de autorização
- **Migrations & Factories** → Base de dados e dados de teste

## Instalação

```bash
# Clonar o repositório
git clone https://github.com/teu-usuario/nome-do-repositorio.git

# Entrar na pasta
cd nome-do-repositorio

# Instalar dependências
composer install

# Configurar o ambiente
cp .env.example .env
php artisan key:generate

# Configurar a base de dados no ficheiro .env
# Depois executar:
php artisan migrate

# (Opcional) Popular a base de dados
php artisan db:seed
