/**
 * CYBER SHIELD - URL Phishing Scanner AJAX Engine
 * File: assets/js/checker.js
 * BCA Final Year Project
 */

document.addEventListener('DOMContentLoaded', () => {
    const scanForm = document.getElementById('urlScanForm');
    const urlInput = document.getElementById('scanUrlInput');
    const scanBtn = document.getElementById('scanSubmitBtn');
    const spinner = document.getElementById('scanSpinner');
    const btnText = document.getElementById('scanBtnText');
    const resultContainer = document.getElementById('scanResultContainer');

    if (!scanForm) return;

    scanForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const url = urlInput.value.trim();

        if (!url) {
            showToast('Please enter a valid website URL or IP address.', 'warning');
            return;
        }

        // Set Loading State
        setScanLoading(true);

        try {
            const formData = new FormData();
            formData.append('url', url);
            
            const csrfMeta = document.querySelector('input[name="csrf_token"]');
            if (csrfMeta) {
                formData.append('csrf_token', csrfMeta.value);
            }

            const response = await fetch('api/scan-url.php', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await response.json();

            if (!data.success) {
                if (data.require_auth) {
                    showAuthPromptModal();
                    return;
                }
                showToast(data.message || 'Analysis could not be completed.', 'danger');
                return;
            }

            // Render Dynamic Threat Card
            renderScanResult(data.data);
            
            // Scroll to results smoothly
            resultContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });

        } catch (error) {
            console.error('Scan API Request failed:', error);
            showToast('Network error while querying the threat detection engine.', 'danger');
        } finally {
            setScanLoading(false);
        }
    });

    function setScanLoading(isLoading) {
        if (isLoading) {
            scanBtn.disabled = true;
            spinner.classList.remove('d-none');
            btnText.textContent = 'Analyzing Threat Vectors...';
        } else {
            scanBtn.disabled = false;
            spinner.classList.add('d-none');
            btnText.textContent = 'Scan URL';
        }
    }

    function renderScanResult(res) {
        resultContainer.classList.remove('d-none');
        
        const score = parseInt(res.risk_score, 10);
        let statusBadgeClass = 'badge-safe';
        let strokeColor = '#10b981';
        let statusTitle = 'URL Appears Safe';
        let statusSubtitle = 'Our heuristic analyzer identified no credential spoofing triggers or evasive structural patterns.';
        
        if (res.result === 'PHISHING') {
            statusBadgeClass = 'badge-phishing';
            strokeColor = '#ef4444';
            statusTitle = 'Potential Phishing URL Detected!';
            statusSubtitle = 'High probability of malicious intent, deceptive identity spoofing, or credential interception.';
        } else if (res.result === 'SUSPICIOUS') {
            statusBadgeClass = 'badge-suspicious';
            strokeColor = '#f59e0b';
            statusTitle = 'URL Requires Caution';
            statusSubtitle = 'Multiple anomalous lexical or structural heuristics were triggered. Exercise extreme vigilance.';
        }

        const reasonsHtml = (res.reasons_array || []).map(reason => `
            <li class="d-flex align-items-start gap-2 mb-2">
                <i class="fa-solid ${res.result === 'SAFE' ? 'fa-check text-success' : 'fa-triangle-exclamation text-warning'} mt-1"></i>
                <span class="text-slate-200 small">${escapeHtml(reason)}</span>
            </li>
        `).join('');

        resultContainer.innerHTML = `
            <div class="cyber-card mt-4 border-${res.result.toLowerCase()}">
                <div class="row align-items-center gy-4">
                    <div class="col-lg-4 text-center border-end border-secondary border-opacity-25">
                        <div class="score-circle" style="color: ${strokeColor};">
                            <span class="score-value">${score}</span>
                            <span class="small text-uppercase fw-bold">Threat Index</span>
                        </div>
                        <div class="mt-3">
                            <span class="badge ${statusBadgeClass} fs-6 px-3 py-2">${res.result}</span>
                        </div>
                    </div>

                    <div class="col-lg-8 ps-lg-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h4 class="fw-bold text-white mb-0">${statusTitle}</h4>
                            <span class="badge bg-dark border border-secondary text-secondary small">
                                <i class="fa-solid fa-clock me-1"></i> Just Now
                            </span>
                        </div>
                        <p class="text-secondary small mb-3">${statusSubtitle}</p>
                        
                        <div class="bg-dark bg-opacity-50 p-2 rounded border border-secondary border-opacity-25 font-monospace text-break small text-cyan mb-3">
                            <i class="fa-solid fa-link me-2 text-secondary"></i>${escapeHtml(res.url || '')}
                        </div>

                        <!-- Heuristics Grid -->
                        <div class="row g-2 mb-3 text-center">
                            <div class="col-sm-3 col-6">
                                <div class="p-2 bg-dark rounded border border-secondary border-opacity-25">
                                    <div class="text-secondary small">Protocol</div>
                                    <div class="fw-bold ${res.has_https ? 'text-success' : 'text-danger'}">
                                        <i class="fa-solid ${res.has_https ? 'fa-lock' : 'fa-lock-open'} me-1"></i>
                                        ${res.protocol ? res.protocol.toUpperCase() : 'N/A'}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3 col-6">
                                <div class="p-2 bg-dark rounded border border-secondary border-opacity-25">
                                    <div class="text-secondary small">Host Type</div>
                                    <div class="fw-bold ${res.has_ip ? 'text-danger' : 'text-info'}">
                                        ${res.has_ip ? 'Raw IP' : 'Hostname'}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3 col-6">
                                <div class="p-2 bg-dark rounded border border-secondary border-opacity-25">
                                    <div class="text-secondary small">Length</div>
                                    <div class="fw-bold text-white">${res.url_length} chars</div>
                                </div>
                            </div>
                            <div class="col-sm-3 col-6">
                                <div class="p-2 bg-dark rounded border border-secondary border-opacity-25">
                                    <div class="text-secondary small">Subdomains</div>
                                    <div class="fw-bold ${res.subdomain_count >= 2 ? 'text-warning' : 'text-white'}">
                                        ${res.subdomain_count} Depth
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reasons breakdown -->
                        <div class="bg-dark bg-opacity-75 p-3 rounded border border-secondary border-opacity-25">
                            <h6 class="text-cyan small fw-bold text-uppercase mb-2">
                                <i class="fa-solid fa-microscope me-1"></i> Why was this score assigned?
                            </h6>
                            <ul class="list-unstyled mb-0">
                                ${reasonsHtml || '<li class="text-muted small">Standard safe profile.</li>'}
                            </ul>
                        </div>

                        ${res.id ? `
                            <div class="mt-3 text-end">
                                <a href="scan-details.php?id=${res.id}" class="btn btn-outline-cyan btn-sm">
                                    <i class="fa-solid fa-file-shield me-1"></i> Full Forensic Record #SEC-${res.id}
                                </a>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    }

    function showAuthPromptModal() {
        const modalEl = document.getElementById('loginRequiredModal');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        } else {
            alert('Please login or create an account to record your URL scans.');
            window.location.href = 'login.php';
        }
    }

    function showToast(msg, type = 'info') {
        const toastContainer = document.querySelector('.toast-container') || createToastContainer();
        const toastEl = document.createElement('div');
        toastEl.className = `toast align-items-center text-bg-${type} border-0 show`;
        toastEl.role = 'alert';
        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${escapeHtml(msg)}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        toastContainer.appendChild(toastEl);
        setTimeout(() => toastEl.remove(), 4000);
    }

    function createToastContainer() {
        const div = document.createElement('div');
        div.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        div.style.zIndex = '9999';
        document.body.appendChild(div);
        return div;
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
});
