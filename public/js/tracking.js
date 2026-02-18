// Tracking state
let trackingInterval = null;
let confirmCallback = null;
let trackingStartTime = null;

// View mode state
let currentViewMode = 'entries';

// Flatpickr instances
let datePicker = null;

// Toast types
const TOAST_TYPES = {
    SUCCESS: 'success',
    ERROR: 'error',
    WARNING: 'warning',
    INFO: 'info'
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeFlatpickr();
    initializePage();
    restoreTracking();
    restorePrivacy();
    restoreViewMode();
    updateClock();
    setInterval(updateClock, 1000);
});

// ==================== FLATPICKR INITIALIZATION ====================

function initializeFlatpickr() {
    // Configure Flatpickr locale to pt-BR with fallback
    const ptLocale = flatpickr.l10ns.pt || {
        weekdays: {
            shorthand: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"],
            longhand: ["Domingo", "Segunda-feira", "Terca-feira", "Quarta-feira", "Quinta-feira", "Sexta-feira", "Sabado"]
        },
        months: {
            shorthand: ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"],
            longhand: ["Janeiro", "Fevereiro", "Marco", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"]
        },
        rangeSeparator: " ate ",
        time_24hr: true,
        firstDayOfWeek: 0
    };

    flatpickr.localize(ptLocale);

    // Date picker only
    datePicker = flatpickr('#entry-date', {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        locale: ptLocale,
        defaultDate: 'today',
        disableMobile: true,
        onChange: function(selectedDates, dateStr) {
            // Ensure the hidden input has the correct format
        }
    });

    // Initialize time input masks
    initializeTimeMasks();
}

// ==================== TIME INPUT MASK ====================

function initializeTimeMasks() {
    const timeInputs = document.querySelectorAll('#entry-start, #entry-end');

    timeInputs.forEach(input => {
        input.addEventListener('input', handleTimeInput);
        input.addEventListener('blur', validateTimeInput);
        input.addEventListener('keydown', handleTimeKeydown);
    });
}

function handleTimeInput(e) {
    const input = e.target;
    let cursorPos = input.selectionStart;
    const oldLen = input.value.length;

    let digits = input.value.replace(/\D/g, ''); // Remove non-digits

    // Limit to 4 digits
    digits = digits.substring(0, 4);

    // Format as HH:mm
    let newValue = '';
    if (digits.length === 0) {
        newValue = '';
    } else if (digits.length <= 2) {
        newValue = digits;
    } else {
        newValue = digits.substring(0, 2) + ':' + digits.substring(2);
    }

    // Only update if value changed
    if (input.value !== newValue) {
        input.value = newValue;

        // Adjust cursor position for the colon
        const newLen = newValue.length;
        if (newLen > oldLen && cursorPos >= 2 && newValue.includes(':')) {
            cursorPos = Math.min(cursorPos + 1, newLen);
        } else {
            cursorPos = Math.min(cursorPos, newLen);
        }

        input.setSelectionRange(cursorPos, cursorPos);
    }
}

function handleTimeKeydown(e) {
    const input = e.target;

    // Allow: backspace, delete, tab, escape, enter
    if ([8, 9, 27, 13, 46].includes(e.keyCode)) {
        // If Ctrl+Backspace, clear everything
        if (e.ctrlKey && e.keyCode === 8) {
            setTimeout(() => { input.value = ''; }, 0);
        }
        return;
    }

    // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
    if ((e.keyCode === 65 || e.keyCode === 67 || e.keyCode === 86 || e.keyCode === 88) && e.ctrlKey) {
        return;
    }

    // Allow: home, end, left, right
    if (e.keyCode >= 35 && e.keyCode <= 39) {
        return;
    }

    // Block if not a number
    const isNumber = (e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105);
    if (!isNumber || e.shiftKey) {
        e.preventDefault();
        return;
    }

    // Allow typing - handleTimeInput will handle the formatting and max length
}

function validateTimeInput(e) {
    const value = e.target.value;
    if (!value) return;

    // Remove non-digits and pad
    let digits = value.replace(/\D/g, '');

    if (digits.length === 0) {
        e.target.value = '';
        return;
    }

    // Pad with zeros if needed
    if (digits.length === 1) {
        digits = '0' + digits + '00';
    } else if (digits.length === 2) {
        digits = digits + '00';
    } else if (digits.length === 3) {
        digits = '0' + digits;
    }

    let hours = parseInt(digits.substring(0, 2), 10);
    let minutes = parseInt(digits.substring(2, 4), 10);

    // Validate and fix hours
    if (isNaN(hours) || hours < 0) hours = 0;
    if (hours > 23) hours = 23;

    // Validate and fix minutes
    if (isNaN(minutes) || minutes < 0) minutes = 0;
    if (minutes > 59) minutes = 59;

    e.target.value = String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0');
}

// ==================== TOAST SYSTEM ====================

function showToast(message, type = TOAST_TYPES.INFO, duration = 4000) {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = 'toast-item';

    // Estilos base do toast
    toast.style.cssText = `
        transform: translateX(100%);
        opacity: 0;
        transition: all 0.3s ease-out;
    `;

    const styles = {
        success: {
            bg: '#064e3b',
            border: '#10b981',
            text: '#a7f3d0',
            icon: '#34d399'
        },
        error: {
            bg: '#7f1d1d',
            border: '#ef4444',
            text: '#fecaca',
            icon: '#f87171'
        },
        warning: {
            bg: '#78350f',
            border: '#f59e0b',
            text: '#fde68a',
            icon: '#fbbf24'
        },
        info: {
            bg: '#164e63',
            border: '#06b6d4',
            text: '#a5f3fc',
            icon: '#22d3ee'
        }
    };

    const style = styles[type] || styles.info;

    const icons = {
        success: `<svg style="width:20px;height:20px;color:${style.icon}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>`,
        error: `<svg style="width:20px;height:20px;color:${style.icon}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>`,
        warning: `<svg style="width:20px;height:20px;color:${style.icon}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>`,
        info: `<svg style="width:20px;height:20px;color:${style.icon}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>`
    };

    toast.innerHTML = `
        <div style="
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border-radius: 10px;
            border: 2px solid ${style.border};
            background: ${style.bg};
            color: ${style.text};
            box-shadow: 0 10px 25px rgba(0,0,0,0.4), 0 0 20px ${style.border}40;
            font-size: 14px;
            font-weight: 500;
            min-width: 280px;
            max-width: 400px;
        ">
            ${icons[type] || icons.info}
            <span style="flex:1">${escapeHtml(message)}</span>
            <button onclick="this.closest('.toast-item').remove()" style="
                background: none;
                border: none;
                cursor: pointer;
                opacity: 0.7;
                transition: opacity 0.2s;
                padding: 4px;
                color: ${style.text};
            " onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">
                <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    `;

    container.appendChild(toast);

    // Animate in
    requestAnimationFrame(() => {
        toast.style.transform = 'translateX(0)';
        toast.style.opacity = '1';
    });

    // Auto remove
    setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

// ==================== CONFIRM MODAL ====================

function showConfirm(message, callback, title = 'Confirmar') {
    const modal = document.getElementById('confirm-modal');
    const titleEl = document.getElementById('confirm-title');
    const messageEl = document.getElementById('confirm-message');

    if (!modal) return;

    titleEl.textContent = title;
    messageEl.textContent = message;
    confirmCallback = callback;

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function executeConfirm() {
    // Salvar callback antes de fechar o modal (closeConfirmModal zera o callback)
    const callback = confirmCallback;
    closeConfirmModal();
    if (callback) {
        callback();
    }
}

function closeConfirmModal() {
    const modal = document.getElementById('confirm-modal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
    confirmCallback = null;
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeConfirmModal();
    }
});

// ==================== PAGE INITIALIZATION ====================

function initializePage() {
    // Date is already set by Flatpickr defaultDate
}

// Update clock display
function updateClock() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('pt-BR');
    const dateString = now.toLocaleDateString('pt-BR', {
        weekday: 'long',
        day: '2-digit',
        month: 'long'
    });

    document.getElementById('current-time').textContent = timeString;
    document.getElementById('current-date').textContent = dateString.charAt(0).toUpperCase() + dateString.slice(1);
}

// ==================== FORMATTING ====================

function formatCurrency(value) {
    return value.toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    });
}

