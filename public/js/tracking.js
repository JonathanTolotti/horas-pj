// Tracking state
let trackingInterval = null;

// Storage keys
const STORAGE_KEYS = {
    ACTIVE: 'tracking_active',
    START_TIME: 'tracking_start_time',
    DATE: 'tracking_date'
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializePage();
    restoreTracking();
    updateClock();
    setInterval(updateClock, 1000);
});

// Initialize page with current date
function initializePage() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('entry-date').value = today;
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

// Format currency
function formatCurrency(value) {
    return value.toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    });
}

// Format hours
function formatHours(hours) {
    return hours.toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }) + 'h';
}

// Restore tracking state from localStorage
function restoreTracking() {
    const isActive = localStorage.getItem(STORAGE_KEYS.ACTIVE) === 'true';
    const startTime = localStorage.getItem(STORAGE_KEYS.START_TIME);
    const trackingDate = localStorage.getItem(STORAGE_KEYS.DATE);

    if (isActive && startTime && trackingDate) {
        const savedDate = new Date(parseInt(startTime));

        // Set form fields
        document.getElementById('entry-date').value = trackingDate;
        document.getElementById('entry-start').value = formatTimeInput(savedDate);

        // Resume tracking UI
        startTrackingUI(savedDate);
    }
}

// Format time for input field
function formatTimeInput(date) {
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${hours}:${minutes}`;
}

// Toggle tracking
function toggleTracking() {
    const isActive = localStorage.getItem(STORAGE_KEYS.ACTIVE) === 'true';

    if (isActive) {
        stopTracking();
    } else {
        startTracking();
    }
}

// Start tracking
function startTracking() {
    const now = new Date();
    const today = now.toISOString().split('T')[0];

    // Save to localStorage
    localStorage.setItem(STORAGE_KEYS.ACTIVE, 'true');
    localStorage.setItem(STORAGE_KEYS.START_TIME, now.getTime().toString());
    localStorage.setItem(STORAGE_KEYS.DATE, today);

    // Update form fields
    document.getElementById('entry-date').value = today;
    document.getElementById('entry-start').value = formatTimeInput(now);
    document.getElementById('entry-end').value = '';

    // Start UI tracking
    startTrackingUI(now);
}

// Start tracking UI updates
function startTrackingUI(startTime) {
    const btn = document.getElementById('track-btn');
    const btnText = document.getElementById('track-btn-text');

    btn.classList.remove('bg-emerald-600', 'hover:bg-emerald-700', 'hover:shadow-emerald-500/30');
    btn.classList.add('bg-red-600', 'hover:bg-red-700', 'hover:shadow-red-500/30');

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

// Stop tracking
function stopTracking() {
    const now = new Date();

    // Clear interval
    if (trackingInterval) {
        clearInterval(trackingInterval);
        trackingInterval = null;
    }

    // Update form field with end time
    document.getElementById('entry-end').value = formatTimeInput(now);

    // Clear localStorage
    localStorage.removeItem(STORAGE_KEYS.ACTIVE);
    localStorage.removeItem(STORAGE_KEYS.START_TIME);
    localStorage.removeItem(STORAGE_KEYS.DATE);

    // Reset button UI
    const btn = document.getElementById('track-btn');
    const btnText = document.getElementById('track-btn-text');

    btn.classList.remove('bg-red-600', 'hover:bg-red-700', 'hover:shadow-red-500/30');
    btn.classList.add('bg-emerald-600', 'hover:bg-emerald-700', 'hover:shadow-emerald-500/30');
    btnText.textContent = 'Iniciar Tracking';
}

// Add new entry
async function addEntry() {
    const date = document.getElementById('entry-date').value;
    const startTime = document.getElementById('entry-start').value;
    const endTime = document.getElementById('entry-end').value;
    const description = document.getElementById('entry-description').value;

    // Validation
    if (!date || !startTime || !endTime || !description) {
        alert('Por favor, preencha todos os campos!');
        return;
    }

    if (endTime <= startTime) {
        alert('O horario de fim deve ser maior que o horario de inicio!');
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
                description: description
            })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Erro ao adicionar lancamento');
        }

        if (data.success) {
            // Add entry to table
            addEntryToTable(data.entry);

            // Update stats
            updateStats(data.stats);

            // Clear form
            document.getElementById('entry-start').value = '';
            document.getElementById('entry-end').value = '';
            document.getElementById('entry-description').value = '';

            // Stop tracking if active
            if (localStorage.getItem(STORAGE_KEYS.ACTIVE) === 'true') {
                stopTracking();
            }
        }
    } catch (error) {
        alert(error.message);
    }
}

// Add entry to table
function addEntryToTable(entry) {
    const tbody = document.getElementById('entries-table');
    const emptyRow = document.getElementById('empty-row');

    // Remove empty message if exists
    if (emptyRow) {
        emptyRow.remove();
    }

    const entryValue = entry.hours * HOURLY_RATE;

    const row = document.createElement('tr');
    row.className = 'hover:bg-gray-800/50 transition-colors';
    row.setAttribute('data-entry-id', entry.id);
    row.innerHTML = `
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">${entry.date_formatted}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300 font-mono">${entry.start_time}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300 font-mono">${entry.end_time}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm">
            <span class="bg-cyan-500/20 text-cyan-300 px-3 py-1 rounded-full font-medium">${formatHours(parseFloat(entry.hours))}</span>
        </td>
        <td class="px-6 py-4 text-sm text-gray-300">${escapeHtml(entry.description)}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-emerald-400">${formatCurrency(entryValue)}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm">
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

// Remove entry
async function removeEntry(id) {
    if (!confirm('Deseja realmente remover este lancamento?')) {
        return;
    }

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

            // Update stats
            updateStats(data.stats);

            // Check if table is empty
            const tbody = document.getElementById('entries-table');
            if (tbody.children.length === 0) {
                const emptyRow = document.createElement('tr');
                emptyRow.id = 'empty-row';
                emptyRow.innerHTML = `
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        Nenhum lancamento encontrado para este mes.
                    </td>
                `;
                tbody.appendChild(emptyRow);
            }
        }
    } catch (error) {
        alert(error.message);
    }
}

// Update stats display
function updateStats(stats) {
    document.getElementById('total-hours').textContent = formatHours(parseFloat(stats.total_hours));
    document.getElementById('total-revenue').textContent = formatCurrency(stats.total_revenue);
    document.getElementById('total-with-extra').textContent = formatCurrency(stats.total_with_extra);

    // Update CNPJ values
    const cnpjValues = document.querySelectorAll('.cnpj-value');
    cnpjValues.forEach(el => {
        el.textContent = formatCurrency(stats.revenue_per_cnpj);
    });
}

// Change month filter
function changeMonth(month) {
    window.location.href = `/dashboard?month=${month}`;
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
