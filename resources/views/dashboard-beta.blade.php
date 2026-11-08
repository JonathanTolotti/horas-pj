@php
    $hm = function ($hours) {
        $minutes = (int) round(((float) $hours) * 60);
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    };
    $brl = fn ($value) => 'R$ ' . number_format((float) $value, 2, ',', '.');

    $totalHours   = (float) $stats['total_hours'];
    $hourlyRate   = (float) $stats['hourly_rate'];
    $extra        = (float) ($stats['extra_value'] ?? 0);
    $discount     = (float) ($stats['discount_value'] ?? 0);
    $onCall       = (float) ($stats['on_call_revenue'] ?? 0);
    $totalFinal   = (float) ($stats['total_final_with_on_call'] ?? $stats['total_final'] ?? $stats['total_with_extra']);
    // A receita por empresa já inclui o sobreaviso, então o percentual usa o total com sobreaviso
    $splitBase    = $totalFinal;

    // Barras "fantasma" de projeção nos dias úteis restantes do ciclo atual
    $projectedHours = ($isCurrentCycle && $projection && $projection['weekdays_worked'] > 0) ? (float) $projection['daily_average_hours'] : 0;
    $showGhost    = $projectedHours > 0 && collect($days)->contains('projected', true);

    $maxDayHours  = max(8, collect($days)->max('hours'), $projectedHours);
    $monthValues  = array_column($months, 'value');
    $monthIndex   = array_search($currentMonth, $monthValues, true);
    $newerMonth   = ($monthIndex !== false && $monthIndex > 0) ? $monthValues[$monthIndex - 1] : null;
    $olderMonth   = ($monthIndex !== false && $monthIndex < count($monthValues) - 1) ? $monthValues[$monthIndex + 1] : null;

    $companies = collect($stats['company_revenues'] ?? [])->filter(fn ($c) => $c['revenue'] > 0)->values();
    $unassigned = (float) ($stats['unassigned_revenue'] ?? 0);
    $splitColors = ['var(--c1)', 'var(--c2)', 'var(--c3)', 'var(--c4)', 'var(--c5)', 'var(--c6)'];

    $todayKey = now()->format('Y-m-d');
    $yesterdayKey = now()->subDay()->format('Y-m-d');
@endphp