function formatHours(hours) {
    const h = Math.floor(hours);
    const m = Math.round((hours - h) * 60);
    return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0');
}

// ==================== TRACKING (SERVER-BASED) ====================

async function restoreTracking() {
    try {
        const response = await fetch('/tracking/status', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            }
        });

        const data = await response.json();

        if (data.active) {
            trackingStartTime = new Date(data.started_at);

            // Set form fields
            if (datePicker) {
                datePicker.setDate(data.date, true);
            }
            document.getElementById('entry-start').value = data.start_time;

            // Resume tracking UI
            startTrackingUI(trackingStartTime);
        }
    } catch (error) {
        console.error('Erro ao verificar status do tracking:', error);
    }
}

function formatTimeInput(date) {
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${hours}:${minutes}`;
}

async function toggleTracking() {
    // Check current state from tracking start time
    if (trackingStartTime !== null) {
        await stopTracking();
    } else {
        await startTracking();
    }
}

async function startTracking() {
    try {
        const response = await fetch('/tracking/start', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            trackingStartTime = new Date(data.started_at);

            // Update form fields
            if (datePicker) {
                datePicker.setDate(data.date, true);
            }
            document.getElementById('entry-start').value = data.start_time;
            document.getElementById('entry-end').value = '';

            // Start UI tracking
            startTrackingUI(trackingStartTime);

            showToast('Tracking iniciado!', TOAST_TYPES.SUCCESS);
        }
    } catch (error) {
        showToast('Erro ao iniciar tracking', TOAST_TYPES.ERROR);
        console.error('Erro ao iniciar tracking:', error);
    }
}

function startTrackingUI(startTime) {
    const btn = document.getElementById('track-btn');
    const btnText = document.getElementById('track-btn-text');
    const iconPlay = document.getElementById('track-icon-play');
    const iconPause = document.getElementById('track-icon-pause');

    btn.classList.remove('bg-emerald-600', 'hover:bg-emerald-700', 'hover:shadow-emerald-500/30');
    btn.classList.add('bg-red-600', 'hover:bg-red-700', 'hover:shadow-red-500/30');

    // Toggle icons: hide play, show pause
    if (iconPlay) iconPlay.classList.add('hidden');
    if (iconPause) iconPause.classList.remove('hidden');

    // Update button text with elapsed time
    trackingInterval = setInterval(() => {
        const elapsed = Math.floor((new Date() - startTime) / 1000);
        const hours = Math.floor(elapsed / 3600);
        const minutes = Math.floor((elapsed % 3600) / 60);
        const seconds = elapsed % 60;

        btnText.textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    }, 1000);

    // Initial update
    const elapsed = Math.floor((new Date() - startTime) / 1000);
    const hours = Math.floor(elapsed / 3600);
    const minutes = Math.floor((elapsed % 3600) / 60);
    const seconds = elapsed % 60;
    btnText.textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
}

async function stopTracking() {
    try {
        const response = await fetch('/tracking/stop', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            // Clear interval
            if (trackingInterval) {
                clearInterval(trackingInterval);
                trackingInterval = null;
            }

            // Clear tracking start time
            trackingStartTime = null;

            // Reset button UI
            const btn = document.getElementById('track-btn');
            const btnText = document.getElementById('track-btn-text');
            const iconPlay = document.getElementById('track-icon-play');
            const iconPause = document.getElementById('track-icon-pause');

            btn.classList.remove('bg-red-600', 'hover:bg-red-700', 'hover:shadow-red-500/30');
            btn.classList.add('bg-emerald-600', 'hover:bg-emerald-700', 'hover:shadow-emerald-500/30');
            btnText.textContent = 'Iniciar Tracking';

            // Toggle icons: show play, hide pause
            if (iconPlay) iconPlay.classList.remove('hidden');
            if (iconPause) iconPause.classList.add('hidden');

            // Check if auto-saved
            if (data.auto_saved && data.entry) {
                // Add entry to table and cards
                addEntryToTable(data.entry);
                addEntryToCards(data.entry);

                // Update stats
                if (data.stats) {
                    updateStats(data.stats);
                }

                // Clear form fields
                document.getElementById('entry-start').value = '';
                document.getElementById('entry-end').value = '';
                document.getElementById('entry-description').value = '';

                showToast('Lançamento salvo automaticamente!', TOAST_TYPES.SUCCESS);
            } else {
                // Update form field with end time for manual save
                document.getElementById('entry-end').value = data.end_time;
                showToast('Tracking parado. Preencha a descrição e adicione o lançamento.', TOAST_TYPES.INFO);
            }
        }
    } catch (error) {
        showToast('Erro ao parar tracking', TOAST_TYPES.ERROR);
        console.error('Erro ao parar tracking:', error);
    }
}

// ==================== ENTRIES CRUD ====================

async function addEntry() {
    const date = document.getElementById('entry-date').value;
    const startTime = document.getElementById('entry-start').value;
    const endTime = document.getElementById('entry-end').value;
    const description = document.getElementById('entry-description').value;
    const projectSelect = document.getElementById('entry-project');
    const projectId = projectSelect ? projectSelect.value : null;

    // Validation
    if (!date || !startTime || !endTime || !description) {
        showToast('Por favor, preencha todos os campos!', TOAST_TYPES.WARNING);
        return;
    }

    if (endTime <= startTime) {
        showToast('O horario de fim deve ser maior que o horario de inicio!', TOAST_TYPES.WARNING);
        return;
    }

    try {
        const response = await fetch('/time-entries', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                date: date,
                start_time: startTime,
                end_time: endTime,
                description: description,
                project_id: projectId || null
            })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Erro ao adicionar lancamento');
        }

        if (data.success) {
            // Add entry to table
            addEntryToTable(data.entry);

            // Add entry to mobile cards
            addEntryToCards(data.entry);

            // Update stats
            updateStats(data.stats);

            // Clear form
            document.getElementById('entry-start').value = '';
            document.getElementById('entry-end').value = '';
            document.getElementById('entry-description').value = '';

            // Stop tracking if active (just reset UI, server already handled)
            if (trackingStartTime !== null) {
                if (trackingInterval) {
                    clearInterval(trackingInterval);
                    trackingInterval = null;
                }
                trackingStartTime = null;

                const btn = document.getElementById('track-btn');
                const btnText = document.getElementById('track-btn-text');
                const iconPlay = document.getElementById('track-icon-play');
                const iconPause = document.getElementById('track-icon-pause');

                btn.classList.remove('bg-red-600', 'hover:bg-red-700', 'hover:shadow-red-500/30');
                btn.classList.add('bg-emerald-600', 'hover:bg-emerald-700', 'hover:shadow-emerald-500/30');
                btnText.textContent = 'Iniciar Tracking';

                // Toggle icons: show play, hide pause
                if (iconPlay) iconPlay.classList.remove('hidden');
                if (iconPause) iconPause.classList.add('hidden');

                // Also stop on server
                fetch('/tracking/stop', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    }
                }).catch(() => {});
            }

            showToast('Lançamento adicionado com sucesso!', TOAST_TYPES.SUCCESS);
        }
    } catch (error) {
        showToast(error.message, TOAST_TYPES.ERROR);
    }
}

function addEntryToTable(entry) {
    const tbody = document.getElementById('entries-table');
    const emptyRow = document.getElementById('empty-row');

    // Remove empty message if exists
    if (emptyRow) {
        emptyRow.remove();
    }

    const entryValue = entry.hours * HOURLY_RATE;
    const projectHtml = entry.project_name
        ? `<span class="bg-purple-500/20 text-purple-300 px-2 py-1 rounded text-xs">${escapeHtml(entry.project_name)}</span>`
        : '<span class="text-gray-500">-</span>';

    const row = document.createElement('tr');
    row.className = 'hover:bg-gray-800/50 transition-colors';
    row.setAttribute('data-entry-id', entry.id);
    row.innerHTML = `
        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-300">${entry.date_formatted}</td>
        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-300 font-mono">${entry.start_time} - ${entry.end_time}</td>
        <td class="px-4 py-4 whitespace-nowrap text-sm">
            <span class="bg-cyan-500/20 text-cyan-300 px-3 py-1 rounded-full font-medium">${formatHours(parseFloat(entry.hours))}</span>
        </td>
        <td class="px-4 py-4 whitespace-nowrap text-sm">${projectHtml}</td>
        <td class="px-4 py-4 text-sm text-gray-300 max-w-xs truncate">${escapeHtml(entry.description)}</td>
        <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-emerald-400 sensitive-value">${formatCurrency(entryValue)}</td>
        <td class="px-4 py-4 whitespace-nowrap text-sm">
            <button onclick="removeEntry(${entry.id})"
                class="text-red-400 hover:text-red-300 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </td>
    `;

    // Insert at the beginning
    tbody.insertBefore(row, tbody.firstChild);
}

function addEntryToCards(entry) {
    const container = document.getElementById('entries-cards');
    const emptyCard = document.getElementById('empty-card');

    // Remove empty message if exists
    if (emptyCard) {
        emptyCard.remove();
    }

    const entryValue = entry.hours * HOURLY_RATE;
    const projectHtml = entry.project_name
        ? `<span class="inline-block bg-purple-500/20 text-purple-300 px-2 py-0.5 rounded text-xs mb-2">${escapeHtml(entry.project_name)}</span>`
        : '';

    const card = document.createElement('div');
    card.className = 'p-4 hover:bg-gray-800/50 transition-colors';
    card.setAttribute('data-entry-id', entry.id);
    card.innerHTML = `
        <div class="flex items-start justify-between mb-2">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-white font-medium">${entry.date_formatted}</span>
                <span class="text-gray-400 font-mono text-sm">${entry.start_time} - ${entry.end_time}</span>
            </div>
            <button onclick="removeEntry(${entry.id})"
                class="text-red-400 hover:text-red-300 transition-colors p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>
        ${projectHtml}
        <p class="text-gray-300 text-sm mb-3">${escapeHtml(entry.description)}</p>
        <div class="flex items-center justify-between">
            <span class="bg-cyan-500/20 text-cyan-300 px-3 py-1 rounded-full text-sm font-medium">${formatHours(parseFloat(entry.hours))}</span>
            <span class="text-emerald-400 font-semibold sensitive-value">${formatCurrency(entryValue)}</span>
        </div>
    `;

    // Insert at the beginning
    container.insertBefore(card, container.firstChild);
}

function removeEntry(id) {
    showConfirm('Deseja realmente remover este lancamento?', async () => {
        try {
            const response = await fetch(`/time-entries/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Erro ao remover lancamento');
            }

            if (data.success) {
                // Remove row from table
                const row = document.querySelector(`tr[data-entry-id="${id}"]`);
                if (row) {
                    row.remove();
                }

                // Remove card from mobile view
                const card = document.querySelector(`div[data-entry-id="${id}"]`);
                if (card) {
                    card.remove();
                }

                // Update stats
                updateStats(data.stats);

                // Check if table is empty
                const tbody = document.getElementById('entries-table');
                if (tbody && tbody.children.length === 0) {
                    const emptyRow = document.createElement('tr');
                    emptyRow.id = 'empty-row';
                    emptyRow.innerHTML = `
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            Nenhum lancamento encontrado para este mes.
                        </td>
                    `;
                    tbody.appendChild(emptyRow);
                }

                // Check if cards container is empty
                const cardsContainer = document.getElementById('entries-cards');
                if (cardsContainer && cardsContainer.children.length === 0) {
                    const emptyCard = document.createElement('div');
                    emptyCard.id = 'empty-card';
                    emptyCard.className = 'p-8 text-center text-gray-500';
                    emptyCard.textContent = 'Nenhum lancamento encontrado para este mes.';
                    cardsContainer.appendChild(emptyCard);
                }

                showToast('Lancamento removido com sucesso!', TOAST_TYPES.SUCCESS);
            }
        } catch (error) {
            showToast(error.message, TOAST_TYPES.ERROR);
        }
    }, 'Remover Lancamento');
}

