// ==================== PREMIUM MODAL ====================

function showPremiumModal(feature) {
    // Dispatch Alpine.js event to open the premium modal
    window.dispatchEvent(new CustomEvent('open-premium-modal'));
}

// ==================== CSV IMPORT ====================

let importFile = null;
let importPreviewData = null;

function openImportModal() {
    const modal = document.getElementById('import-csv-modal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        resetImportModal();
    }
}

function closeImportModal() {
    const modal = document.getElementById('import-csv-modal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
    resetImportModal();
}

function resetImportModal() {
    importFile = null;
    importPreviewData = null;

    // Reset file input
    const fileInput = document.getElementById('import-file-input');
    if (fileInput) fileInput.value = '';

    // Show upload area, hide others
    showImportElement('import-upload-area');
    hideImportElement('import-preview-area');
    hideImportElement('import-loading');
    hideImportElement('import-result-area');
    hideImportElement('import-file-name');

    // Reset buttons
    hideImportElement('import-preview-btn');
    hideImportElement('import-execute-btn');

    // Reset dropzone style
    const dropzone = document.getElementById('import-dropzone');
    if (dropzone) {
        dropzone.classList.remove('border-cyan-500', 'bg-cyan-500/10');
        dropzone.classList.add('border-gray-600');
    }
}

function handleDragOver(e) {
    e.preventDefault();
    e.stopPropagation();
    const dropzone = document.getElementById('import-dropzone');
    if (dropzone) {
        dropzone.classList.add('border-cyan-500', 'bg-cyan-500/10');
        dropzone.classList.remove('border-gray-600');
    }
}

function handleDragLeave(e) {
    e.preventDefault();
    e.stopPropagation();
    const dropzone = document.getElementById('import-dropzone');
    if (dropzone) {
        dropzone.classList.remove('border-cyan-500', 'bg-cyan-500/10');
        dropzone.classList.add('border-gray-600');
    }
}

function handleDrop(e) {
    e.preventDefault();
    e.stopPropagation();

    const dropzone = document.getElementById('import-dropzone');
    if (dropzone) {
        dropzone.classList.remove('border-cyan-500', 'bg-cyan-500/10');
        dropzone.classList.add('border-gray-600');
    }

    const files = e.dataTransfer.files;
    if (files.length > 0) {
        handleFile(files[0]);
    }
}

function handleFileSelect(e) {
    const files = e.target.files;
    if (files.length > 0) {
        handleFile(files[0]);
    }
}

function handleFile(file) {
    // Validate file type
    const validTypes = ['text/csv', 'text/plain', 'application/vnd.ms-excel'];
    const validExtensions = ['.csv', '.txt'];
    const extension = '.' + file.name.split('.').pop().toLowerCase();

    if (!validTypes.includes(file.type) && !validExtensions.includes(extension)) {
        showImportToast('Por favor, selecione um arquivo CSV', 'warning');
        return;
    }

    importFile = file;

    // Show file name
    const fileNameEl = document.getElementById('import-file-name');
    if (fileNameEl) {
        fileNameEl.textContent = `Arquivo selecionado: ${file.name}`;
        fileNameEl.classList.remove('hidden');
    }

    // Show preview button
    showImportElement('import-preview-btn');
}

async function previewImport() {
    if (!importFile) {
        showImportToast('Selecione um arquivo primeiro', 'warning');
        return;
    }

    // Show loading
    hideImportElement('import-upload-area');
    showImportElement('import-loading');
    document.getElementById('import-loading-text').textContent = 'Analisando arquivo...';

    try {
        const formData = new FormData();
        formData.append('file', importFile);

        const response = await fetch('/import/csv/preview', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Erro ao processar arquivo');
        }

        if (data.success) {
            importPreviewData = data.preview;
            showPreviewResults(data.preview);
        }
    } catch (error) {
        showImportToast(error.message, 'error');
        resetImportModal();
    }
}

function showPreviewResults(preview) {
    hideImportElement('import-loading');
    showImportElement('import-preview-area');

    // Update counts
    document.getElementById('import-valid-count').textContent = preview.valid_count;
    document.getElementById('import-error-count').textContent = preview.error_count;

    // Show valid entries table
    if (preview.valid_count > 0) {
        showImportElement('import-valid-entries');
        const tbody = document.getElementById('import-valid-table');
        tbody.innerHTML = '';

        preview.valid_entries.forEach(entry => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-700/50';
            row.innerHTML = `
                <td class="px-3 py-2 text-gray-300">${escapeImportHtml(entry.date_formatted)}</td>
                <td class="px-3 py-2 text-gray-300 font-mono">${entry.start_time} - ${entry.end_time}</td>
                <td class="px-3 py-2"><span class="bg-cyan-500/20 text-cyan-300 px-2 py-0.5 rounded text-xs">${formatImportHours(entry.hours)}</span></td>
                <td class="px-3 py-2">${entry.project_name ? `<span class="bg-purple-500/20 text-purple-300 px-2 py-0.5 rounded text-xs">${escapeImportHtml(entry.project_name)}</span>` : '<span class="text-gray-500">-</span>'}</td>
                <td class="px-3 py-2 text-gray-300 truncate max-w-xs">${escapeImportHtml(entry.description)}</td>
            `;
            tbody.appendChild(row);
        });

        showImportElement('import-execute-btn');
    } else {
        hideImportElement('import-valid-entries');
        hideImportElement('import-execute-btn');
    }

    // Show errors
    if (preview.error_count > 0) {
        showImportElement('import-errors');
        const errorList = document.getElementById('import-error-list');
        errorList.innerHTML = '';

        preview.errors.forEach(error => {
            const li = document.createElement('li');
            li.className = 'text-red-300';
            li.innerHTML = `
                <span class="text-red-400 font-medium">Linha ${error.line}:</span>
                ${error.messages.map(m => escapeImportHtml(m)).join(', ')}
            `;
            errorList.appendChild(li);
        });
    } else {
        hideImportElement('import-errors');
    }
}

