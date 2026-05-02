<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Documentação da API REST do Horas PJ. Integre seu sistema de controle de horas com outras ferramentas.">
    <title>API Reference — Horas PJ</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ file_exists(public_path('favicon.ico')) ? filemtime(public_path('favicon.ico')) : 1 }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #020817; color: #e2e8f0; }

        .sidebar { width: 260px; flex-shrink: 0; }
        .content { flex: 1; min-width: 0; }

        .nav-link { display: block; padding: 6px 12px; border-radius: 6px; font-size: 13px; color: #94a3b8; text-decoration: none; transition: all 0.15s; }
        .nav-link:hover { background: rgba(255,255,255,0.05); color: #e2e8f0; }
        .nav-link.active { background: rgba(34,211,238,0.1); color: #22d3ee; }

        .section { border-top: 1px solid rgba(255,255,255,0.06); padding-top: 2.5rem; margin-top: 2.5rem; }
        .section:first-child { border-top: none; padding-top: 0; margin-top: 0; }

        .endpoint { background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 10px; padding: 1.25rem; margin-top: 0.75rem; }
        .method { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; font-family: monospace; letter-spacing: 0.05em; }
        .method-get    { background: rgba(16,185,129,0.15); color: #34d399; }
        .method-post   { background: rgba(59,130,246,0.15); color: #60a5fa; }
        .method-put    { background: rgba(245,158,11,0.15); color: #fbbf24; }
        .method-delete { background: rgba(239,68,68,0.15); color: #f87171; }

        .path { font-family: monospace; font-size: 14px; color: #e2e8f0; }
        .badge-premium { display: inline-block; padding: 1px 7px; background: rgba(245,158,11,0.15); color: #fbbf24; border-radius: 4px; font-size: 10px; font-weight: 600; letter-spacing: 0.05em; }
        .badge-free    { display: inline-block; padding: 1px 7px; background: rgba(16,185,129,0.12); color: #6ee7b7; border-radius: 4px; font-size: 10px; font-weight: 600; letter-spacing: 0.05em; }

        code { font-family: 'SF Mono', 'Fira Code', monospace; }
        .code-block { background: #0f172a; border: 1px solid rgba(255,255,255,0.08); border-radius: 8px; padding: 1rem; font-size: 13px; font-family: monospace; overflow-x: auto; color: #94a3b8; line-height: 1.6; }
        .code-block .key { color: #60a5fa; }
        .code-block .string { color: #34d399; }
        .code-block .number { color: #f59e0b; }
        .code-block .comment { color: #475569; }

        .param-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .param-table th { text-align: left; padding: 8px 12px; color: #64748b; font-weight: 500; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .param-table td { padding: 8px 12px; border-bottom: 1px solid rgba(255,255,255,0.04); vertical-align: top; }
        .param-table tr:last-child td { border-bottom: none; }
        .param-name { color: #22d3ee; font-family: monospace; }
        .param-type { color: #60a5fa; font-size: 11px; }
        .param-required { color: #f87171; font-size: 10px; font-weight: 600; }
        .param-optional { color: #475569; font-size: 10px; }

        h1 { font-size: 2rem; font-weight: 800; color: white; }
        h2 { font-size: 1.25rem; font-weight: 700; color: white; }
        h3 { font-size: 1rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem; }

        .toc-group { font-size: 11px; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.08em; padding: 12px 12px 4px; }
        .highlight { background: linear-gradient(135deg, rgba(34,211,238,0.07), rgba(59,130,246,0.1)); border: 1px solid rgba(34,211,238,0.15); border-radius: 8px; padding: 1rem; }

        @media (max-width: 768px) {
            .sidebar { display: none; }
        }
    </style>
</head>
<body>

<header style="border-bottom: 1px solid rgba(255,255,255,0.06); background: rgba(2,8,23,0.95); backdrop-filter: blur(12px); position: sticky; top: 0; z-index: 50;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; height: 56px; display: flex; align-items: center; justify-content: space-between;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <a href="{{ route('landing') }}" style="display: flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                <img src="{{ asset('images/logo.png?v=' . (file_exists(public_path('images/logo.png')) ? filemtime(public_path('images/logo.png')) : 1)) }}" alt="Horas PJ" style="height: 32px; width: auto;">
            </a>
            <span style="color: rgba(255,255,255,0.2);">|</span>
            <span style="font-size: 14px; font-weight: 600; color: #22d3ee;">API Reference</span>
        </div>
        <div style="display: flex; align-items: center; gap: 1rem;">
            @auth
                <a href="{{ route('dashboard') }}" style="font-size: 13px; color: #64748b; text-decoration: none; hover: color: #94a3b8;">Dashboard</a>
            @else
                <a href="{{ route('login') }}" style="font-size: 13px; color: #64748b; text-decoration: none;">Entrar</a>
                <a href="{{ route('register') }}" style="font-size: 13px; color: #22d3ee; text-decoration: none; background: rgba(34,211,238,0.1); padding: 6px 14px; border-radius: 6px;">Criar conta</a>
            @endauth
        </div>
    </div>
</header>

<div style="max-width: 1200px; margin: 0 auto; padding: 2rem 1.5rem; display: flex; gap: 3rem;">

    <!-- Sidebar / TOC -->
    <aside class="sidebar" style="position: sticky; top: 72px; max-height: calc(100vh - 72px); overflow-y: auto; padding-bottom: 2rem;">
        <div class="toc-group">Introdução</div>
        <a href="#autenticacao" class="nav-link">Autenticação</a>
        <a href="#erros" class="nav-link">Erros</a>
        <a href="#rate-limiting" class="nav-link">Rate Limiting</a>
        <div class="toc-group" style="margin-top: 8px;">Endpoints</div>
        <a href="#me" class="nav-link">Usuário</a>
        <a href="#time-entries" class="nav-link">Lançamentos</a>
        <a href="#projects" class="nav-link">Projetos</a>
        <a href="#companies" class="nav-link">Empresas</a>
        <a href="#settings" class="nav-link">Configurações</a>
        <a href="#tracking" class="nav-link">Tracking</a>
        <a href="#on-call" class="nav-link">Sobreaviso <span class="badge-premium">Premium</span></a>
    </aside>

    <!-- Conteúdo principal -->
    <main class="content" style="max-width: 820px;">

        <div>
            <div style="display: inline-block; padding: 4px 12px; background: rgba(34,211,238,0.1); border: 1px solid rgba(34,211,238,0.2); border-radius: 20px; font-size: 12px; color: #22d3ee; font-weight: 600; margin-bottom: 1rem;">v1</div>
            <h1>API Reference</h1>
            <p style="color: #64748b; font-size: 16px; margin-top: 0.75rem; line-height: 1.7;">
                A API REST do Horas PJ permite integrar seu sistema de controle de horas com outras ferramentas, automatizar lançamentos e consultar dados.
            </p>
            <div style="margin-top: 1.25rem; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 1rem;">
                <span style="font-weight: 600; color: #94a3b8; font-size: 13px;">URL Base:</span>
                <code style="margin-left: 8px; color: #22d3ee; font-size: 14px;">{{ config('app.url') }}/api/v1</code>
            </div>
        </div>

        <!-- AUTENTICAÇÃO -->
        <div class="section" id="autenticacao">
            <h2>Autenticação</h2>
            <p style="color: #64748b; font-size: 14px; margin-top: 0.5rem; line-height: 1.7;">
                A API utiliza <strong style="color: #94a3b8;">Bearer Token</strong>. Envie o token em todas as requisições via header <code style="color: #22d3ee;">Authorization</code>.
            </p>

            <h3 style="margin-top: 1.5rem;">Como gerar um token</h3>
            <ol style="color: #64748b; font-size: 14px; line-height: 2; padding-left: 1.25rem;">
                <li>Acesse <a href="{{ route('settings') }}" style="color: #22d3ee;">Configurações</a> → seção <strong style="color: #94a3b8;">Tokens de API</strong></li>
                <li>Digite um nome para identificar o token (ex: "Zapier", "Script Python")</li>
                <li>Clique em <strong style="color: #94a3b8;">Gerar Token</strong></li>
                <li>Copie o token exibido — ele <strong style="color: #f87171;">não será exibido novamente</strong></li>
            </ol>

            <h3 style="margin-top: 1.25rem;">Usando o token</h3>
            <div class="code-block">
<span class="comment"># Exemplo curl</span>
curl -X GET {{ config('app.url') }}/api/v1/me \
  -H <span class="string">"Authorization: Bearer SEU_TOKEN_AQUI"</span> \
  -H <span class="string">"Accept: application/json"</span>
            </div>
        </div>

        <!-- ERROS -->
        <div class="section" id="erros">
            <h2>Erros</h2>
            <p style="color: #64748b; font-size: 14px; margin-top: 0.5rem; line-height: 1.7;">
                Todos os erros retornam JSON com o campo <code style="color: #22d3ee;">message</code>. Erros de validação incluem o campo <code style="color: #22d3ee;">errors</code>.
            </p>
            <div style="overflow-x: auto; margin-top: 1rem;">
                <table class="param-table">
                    <thead><tr>
                        <th>Código</th><th>Significado</th>
                    </tr></thead>
                    <tbody>
                        <tr><td><code class="param-name">401</code></td><td style="color: #64748b;">Token ausente ou inválido</td></tr>
                        <tr><td><code class="param-name">403</code></td><td style="color: #64748b;">Sem permissão (ex: recurso Premium)</td></tr>
                        <tr><td><code class="param-name">404</code></td><td style="color: #64748b;">Recurso não encontrado</td></tr>
                        <tr><td><code class="param-name">422</code></td><td style="color: #64748b;">Erro de validação — veja o campo <code>errors</code></td></tr>
                        <tr><td><code class="param-name">429</code></td><td style="color: #64748b;">Rate limit excedido</td></tr>
                        <tr><td><code class="param-name">500</code></td><td style="color: #64748b;">Erro interno do servidor</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="code-block" style="margin-top: 1rem;">
<span class="comment">// Exemplo de erro de validação (422)</span>
{
  <span class="key">"message"</span>: <span class="string">"The date field is required."</span>,
  <span class="key">"errors"</span>: {
    <span class="key">"date"</span>: [<span class="string">"The date field is required."</span>],
    <span class="key">"start_time"</span>: [<span class="string">"The start time field is required."</span>]
  }
}
            </div>
        </div>

        <!-- RATE LIMITING -->
        <div class="section" id="rate-limiting">
            <h2>Rate Limiting</h2>
            <p style="color: #64748b; font-size: 14px; margin-top: 0.5rem; line-height: 1.7;">
                Limite de <strong style="color: #94a3b8;">60 requisições por minuto</strong> por token. Quando excedido, a API retorna <code style="color: #f87171;">HTTP 429</code>.
                Os headers <code style="color: #22d3ee;">X-RateLimit-Limit</code> e <code style="color: #22d3ee;">X-RateLimit-Remaining</code> indicam os limites em cada resposta.
            </p>
        </div>

        <!-- ME -->
        <div class="section" id="me">
            <h2>Usuário</h2>
            <p style="color: #64748b; font-size: 14px; margin-top: 0.5rem;">Retorna os dados do usuário autenticado.</p>

            <div class="endpoint">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="method method-get">GET</span>
                    <span class="path">/api/v1/me</span>
                    <span class="badge-free">Grátis</span>
                </div>
                <div class="code-block" style="margin-top: 1rem;">
<span class="comment">// Resposta 200</span>
{
  <span class="key">"data"</span>: {
    <span class="key">"id"</span>: <span class="number">1</span>,
    <span class="key">"name"</span>: <span class="string">"João Silva"</span>,
    <span class="key">"email"</span>: <span class="string">"joao@exemplo.com"</span>,
    <span class="key">"is_premium"</span>: <span class="number">true</span>,
    <span class="key">"plan"</span>: <span class="string">"premium"</span>
  }
}
                </div>
            </div>
        </div>

        <!-- LANÇAMENTOS -->
        <div class="section" id="time-entries">
            <h2>Lançamentos de Horas</h2>

            <div class="endpoint">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="method method-get">GET</span>
                    <span class="path">/api/v1/time-entries</span>
                    <span class="badge-free">Grátis</span>
                </div>
                <p style="color: #64748b; font-size: 13px; margin-top: 0.75rem;">Lista os lançamentos do mês. Paginado em 50 por página.</p>
                <h3 style="margin-top: 1rem;">Parâmetros de query</h3>
                <table class="param-table">
                    <thead><tr><th>Parâmetro</th><th>Tipo</th><th>Descrição</th></tr></thead>
                    <tbody>
                        <tr>
                            <td><span class="param-name">month</span></td>
                            <td><span class="param-type">string</span> <span class="param-optional">opcional</span></td>
                            <td style="color: #64748b;">Mês no formato <code>YYYY-MM</code>. Padrão: mês atual.</td>
                        </tr>
                        <tr>
                            <td><span class="param-name">page</span></td>
                            <td><span class="param-type">integer</span> <span class="param-optional">opcional</span></td>
                            <td style="color: #64748b;">Número da página. Padrão: 1.</td>
                        </tr>
                    </tbody>
                </table>
                <div class="code-block" style="margin-top: 1rem;">
<span class="comment">// Resposta 200</span>
{
  <span class="key">"data"</span>: [
    {
      <span class="key">"id"</span>: <span class="number">42</span>,
      <span class="key">"date"</span>: <span class="string">"2026-05-02"</span>,
      <span class="key">"start_time"</span>: <span class="string">"09:00"</span>,
      <span class="key">"end_time"</span>: <span class="string">"12:00"</span>,
      <span class="key">"hours"</span>: <span class="number">3</span>,
      <span class="key">"description"</span>: <span class="string">"Desenvolvimento de features"</span>,
      <span class="key">"project"</span>: { <span class="key">"id"</span>: <span class="number">1</span>, <span class="key">"name"</span>: <span class="string">"Projeto Alpha"</span> }
    }
  ],
  <span class="key">"meta"</span>: { <span class="key">"current_page"</span>: <span class="number">1</span>, <span class="key">"last_page"</span>: <span class="number">1</span>, <span class="key">"total"</span>: <span class="number">15</span>, <span class="key">"month"</span>: <span class="string">"2026-05"</span> }
}
                </div>
            </div>

            <div class="endpoint">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="method method-get">GET</span>
                    <span class="path">/api/v1/time-entries/stats</span>
                    <span class="badge-free">Grátis</span>
                </div>
                <p style="color: #64748b; font-size: 13px; margin-top: 0.75rem;">Retorna as estatísticas mensais (horas, receita, etc.).</p>
                <table class="param-table" style="margin-top: 1rem;">
                    <thead><tr><th>Parâmetro</th><th>Tipo</th><th>Descrição</th></tr></thead>
                    <tbody>
                        <tr>
                            <td><span class="param-name">month</span></td>
                            <td><span class="param-type">string</span> <span class="param-optional">opcional</span></td>
                            <td style="color: #64748b;">Mês no formato <code>YYYY-MM</code>. Padrão: mês atual.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="endpoint">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="method method-post">POST</span>
                    <span class="path">/api/v1/time-entries</span>
                    <span class="badge-free">Grátis</span>
                </div>
                <p style="color: #64748b; font-size: 13px; margin-top: 0.75rem;">Cria um novo lançamento de horas.</p>
                <h3 style="margin-top: 1rem;">Body (JSON)</h3>
                <table class="param-table">
                    <thead><tr><th>Campo</th><th>Tipo</th><th>Descrição</th></tr></thead>
                    <tbody>
                        <tr><td><span class="param-name">date</span></td><td><span class="param-type">string</span> <span class="param-required">obrigatório</span></td><td style="color: #64748b;">Data no formato <code>YYYY-MM-DD</code></td></tr>
                        <tr><td><span class="param-name">start_time</span></td><td><span class="param-type">string</span> <span class="param-required">obrigatório</span></td><td style="color: #64748b;">Hora de início <code>HH:MM</code></td></tr>
                        <tr><td><span class="param-name">end_time</span></td><td><span class="param-type">string</span> <span class="param-required">obrigatório</span></td><td style="color: #64748b;">Hora de fim <code>HH:MM</code></td></tr>
                        <tr><td><span class="param-name">project_id</span></td><td><span class="param-type">integer</span> <span class="param-optional">opcional</span></td><td style="color: #64748b;">ID do projeto</td></tr>
                        <tr><td><span class="param-name">description</span></td><td><span class="param-type">string</span> <span class="param-optional">opcional</span></td><td style="color: #64748b;">Descrição do trabalho</td></tr>
                    </tbody>
                </table>
                <div class="code-block" style="margin-top: 1rem;">
<span class="comment">// Exemplo</span>
{
  <span class="key">"date"</span>: <span class="string">"2026-05-02"</span>,
  <span class="key">"start_time"</span>: <span class="string">"09:00"</span>,
  <span class="key">"end_time"</span>: <span class="string">"12:30"</span>,
  <span class="key">"project_id"</span>: <span class="number">1</span>,
  <span class="key">"description"</span>: <span class="string">"Desenvolvimento de features"</span>
}
                </div>
            </div>

            <div class="endpoint">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="method method-delete">DELETE</span>
                    <span class="path">/api/v1/time-entries/{id}</span>
                    <span class="badge-free">Grátis</span>
                </div>
                <p style="color: #64748b; font-size: 13px; margin-top: 0.75rem;">Exclui um lançamento. Retorna <code>HTTP 200</code> com mensagem de confirmação.</p>
            </div>
        </div>

        <!-- PROJETOS -->
        <div class="section" id="projects">
            <h2>Projetos</h2>

            <div class="endpoint">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="method method-get">GET</span>
                    <span class="path">/api/v1/projects</span>
                    <span class="badge-free">Grátis</span>
                </div>
                <p style="color: #64748b; font-size: 13px; margin-top: 0.75rem;">Lista os projetos ativos do usuário.</p>
            </div>

            <div class="endpoint">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="method method-post">POST</span>
                    <span class="path">/api/v1/projects</span>
                    <span class="badge-free">Grátis</span>
                </div>
                <p style="color: #64748b; font-size: 13px; margin-top: 0.75rem;">Cria um novo projeto.</p>
                <table class="param-table" style="margin-top: 1rem;">
                    <thead><tr><th>Campo</th><th>Tipo</th><th>Descrição</th></tr></thead>
                    <tbody>
                        <tr><td><span class="param-name">name</span></td><td><span class="param-type">string</span> <span class="param-required">obrigatório</span></td><td style="color: #64748b;">Nome do projeto</td></tr>
                        <tr><td><span class="param-name">active</span></td><td><span class="param-type">boolean</span> <span class="param-optional">opcional</span></td><td style="color: #64748b;">Padrão: <code>true</code></td></tr>
                        <tr><td><span class="param-name">is_default</span></td><td><span class="param-type">boolean</span> <span class="param-optional">opcional</span></td><td style="color: #64748b;">Projeto padrão para tracking automático</td></tr>
                        <tr><td><span class="param-name">default_description</span></td><td><span class="param-type">string</span> <span class="param-optional">opcional</span></td><td style="color: #64748b;">Descrição padrão ao salvar tracking</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="endpoint">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="method method-put">PUT</span>
                    <span class="path">/api/v1/projects/{id}</span>
                    <span class="badge-free">Grátis</span>
                </div>
                <p style="color: #64748b; font-size: 13px; margin-top: 0.75rem;">Atualiza um projeto. Mesmos campos do POST.</p>
            </div>

            <div class="endpoint">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="method method-delete">DELETE</span>
                    <span class="path">/api/v1/projects/{id}</span>
                    <span class="badge-free">Grátis</span>
                </div>
                <p style="color: #64748b; font-size: 13px; margin-top: 0.75rem;">Exclui um projeto.</p>
            </div>
        </div>

        <!-- EMPRESAS -->
        <div class="section" id="companies">
            <h2>Empresas</h2>

            <div class="endpoint">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="method method-get">GET</span>
                    <span class="path">/api/v1/companies</span>
                    <span class="badge-free">Grátis</span>
                </div>
                <p style="color: #64748b; font-size: 13px; margin-top: 0.75rem;">Lista as empresas do usuário.</p>
            </div>

            <div class="endpoint">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="method method-get">GET</span>
                    <span class="path">/api/v1/companies/{id}</span>
                    <span class="badge-free">Grátis</span>
                </div>
                <p style="color: #64748b; font-size: 13px; margin-top: 0.75rem;">Retorna os dados de uma empresa específica, incluindo endereço e responsável.</p>
            </div>

            <div class="endpoint">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="method method-post">POST</span>
                    <span class="path">/api/v1/companies</span>
                    <span class="badge-free">Grátis</span>
                </div>
                <p style="color: #64748b; font-size: 13px; margin-top: 0.75rem;">Cria uma nova empresa.</p>
                <table class="param-table" style="margin-top: 1rem;">
                    <thead><tr><th>Campo</th><th>Tipo</th><th>Descrição</th></tr></thead>
                    <tbody>
                        <tr><td><span class="param-name">name</span></td><td><span class="param-type">string</span> <span class="param-required">obrigatório</span></td><td style="color: #64748b;">Nome fantasia</td></tr>
                        <tr><td><span class="param-name">cnpj</span></td><td><span class="param-type">string</span> <span class="param-required">obrigatório</span></td><td style="color: #64748b;">CNPJ no formato <code>XX.XXX.XXX/XXXX-XX</code></td></tr>
                        <tr><td><span class="param-name">active</span></td><td><span class="param-type">boolean</span> <span class="param-optional">opcional</span></td><td style="color: #64748b;">Padrão: <code>true</code></td></tr>
                        <tr><td><span class="param-name">razao_social</span></td><td><span class="param-type">string</span> <span class="param-optional">opcional</span></td><td style="color: #64748b;">Razão social</td></tr>
                        <tr><td><span class="param-name">email</span></td><td><span class="param-type">string</span> <span class="param-optional">opcional</span></td><td style="color: #64748b;">E-mail de contato</td></tr>
                        <tr><td><span class="param-name">telefone</span></td><td><span class="param-type">string</span> <span class="param-optional">opcional</span></td><td style="color: #64748b;">Telefone</td></tr>
                        <tr><td><span class="param-name">cep / logradouro / numero / bairro / cidade / uf</span></td><td><span class="param-type">string</span> <span class="param-optional">opcional</span></td><td style="color: #64748b;">Endereço</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="endpoint">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="method method-put">PUT</span>
                    <span class="path">/api/v1/companies/{id}</span>
                    <span class="badge-free">Grátis</span>
                </div>
                <p style="color: #64748b; font-size: 13px; margin-top: 0.75rem;">Atualiza uma empresa. Mesmos campos do POST.</p>
            </div>

            <div class="endpoint">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="method method-delete">DELETE</span>
                    <span class="path">/api/v1/companies/{id}</span>
                    <span class="badge-free">Grátis</span>
                </div>
                <p style="color: #64748b; font-size: 13px; margin-top: 0.75rem;">Exclui uma empresa.</p>
            </div>
        </div>

        <!-- CONFIGURAÇÕES -->
        <div class="section" id="settings">
            <h2>Configurações</h2>

            <div class="endpoint">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="method method-get">GET</span>
                    <span class="path">/api/v1/settings</span>
                    <span class="badge-free">Grátis</span>
                </div>
                <p style="color: #64748b; font-size: 13px; margin-top: 0.75rem;">Retorna as configurações do usuário (valor/hora, extras, descontos, etc.).</p>
            </div>

            <div class="endpoint">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="method method-put">PUT</span>
                    <span class="path">/api/v1/settings</span>
                    <span class="badge-free">Grátis</span>
                </div>
                <p style="color: #64748b; font-size: 13px; margin-top: 0.75rem;">Atualiza as configurações do usuário.</p>
                <table class="param-table" style="margin-top: 1rem;">
                    <thead><tr><th>Campo</th><th>Tipo</th><th>Descrição</th></tr></thead>
                    <tbody>
                        <tr><td><span class="param-name">hourly_rate</span></td><td><span class="param-type">number</span> <span class="param-optional">opcional</span></td><td style="color: #64748b;">Valor por hora (R$)</td></tr>
                        <tr><td><span class="param-name">extra_value</span></td><td><span class="param-type">number</span> <span class="param-optional">opcional</span></td><td style="color: #64748b;">Valor extra fixo (R$)</td></tr>
                        <tr><td><span class="param-name">discount_value</span></td><td><span class="param-type">number</span> <span class="param-optional">opcional</span></td><td style="color: #64748b;">Desconto fixo (R$)</td></tr>
                        <tr><td><span class="param-name">on_call_hourly_rate</span></td><td><span class="param-type">number</span> <span class="param-optional">opcional</span></td><td style="color: #64748b;">Valor por hora de sobreaviso (R$)</td></tr>
                        <tr><td><span class="param-name">auto_save_tracking</span></td><td><span class="param-type">boolean</span> <span class="param-optional">opcional</span></td><td style="color: #64748b;">Salvar tracking automaticamente</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TRACKING -->
        <div class="section" id="tracking">
            <h2>Tracking</h2>
            <p style="color: #64748b; font-size: 14px; margin-top: 0.5rem;">Controle o tracking de horas em tempo real.</p>

            <div class="endpoint">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="method method-get">GET</span>
                    <span class="path">/api/v1/tracking</span>
                    <span class="badge-free">Grátis</span>
                </div>
                <p style="color: #64748b; font-size: 13px; margin-top: 0.75rem;">Retorna o status atual do tracking.</p>
                <div class="code-block" style="margin-top: 1rem;">
<span class="comment">// Resposta quando ativo</span>
{ <span class="key">"data"</span>: { <span class="key">"active"</span>: <span class="number">true</span>, <span class="key">"started_at"</span>: <span class="string">"2026-05-02T09:00:00Z"</span> } }

<span class="comment">// Resposta quando inativo</span>
{ <span class="key">"data"</span>: { <span class="key">"active"</span>: <span class="number">false</span>, <span class="key">"started_at"</span>: <span class="number">null</span> } }
                </div>
            </div>

            <div class="endpoint">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="method method-post">POST</span>
                    <span class="path">/api/v1/tracking/start</span>
                    <span class="badge-free">Grátis</span>
                </div>
                <p style="color: #64748b; font-size: 13px; margin-top: 0.75rem;">Inicia o tracking. Retorna <code>HTTP 409</code> se já existe um tracking ativo.</p>
            </div>

            <div class="endpoint">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="method method-post">POST</span>
                    <span class="path">/api/v1/tracking/stop</span>
                    <span class="badge-free">Grátis</span>
                </div>
                <p style="color: #64748b; font-size: 13px; margin-top: 0.75rem;">Para o tracking e retorna as horas trabalhadas. Não salva o lançamento automaticamente — use <code>POST /time-entries</code> para registrar.</p>
                <div class="code-block" style="margin-top: 1rem;">
<span class="comment">// Resposta 200</span>
{
  <span class="key">"data"</span>: {
    <span class="key">"started_at"</span>: <span class="string">"2026-05-02T09:00:00Z"</span>,
    <span class="key">"stopped_at"</span>: <span class="string">"2026-05-02T12:30:00Z"</span>,
    <span class="key">"hours"</span>: <span class="number">3.5</span>
  }
}
                </div>
            </div>
        </div>

        <!-- ON-CALL -->
        <div class="section" id="on-call">
            <h2>Sobreaviso <span class="badge-premium" style="vertical-align: middle;">Premium</span></h2>
            <p style="color: #64748b; font-size: 14px; margin-top: 0.5rem;">Gerencie períodos de sobreaviso. Requer assinatura <strong style="color: #fbbf24;">Premium</strong>.</p>

            <div class="endpoint">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="method method-get">GET</span>
                    <span class="path">/api/v1/on-call</span>
                    <span class="badge-premium">Premium</span>
                </div>
                <p style="color: #64748b; font-size: 13px; margin-top: 0.75rem;">Lista os períodos de sobreaviso do mês.</p>
                <table class="param-table" style="margin-top: 1rem;">
                    <thead><tr><th>Parâmetro</th><th>Tipo</th><th>Descrição</th></tr></thead>
                    <tbody>
                        <tr><td><span class="param-name">month</span></td><td><span class="param-type">string</span> <span class="param-optional">opcional</span></td><td style="color: #64748b;">Mês no formato <code>YYYY-MM</code>. Padrão: mês atual.</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="endpoint">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="method method-get">GET</span>
                    <span class="path">/api/v1/on-call/stats</span>
                    <span class="badge-premium">Premium</span>
                </div>
                <p style="color: #64748b; font-size: 13px; margin-top: 0.75rem;">Retorna estatísticas de sobreaviso do mês.</p>
            </div>

            <div class="endpoint">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="method method-post">POST</span>
                    <span class="path">/api/v1/on-call</span>
                    <span class="badge-premium">Premium</span>
                </div>
                <p style="color: #64748b; font-size: 13px; margin-top: 0.75rem;">Cria um período de sobreaviso.</p>
                <table class="param-table" style="margin-top: 1rem;">
                    <thead><tr><th>Campo</th><th>Tipo</th><th>Descrição</th></tr></thead>
                    <tbody>
                        <tr><td><span class="param-name">start_datetime</span></td><td><span class="param-type">datetime</span> <span class="param-required">obrigatório</span></td><td style="color: #64748b;">Início do sobreaviso <code>YYYY-MM-DD HH:MM</code></td></tr>
                        <tr><td><span class="param-name">end_datetime</span></td><td><span class="param-type">datetime</span> <span class="param-required">obrigatório</span></td><td style="color: #64748b;">Fim do sobreaviso <code>YYYY-MM-DD HH:MM</code></td></tr>
                        <tr><td><span class="param-name">hourly_rate</span></td><td><span class="param-type">number</span> <span class="param-required">obrigatório</span></td><td style="color: #64748b;">Valor por hora de sobreaviso (R$)</td></tr>
                        <tr><td><span class="param-name">project_id</span></td><td><span class="param-type">integer</span> <span class="param-optional">opcional</span></td><td style="color: #64748b;">ID do projeto vinculado</td></tr>
                        <tr><td><span class="param-name">description</span></td><td><span class="param-type">string</span> <span class="param-optional">opcional</span></td><td style="color: #64748b;">Descrição</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="endpoint">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="method method-put">PUT</span>
                    <span class="path">/api/v1/on-call/{id}</span>
                    <span class="badge-premium">Premium</span>
                </div>
                <p style="color: #64748b; font-size: 13px; margin-top: 0.75rem;">Atualiza um período de sobreaviso. Mesmos campos do POST.</p>
            </div>

            <div class="endpoint">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="method method-delete">DELETE</span>
                    <span class="path">/api/v1/on-call/{id}</span>
                    <span class="badge-premium">Premium</span>
                </div>
                <p style="color: #64748b; font-size: 13px; margin-top: 0.75rem;">Exclui um período de sobreaviso.</p>
            </div>
        </div>

        <!-- Footer -->
        <div style="margin-top: 4rem; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.06); text-align: center;">
            <p style="color: #334155; font-size: 13px;">© {{ date('Y') }} Horas PJ · <a href="{{ route('landing') }}" style="color: #475569; text-decoration: none;">horas-pj.com</a></p>
        </div>

    </main>
</div>

<script>
    // Highlight active nav link on scroll
    const sections = document.querySelectorAll('[id]');
    const navLinks = document.querySelectorAll('.nav-link[href^="#"]');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === '#' + entry.target.id) {
                        link.classList.add('active');
                    }
                });
            }
        });
    }, { rootMargin: '-20% 0px -70% 0px' });

    sections.forEach(section => observer.observe(section));
</script>

</body>
</html>
