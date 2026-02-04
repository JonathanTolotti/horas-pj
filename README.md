# Controle de Horas PJ

Sistema web para controle de horas trabalhadas e faturamento para profissionais PJ (Pessoa Juridica).

![Laravel](https://img.shields.io/badge/Laravel-12-red)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.x-38bdf8)

## Funcionalidades

- **Tracking de tempo** - Inicie e pare o contador com um clique, com persistencia no servidor
- **Lancamentos manuais** - Adicione horas trabalhadas informando data, horario de inicio/fim e descricao
- **Projetos** - Organize seus lancamentos por projeto
- **Dashboard** - Visualize total de horas, valor/hora e faturamento do mes
- **Filtro por mes** - Navegue entre meses para consultar historico
- **Divisao por CNPJ** - Visualize a divisao do faturamento entre multiplos CNPJs
- **Modo privacidade** - Oculte valores sensiveis com um clique
- **Dark mode** - Interface escura para conforto visual

## Requisitos

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL ou SQLite

## Instalacao

```bash
# Clone o repositorio
git clone https://github.com/seu-usuario/horas-pj.git
cd horas-pj

# Instale as dependencias PHP
composer install

# Instale as dependencias Node
npm install

# Copie o arquivo de configuracao
cp .env.example .env

# Gere a chave da aplicacao
php artisan key:generate

# Configure o banco de dados no .env e execute as migrations
php artisan migrate

# (Opcional) Popule com dados de exemplo
php artisan db:seed

# Compile os assets
npm run build

# Inicie o servidor
php artisan serve
```

## Configuracao

Edite o arquivo `.env` para configurar:

```env
# Valor da hora em reais
HOURLY_RATE=150

# Valor extra mensal fixo
EXTRA_VALUE=0

# CNPJs para divisao do faturamento
CNPJ_1_NAME="Empresa Alpha LTDA"
CNPJ_1_NUMBER="12.345.678/0001-90"

CNPJ_2_NAME="Empresa Beta ME"
CNPJ_2_NUMBER="98.765.432/0001-10"

CNPJ_3_NAME="Empresa Gamma EIRELI"
CNPJ_3_NUMBER="11.222.333/0001-44"
```

## Uso

1. Acesse o sistema e faca login
2. Use o botao **Iniciar Tracking** para comecar a contar o tempo automaticamente
3. Ou adicione lancamentos manualmente preenchendo data, horario e descricao
4. Visualize o resumo de horas e faturamento no dashboard
5. Use o filtro de mes para navegar entre periodos

## Stack

- **Backend:** Laravel 12
- **Frontend:** Blade + Tailwind CSS
- **JavaScript:** Vanilla JS
- **Banco de dados:** MySQL/SQLite
- **Autenticacao:** Laravel Breeze

## Licenca

Este projeto e privado e de uso pessoal.