// ==================== STATS ====================

function updateStats(stats) {
    document.getElementById('total-hours').textContent = formatHours(parseFloat(stats.total_hours));
    document.getElementById('total-revenue').textContent = formatCurrency(stats.total_revenue);

    // Update hourly rate
    const hourlyRateEl = document.getElementById('hourly-rate');
    if (hourlyRateEl && stats.hourly_rate !== undefined) {
        hourlyRateEl.textContent = formatCurrency(stats.hourly_rate);
    }

    // Update extra value
    const extraValueEl = document.getElementById('extra-value');
    if (extraValueEl) {
        extraValueEl.textContent = '+' + formatCurrency(stats.extra_value);
    }

    // Update discount value
    const discountValueEl = document.getElementById('discount-value');
    if (discountValueEl && stats.discount_value !== undefined) {
        discountValueEl.textContent = '-' + formatCurrency(stats.discount_value);
    }

    // Update total final (with extra and discount; prefer total_final_with_on_call if present)
    const totalFinalEl = document.getElementById('total-final');
    if (totalFinalEl) {
        const finalValue = stats.total_final_with_on_call ?? stats.total_final;
        if (finalValue !== undefined) {
            totalFinalEl.textContent = formatCurrency(finalValue);
        }
    }

    // Update company revenues (dynamic cards)
    if (stats.company_revenues) {
        Object.values(stats.company_revenues).forEach(company => {
            const el = document.querySelector(`[data-company-id="${company.id}"] .company-revenue`);
            if (el) {
                el.textContent = formatCurrency(company.revenue);
            }
        });
    }

    // Update unassigned revenue
    const unassignedEl = document.querySelector('.unassigned-revenue');
    if (unassignedEl && stats.unassigned_revenue !== undefined) {
        unassignedEl.textContent = formatCurrency(stats.unassigned_revenue);
    }
}