<x-app-layout>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=bricolage-grotesque:400,500,600,700&display=swap" rel="stylesheet" />

    <style>
        .bt {
            --bg: #141922;
            --surface: #1b2130;
            --raised: #222a3b;
            --line: #2c3446;
            --ink: #e6eaf2;
            --muted: #929cb0;
            --faint: #5f6a80;
            --accent: #e0a93b;
            --accent-text: #ebbd60;
            --accent-ink: #1d1505;
            --accent-soft: rgba(224, 169, 59, .14);
            --money: #6cc59a;
            --danger: #f08a7e;
            --bar-empty: #252d3e;
            --weekend: rgba(255, 255, 255, .025);
            --field: #141922;
            --c1: #e0a93b; --c2: #6cc59a; --c3: #7aa7e8; --c4: #c793d9; --c5: #e58c6b; --c6: #8fb5b0;
            --oncall: #9aa6f2;
            --oncall-text: #b4bdf7;
            --oncall-soft: rgba(154, 166, 242, .13);
            color-scheme: dark;
        }
        html:not(.dark) .bt {
            --bg: #f3f5f8;
            --surface: #ffffff;
            --raised: #f6f7fa;
            --line: #dde2ea;
            --ink: #1a2130;
            --muted: #586274;
            --faint: #8a93a3;
            --accent: #d49a26;
            --accent-text: #8a5c00;
            --accent-ink: #241900;
            --accent-soft: rgba(212, 154, 38, .14);
            --money: #1d7a51;
            --danger: #b4382c;
            --bar-empty: #e6eaf0;
            --weekend: rgba(26, 33, 48, .03);
            --field: #ffffff;
            --c1: #d49a26; --c2: #2f9a68; --c3: #3f78c9; --c4: #9b5fb3; --c5: #cf6a45; --c6: #4f8f88;
            --oncall: #5b67d1;
            --oncall-text: #3f4ab0;
            --oncall-soft: rgba(91, 103, 209, .10);
            color-scheme: light;
        }

        .bt {
            background: var(--bg);
            color: var(--ink);
            min-height: calc(100vh - 4rem);
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            line-height: 1.5;
        }
        .bt [hidden] { display: none !important; }
        .bt .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }
        .bt .wrap { max-width: 1180px; margin: 0 auto; padding: 28px 16px 96px; }
        @media (min-width: 768px) { .bt .wrap { padding: 40px 32px 112px; } }

        .bt .display { font-family: 'Bricolage Grotesque', 'Inter', sans-serif; font-variant-numeric: tabular-nums; }
        .bt .num { font-variant-numeric: tabular-nums; }

        /* ---------- topo: ciclo ---------- */
        .bt .topbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; }
        .bt .cycle { display: flex; align-items: center; gap: 4px; }
        .bt .cycle a, .bt .cycle span.off {
            display: inline-grid; place-items: center; width: 34px; height: 34px; border-radius: 8px; color: var(--muted);
        }
        .bt .cycle a:hover { background: var(--raised); color: var(--ink); }
        .bt .cycle span.off { opacity: .35; }
        .bt .cycle select {
            appearance: none; background: transparent; border: 0; color: var(--ink); font: 600 15px/1 'Inter', sans-serif;
            padding: 8px 26px 8px 8px; border-radius: 8px; cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23929cb0' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 8px center;
        }
        .bt .cycle select:hover { background-color: var(--raised); }
        .bt .cycle select option { background: var(--surface); color: var(--ink); }
        .bt .classic { color: var(--muted); font-size: 14px; display: inline-flex; align-items: center; gap: 8px; }
        .bt .classic:hover { color: var(--ink); }
        .bt .beta-tag {
            font-size: 12px; font-weight: 600; color: var(--accent-text); background: var(--accent-soft);
            padding: 2px 8px; border-radius: 999px;
        }

        /* ---------- hero ---------- */
        .bt .hero { margin-top: 28px; }
        .bt .hero h1 {
            font-family: 'Bricolage Grotesque', 'Inter', sans-serif; font-variant-numeric: tabular-nums;
            font-weight: 500; font-size: clamp(28px, 4.6vw, 48px); line-height: 1.12; letter-spacing: -0.02em;
            max-width: 22ch; margin: 0;
        }
        .bt .hero h1 strong { font-weight: 700; }
        .bt .hero p { margin: 14px 0 0; color: var(--muted); max-width: 60ch; }

        /* ---------- faixa de dias ---------- */
        .bt .strip { margin-top: 32px; position: relative; }
        .bt .strip-grid { display: grid; grid-template-columns: repeat(var(--n), minmax(0, 1fr)); gap: 3px; position: relative; }
        @media (min-width: 640px) { .bt .strip-grid { gap: 5px; } }
        .bt .day { display: flex; flex-direction: column; align-items: stretch; gap: 8px; border-radius: 6px; padding: 4px 0 6px; text-decoration: none; color: inherit; }
        .bt .day.weekend { background: var(--weekend); }
        .bt a.day:hover .bar-fill { filter: brightness(1.12); }
        .bt a.day:hover .dnum { color: var(--ink); }
        .bt .bar { height: 112px; display: flex; align-items: flex-end; justify-content: center; }
        @media (min-width: 768px) { .bt .bar { height: 136px; } }
        .bt .bar-fill {
            width: 100%; max-width: 22px; border-radius: 3px 3px 2px 2px; background: var(--accent);
            transform-origin: bottom; animation: bt-grow .7s cubic-bezier(.2,.7,.2,1) both; animation-delay: calc(var(--i) * 18ms);
        }
        .bt .bar-empty { width: 100%; max-width: 22px; height: 3px; border-radius: 2px; background: var(--bar-empty); }
        .bt .bar-ghost {
            width: 100%; max-width: 22px; border-radius: 3px 3px 2px 2px; box-sizing: border-box;
            border: 1.5px dashed var(--accent); border-bottom: 0; background: var(--accent-soft); opacity: .85;
        }
        .bt .legend i.ghost-sw { border: 1.5px dashed var(--accent); background: var(--accent-soft); box-sizing: border-box; }

        /* ---------- projeção ---------- */
        .bt .projection {
            margin-top: 28px; padding: 20px; border: 1px solid var(--line); border-radius: 14px; background: var(--surface);
            display: grid; gap: 18px;
        }
        @media (min-width: 1024px) { .bt .projection { grid-template-columns: minmax(0, 1fr) minmax(0, 1.6fr); column-gap: 32px; align-items: center; } }
        .bt .proj-text h2 { margin-bottom: 6px; }
        .bt .proj-text p { margin: 0; color: var(--muted); max-width: 52ch; }
        .bt .proj-text strong { color: var(--ink); font-weight: 600; }
        .bt .proj-nums { margin: 0; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px 20px; }
        @media (min-width: 640px) { .bt .proj-nums { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
        .bt .proj-nums dt { font-size: 13px; color: var(--muted); }
        .bt .proj-nums dd {
            margin: 2px 0 0; font-family: 'Bricolage Grotesque', 'Inter', sans-serif; font-variant-numeric: tabular-nums;
            font-size: 20px; font-weight: 600; line-height: 1.25;
        }
        .bt .proj-nums dd small { display: block; font-family: 'Inter', sans-serif; font-size: 12px; font-weight: 400; color: var(--faint); }
        .bt .proj-total dt { color: var(--accent-text); font-weight: 600; }
        .bt .proj-total dd { font-size: 24px; font-weight: 700; }
        .bt .proj-progress { display: grid; gap: 6px; font-size: 12px; color: var(--muted); }
        @media (min-width: 1024px) { .bt .proj-progress { grid-column: 1 / -1; } }
        .bt .proj-progress .meter { margin-top: 0; background: var(--bar-empty); }
        .bt .locked-row { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 14px; }
        .bt .day.future .bar-empty { background: transparent; border-top: 2px dotted var(--line); height: 0; }
        .bt .dnum { text-align: center; font-size: 11px; color: var(--faint); line-height: 1.2; }
        .bt .dnum .wd { display: none; }
        @media (min-width: 1024px) { .bt .dnum { font-size: 12px; } .bt .dnum .wd { display: block; font-size: 11px; } }
        .bt .day.has .dnum { color: var(--muted); }
        .bt .day.today .dnum b { background: var(--accent); color: var(--accent-ink); border-radius: 4px; padding: 0 3px; }
        .bt .dnum b { font-weight: 600; }
        .bt .ref {
            position: absolute; left: 0; right: 0; border-top: 1px dashed var(--line); pointer-events: none;
        }
        .bt .ref span { position: absolute; right: 0; top: -18px; font-size: 11px; color: var(--faint); }
        @keyframes bt-grow { from { transform: scaleY(0); } to { transform: scaleY(1); } }
        @media (prefers-reduced-motion: reduce) { .bt .bar-fill { animation: none; } }

        /* ---------- colunas ---------- */
        .bt .cols { display: grid; gap: 28px; margin-top: 44px; }
        @media (min-width: 1024px) { .bt .cols { grid-template-columns: minmax(0, 1fr) 360px; gap: 40px; align-items: start; } }
        .bt .side { display: grid; gap: 20px; }
        @media (max-width: 1023px) { .bt .side { order: -1; } }

        .bt h2 { font-family: 'Bricolage Grotesque', 'Inter', sans-serif; font-size: 20px; font-weight: 600; letter-spacing: -0.01em; margin: 0; }
        .bt .panel { background: var(--surface); border: 1px solid var(--line); border-radius: 14px; padding: 20px; }

        /* ---------- tracker ---------- */
        .bt .tracker {
            margin-top: 20px; padding: 20px; border-radius: 18px; background: var(--surface); border: 1px solid var(--line);
            display: grid; gap: 18px 28px; align-items: center;
            grid-template-columns: minmax(0, 1fr) auto; grid-template-areas: "clock btn" "fields fields";
            transition: background-color .25s, border-color .25s, color .25s;
        }
        @media (min-width: 960px) {
            .bt .tracker { grid-template-columns: auto minmax(0, 1fr) auto; grid-template-areas: "clock fields btn"; padding: 24px 28px; }
        }
        .bt .tk-clock { grid-area: clock; min-width: 0; }
        .bt .tracker .clock {
            font-family: 'Bricolage Grotesque', 'Inter', sans-serif; font-variant-numeric: tabular-nums;
            font-size: clamp(40px, 6vw, 60px); font-weight: 600; line-height: 1; letter-spacing: -0.02em;
        }
        .bt .tracker .state { display: flex; align-items: center; gap: 8px; font-size: 14px; color: var(--muted); margin-top: 8px; }
        .bt .tracker .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--faint); flex-shrink: 0; }
        .bt .tk-fields { grid-area: fields; display: grid; gap: 10px 12px; min-width: 0; }
        @media (min-width: 640px) { .bt .tk-fields { grid-template-columns: minmax(150px, 220px) minmax(0, 1fr); } }
        .bt .tk-field { display: grid; gap: 6px; min-width: 0; }
        .bt .tk-field label { font-size: 13px; font-weight: 500; color: var(--muted); }
        .bt .tk-field :is(input, select) {
            width: 100%; background: var(--field); color: var(--ink); border: 1px solid var(--line); border-radius: 10px;
            padding: 12px 14px; font: 400 15px/1.3 'Inter', sans-serif;
        }
        .bt .tk-field input::placeholder { color: var(--faint); }
        .bt .tk-field :is(input, select):focus-visible { outline: 2px solid var(--accent); outline-offset: 0; border-color: var(--accent); }
        .bt .tk-hint { grid-column: 1 / -1; margin: 0; font-size: 12px; color: var(--faint); }
        .bt .tk-auto { grid-area: fields; margin: 0; font-size: 14px; color: var(--muted); }
        .bt .tk-auto a { color: var(--accent-text); text-decoration: underline; text-underline-offset: 2px; }
        .bt .tk-btn {
            grid-area: btn; display: inline-flex; align-items: center; justify-content: center; gap: 10px;
            min-height: 56px; padding: 0 28px; border: 0; border-radius: 14px; cursor: pointer;
            background: var(--accent); color: var(--accent-ink); font: 700 16px/1 'Inter', sans-serif;
            transition: filter .15s, background-color .25s, color .25s;
        }
        .bt .tk-btn:hover { filter: brightness(1.06); }
        .bt .tk-btn:disabled { opacity: .6; cursor: progress; }
        .bt .tk-btn:focus-visible { outline: 2px solid var(--ink); outline-offset: 3px; }

        /* Rodando: a faixa inteira assume o ocre do cartão de ponto */
        .bt .tracker.running { background: var(--accent); border-color: var(--accent); color: var(--accent-ink); }
        .bt .tracker.running .state,
        .bt .tracker.running .tk-field label,
        .bt .tracker.running .tk-hint,
        .bt .tracker.running .tk-auto { color: var(--accent-ink); opacity: .8; }
        .bt .tracker.running .tk-auto a { color: var(--accent-ink); }
        .bt .tracker.running .dot { background: var(--accent-ink); animation: bt-pulse 1.6s ease-in-out infinite; }
        .bt .tracker.running .tk-field :is(input, select) {
            background: rgba(255, 255, 255, .32); border-color: rgba(0, 0, 0, .18); color: var(--accent-ink);
        }
        .bt .tracker.running .tk-field input::placeholder { color: rgba(0, 0, 0, .5); }
        .bt .tracker.running .tk-field :is(input, select):focus-visible { outline-color: var(--accent-ink); border-color: var(--accent-ink); }
        .bt .tracker.running .tk-btn { background: var(--accent-ink); color: var(--accent); }
        .bt .tracker.running .tk-btn:focus-visible { outline-color: var(--accent-ink); }
        @keyframes bt-pulse { 50% { opacity: .35; } }
        @media (prefers-reduced-motion: reduce) { .bt .tracker.running .dot { animation: none; } }
        .bt .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px; border-radius: 10px; border: 0;
            font: 600 14px/1 'Inter', sans-serif; padding: 12px 16px; cursor: pointer; transition: filter .15s, background-color .15s;
        }
        .bt .btn:disabled { opacity: .6; cursor: progress; }
        .bt .btn-primary { background: var(--ink); color: var(--bg); }
        .bt .btn-primary:hover { filter: brightness(1.08); }
        .bt .btn-quiet { background: transparent; color: var(--muted); padding: 10px 12px; }
        .bt .btn-quiet:hover { color: var(--ink); background: var(--raised); }

        /* ---------- formulário ---------- */
        .bt form .grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .bt form .field { display: grid; gap: 6px; margin-top: 14px; }
        .bt form label { font-size: 13px; color: var(--muted); font-weight: 500; }
        .bt form input, .bt form select {
            width: 100%; background: var(--field); color: var(--ink); border: 1px solid var(--line); border-radius: 8px;
            padding: 10px 12px; font: 400 15px/1.3 'Inter', sans-serif; font-variant-numeric: tabular-nums;
        }
        .bt form input::placeholder { color: var(--faint); }
        .bt form .actions { display: flex; align-items: center; gap: 8px; margin-top: 18px; }
        .bt form .actions .btn-primary { flex: 1; }
        .bt .form-note { font-size: 13px; margin-top: 12px; padding: 10px 12px; border-radius: 8px; background: var(--accent-soft); color: var(--ink); }
        .bt .form-error { font-size: 13px; margin-top: 12px; color: var(--danger); }

        .bt :is(a, button, input, select):focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
        .bt form :is(input, select):focus-visible { outline-offset: 0; border-color: var(--accent); }

        /* ---------- fechamento ---------- */
        .bt .calc { margin: 14px 0 0; display: grid; grid-template-columns: 1fr auto; row-gap: 10px; column-gap: 16px; font-size: 14px; }
        .bt .calc dt { color: var(--muted); }
        .bt .calc dt small { display: block; font-size: 12px; color: var(--faint); }
        .bt .calc dd { margin: 0; text-align: right; font-variant-numeric: tabular-nums; }
        .bt .calc .total { border-top: 1px solid var(--line); padding-top: 12px; margin-top: 4px; }
        .bt .calc dt.total { color: var(--ink); font-weight: 600; }
        .bt .calc dd.total { font-family: 'Bricolage Grotesque', 'Inter', sans-serif; font-size: 22px; font-weight: 700; }
        .bt .minus { color: var(--danger); }
        .bt .calc-link { color: inherit; text-decoration: underline; text-decoration-color: var(--line); text-underline-offset: 3px; }
        .bt .calc-link:hover { color: var(--ink); text-decoration-color: currentColor; }

        /* ---------- empresas ---------- */
        .bt .split-bar { display: flex; height: 10px; border-radius: 999px; overflow: hidden; margin-top: 14px; background: var(--bar-empty); gap: 2px; }
        .bt .split-bar span { display: block; height: 100%; }
        .bt .companies { margin-top: 28px; }
        .bt .companies .split-bar { margin-top: 0; max-width: 720px; }
        .bt .co-list { list-style: none; margin: 16px 0 0; padding: 0; display: grid; gap: 14px 28px; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); }
        .bt .co-list li { display: grid; grid-template-columns: 10px minmax(0, 1fr); gap: 10px; align-items: start; }
        .bt .co-list .sw { width: 10px; height: 10px; border-radius: 3px; margin-top: 5px; }
        .bt .co-body { display: grid; min-width: 0; }
        .bt .co-name { font-size: 14px; color: var(--muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .bt .co-value { font-size: 22px; font-weight: 600; line-height: 1.25; letter-spacing: -0.01em; }
        .bt .co-meta { font-size: 12px; color: var(--faint); }
        .bt .co-cnpj { white-space: nowrap; }

        /* ---------- folha (lançamentos) ---------- */
        .bt .sheet-head { display: flex; align-items: baseline; justify-content: space-between; gap: 12px; }
        .bt .sheet-head p { margin: 0; color: var(--muted); font-size: 14px; }
        .bt .dayblock { margin-top: 22px; scroll-margin-top: 24px; }
        .bt .dayblock:target .dayhead { color: var(--accent-text); }
        .bt .dayhead { display: flex; justify-content: space-between; align-items: baseline; gap: 12px; padding-bottom: 8px; border-bottom: 1px solid var(--line); }
        .bt .dayhead h3 { margin: 0; font-size: 15px; font-weight: 600; }
        .bt .dayhead h3 span { color: var(--muted); font-weight: 400; margin-left: 6px; }
        .bt .dayhead .tot { font-size: 14px; color: var(--muted); display: flex; gap: 14px; }
        .bt .dayhead .tot b { color: var(--ink); font-weight: 600; }
        .bt .rows { list-style: none; margin: 0; padding: 0; }
        .bt .row {
            display: grid; grid-template-columns: 104px minmax(0, 1fr) auto 36px; gap: 4px 16px; align-items: center;
            padding: 12px 0; border-bottom: 1px solid var(--line);
        }
        .bt .row .when { font-variant-numeric: tabular-nums; font-size: 14px; color: var(--muted); }
        .bt .row .when b { display: block; color: var(--ink); font-weight: 600; font-size: 15px; }
        .bt .row .what { min-width: 0; }
        .bt .row .what p { margin: 0; overflow-wrap: anywhere; }
        .bt .row .what small { display: block; font-size: 13px; color: var(--muted); margin-top: 2px; }
        .bt .row .val { font-variant-numeric: tabular-nums; color: var(--money); font-weight: 600; font-size: 14px; text-align: right; }
        .bt .row .del {
            width: 36px; height: 36px; display: grid; place-items: center; border-radius: 8px; border: 0; background: transparent;
            color: var(--faint); cursor: pointer;
        }
        .bt .row .del:hover { color: var(--danger); background: var(--raised); }
        @media (max-width: 639px) {
            .bt .row { grid-template-columns: minmax(0, 1fr) auto 36px; }
            .bt .row .when { grid-column: 1 / -1; display: flex; gap: 10px; align-items: baseline; }
            .bt .row .when b { display: inline; }
        }
        .bt .empty { margin-top: 22px; padding: 28px 20px; border: 1px dashed var(--line); border-radius: 14px; color: var(--muted); }
        .bt .empty strong { color: var(--ink); display: block; margin-bottom: 4px; font-weight: 600; }

        /* ---------- sobreaviso na faixa de dias ---------- */
        .bt .oc-band { height: 3px; border-radius: 2px; margin: 0 2px; background: transparent; }
        .bt .day.oncall .oc-band { background: var(--oncall); }
        .bt .legend { display: flex; flex-wrap: wrap; gap: 16px; margin-top: 12px; font-size: 13px; color: var(--muted); }
        .bt .legend span { display: inline-flex; align-items: center; gap: 6px; }
        .bt .legend i { display: inline-block; width: 12px; height: 8px; border-radius: 2px; }

        /* ---------- botões secundários ---------- */
        .bt .btn-outline { background: transparent; color: var(--ink); border: 1px solid var(--line); }
        .bt .btn-outline:hover { background: var(--raised); }
        .bt .btn-sm { padding: 9px 12px; font-size: 13px; }
        .bt .std-hint { font-size: 13px; color: var(--muted); margin-top: 10px; }
        .bt .std-hint a { color: var(--accent-text); text-decoration: underline; text-underline-offset: 2px; }

        /* ---------- seção de sobreaviso ---------- */
        /* ---------- abas ---------- */
        .bt .tabs { display: flex; gap: 4px; border-bottom: 1px solid var(--line); }
        .bt .tab {
            position: relative; display: inline-flex; align-items: center; gap: 8px; background: none; border: 0; cursor: pointer;
            font: 600 15px/1 'Inter', sans-serif; color: var(--muted); padding: 12px 12px 14px; margin-bottom: -1px;
            border-bottom: 2px solid transparent; border-radius: 8px 8px 0 0;
        }
        .bt .tab:first-child { padding-left: 2px; }
        .bt .tab:hover { color: var(--ink); }
        .bt .tab[aria-selected="true"] { color: var(--ink); border-bottom-color: var(--accent); }
        .bt .tab-oc[aria-selected="true"] { border-bottom-color: var(--oncall); }
        .bt .tab .count { font-size: 12px; font-weight: 600; padding: 3px 7px; border-radius: 999px; background: var(--raised); color: var(--muted); }
        .bt .tab[aria-selected="true"] .count { background: var(--accent-soft); color: var(--accent-text); }
        .bt .tab-oc[aria-selected="true"] .count { background: var(--oncall-soft); color: var(--oncall-text); }
        .bt .tabpanel:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }

        /* Rolagem própria para lançamentos e sobreaviso: a página não cresce com as horas */
        .bt .tabpanel {
            margin-top: 12px; background: var(--surface); border: 1px solid var(--line); border-radius: 14px;
            padding: 0 16px 16px; max-height: 70vh; overflow-y: auto; overscroll-behavior: contain;
            scrollbar-width: thin; scrollbar-color: var(--line) transparent;
        }
        @media (min-width: 1024px) { .bt .tabpanel { max-height: min(760px, calc(100vh - 120px)); padding: 0 20px 20px; } }
        .bt .tabpanel .dayblock { margin-top: 0; scroll-margin-top: 0; }
        .bt .tabpanel .dayhead {
            position: sticky; top: 0; z-index: 1; background: var(--surface); padding-top: 16px;
        }
        .bt .tabpanel .rows .row:last-child { border-bottom: 0; }
        .bt .tabpanel > .empty { margin-top: 16px; }
        .bt .tabpanel.oncall { padding-top: 16px; }

        .bt .oncall .sheet-head { flex-wrap: wrap; align-items: center; }
        .bt .oncall .sheet-head .intro { margin: 0; flex: 1 1 280px; }
        .bt .oncall .empty, .bt .oncall .locked { margin-top: 16px; }
        .bt .panel-head { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
        .bt .panel-head .btn { margin-right: -8px; }
        .bt .oncall .head-actions { display: flex; gap: 8px; }
        .bt .oncall .intro { color: var(--muted); font-size: 14px; margin: 6px 0 0; max-width: 62ch; }
        .bt .periods { list-style: none; margin: 18px 0 0; padding: 0; display: grid; gap: 12px; }
        .bt .period { border: 1px solid var(--line); border-left: 3px solid var(--oncall); border-radius: 12px; padding: 16px; background: var(--surface); }
        .bt .period-top { display: flex; justify-content: space-between; gap: 12px; align-items: flex-start; }
        .bt .period-top h3 { margin: 0; font-size: 15px; font-weight: 600; font-variant-numeric: tabular-nums; }
        .bt .period-top p { margin: 2px 0 0; font-size: 13px; color: var(--muted); }
        .bt .period-actions { display: flex; gap: 2px; flex-shrink: 0; }
        .bt .icon-btn { width: 36px; height: 36px; display: grid; place-items: center; border-radius: 8px; border: 0; background: transparent; color: var(--faint); cursor: pointer; }
        .bt .icon-btn:hover { color: var(--ink); background: var(--raised); }
        .bt .icon-btn.danger:hover { color: var(--danger); }
        .bt .period-nums { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px 16px; margin: 14px 0 0; }
        @media (min-width: 640px) { .bt .period-nums { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
        .bt .period-nums dt { font-size: 12px; color: var(--muted); }
        .bt .period-nums dd { margin: 2px 0 0; font-variant-numeric: tabular-nums; font-weight: 600; }
        .bt .period-nums .oc { color: var(--oncall-text); }
        .bt .period-nums .wk { color: var(--accent-text); }
        .bt .period-nums .money { color: var(--money); }
        .bt .meter { height: 6px; border-radius: 999px; background: var(--oncall-soft); margin-top: 14px; overflow: hidden; }
        .bt .meter span { display: block; height: 100%; background: var(--accent); border-radius: 999px; }
        .bt .locked { margin-top: 18px; padding: 20px; border-radius: 14px; background: var(--oncall-soft); }
        .bt .locked p { margin: 0 0 14px; color: var(--ink); max-width: 60ch; }

        /* ---------- modal (dialog nativo) ---------- */
        .bt dialog {
            width: min(520px, calc(100vw - 32px)); max-height: calc(100vh - 32px); overflow-y: auto;
            background: var(--surface); color: var(--ink); border: 1px solid var(--line); border-radius: 16px; padding: 24px;
            box-shadow: 0 24px 60px rgba(0, 0, 0, .35);
        }
        .bt dialog::backdrop { background: rgba(10, 12, 18, .6); }
        .bt dialog h2 { margin-bottom: 4px; }
        .bt dialog .sub { margin: 0; font-size: 14px; color: var(--muted); }
        .bt dialog .hint { font-size: 12px; color: var(--faint); }
        .bt dialog .actions { justify-content: flex-end; }
        .bt dialog .actions .btn-primary { flex: 0 0 auto; }
    </style>

    <div class="bt">
        <div class="wrap">

            {{-- Ciclo + volta ao painel clássico --}}
            <div class="topbar">
                <form method="GET" action="{{ route('dashboard.beta') }}" class="cycle">
                    @if($olderMonth)
                        <a href="{{ route('dashboard.beta', ['month' => $olderMonth]) }}" aria-label="Ciclo anterior">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>
                        </a>
                    @else
                        <span class="off" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M15 18l-6-6 6-6"/></svg></span>
                    @endif

                    <label for="bt-month" class="sr-only">Ciclo de faturamento</label>
                    <select id="bt-month" name="month" onchange="this.form.submit()">
                        @foreach($months as $m)
                            <option value="{{ $m['value'] }}" @selected($m['value'] === $currentMonth)>{{ $m['label'] }}</option>
                        @endforeach
                    </select>
                    <noscript><button type="submit" class="btn btn-quiet">Ver</button></noscript>

                    @if($newerMonth)
                        <a href="{{ route('dashboard.beta', ['month' => $newerMonth]) }}" aria-label="Próximo ciclo">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>
                        </a>
                    @else
                        <span class="off" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M9 18l6-6-6-6"/></svg></span>
                    @endif
                </form>

                <a href="{{ route('dashboard') }}" class="classic">
                    <span class="beta-tag">Beta</span>
                    Voltar ao painel clássico
                </a>
            </div>

            {{-- Cronômetro: a ação principal da tela --}}
            <section class="tracker" id="bt-tracker" aria-label="Cronômetro">
                <div class="tk-clock">
                    <div class="clock" id="bt-clock" role="timer" aria-label="Tempo do cronômetro">00:00:00</div>
                    <div class="state" id="bt-state" aria-live="polite">
                        <span class="dot" aria-hidden="true"></span>
                        <span id="bt-state-text">{{ $todayHours > 0 ? 'Hoje você já lançou ' . $hm($todayHours) . '.' : 'Nenhuma hora lançada hoje.' }}</span>
                    </div>
                </div>

                @if(!$autoSaveTracking)
                    <div class="tk-fields">
                        <div class="tk-field">
                            <label for="bt-tk-project">Projeto</label>
                            <select id="bt-tk-project">
                                <option value="">Sem projeto</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" @selected($defaultProjectId == $project->id)>{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="tk-field">
                            <label for="bt-tk-desc">No que você está trabalhando?</label>
                            <input type="text" id="bt-tk-desc" maxlength="1000" autocomplete="off"
                                   placeholder="Ex.: ajustes no relatório de NF" aria-describedby="bt-tk-hint">
                        </div>
                        <p class="tk-hint" id="bt-tk-hint">Com a descrição preenchida, o lançamento é salvo assim que você parar.</p>
                    </div>
                @else
                    <p class="tk-auto">
                        Ao parar, o lançamento é salvo automaticamente no projeto padrão.
                        <a href="{{ route('settings') }}">Alterar nas configurações</a>
                    </p>
                @endif

                <button type="button" class="tk-btn" id="bt-track-btn" onclick="btToggleTracking()">
                    <svg id="bt-icon-play" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5.5v13a1 1 0 001.5.86l11-6.5a1 1 0 000-1.72l-11-6.5A1 1 0 008 5.5z"/></svg>
                    <svg id="bt-icon-stop" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" hidden><rect x="6" y="6" width="12" height="12" rx="2"/></svg>
                    <span id="bt-track-label">Iniciar</span>
                </button>
            </section>

            {{-- Resumo do ciclo --}}
            <section class="hero" aria-labelledby="bt-summary">
                @if($totalHours > 0)
                    <h1 id="bt-summary">
                        {{ $isCurrentCycle ? 'Neste ciclo você registrou' : 'Neste ciclo foram registradas' }}
                        <strong>{{ $hm($totalHours) }}</strong> horas, que somam
                        <strong class="sensitive-value">{{ $brl($totalFinal) }}</strong>.
                    </h1>
                @else
                    <h1 id="bt-summary">Nenhuma hora registrada neste ciclo ainda.</h1>
                @endif
                <p>
                    {{ $daysWorked }} {{ $daysWorked === 1 ? 'dia com lançamento' : 'dias com lançamento' }}
                    entre {{ $cycleStart->format('d/m') }} e {{ $cycleEnd->format('d/m/Y') }}.
                    @if($isCurrentCycle)
                        @if($workdaysLeft > 0)
                            {{ $workdaysLeft === 1 ? 'Falta 1 dia útil' : "Faltam {$workdaysLeft} dias úteis" }} para fechar o ciclo.
                        @else
                            Hoje é o último dia útil do ciclo.
                        @endif
                    @else
                        Ciclo encerrado.
                    @endif
                </p>
            </section>

            {{-- Faturado por empresa: logo abaixo do resumo, sem rolagem --}}
            @if($companies->isNotEmpty() || $unassigned > 0)
                <section class="companies" aria-labelledby="bt-split">
                    <h2 id="bt-split" class="sr-only">Faturado por empresa</h2>
                    <div class="split-bar" aria-hidden="true">
                        @foreach($companies as $i => $c)
                            <span style="flex: {{ $c['revenue'] }}; background: {{ $splitColors[$i % 6] }}"></span>
                        @endforeach
                        @if($unassigned > 0)
                            <span style="flex: {{ $unassigned }}; background: var(--faint)"></span>
                        @endif
                    </div>
                    <ul class="co-list">
                        @foreach($companies as $i => $c)
                            <li>
                                <span class="sw" style="background: {{ $splitColors[$i % 6] }}" aria-hidden="true"></span>
                                <div class="co-body">
                                    <span class="co-name" title="{{ $c['name'] }}">{{ $c['name'] }}</span>
                                    <span class="co-value display sensitive-value">{{ $brl($c['revenue']) }}</span>
                                    <span class="co-meta num">{{ $splitBase > 0 ? number_format($c['revenue'] / $splitBase * 100, 0) : 0 }}% do ciclo</span>
                                    @if(!empty($c['cnpj']))
                                        <span class="co-meta co-cnpj num">CNPJ {{ $c['cnpj'] }}</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                        @if($unassigned > 0)
                            <li>
                                <span class="sw" style="background: var(--faint)" aria-hidden="true"></span>
                                <div class="co-body">
                                    <span class="co-name">Sem empresa</span>
                                    <span class="co-value display sensitive-value">{{ $brl($unassigned) }}</span>
                                    <span class="co-meta num">{{ $splitBase > 0 ? number_format($unassigned / $splitBase * 100, 0) : 0 }}% do ciclo, projetos sem empresa vinculada</span>
                                </div>
                            </li>
                        @endif
                    </ul>
                </section>
            @endif

            {{-- Faixa de dias do ciclo --}}
            <figure class="strip" aria-label="Horas por dia do ciclo" style="margin-left:0;margin-right:0;margin-bottom:0;">
                <div class="strip-grid" style="--n: {{ count($days) }}">
                    @foreach($days as $i => $d)
                        @php
                            $classes = trim(($d['weekend'] ? 'weekend ' : '') . ($d['today'] ? 'today ' : '') . ($d['future'] ? 'future ' : '') . ($d['on_call'] ? 'oncall ' : '') . ($d['hours'] > 0 ? 'has' : ''));
                            $aria = $d['label'] . ': ' . ($d['hours'] > 0 ? $hm($d['hours']) . ' horas' : ($d['projected'] && $projectedHours > 0 ? 'projeção de ' . $hm($projectedHours) . ' horas' : 'sem lançamento')) . ($d['on_call'] ? ', em sobreaviso' : '');
                        @endphp
                        @if($d['hours'] > 0)
                            <a href="#dia-{{ $d['key'] }}" class="day {{ $classes }}" title="{{ $aria }}" aria-label="{{ $aria }}">
                        @else
                            <div class="day {{ $classes }}" title="{{ $aria }}" aria-label="{{ $aria }}" role="img">
                        @endif
                                <div class="bar">
                                    @if($d['hours'] > 0)
                                        <div class="bar-fill" style="height: {{ max(4, round($d['hours'] / $maxDayHours * 100, 1)) }}%; --i: {{ $i }}"></div>
                                    @elseif($d['projected'] && $projectedHours > 0)
                                        <div class="bar-ghost" style="height: {{ max(4, round($projectedHours / $maxDayHours * 100, 1)) }}%"></div>
                                    @else
                                        <div class="bar-empty"></div>
                                    @endif
                                </div>
                                <div class="oc-band" aria-hidden="true"></div>
                                <div class="dnum" aria-hidden="true">
                                    <b>{{ $d['day'] }}</b>
                                    <span class="wd">{{ rtrim($d['weekday'], '.') }}</span>
                                </div>
                        @if($d['hours'] > 0)
                            </a>
                        @else
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="ref" aria-hidden="true" id="bt-ref"><span>8h</span></div>
                @if($onCallPeriods->isNotEmpty() || $showGhost)
                    <figcaption class="legend">
                        <span><i style="background: var(--accent)"></i>Horas trabalhadas</span>
                        @if($showGhost)
                            <span><i class="ghost-sw"></i>Projeção nos dias úteis restantes</span>
                        @endif
                        @if($onCallPeriods->isNotEmpty())
                            <span><i style="background: var(--oncall)"></i>Dias em sobreaviso</span>
                        @endif
                    </figcaption>
                @endif
            </figure>

            {{-- Projeção do ciclo --}}
            @if($isCurrentCycle)
                @if($projection)
                    @php
                        $pct = $projection['total_weekdays'] > 0 ? round($projection['elapsed_weekdays'] / $projection['total_weekdays'] * 100) : 0;
                    @endphp
                    <section class="projection" aria-labelledby="bt-proj">
                        <div class="proj-text">
                            <h2 id="bt-proj">Projeção do ciclo</h2>
                            @if($projection['weekdays_worked'] > 0)
                                <p>
                                    Mantendo a média de <strong class="sensitive-value">{{ $brl($projection['daily_average_revenue']) }}</strong> por dia útil,
                                    o ciclo fecha em <strong class="sensitive-value">{{ $brl($projection['projected_total']) }}</strong>.
                                </p>
                            @else
                                <p>Lance horas em pelo menos um dia útil para calcular a projeção do ciclo.</p>
                            @endif
                        </div>
                        @if($projection['weekdays_worked'] > 0)
                        <dl class="proj-nums">
                            <div>
                                <dt>Faturado até hoje</dt>
                                <dd class="sensitive-value">{{ $brl($projection['current_revenue']) }}</dd>
                            </div>
                            <div>
                                <dt>Média por dia útil</dt>
                                <dd><span class="sensitive-value">{{ $brl($projection['daily_average_revenue']) }}</span><small>{{ $hm($projection['daily_average_hours']) }} h em {{ $projection['weekdays_worked'] }} {{ $projection['weekdays_worked'] === 1 ? 'dia' : 'dias' }}</small></dd>
                            </div>
                            <div>
                                <dt>Dias úteis restantes</dt>
                                <dd>{{ $projection['remaining_weekdays'] }}<small>de {{ $projection['total_weekdays'] }} no ciclo</small></dd>
                            </div>
                            <div class="proj-total">
                                <dt>Projeção</dt>
                                <dd class="sensitive-value">{{ $brl($projection['projected_total']) }}</dd>
                            </div>
                        </dl>
                        @endif
                        <div class="proj-progress">
                            <div class="meter" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $pct }}"
                                 aria-label="{{ $projection['elapsed_weekdays'] }} de {{ $projection['total_weekdays'] }} dias úteis concluídos">
                                <span style="width: {{ $pct }}%"></span>
                            </div>
                            <span class="num">{{ $projection['elapsed_weekdays'] }} de {{ $projection['total_weekdays'] }} dias úteis concluídos</span>
                        </div>
                    </section>
                @elseif(!$canProject)
                    <section class="projection locked-row" aria-labelledby="bt-proj">
                        <div class="proj-text">
                            <h2 id="bt-proj">Projeção do ciclo</h2>
                            <p>Veja quanto o ciclo deve fechar com base na sua média por dia útil. Disponível no plano Premium.</p>
                        </div>
                        <button type="button" class="btn btn-outline btn-sm" onclick="showPremiumModal('projeção do ciclo')">Conhecer o Premium</button>
                    </section>
                @endif
            @endif

            <div class="cols">
                <div>
                {{-- Abas: lançamentos / sobreaviso --}}
                <div class="tabs" role="tablist" aria-label="Conteúdo do ciclo">
                    <button type="button" role="tab" class="tab" data-tab="folha" id="tab-folha" aria-controls="panel-folha" aria-selected="true">
                        Lançamentos <span class="count num">{{ $entriesCount }}</span>
                    </button>
                    <button type="button" role="tab" class="tab tab-oc" data-tab="sobreaviso" id="tab-sobreaviso" aria-controls="sobreaviso" aria-selected="false" tabindex="-1">
                        Sobreaviso
                        @if($canUseOnCall)
                            <span class="count num">{{ $onCallPeriods->count() }}</span>
                        @else
                            <span class="count">Premium</span>
                        @endif
                    </button>
                </div>

                {{-- Folha de lançamentos --}}
                <section id="panel-folha" role="tabpanel" aria-labelledby="tab-folha" class="tabpanel" tabindex="0">

                    @forelse($entriesByDay as $dateKey => $dayEntries)
                        @php
                            $date = $dayEntries->first()->date;
                            $dayHours = (float) $dayEntries->sum('hours');
                            $dayName = $dateKey === $todayKey ? 'Hoje' : ($dateKey === $yesterdayKey ? 'Ontem' : ucfirst($date->isoFormat('dddd')));
                        @endphp
                        <div class="dayblock" id="dia-{{ $dateKey }}">
                            <div class="dayhead">
                                <h3>{{ $dayName }}<span>{{ $date->format('d/m') }}</span></h3>
                                <div class="tot">
                                    <b class="num">{{ $hm($dayHours) }}</b>
                                    <span class="num sensitive-value">{{ $brl($dayHours * $hourlyRate) }}</span>
                                </div>
                            </div>
                            <ul class="rows">
                                @foreach($dayEntries as $entry)
                                    <li class="row" data-entry-id="{{ $entry->id }}">
                                        <div class="when">
                                            <b>{{ $hm($entry->hours) }}</b>
                                            <span>{{ substr($entry->start_time, 0, 5) }}–{{ substr($entry->end_time, 0, 5) }}</span>
                                        </div>
                                        <div class="what">
                                            <p>{{ $entry->description }}</p>
                                            @if($entry->project)
                                                <small>{{ $entry->project->name }}</small>
                                            @endif
                                        </div>
                                        <div class="val sensitive-value">{{ $brl($entry->hours * $hourlyRate) }}</div>
                                        <button type="button" class="del" onclick="btRemoveEntry({{ $entry->id }})"
                                                aria-label="Excluir lançamento das {{ substr($entry->start_time, 0, 5) }} às {{ substr($entry->end_time, 0, 5) }} de {{ $date->format('d/m') }}">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @empty
                        <div class="empty">
                            <strong>Nenhum lançamento neste ciclo.</strong>
                            Inicie o cronômetro ou preencha o horário em “Registrar horas” para começar.
                        </div>
                    @endforelse
                </section>

                {{-- Sobreaviso --}}
                <section class="tabpanel oncall" id="sobreaviso" role="tabpanel" aria-labelledby="tab-sobreaviso" tabindex="0" hidden>
                    <div class="sheet-head">
                        @if($canUseOnCall)
                            <p class="intro">Horas trabalhadas dentro do período são pagas como normais e saem do total de sobreaviso.</p>
                            <div class="head-actions">
                                @if($onCallPeriods->isNotEmpty())
                                    <button type="button" class="btn btn-quiet btn-sm" onclick="btRecalculateOnCall()" id="bt-oc-recalc"
                                            title="Revincula os lançamentos aos períodos de sobreaviso">Recalcular</button>
                                @endif
                                <button type="button" class="btn btn-outline btn-sm" onclick="btOpenOnCall()">Novo período</button>
                            </div>
                        @endif
                    </div>

                    @if($canUseOnCall)
                        @if($onCallPeriods->isEmpty())
                            <div class="empty">
                                <strong>Nenhum período de sobreaviso neste ciclo.</strong>
                                Cadastre o intervalo em que você ficou disponível. As horas trabalhadas dentro dele são descontadas automaticamente.
                            </div>
                        @else
                            <ul class="periods">
                                @foreach($onCallPeriods as $period)
                                    @php
                                        $samePeriodDay = $period->start_datetime->isSameDay($period->end_datetime);
                                        $workedPct = $period->total_hours > 0 ? min(100, $period->worked_hours / $period->total_hours * 100) : 0;
                                        $periodData = [
                                            'id' => $period->id,
                                            'start' => $period->start_datetime->format('Y-m-d\TH:i'),
                                            'end' => $period->end_datetime->format('Y-m-d\TH:i'),
                                            'project_id' => $period->project_id,
                                            'hourly_rate' => (float) $period->hourly_rate,
                                            'description' => $period->description ?? '',
                                        ];
                                    @endphp
                                    <li class="period" data-period="{{ json_encode($periodData) }}">
                                        <div class="period-top">
                                            <div>
                                                <h3>
                                                    {{ $period->start_datetime->format('d/m H:i') }} até
                                                    {{ $samePeriodDay ? $period->end_datetime->format('H:i') : $period->end_datetime->format('d/m H:i') }}
                                                </h3>
                                                <p>
                                                    {{ $period->project?->name ?? 'Todos os projetos' }}@if($period->description), {{ $period->description }}@endif
                                                </p>
                                            </div>
                                            <div class="period-actions">
                                                <button type="button" class="icon-btn" onclick="btEditOnCall(this)" aria-label="Editar período de {{ $period->start_datetime->format('d/m H:i') }}">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                </button>
                                                <button type="button" class="icon-btn danger" onclick="btDeleteOnCall({{ $period->id }})" aria-label="Excluir período de {{ $period->start_datetime->format('d/m H:i') }}">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                        <dl class="period-nums">
                                            <div><dt>Período</dt><dd>{{ $hm($period->total_hours) }}</dd></div>
                                            <div><dt>Trabalhado</dt><dd class="wk">{{ $hm($period->worked_hours) }}</dd></div>
                                            <div><dt>Sobreaviso</dt><dd class="oc">{{ $hm($period->on_call_hours) }}</dd></div>
                                            <div><dt>A receber <span class="sensitive-value">({{ $brl($period->hourly_rate) }}/h)</span></dt><dd class="money sensitive-value">{{ $brl($period->on_call_hours * $period->hourly_rate) }}</dd></div>
                                        </dl>
                                        <div class="meter" role="img" aria-label="{{ number_format($workedPct, 0) }}% do período foi trabalhado">
                                            <span style="width: {{ round($workedPct, 1) }}%"></span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    @else
                        <div class="locked">
                            <p>Registre plantões e períodos de disponibilidade. O sistema separa as horas trabalhadas das horas de sobreaviso e calcula o valor de cada uma. Disponível no plano Premium.</p>
                            <button type="button" class="btn btn-primary btn-sm" onclick="showPremiumModal('sobreaviso')">Conhecer o Premium</button>
                        </div>
                    @endif
                </section>
                </div>

                {{-- Lateral: lançamento manual + fechamento --}}
                <aside class="side">
                    <section class="panel" aria-labelledby="bt-new">
                        <h2 id="bt-new">Registrar horas</h2>
                        <form id="bt-form" onsubmit="btAddEntry(event)" novalidate>
                            <div class="field">
                                <label for="bt-date">Data</label>
                                <input type="date" id="bt-date" name="date" value="{{ $isCurrentCycle ? $todayKey : $cycleEnd->format('Y-m-d') }}" required>
                            </div>
                            <div class="grid2">
                                <div class="field">
                                    <label for="bt-start">Início</label>
                                    <input type="time" id="bt-start" name="start_time" required>
                                </div>
                                <div class="field">
                                    <label for="bt-end">Fim</label>
                                    <input type="time" id="bt-end" name="end_time" required>
                                </div>
                            </div>
                            <div class="field">
                                <label for="bt-project">Projeto</label>
                                <select id="bt-project" name="project_id">
                                    <option value="">Sem projeto</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" @selected($defaultProjectId == $project->id)>{{ $project->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label for="bt-description">O que você fez</label>
                                <input type="text" id="bt-description" name="description" maxlength="1000" placeholder="Ex.: revisão do módulo de faturas" required>
                            </div>
                            <div class="form-note" id="bt-note" hidden></div>
                            <div class="form-error" id="bt-error" role="alert" hidden></div>
                            <div class="actions">
                                <button type="submit" class="btn btn-primary" id="bt-submit">Salvar lançamento</button>
                                @if(!empty($standardDayPeriods))
                                    <button type="button" class="btn btn-outline" id="bt-std-btn" onclick="btLaunchStandardDay()"
                                            aria-describedby="bt-std-hint">Dia padrão</button>
                                @endif
                            </div>
                            @if(!empty($standardDayPeriods))
                                <p class="std-hint num" id="bt-std-hint">
                                    O dia padrão lança
                                    {{ collect($standardDayPeriods)->map(fn ($p) => $p['start'] . '–' . $p['end'])->join(', ', ' e ') }}
                                    na data escolhida.
                                </p>
                            @else
                                <p class="std-hint">
                                    Repete sempre os mesmos horários? <a href="{{ route('settings') }}">Configure um dia padrão</a> e lance o dia inteiro com um clique.
                                </p>
                            @endif
                        </form>
                    </section>

                    <section class="panel" aria-labelledby="bt-close">
                        <div class="panel-head">
                            <h2 id="bt-close">Fechamento</h2>
                            <button type="button" class="btn btn-quiet btn-sm" onclick="btOpenAdjust()">Ajustar valores</button>
                        </div>
                        <dl class="calc">
                            <dt>Horas<small class="num">{{ $hm($totalHours) }} × <span class="sensitive-value">{{ $brl($hourlyRate) }}</span></small></dt>
                            <dd class="sensitive-value">{{ $brl($stats['total_revenue']) }}</dd>
                            @if($extra > 0)
                                <dt>Acréscimo</dt>
                                <dd class="sensitive-value">+ {{ $brl($extra) }}</dd>
                            @endif
                            @if($discount > 0)
                                <dt>Desconto</dt>
                                <dd class="sensitive-value minus">− {{ $brl($discount) }}</dd>
                            @endif
                            @if($onCall > 0)
                                <dt><a href="#sobreaviso" class="calc-link">Sobreaviso</a><small class="num">{{ $hm($stats['on_call_hours']) }}</small></dt>
                                <dd class="sensitive-value">+ {{ $brl($onCall) }}</dd>
                            @endif
                            <dt class="total">A faturar</dt>
                            <dd class="total sensitive-value">{{ $brl($totalFinal) }}</dd>
                        </dl>
                    </section>

                </aside>
            </div>
        </div>

        <dialog id="bt-adj-dialog" aria-labelledby="bt-adj-title">
            <form id="bt-adj-form" onsubmit="btSaveAdjust(event)" novalidate>
                <h2 id="bt-adj-title">Ajustes do ciclo</h2>
                <p class="sub">Valem só para {{ $currentLabel }}. Os valores padrão ficam em <a href="{{ route('settings') }}" class="calc-link">Configurações</a>.</p>
                <div class="field">
                    <label for="bt-adj-rate">Valor por hora (R$)</label>
                    <input type="text" id="bt-adj-rate" inputmode="decimal" required>
                </div>
                <div class="grid2">
                    <div class="field">
                        <label for="bt-adj-extra">Acréscimo (R$)</label>
                        <input type="text" id="bt-adj-extra" inputmode="decimal" aria-describedby="bt-adj-extra-hint">
                        <span class="hint" id="bt-adj-extra-hint">Ex.: home office, ajuda de custo</span>
                    </div>
                    <div class="field">
                        <label for="bt-adj-discount">Desconto (R$)</label>
                        <input type="text" id="bt-adj-discount" inputmode="decimal" aria-describedby="bt-adj-discount-hint">
                        <span class="hint" id="bt-adj-discount-hint">Ex.: impostos, taxas</span>
                    </div>
                </div>
                <div class="form-error" id="bt-adj-error" role="alert" hidden></div>
                <div class="actions">
                    <button type="button" class="btn btn-quiet" onclick="document.getElementById('bt-adj-dialog').close()">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="bt-adj-submit">Salvar ajustes</button>
                </div>
            </form>
        </dialog>

        @if($canUseOnCall)
            <dialog id="bt-oc-dialog" aria-labelledby="bt-oc-title">
                <form id="bt-oc-form" onsubmit="btSaveOnCall(event)" novalidate>
                    <h2 id="bt-oc-title">Novo período de sobreaviso</h2>
                    <p class="sub">O intervalo em que você ficou disponível para ser acionado.</p>
                    <input type="hidden" id="bt-oc-id">
                    <div class="grid2">
                        <div class="field">
                            <label for="bt-oc-start">Início</label>
                            <input type="datetime-local" id="bt-oc-start" required>
                        </div>
                        <div class="field">
                            <label for="bt-oc-end">Fim</label>
                            <input type="datetime-local" id="bt-oc-end" required>
                        </div>
                    </div>
                    <div class="field">
                        <label for="bt-oc-project">Projeto</label>
                        <select id="bt-oc-project">
                            <option value="">Todos os projetos</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="bt-oc-rate">Valor por hora de sobreaviso (R$)</label>
                        <input type="text" id="bt-oc-rate" inputmode="decimal" placeholder="Ex.: 50,00" aria-describedby="bt-oc-rate-hint">
                        <span class="hint" id="bt-oc-rate-hint">Em branco, usa o valor das configurações ou 1/3 do seu valor/hora.</span>
                    </div>
                    <div class="field">
                        <label for="bt-oc-description">Descrição (opcional)</label>
                        <input type="text" id="bt-oc-description" maxlength="1000" placeholder="Ex.: plantão de fim de semana">
                    </div>
                    <div class="form-error" id="bt-oc-error" role="alert" hidden></div>
                    <div class="actions">
                        <button type="button" class="btn btn-quiet" onclick="document.getElementById('bt-oc-dialog').close()">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="bt-oc-submit">Salvar período</button>
                    </div>
                </form>
            </dialog>
        @endif
    </div>

    @push('scripts')
    <script>
    (function () {
        const MAX_DAY_HOURS = {{ $maxDayHours }};
        const FLASH_KEY = 'bt_flash';
        let trackingStart = null;
        let tickTimer = null;

        // Linha de referência de 8h alinhada à altura real das barras
        function placeRef() {
            const bar = document.querySelector('.bt .bar');
            const ref = document.getElementById('bt-ref');
            if (!bar || !ref) return;
            const strip = ref.parentElement.getBoundingClientRect();
            const b = bar.getBoundingClientRect();
            ref.style.top = (b.bottom - strip.top - b.height * (8 / MAX_DAY_HOURS)) + 'px';
        }
        window.addEventListener('resize', placeRef);
        placeRef();

        // Mensagem pós-recarga
        try {
            const flash = sessionStorage.getItem(FLASH_KEY);
            if (flash) { sessionStorage.removeItem(FLASH_KEY); showToast(flash, TOAST_TYPES.SUCCESS); }
        } catch (e) {}
        function reloadWith(message) {
            try { sessionStorage.setItem(FLASH_KEY, message); } catch (e) {}
            window.location.reload();
        }

        const headers = { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN };
        const $ = id => document.getElementById(id);

        function firstError(data, fallback) {
            if (data && data.errors) {
                const first = Object.values(data.errors)[0];
                if (first && first[0]) return first[0];
            }
            return (data && data.message) || fallback;
        }

        // ---------- Cronômetro ----------
        const IDLE_TEXT = $('bt-state-text').textContent;
        const BASE_TITLE = document.title;
        const CTX_KEY = 'bt_track_ctx';
        const tkProject = $('bt-tk-project');
        const tkDesc = $('bt-tk-desc');

        // Projeto e descrição do cronômetro sobrevivem a recarregamentos
        try {
            const ctx = JSON.parse(localStorage.getItem(CTX_KEY) || 'null');
            if (ctx && tkDesc) {
                tkDesc.value = ctx.desc || '';
                if (ctx.project !== undefined && tkProject.querySelector(`option[value="${ctx.project}"]`)) tkProject.value = ctx.project;
            }
        } catch (e) {}
        function saveCtx() {
            if (!tkDesc) return;
            try { localStorage.setItem(CTX_KEY, JSON.stringify({ project: tkProject.value, desc: tkDesc.value })); } catch (e) {}
        }
        function clearCtx() { try { localStorage.removeItem(CTX_KEY); } catch (e) {} }
        tkDesc?.addEventListener('input', saveCtx);
        tkProject?.addEventListener('change', saveCtx);
        // Enter no campo de descrição inicia o cronômetro (nunca para, para evitar paradas acidentais)
        tkDesc?.addEventListener('keydown', e => {
            if (e.key === 'Enter') { e.preventDefault(); if (trackingStart === null) btToggleTracking(); }
        });

        function pad(n) { return String(n).padStart(2, '0'); }
        function render() {
            const s = Math.max(0, Math.floor((Date.now() - trackingStart) / 1000));
            const txt = pad(Math.floor(s / 3600)) + ':' + pad(Math.floor((s % 3600) / 60)) + ':' + pad(s % 60);
            $('bt-clock').textContent = txt;
            document.title = txt + ' – ' + BASE_TITLE;
        }
        function setRunning(startedAt, startTime) {
            trackingStart = startedAt;
            $('bt-tracker').classList.add('running');
            $('bt-state-text').textContent = 'Rodando desde ' + startTime;
            $('bt-track-label').textContent = 'Parar';
            $('bt-icon-play').hidden = true;
            $('bt-icon-stop').hidden = false;
            render();
            clearInterval(tickTimer);
            tickTimer = setInterval(render, 1000);
        }
        function setStopped() {
            trackingStart = null;
            clearInterval(tickTimer);
            document.title = BASE_TITLE;
            $('bt-tracker').classList.remove('running');
            $('bt-clock').textContent = '00:00:00';
            $('bt-state-text').textContent = IDLE_TEXT;
            $('bt-track-label').textContent = 'Iniciar';
            $('bt-icon-play').hidden = false;
            $('bt-icon-stop').hidden = true;
        }

        fetch('/tracking/status', { headers })
            .then(r => r.json())
            .then(d => { if (d.active) setRunning(d.started_at, d.start_time); })
            .catch(() => {});

        // Sem descrição (ou se o salvamento falhar), leva o intervalo para o formulário manual
        function handOffToForm(tracking, endTime, message) {
            $('bt-date').value = tracking.date;
            $('bt-start').value = tracking.start_time;
            $('bt-end').value = endTime;
            if (tkProject) $('bt-project').value = tkProject.value;
            if (tkDesc && tkDesc.value.trim()) $('bt-description').value = tkDesc.value.trim();
            const note = $('bt-note');
            note.textContent = message;
            note.hidden = false;
            $('bt-form').scrollIntoView({ behavior: 'smooth', block: 'center' });
            $('bt-description').focus({ preventScroll: true });
        }

        window.btToggleTracking = async function () {
            const btn = $('bt-track-btn');
            btn.disabled = true;
            try {
                if (trackingStart === null) {
                    const r = await fetch('/tracking/start', { method: 'POST', headers });
                    const d = await r.json();
                    if (!r.ok || !d.success) throw new Error(firstError(d, 'Não foi possível iniciar o cronômetro.'));
                    setRunning(d.started_at, d.start_time);
                    return;
                }

                const r = await fetch('/tracking/stop', { method: 'POST', headers });
                const d = await r.json();
                if (!r.ok || !d.success) throw new Error(firstError(d, 'Não foi possível parar o cronômetro.'));
                setStopped();

                if (d.auto_saved) {
                    reloadWith('Cronômetro parado. Lançamento salvo automaticamente.');
                    return;
                }

                const desc = tkDesc ? tkDesc.value.trim() : '';
                if (!desc) {
                    handOffToForm(d.tracking, d.end_time, `Cronômetro parado às ${d.end_time}. Descreva o que você fez e salve o lançamento.`);
                    return;
                }

                const r2 = await fetch('/time-entries', {
                    method: 'POST', headers,
                    body: JSON.stringify({
                        date: d.tracking.date,
                        start_time: d.tracking.start_time,
                        end_time: d.end_time,
                        project_id: tkProject.value || null,
                        description: desc,
                    }),
                });
                const d2 = await r2.json();
                if (r2.ok && d2.success) {
                    clearCtx();
                    reloadWith(`Lançamento salvo: ${d.tracking.start_time}–${d.end_time}.`);
                    return;
                }
                handOffToForm(d.tracking, d.end_time,
                    `Cronômetro parado às ${d.end_time}, mas o lançamento não foi salvo: ${firstError(d2, 'erro ao salvar.')} Revise e salve.`);
            } catch (e) {
                showToast(e.message, TOAST_TYPES.ERROR);
            } finally {
                btn.disabled = false;
            }
        };

        // ---------- Novo lançamento ----------
        window.btAddEntry = async function (event) {
            event.preventDefault();
            const err = $('bt-error');
            err.hidden = true;

            const payload = {
                date: $('bt-date').value,
                start_time: $('bt-start').value,
                end_time: $('bt-end').value,
                project_id: $('bt-project').value || null,
                description: $('bt-description').value.trim(),
            };
            if (!payload.date || !payload.start_time || !payload.end_time || !payload.description) {
                err.textContent = 'Preencha data, início, fim e a descrição do que você fez.';
                err.hidden = false;
                return;
            }

            const btn = $('bt-submit');
            btn.disabled = true;
            try {
                const r = await fetch('/time-entries', { method: 'POST', headers, body: JSON.stringify(payload) });
                const d = await r.json();
                if (!r.ok || !d.success) throw new Error(firstError(d, 'Não foi possível salvar o lançamento.'));
                reloadWith('Lançamento salvo.');
            } catch (e) {
                err.textContent = e.message;
                err.hidden = false;
                btn.disabled = false;
            }
        };

        // ---------- Exclusão ----------
        window.btRemoveEntry = function (id) {
            showConfirm('Este lançamento será removido da folha do ciclo.', async () => {
                try {
                    const r = await fetch('/time-entries/' + id, { method: 'DELETE', headers });
                    const d = await r.json();
                    if (!r.ok || !d.success) throw new Error(firstError(d, 'Não foi possível excluir o lançamento.'));
                    reloadWith('Lançamento excluído.');
                } catch (e) {
                    showToast(e.message, TOAST_TYPES.ERROR);
                }
            }, 'Excluir lançamento', 'Excluir');
        };

        // ---------- Dia padrão ----------
        const STANDARD_DAY = {
            periods: @json($standardDayPeriods ?? []),
            projectId: {!! json_encode($standardDayProjectId) !!},
            description: @json($standardDayDescription ?? ''),
        };

        window.btLaunchStandardDay = async function () {
            const err = $('bt-error');
            err.hidden = true;
            const date = $('bt-date').value;
            if (!date) {
                err.textContent = 'Escolha a data antes de lançar o dia padrão.';
                err.hidden = false;
                return;
            }

            const btn = $('bt-std-btn');
            btn.disabled = true;
            let created = 0;
            const errors = [];
            for (const p of STANDARD_DAY.periods) {
                try {
                    const r = await fetch('/time-entries', {
                        method: 'POST', headers,
                        body: JSON.stringify({
                            date, start_time: p.start, end_time: p.end,
                            description: STANDARD_DAY.description || 'Trabalho',
                            project_id: STANDARD_DAY.projectId || null,
                        }),
                    });
                    const d = await r.json();
                    if (!r.ok || !d.success) errors.push(p.start + '–' + p.end + ': ' + firstError(d, 'não foi possível lançar.'));
                    else created++;
                } catch (e) {
                    errors.push(p.start + '–' + p.end + ': não foi possível lançar.');
                }
            }

            if (created > 0 && errors.length === 0) {
                reloadWith(created === 1 ? 'Dia padrão lançado: 1 lançamento salvo.' : `Dia padrão lançado: ${created} lançamentos salvos.`);
                return;
            }
            btn.disabled = false;
            err.textContent = (created > 0 ? `${created} salvo(s). ` : '') + 'Não lançados: ' + errors.join(' ');
            err.hidden = false;
            if (created > 0) setTimeout(() => window.location.reload(), 2500);
        };

        // ---------- Sobreaviso ----------
        const ocDialog = $('bt-oc-dialog');

        function parseMoney(value) {
            if (!value || !value.trim()) return null;
            const n = parseFloat(value.replace(/[^\d,.-]/g, '').replace(/\.(?=\d{3}(\D|$))/g, '').replace(',', '.'));
            return isNaN(n) ? NaN : n;
        }

        window.btOpenOnCall = function (data = null) {
            if (!ocDialog) { showPremiumModal('sobreaviso'); return; }
            $('bt-oc-title').textContent = data ? 'Editar período de sobreaviso' : 'Novo período de sobreaviso';
            $('bt-oc-submit').textContent = data ? 'Salvar alterações' : 'Salvar período';
            $('bt-oc-id').value = data ? data.id : '';
            $('bt-oc-start').value = data ? data.start : '';
            $('bt-oc-end').value = data ? data.end : '';
            $('bt-oc-project').value = data && data.project_id ? String(data.project_id) : '';
            $('bt-oc-rate').value = data && data.hourly_rate ? data.hourly_rate.toFixed(2).replace('.', ',') : '';
            $('bt-oc-description').value = data ? data.description : '';
            $('bt-oc-error').hidden = true;
            ocDialog.showModal();
            $('bt-oc-start').focus();
        };

        window.btEditOnCall = function (button) {
            const li = button.closest('[data-period]');
            btOpenOnCall(JSON.parse(li.dataset.period));
        };

        if (ocDialog) {
            // Fecha ao clicar fora do conteúdo
            ocDialog.addEventListener('click', e => { if (e.target === ocDialog) ocDialog.close(); });
        }

        window.btSaveOnCall = async function (event) {
            event.preventDefault();
            const err = $('bt-oc-error');
            err.hidden = true;
            const show = msg => { err.textContent = msg; err.hidden = false; };

            const id = $('bt-oc-id').value;
            const start = $('bt-oc-start').value;
            const end = $('bt-oc-end').value;
            const rate = parseMoney($('bt-oc-rate').value);

            if (!start || !end) return show('Informe a data e a hora de início e de fim.');
            if (new Date(end) <= new Date(start)) return show('O fim precisa ser depois do início.');
            if (Number.isNaN(rate) || (rate !== null && rate < 0)) return show('Valor por hora inválido. Use, por exemplo, 50,00.');

            const btn = $('bt-oc-submit');
            btn.disabled = true;
            try {
                const r = await fetch(id ? '/on-call/' + id : '/on-call', {
                    method: id ? 'PUT' : 'POST', headers,
                    body: JSON.stringify({
                        start_datetime: start,
                        end_datetime: end,
                        project_id: $('bt-oc-project').value || null,
                        hourly_rate: rate,
                        description: $('bt-oc-description').value.trim() || null,
                    }),
                });
                const d = await r.json();
                if (!r.ok) throw new Error(firstError(d, 'Não foi possível salvar o período.'));
                ocDialog.close();
                reloadWith(id ? 'Período de sobreaviso atualizado.' : 'Período de sobreaviso salvo.');
            } catch (e) {
                show(e.message);
                btn.disabled = false;
            }
        };

        window.btDeleteOnCall = function (id) {
            showConfirm('O período será removido e os lançamentos dentro dele voltam a contar só como horas normais.', async () => {
                try {
                    const r = await fetch('/on-call/' + id, { method: 'DELETE', headers });
                    const d = await r.json();
                    if (!r.ok) throw new Error(firstError(d, 'Não foi possível excluir o período.'));
                    reloadWith('Período de sobreaviso excluído.');
                } catch (e) {
                    showToast(e.message, TOAST_TYPES.ERROR);
                }
            }, 'Excluir período de sobreaviso', 'Excluir');
        };

        window.btRecalculateOnCall = async function () {
            const btn = $('bt-oc-recalc');
            btn.disabled = true;
            try {
                const r = await fetch('/on-call/recalculate?month=' + encodeURIComponent(@json($currentMonth)), { method: 'POST', headers });
                const d = await r.json();
                if (!r.ok) throw new Error(firstError(d, 'Não foi possível recalcular.'));
                reloadWith(d.message || 'Sobreaviso recalculado.');
            } catch (e) {
                showToast(e.message, TOAST_TYPES.ERROR);
                btn.disabled = false;
            }
        };

        // ---------- Abas (lançamentos / sobreaviso) ----------
        const TAB_KEY = 'bt_tab';
        const tabs = Array.from(document.querySelectorAll('.bt [role="tab"]'));

        function selectTab(name, focus = false) {
            tabs.forEach(tab => {
                const on = tab.dataset.tab === name;
                tab.setAttribute('aria-selected', on ? 'true' : 'false');
                tab.tabIndex = on ? 0 : -1;
                $(tab.getAttribute('aria-controls')).hidden = !on;
                if (on && focus) tab.focus();
            });
            try { localStorage.setItem(TAB_KEY, name); } catch (e) {}
        }

        tabs.forEach((tab, i) => {
            tab.addEventListener('click', () => selectTab(tab.dataset.tab));
            tab.addEventListener('keydown', e => {
                const dir = e.key === 'ArrowRight' ? 1 : e.key === 'ArrowLeft' ? -1 : 0;
                if (dir) { e.preventDefault(); selectTab(tabs[(i + dir + tabs.length) % tabs.length].dataset.tab, true); }
                if (e.key === 'Home') { e.preventDefault(); selectTab(tabs[0].dataset.tab, true); }
                if (e.key === 'End') { e.preventDefault(); selectTab(tabs[tabs.length - 1].dataset.tab, true); }
            });
        });

        // Links para #sobreaviso ou #dia-... abrem a aba certa antes de rolar
        function tabFromHash() {
            if (location.hash === '#sobreaviso') return 'sobreaviso';
            if (location.hash.startsWith('#dia-')) return 'folha';
            return null;
        }
        document.addEventListener('click', e => {
            const a = e.target.closest('.bt a[href^="#sobreaviso"], .bt a[href^="#dia-"]');
            if (a) selectTab(a.getAttribute('href') === '#sobreaviso' ? 'sobreaviso' : 'folha');
        });
        window.addEventListener('hashchange', () => {
            const t = tabFromHash();
            if (!t) return;
            selectTab(t);
            document.querySelector(location.hash)?.scrollIntoView({ block: 'start' });
        });
        (function initTab() {
            let initial = tabFromHash();
            if (!initial) { try { initial = localStorage.getItem(TAB_KEY); } catch (e) {} }
            if (initial && tabs.some(t => t.dataset.tab === initial)) selectTab(initial);
        })();

        // ---------- Ajustes do ciclo (valor/hora, acréscimo, desconto) ----------
        const ADJUST = {
            month: @json($currentMonth),
            hourlyRate: {{ (float) $hourlyRate }},
            extra: {{ (float) $extra }},
            discount: {{ (float) $discount }},
        };
        const adjDialog = $('bt-adj-dialog');
        const toBRL = v => Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        window.btOpenAdjust = function () {
            $('bt-adj-rate').value = toBRL(ADJUST.hourlyRate);
            $('bt-adj-extra').value = toBRL(ADJUST.extra);
            $('bt-adj-discount').value = toBRL(ADJUST.discount);
            $('bt-adj-error').hidden = true;
            adjDialog.showModal();
            $('bt-adj-rate').select();
        };
        adjDialog.addEventListener('click', e => { if (e.target === adjDialog) adjDialog.close(); });

        window.btSaveAdjust = async function (event) {
            event.preventDefault();
            const err = $('bt-adj-error');
            err.hidden = true;
            const show = msg => { err.textContent = msg; err.hidden = false; };

            const rate = parseMoney($('bt-adj-rate').value);
            const extra = parseMoney($('bt-adj-extra').value) ?? 0;
            const discount = parseMoney($('bt-adj-discount').value) ?? 0;
            if (rate === null || Number.isNaN(rate) || rate < 0) return show('Informe um valor por hora válido, por exemplo 150,00.');
            if (Number.isNaN(extra) || extra < 0) return show('Acréscimo inválido. Use, por exemplo, 200,00.');
            if (Number.isNaN(discount) || discount < 0) return show('Desconto inválido. Use, por exemplo, 50,00.');

            const btn = $('bt-adj-submit');
            btn.disabled = true;
            try {
                const r = await fetch('/monthly-adjustments/' + encodeURIComponent(ADJUST.month), {
                    method: 'PUT', headers,
                    body: JSON.stringify({ hourly_rate: rate, extra_value: extra, discount_value: discount }),
                });
                const d = await r.json();
                if (!r.ok || !d.success) throw new Error(firstError(d, 'Não foi possível salvar os ajustes.'));
                adjDialog.close();
                reloadWith('Ajustes do ciclo salvos.');
            } catch (e) {
                show(e.message);
                btn.disabled = false;
            }
        };

        // ---------- Modo privacidade (o botão do menu depende desta função) ----------
        if (typeof window.togglePrivacy !== 'function') {
            const sync = hidden => {
                [['eye-open', 'eye-closed'], ['eye-open-mobile', 'eye-closed-mobile']].forEach(([o, c]) => {
                    $(o)?.classList.toggle('hidden', hidden);
                    $(c)?.classList.toggle('hidden', !hidden);
                });
            };
            window.togglePrivacy = function () {
                const hidden = !document.documentElement.classList.contains('privacy-mode');
                document.documentElement.classList.toggle('privacy-mode', hidden);
                try { localStorage.setItem('privacy_mode', String(hidden)); } catch (e) {}
                sync(hidden);
            };
            sync(document.documentElement.classList.contains('privacy-mode'));
        }
    })();
    </script>
    @endpush
</x-app-layout>
