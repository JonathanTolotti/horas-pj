(function () {
    var TOUR_KEY = 'horas_pj_tour_v1';

    function buildSteps() {
        var steps = [];

        // 1. Boas-vindas
        steps.push({
            popover: {
                title: 'Bem-vindo ao Horas PJ!',
                description: 'Este guia vai te mostrar todas as funcionalidades do sistema. Você pode fechar a qualquer momento e rever clicando no ícone <strong>?</strong> no menu.',
            }
        });

        // 2. Dashboard
        if (document.getElementById('nav-dashboard-link')) {
            steps.push({
                element: '#nav-dashboard-link',
                popover: {
                    title: 'Dashboard',
                    description: 'A tela principal do sistema. Aqui você visualiza o resumo do mês, lança horas de trabalho e acompanha seu faturamento em tempo real.',
                    side: 'bottom',
                    align: 'start',
                }
            });
        }

        // 3. Relatórios
        if (document.getElementById('nav-reports-btn')) {
            steps.push({
                element: '#nav-reports-btn',
                popover: {
                    title: 'Relatórios',
                    description: 'Acesse todos os relatórios e exportações:<br><br><strong>Exportar PDFs</strong> — relatório mensal, anual e NF por empresa<br><strong>Consolidação</strong> — visão multi-mês dos seus dados<br><strong>Analytics</strong> — gráficos e análise de tendências<br><strong>Faturas</strong> — gerencie suas notas fiscais emitidas',
                    side: 'bottom',
                    align: 'start',
                }
            });
        }

        // 4. Empresas
        if (document.getElementById('nav-companies-link')) {
            steps.push({
                element: '#nav-companies-link',
                popover: {
                    title: 'Empresas e Faturamento por NF',
                    description: 'Cadastre as empresas para as quais você presta serviço. Com as empresas cadastradas você pode:<br><br>Vincular lançamentos de horas a cada empresa<br>Gerar o <strong>Relatório para NF</strong> com horas e valores separados por empresa<br>Visualizar a receita de cada CNPJ no resumo mensal',
                    side: 'bottom',
                    align: 'start',
                }
            });
        }

        // 5. Configurações
        if (document.getElementById('nav-settings-btn')) {
            steps.push({
                element: '#nav-settings-btn',
                popover: {
                    title: 'Configurações',
                    description: 'Configure o sistema para o seu perfil:<br><br><strong>Valor/hora</strong> — quanto você cobra por hora trabalhada<br><strong>Projetos</strong> — organize lançamentos por projeto ou cliente<br><strong>Empresas</strong> — cadastre CNPJs para emissão de NF<br><strong>Valor/hora sobreaviso</strong> — tarifa diferenciada para plantão<br><strong>Ajustes mensais</strong> — acréscimos e descontos fixos por mês',
                    side: 'bottom',
                    align: 'start',
                }
            });
        }

        // 6. Seletor de mês
        if (document.getElementById('month-filter')) {
            steps.push({
                element: '#month-filter',
                popover: {
                    title: 'Seletor de Mês',
                    description: 'Navegue entre os meses para visualizar ou lançar horas. Todo o dashboard — resumos, lançamentos e cálculos — muda conforme o mês selecionado.',
                    side: 'bottom',
                    align: 'start',
                }
            });
        }

        // 7. Botão Exportar
        if (document.getElementById('export-dropdown-btn')) {
            steps.push({
                element: '#export-dropdown-btn',
                popover: {
                    title: 'Exportar e Importar',
                    description: 'Acesse as opções de exportação e importação:<br><br><strong>Relatório PDF</strong> — relatório completo do mês<br><strong>Planilha CSV</strong> — para abrir no Excel<br><strong>Relatório para NF</strong> — horas e valores por empresa para emissão de nota fiscal<br><strong>Importar CSV</strong> — lançar várias horas de uma vez',
                    side: 'bottom',
                    align: 'end',
                }
            });
        }

        // 8. Cards de resumo
        if (document.getElementById('summary-cards')) {
            steps.push({
                element: '#summary-cards',
                popover: {
                    title: 'Resumo do Mês',
                    description: 'Acompanhe em tempo real o total de horas trabalhadas, seu valor/hora configurado, o faturamento calculado automaticamente e os ajustes mensais (acréscimos e descontos fixos).',
                    side: 'bottom',
                    align: 'start',
                }
            });
        }

        // 9. Total final
        if (document.getElementById('total-final-card')) {
            steps.push({
                element: '#total-final-card',
                popover: {
                    title: 'Total Final do Mês',
                    description: 'O valor total é calculado automaticamente:<br><br><strong>horas trabalhadas × valor/hora<br>+ acréscimos fixos<br>− descontos fixos<br>+ receita de sobreaviso</strong>',
                    side: 'top',
                    align: 'start',
                }
            });
        }

        // 10. Formulário de lançamento
        if (document.getElementById('entry-form')) {
            steps.push({
                element: '#entry-form',
                popover: {
                    title: 'Novo Lançamento de Horas',
                    description: 'Aqui você registra suas horas de trabalho. Preencha a data, horário de início, fim, projeto e uma descrição.',
                    side: 'top',
                    align: 'start',
                }
            });

            // 11. Data (aponta para o wrapper do campo, não para o input)
            if (document.getElementById('entry-date-wrapper')) {
                steps.push({
                    element: '#entry-date-wrapper',
                    popover: {
                        title: 'Data do Trabalho',
                        description: 'Selecione a data em que você trabalhou. O campo já vem preenchido com a data de hoje por padrão.',
                        side: 'bottom',
                        align: 'start',
                    }
                });
            }

            // 12. Horário início/fim
            if (document.getElementById('entry-start')) {
                steps.push({
                    element: '#entry-start',
                    popover: {
                        title: 'Horário de Início e Fim',
                        description: 'Informe o horário que você começou (ex: <strong>09:00</strong>) e quando terminou (ex: <strong>18:00</strong>). O total de horas é calculado automaticamente.',
                        side: 'bottom',
                        align: 'start',
                    }
                });
            }

            // 13. Botão Adicionar
            if (document.querySelector('button[onclick="addEntry()"]')) {
                steps.push({
                    element: 'button[onclick="addEntry()"]',
                    popover: {
                        title: 'Salvar o Lançamento',
                        description: 'Clique em <strong>Adicionar</strong> para registrar o lançamento. O resumo do mês é atualizado imediatamente.',
                        side: 'top',
                        align: 'start',
                    }
                });
            }

            // 14. Botão Dia Padrão (condicional — aparece quando há horário padrão configurado)
            if (document.getElementById('standard-day-btn')) {
                steps.push({
                    element: '#standard-day-btn',
                    popover: {
                        title: 'Dia Padrão',
                        description: 'Lança automaticamente o <strong>horário padrão configurado</strong> para a data de hoje. Ideal para quem trabalha sempre no mesmo horário — um clique registra o dia inteiro sem precisar preencher os campos manualmente.',
                        side: 'top',
                        align: 'start',
                    }
                });
            }
        }

        // 15. Tracking
        if (document.getElementById('track-btn')) {
            steps.push({
                element: '#track-btn',
                popover: {
                    title: 'Tracking em Tempo Real',
                    description: 'Clique em <strong>Iniciar Tracking</strong> para cronometrar seu trabalho em tempo real. Ao parar, o sistema calcula e registra o tempo automaticamente — sem precisar preencher horário de início e fim.',
                    side: 'top',
                    align: 'start',
                }
            });
        }

        // 16. Lista de lançamentos
        if (document.getElementById('entries-section')) {
            steps.push({
                element: '#entries-section',
                popover: {
                    title: 'Lista de Lançamentos',
                    description: 'Todos os lançamentos do mês aparecem aqui. Você pode excluí-los individualmente ou alternar entre a visualização por <strong>Batidas</strong> (cada entrada separada) e <strong>Por Dia</strong> (agrupado por data).',
                    side: 'top',
                    align: 'start',
                }
            });
        }

        // 17. Seção de Sobreaviso (sempre presente no dashboard)
        if (document.getElementById('on-call-section')) {
            steps.push({
                element: '#on-call-section',
                popover: {
                    title: 'Sobreaviso (Plantão)',
                    description: 'Gerencie seus períodos de sobreaviso aqui. O sobreaviso é o período em que você fica de plantão disponível para ser acionado.<br><br>Horas efetivamente trabalhadas dentro do sobreaviso são pagas como horas normais e deduzidas do período — o restante é pago como sobreaviso.<br><br>Configure o valor/hora do sobreaviso nas <strong>Configurações</strong>.',
                    side: 'top',
                    align: 'start',
                }
            });
        }

        // 18. Faturamento por Empresa (sempre presente no dashboard)
        if (document.getElementById('company-revenue-section')) {
            steps.push({
                element: '#company-revenue-section',
                popover: {
                    title: 'Faturamento por Empresa',
                    description: 'Visualize o faturamento separado por empresa. Cada card mostra as horas e o valor de cada CNPJ no mês.<br><br>Para habilitar esta divisão, cadastre suas empresas em <strong>Empresas</strong> e vincule os lançamentos de horas a cada empresa ao criar um projeto.',
                    side: 'top',
                    align: 'start',
                }
            });
        }

        // 19. Conclusão
        steps.push({
            popover: {
                title: 'Tudo pronto!',
                description: 'Agora você conhece todas as funcionalidades do Horas PJ!<br><br>Comece pelas <strong>Configurações</strong> para definir seu valor/hora. Depois registre seus primeiros lançamentos no <strong>Dashboard</strong>.<br><br>Sempre que quiser rever o tutorial, clique no ícone <strong>?</strong> no menu.',
            }
        });

        return steps;
    }

    window.startTour = function () {
        if (!window.driver || !window.driver.js) {
            console.warn('[Tour] driver.js não carregado.');
            return;
        }

        var driverFn = window.driver.js.driver;

        var driverObj = driverFn({
            animate: true,
            showProgress: true,
            progressText: '{{current}} de {{total}}',
            nextBtnText: 'Próximo →',
            prevBtnText: '← Anterior',
            doneBtnText: '✓ Concluir',
            allowClose: true,
            steps: buildSteps(),
            onDestroyed: function () {
                localStorage.setItem(TOUR_KEY, 'done');
            },
        });

        driverObj.drive();
    };

    function initTour() {
        if (!document.getElementById('entry-form')) return;
        if (localStorage.getItem(TOUR_KEY)) return;
        setTimeout(window.startTour, 800);
    }

    document.addEventListener('DOMContentLoaded', initTour);
})();