// ==================== NAVIGATION ====================

function changeMonth(month) {
    window.location.href = `/dashboard?month=${month}`;
}

// ==================== UTILITIES ====================

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ==================== PRIVACY MODE ====================

function restorePrivacy() {
    const isHidden = localStorage.getItem('privacy_mode') === 'true';
    if (isHidden) {
        applyPrivacyMode(true);
    }
}

function togglePrivacy() {
    const isCurrentlyHidden = document.documentElement.classList.contains('privacy-mode');
    const newState = !isCurrentlyHidden;

    localStorage.setItem('privacy_mode', newState.toString());
    applyPrivacyMode(newState);
}

function applyPrivacyMode(hidden) {
    const eyeOpen = document.getElementById('eye-open');
    const eyeClosed = document.getElementById('eye-closed');
    const eyeOpenMobile = document.getElementById('eye-open-mobile');
    const eyeClosedMobile = document.getElementById('eye-closed-mobile');

    if (hidden) {
        document.documentElement.classList.add('privacy-mode');
        if (eyeOpen) eyeOpen.classList.add('hidden');
        if (eyeClosed) eyeClosed.classList.remove('hidden');
        if (eyeOpenMobile) eyeOpenMobile.classList.add('hidden');
        if (eyeClosedMobile) eyeClosedMobile.classList.remove('hidden');
    } else {
        document.documentElement.classList.remove('privacy-mode');
        if (eyeOpen) eyeOpen.classList.remove('hidden');
        if (eyeClosed) eyeClosed.classList.add('hidden');
        if (eyeOpenMobile) eyeOpenMobile.classList.remove('hidden');
        if (eyeClosedMobile) eyeClosedMobile.classList.add('hidden');
    }
}