async function executeImport() {
    if (!importFile) {
        showImportToast('Selecione um arquivo primeiro', 'warning');
        return;
    }

    // Show loading
    hideImportElement('import-preview-area');
    showImportElement('import-loading');
    document.getElementById('import-loading-text').textContent = 'Importando lancamentos...';

    const ignoreOverlaps = document.getElementById('import-ignore-overlaps')?.checked || false;

    try {
        const formData = new FormData();
        formData.append('file', importFile);
        formData.append('ignore_overlaps', ignoreOverlaps ? '1' : '0');

        const response = await fetch('/import/csv', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Erro ao importar arquivo');
        }

        if (data.success) {
            hideImportElement('import-loading');
            showImportElement('import-result-area');

            let resultText = `${data.imported} lancamento(s) importado(s) com sucesso.`;
            if (data.skipped > 0) {
                resultText += ` ${data.skipped} ignorado(s) por sobreposicao.`;
            }
            document.getElementById('import-result-text').textContent = resultText;

            showImportToast(`${data.imported} lancamento(s) importado(s)!`, 'success');

            // Reload page after a short delay
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        }
    } catch (error) {
        showImportToast(error.message, 'error');
        hideImportElement('import-loading');
        showImportElement('import-preview-area');
    }
}

// Helper functions
function showImportElement(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('hidden');
}

function hideImportElement(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('hidden');
}

function escapeImportHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatImportHours(hours) {
    const h = Math.floor(hours);
    const m = Math.round((hours - h) * 60);
    return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0');
}

// Simple toast for import (uses existing toast if available, otherwise creates simple one)
function showImportToast(message, type) {
    // Try to use existing showToast function
    if (typeof showToast === 'function' && typeof TOAST_TYPES !== 'undefined') {
        const toastType = TOAST_TYPES[type.toUpperCase()] || type;
        showToast(message, toastType);
        return;
    }

    // Fallback: create simple toast
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(container);
    }

    const colors = {
        success: { bg: '#064e3b', border: '#10b981', text: '#a7f3d0' },
        error: { bg: '#7f1d1d', border: '#ef4444', text: '#fecaca' },
        warning: { bg: '#78350f', border: '#f59e0b', text: '#fde68a' },
        info: { bg: '#164e63', border: '#06b6d4', text: '#a5f3fc' }
    };

    const style = colors[type] || colors.info;

    const toast = document.createElement('div');
    toast.style.cssText = `
        padding: 14px 18px;
        border-radius: 10px;
        border: 2px solid ${style.border};
        background: ${style.bg};
        color: ${style.text};
        box-shadow: 0 10px 25px rgba(0,0,0,0.4);
        font-size: 14px;
        font-weight: 500;
        min-width: 280px;
        max-width: 400px;
        transform: translateX(100%);
        opacity: 0;
        transition: all 0.3s ease-out;
    `;
    toast.textContent = message;

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
    }, 4000);
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('import-csv-modal');
        if (modal && !modal.classList.contains('hidden')) {
            closeImportModal();
        }
    }
});