// ==================== VIEW MODE (BATIDAS / POR DIA) ====================

function restoreViewMode() {
    let savedMode = localStorage.getItem('view_mode') || 'entries';

    // If user doesn't have premium, force entries mode
    if (savedMode === 'daily' && typeof CAN_VIEW_BY_DAY !== 'undefined' && !CAN_VIEW_BY_DAY) {
        savedMode = 'entries';
        localStorage.setItem('view_mode', 'entries');
    }

    setViewMode(savedMode, false);
}

function setViewMode(mode, save = true) {
    // Check if user can view by day (premium feature)
    if (mode === 'daily' && typeof CAN_VIEW_BY_DAY !== 'undefined' && !CAN_VIEW_BY_DAY) {
        // Dispatch event to open premium modal
        window.dispatchEvent(new CustomEvent('open-premium-modal'));
        return;
    }

    currentViewMode = mode;

    // Save preference
    if (save) {
        localStorage.setItem('view_mode', mode);
    }

    // Update html class for CSS-based visibility (prevents flash)
    if (mode === 'daily') {
        document.documentElement.classList.add('view-mode-daily');
    } else {
        document.documentElement.classList.remove('view-mode-daily');
    }

    // Get view containers
    const viewEntries = document.getElementById('view-entries');
    const viewDaily = document.getElementById('view-daily');
    const btnEntries = document.getElementById('view-entries-btn');
    const btnDaily = document.getElementById('view-daily-btn');
    const title = document.getElementById('entries-title');

    if (!viewEntries || !viewDaily) return;

    // Toggle visibility
    if (mode === 'entries') {
        viewEntries.classList.remove('hidden');
        viewDaily.classList.add('hidden');
        if (title) title.textContent = 'Últimos lançamentos';
    } else {
        viewEntries.classList.add('hidden');
        viewDaily.classList.remove('hidden');
        if (title) title.textContent = 'Lançamentos por dia';
    }

    // Update button styles
    if (btnEntries && btnDaily) {
        if (mode === 'entries') {
            btnEntries.classList.add('bg-cyan-600', 'text-white');
            btnEntries.classList.remove('text-gray-400', 'hover:text-white');
            btnDaily.classList.remove('bg-cyan-600', 'text-white');
            btnDaily.classList.add('text-gray-400', 'hover:text-white');
        } else {
            btnDaily.classList.add('bg-cyan-600', 'text-white');
            btnDaily.classList.remove('text-gray-400', 'hover:text-white');
            btnEntries.classList.remove('bg-cyan-600', 'text-white');
            btnEntries.classList.add('text-gray-400', 'hover:text-white');
        }
    }
}

function toggleDayDetails(dateKey) {
    const detailsRow = document.getElementById(`details-${dateKey}`);
    const chevron = document.getElementById(`chevron-${dateKey}`);

    if (!detailsRow) return;

    const isHidden = detailsRow.classList.contains('hidden');

    if (isHidden) {
        detailsRow.classList.remove('hidden');
        if (chevron) chevron.classList.add('rotate-180');
    } else {
        detailsRow.classList.add('hidden');
        if (chevron) chevron.classList.remove('rotate-180');
    }
}
