// ---- INSTALLER PORTAL ----
const API = window.location.origin;
let authToken = sessionStorage.getItem('installerToken') || '';
let userRole = sessionStorage.getItem('installerRole') || '';
let userName = sessionStorage.getItem('installerName') || '';

// ---- ELEMENTS ----
const $ = id => document.getElementById(id);
const loginScreen = $('loginScreen');
const dashboard = $('dashboard');
const loginForm = $('loginForm');
const loginError = $('loginError');
const loginBtn = $('loginBtn');
const logoutBtn = $('logoutBtn');
const findingForm = $('findingForm');
const findingError = $('findingError');
const findingSuccess = $('findingSuccess');
const submitFindingBtn = $('submitFindingBtn');
const findingsList = $('findingsList');
const welcomeUser = $('welcomeUser');

// ---- HELPERS ----
function headers(json) {
    const h = { 'Authorization': 'Bearer ' + authToken };
    if (json) h['Content-Type'] = 'application/json';
    return h;
}

function escapeHtml(text) {
    const d = document.createElement('div');
    d.textContent = text || '';
    return d.innerHTML;
}

// ---- AUTH ----
async function checkSession() {
    if (!authToken) return showLogin();
    try {
        const res = await fetch(API + '/api/installer/findings', { headers: headers() });
        if (res.status === 401) { clearSession(); return showLogin(); }
        showDashboard();
        const data = await res.json();
        renderFindings(data.findings || []);
    } catch { showLogin(); }
}

function showLogin() {
    loginScreen.style.display = 'flex';
    dashboard.style.display = 'none';
}

function showDashboard() {
    loginScreen.style.display = 'none';
    dashboard.style.display = 'block';
    welcomeUser.textContent = userName || 'Plumcert';

    // Show admin tabs
    document.querySelectorAll('.admin-only').forEach(el => {
        el.style.display = userRole === 'admin' ? '' : 'none';
    });

    // Show/hide approve & delete buttons based on role
    document.querySelectorAll('.admin-action').forEach(el => {
        el.style.display = userRole === 'admin' ? '' : 'none';
    });
}

function clearSession() {
    sessionStorage.removeItem('installerToken');
    sessionStorage.removeItem('installerRole');
    sessionStorage.removeItem('installerName');
    authToken = '';
    userRole = '';
    userName = '';
}

// ---- LOGIN ----
loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    loginError.style.display = 'none';
    loginBtn.disabled = true;
    loginBtn.textContent = 'Signing in...';

    try {
        const res = await fetch(API + '/api/installer/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                username: $('loginUsername').value,
                password: $('loginPassword').value
            })
        });

        const data = await res.json();
        if (data.success) {
            authToken = data.token;
            userRole = data.role;
            userName = data.username;
            sessionStorage.setItem('installerToken', authToken);
            sessionStorage.setItem('installerRole', userRole);
            sessionStorage.setItem('installerName', userName);

            // Splash transition before showing the dashboard
            const splash = $('splashScreen');
            loginScreen.style.display = 'none';
            if (splash) splash.classList.add('active');
            setTimeout(() => {
                if (splash) splash.classList.remove('active');
                showDashboard();
                loadFindings();
                if (userRole === 'admin') loadUsers();
                loginBtn.disabled = false;
                loginBtn.textContent = 'Sign In';
            }, 1300);
            return;
        } else {
            loginError.textContent = data.error || 'Login failed';
            loginError.style.display = 'block';
        }
    } catch {
        loginError.textContent = 'Connection error. Please try again.';
        loginError.style.display = 'block';
    }

    loginBtn.disabled = false;
    loginBtn.textContent = 'Sign In';
});

// ---- LOGOUT ----
logoutBtn.addEventListener('click', () => {
    clearSession();
    showLogin();
});

// ---- TABS ----
document.querySelectorAll('.installer-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.installer-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        $('tab-' + tab.dataset.tab).classList.add('active');

        if (tab.dataset.tab === 'history') loadFindings();
        if (tab.dataset.tab === 'users') loadUsers();
        if (tab.dataset.tab === 'certificates') { loadCertificates(); prefillInstallerDetails(); }
    });
});

// ---- PHOTO PREVIEWS ----
function setupPhoto(inputId, thumbId, placeholderId) {
    const input = $(inputId);
    const thumb = $(thumbId);
    const placeholder = $(placeholderId);

    input.addEventListener('change', () => {
        const file = input.files[0];
        if (!file) return;
        if (file.size > 5 * 1024 * 1024) {
            alert('File too large. Maximum 5MB.');
            input.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = (e) => {
            thumb.src = e.target.result;
            thumb.style.display = 'block';
            placeholder.style.display = 'none';
        };
        reader.readAsDataURL(file);
    });

    input.closest('.photo-upload').addEventListener('click', (e) => {
        if (e.target !== input) input.click();
    });
}

setupPhoto('beforePhoto', 'beforeThumb', 'beforePreview');
setupPhoto('afterPhoto', 'afterThumb', 'afterPreview');

// ---- AI REWRITE / SPELL-CHECK ----
// Each text field (description / fix / outcome) gets two buttons: rewrite and proofread.
// Stores per-field "original" text so Undo can restore it after either action.
const aiOriginal = {};
const FIELD_TO_INPUT_ID = { description: 'findingDescription', fix: 'findingFix', outcome: 'findingOutcome' };

function setStatus(field, text, kind) {
    const el = document.querySelector('[data-field-status="' + field + '"]');
    if (!el) return;
    el.textContent = text;
    el.className = 'ai-status' + (kind ? ' ' + kind : '');
    if (kind === 'error' || kind === 'success' || !kind) {
        setTimeout(() => { if (el.textContent === text) { el.textContent = ''; el.className = 'ai-status'; } }, 4000);
    }
}

function setUndoVisible(field, visible) {
    const btn = document.querySelector('.ai-undo-btn[data-field="' + field + '"]');
    if (btn) btn.style.display = visible ? 'inline-flex' : 'none';
}

const SPINNER_SVG = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="spin"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg> ';
const SPARK_SVG = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3 7h7l-5.5 4 2 7L12 16l-6.5 4 2-7L2 9h7z"/></svg> ';

document.querySelectorAll('.ai-rewrite-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const field = btn.dataset.field;
        const mode = btn.dataset.mode || 'rewrite';
        const inputId = FIELD_TO_INPUT_ID[field];
        const input = document.getElementById(inputId);
        if (!input) return;
        const text = input.value.trim();
        if (text.length < 5) {
            setStatus(field, 'Write a few words first', 'error');
            return;
        }

        aiOriginal[field] = input.value;
        const originalLabel = btn.innerHTML;
        const isRewrite = mode === 'rewrite';
        btn.disabled = true;
        btn.innerHTML = SPINNER_SVG + (isRewrite ? 'Rewriting...' : 'Checking...');

        try {
            const res = await fetch(API + '/api/installer/ai-rewrite', {
                method: 'POST',
                headers: headers(true),
                body: JSON.stringify({ text, fieldType: field, mode })
            });
            const data = await res.json();
            if (data.success) {
                input.value = data.rewritten;
                setUndoVisible(field, true);
                setStatus(field, isRewrite ? 'Rewritten by AI' : 'Spelling & grammar checked', 'success');
            } else {
                setStatus(field, data.error || 'Failed', 'error');
            }
        } catch {
            setStatus(field, 'Connection error', 'error');
        }

        btn.disabled = false;
        btn.innerHTML = originalLabel;
    });
});

document.querySelectorAll('.ai-undo-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const field = btn.dataset.field;
        const inputId = FIELD_TO_INPUT_ID[field];
        const input = document.getElementById(inputId);
        if (!input || aiOriginal[field] === undefined) return;
        input.value = aiOriginal[field];
        setUndoVisible(field, false);
        setStatus(field, 'Restored original', '');
    });
});

// ---- SUBMIT FINDING ----
findingForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    findingError.style.display = 'none';
    findingSuccess.style.display = 'none';
    submitFindingBtn.disabled = true;
    submitFindingBtn.textContent = 'Submitting...';

    try {
        const formData = new FormData(findingForm);
        const res = await fetch(API + '/api/installer/findings', {
            method: 'POST',
            headers: { 'Authorization': 'Bearer ' + authToken },
            body: formData
        });

        const data = await res.json();
        if (data.success) {
            findingSuccess.style.display = 'flex';
            findingForm.reset();
            $('beforeThumb').style.display = 'none';
            $('beforePreview').style.display = 'flex';
            $('afterThumb').style.display = 'none';
            $('afterPreview').style.display = 'flex';
            // Reset AI undo buttons + status text for all fields
            document.querySelectorAll('.ai-undo-btn').forEach(b => { b.style.display = 'none'; });
            document.querySelectorAll('.ai-status').forEach(s => { s.textContent = ''; s.className = 'ai-status'; });
            setTimeout(() => { findingSuccess.style.display = 'none'; }, 5000);
        } else {
            findingError.textContent = data.error || 'Submission failed';
            findingError.style.display = 'block';
        }
    } catch {
        findingError.textContent = 'Connection error. Please try again.';
        findingError.style.display = 'block';
    }

    submitFindingBtn.disabled = false;
    submitFindingBtn.textContent = 'Submit Finding';
});

// ---- FINDINGS LIST ----
async function loadFindings() {
    try {
        const res = await fetch(API + '/api/installer/findings', { headers: headers() });
        if (res.status === 401) { clearSession(); return showLogin(); }
        const data = await res.json();
        renderFindings(data.findings || []);
    } catch {
        findingsList.innerHTML = '<p class="empty-state">Could not load findings.</p>';
    }
}

function renderFindings(findings) {
    if (findings.length === 0) {
        findingsList.innerHTML = '<p class="empty-state">No findings submitted yet.</p>';
        return;
    }

    const classColors = { ID: '#D6432A', AR: '#E8A85A', NCS: '#8E1A1F' };
    const isAdmin = userRole === 'admin';

    findingsList.innerHTML = findings.map(f => {
        // Admin can delete anything; installer can delete only their own findings
        const canDelete = isAdmin || (f.submittedBy && f.submittedBy === userName);
        return `
        <div class="finding-item">
            <div class="finding-item-images">
                ${f.beforeImage ? `<img src="../${f.beforeImage}" alt="Before">` : '<div class="no-img">No before</div>'}
                ${f.afterImage ? `<img src="../${f.afterImage}" alt="After">` : '<div class="no-img">No after</div>'}
            </div>
            <div class="finding-item-content">
                <div class="finding-item-header">
                    <h3>${escapeHtml(f.title)}</h3>
                    <div class="finding-item-badges">
                        <span class="code-badge" style="background:${classColors[f.classification] || '#999'}">${f.classification}</span>
                        <span class="status-badge ${f.status}">${f.status}</span>
                    </div>
                </div>
                <p class="finding-item-meta">
                    ${escapeHtml(f.location)}${f.propertyType ? ' \u2014 ' + escapeHtml(f.propertyType) : ''}
                    \u2014 ${new Date(f.submittedAt).toLocaleDateString('en-GB')}
                    ${f.submittedBy ? ' \u2014 by ' + escapeHtml(f.submittedBy) : ''}
                </p>
                <p class="finding-item-desc">${escapeHtml(f.description)}</p>
                ${(isAdmin || canDelete) ? `
                    <div class="finding-item-actions">
                        ${isAdmin ? `
                            <button class="btn btn-sm ${f.status === 'approved' ? 'btn-outline' : 'btn-primary'}" onclick="toggleApproval('${f.id}')">
                                ${f.status === 'approved' ? 'Unapprove' : 'Approve'}
                            </button>
                        ` : ''}
                        ${canDelete ? `
                            <button class="btn btn-sm btn-danger" onclick="deleteFinding('${f.id}')">Delete</button>
                        ` : ''}
                    </div>
                ` : ''}
            </div>
        </div>`;
    }).join('');
}

async function toggleApproval(id) {
    try {
        const res = await fetch(API + '/api/installer/findings/' + id + '/approve', {
            method: 'POST',
            headers: headers()
        });
        if (res.status === 401) { clearSession(); return showLogin(); }
        loadFindings();
    } catch { alert('Failed to update finding'); }
}

async function deleteFinding(id) {
    if (!confirm('Delete this finding permanently? This cannot be undone.')) return;
    try {
        const res = await fetch(API + '/api/installer/findings/' + id, {
            method: 'DELETE',
            headers: headers()
        });
        if (res.status === 401) { clearSession(); return showLogin(); }
        const data = await res.json().catch(() => ({}));
        if (data.success) {
            loadFindings();
        } else {
            alert(data.error || 'Failed to delete finding');
        }
    } catch { alert('Failed to delete finding'); }
}

// ---- USER MANAGEMENT (admin only) ----
async function loadUsers() {
    if (userRole !== 'admin') return;
    try {
        const res = await fetch(API + '/api/admin/users', { headers: headers() });
        if (res.status === 401) { clearSession(); return showLogin(); }
        const data = await res.json();
        renderUsers(data.users || []);
    } catch {
        $('usersList').innerHTML = '<p class="empty-state">Could not load users.</p>';
    }
}

function renderUsers(users) {
    if (users.length === 0) {
        $('usersList').innerHTML = '<p class="empty-state">No users found.</p>';
        return;
    }

    $('usersList').innerHTML = users.map(u => `
        <div class="user-item">
            <div class="user-info">
                <div class="user-avatar">${escapeHtml((u.username || '?')[0].toUpperCase())}</div>
                <div>
                    <strong>${escapeHtml(u.username)}</strong>
                    <span class="user-role ${u.role}">${u.role}</span>
                    <small>Created ${new Date(u.createdAt).toLocaleDateString('en-GB')}</small>
                </div>
            </div>
            <div class="user-actions">
                <button class="btn btn-sm btn-outline" onclick="resetPassword('${u.id}', '${escapeHtml(u.username)}')">Reset Password</button>
                ${u.id !== 'admin-001' ? `<button class="btn btn-sm btn-danger" onclick="deleteUser('${u.id}')">Delete</button>` : ''}
            </div>
        </div>
    `).join('');
}

// Create user
$('createUserForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    $('userError').style.display = 'none';
    $('userSuccess').style.display = 'none';
    $('createUserBtn').disabled = true;

    try {
        const res = await fetch(API + '/api/admin/users', {
            method: 'POST',
            headers: headers(true),
            body: JSON.stringify({
                username: $('newUsername').value,
                password: $('newPassword').value,
                role: $('newRole').value
            })
        });

        const data = await res.json();
        if (data.success) {
            $('userSuccessMsg').textContent = `Account created for ${data.user.username}`;
            $('userSuccess').style.display = 'flex';
            $('createUserForm').reset();
            loadUsers();
            setTimeout(() => { $('userSuccess').style.display = 'none'; }, 5000);
        } else {
            $('userError').textContent = data.error;
            $('userError').style.display = 'block';
        }
    } catch {
        $('userError').textContent = 'Connection error';
        $('userError').style.display = 'block';
    }

    $('createUserBtn').disabled = false;
});

async function resetPassword(userId, username) {
    const newPass = prompt(`Enter new password for ${username}:`);
    if (!newPass) return;
    if (newPass.length < 6) { alert('Password must be at least 6 characters'); return; }

    try {
        const res = await fetch(API + '/api/admin/users/' + userId + '/reset-password', {
            method: 'POST',
            headers: headers(true),
            body: JSON.stringify({ password: newPass })
        });
        const data = await res.json();
        if (data.success) {
            alert('Password reset successfully');
        } else {
            alert(data.error || 'Failed to reset password');
        }
    } catch { alert('Connection error'); }
}

async function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user account?')) return;
    try {
        const res = await fetch(API + '/api/admin/users/' + userId, {
            method: 'DELETE',
            headers: headers()
        });
        const data = await res.json();
        if (data.success) {
            loadUsers();
        } else {
            alert(data.error || 'Failed to delete user');
        }
    } catch { alert('Connection error'); }
}

// ---- CERTIFICATES ----

const APPLIANCE_TEMPLATE = `
<div class="appliance-row" style="border:1px solid #ddd;padding:10px;margin-bottom:10px;border-radius:6px;">
  <div class="form-row">
    <div class="form-group"><label>Location</label><input type="text" name="ap_location" placeholder="e.g. Kitchen"></div>
    <div class="form-group"><label>Appliance Type</label><input type="text" name="ap_type" placeholder="e.g. Combination Boiler"></div>
    <div class="form-group"><label>Make</label><input type="text" name="ap_make"></div>
    <div class="form-group"><label>Model</label><input type="text" name="ap_model"></div>
  </div>
  <div class="form-row">
    <div class="form-group"><label>Flue Type</label><select name="ap_flue"><option value="">--</option><option>RS</option><option>CF</option><option>OF</option><option>SE</option></select></div>
    <div class="form-group"><label>Appliance Inspected</label><select name="ap_inspected"><option value="">--</option><option>Y</option><option>N</option></select></div>
    <div class="form-group"><label>Operating Pressure (mbar)</label><input type="text" name="ap_pressure"></div>
    <div class="form-group"><label>Heat Input (kWh)</label><input type="text" name="ap_heat"></div>
  </div>
  <div class="form-row">
    <div class="form-group"><label>High Comb. Ratio</label><input type="text" name="ap_highRatio" style="width:80px"></div>
    <div class="form-group"><label>High CO ppm</label><input type="text" name="ap_highCO" style="width:80px"></div>
    <div class="form-group"><label>High CO2%</label><input type="text" name="ap_highCO2" style="width:80px"></div>
    <div class="form-group"><label>Low Comb. Ratio</label><input type="text" name="ap_lowRatio" style="width:80px"></div>
    <div class="form-group"><label>Low CO ppm</label><input type="text" name="ap_lowCO" style="width:80px"></div>
    <div class="form-group"><label>Low CO2%</label><input type="text" name="ap_lowCO2" style="width:80px"></div>
  </div>
  <div class="form-row">
    <div class="form-group"><label>Safety Devices</label><select name="ap_safety"><option value="">--</option><option>Y</option><option>N</option><option>NA</option></select></div>
    <div class="form-group"><label>Ventilation</label><select name="ap_ventilation"><option value="">--</option><option>Y</option><option>N</option><option>NA</option></select></div>
    <div class="form-group"><label>Flue Visual</label><select name="ap_flueVisual"><option value="">--</option><option>Y</option><option>N</option><option>NA</option></select></div>
    <div class="form-group"><label>Flue Performance</label><select name="ap_fluePerf"><option value="">--</option><option>Y</option><option>N</option><option>NA</option></select></div>
    <div class="form-group"><label>Serviced</label><select name="ap_serviced"><option value="">--</option><option>Y</option><option>N</option><option>NA</option></select></div>
    <div class="form-group"><label>Safe to Use</label><select name="ap_safeToUse"><option value="">--</option><option>Y</option><option>N</option><option>NA</option><option>ID</option><option>AR</option></select></div>
  </div>
  <button type="button" class="btn btn-sm btn-danger remove-appliance-btn" style="margin-top:4px">Remove</button>
</div>`;

function initApplianceRows() {
    const container = $('applianceRows');
    if (!container) return;
    container.innerHTML = APPLIANCE_TEMPLATE;
    container.querySelector('.remove-appliance-btn').addEventListener('click', function() {
        if (container.querySelectorAll('.appliance-row').length > 1) this.closest('.appliance-row').remove();
    });
}

const addApplianceBtn = $('addApplianceBtn');
if (addApplianceBtn) {
    addApplianceBtn.addEventListener('click', () => {
        const container = $('applianceRows');
        if (container.querySelectorAll('.appliance-row').length >= 6) {
            alert('Maximum 6 appliances per certificate');
            return;
        }
        const div = document.createElement('div');
        div.innerHTML = APPLIANCE_TEMPLATE;
        const row = div.firstElementChild;
        row.querySelector('.remove-appliance-btn').addEventListener('click', function() {
            row.remove();
        });
        container.appendChild(row);
    });
}

// Pre-fill installer details from session on cert tab load
function prefillInstallerDetails() {
    if ($('ins_engineer') && !$('ins_engineer').value) {
        $('ins_engineer').value = userName || '';
    }
    const today = new Date().toISOString().split('T')[0];
    if ($('certDate') && !$('certDate').value) $('certDate').value = today;
    const nextYear = new Date();
    nextYear.setFullYear(nextYear.getFullYear() + 1);
    if ($('nextInspection') && !$('nextInspection').value) $('nextInspection').value = nextYear.toISOString().split('T')[0];
    if ($('issuedBy') && !$('issuedBy').value) $('issuedBy').value = userName || '';
}

const certForm = $('certForm');
if (certForm) {
    certForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        $('certError').style.display = 'none';
        $('certSuccess').style.display = 'none';
        const btn = $('issueCertBtn');
        btn.disabled = true;
        btn.textContent = 'Generating PDF...';

        try {
            // Collect appliance rows
            const applianceRows = document.querySelectorAll('#applianceRows .appliance-row');
            const appliances = Array.from(applianceRows).map(row => {
                const f = n => (row.querySelector(`[name="${n}"]`) || {}).value || '';
                return {
                    location: f('ap_location'), type: f('ap_type'), make: f('ap_make'), model: f('ap_model'),
                    flue: f('ap_flue'), inspected: f('ap_inspected'), pressure: f('ap_pressure'), heat: f('ap_heat'),
                    highRatio: f('ap_highRatio'), highCO: f('ap_highCO'), highCO2: f('ap_highCO2'),
                    lowRatio: f('ap_lowRatio'), lowCO: f('ap_lowCO'), lowCO2: f('ap_lowCO2'),
                    safety: f('ap_safety'), ventilation: f('ap_ventilation'), flueVisual: f('ap_flueVisual'),
                    fluePerf: f('ap_fluePerf'), serviced: f('ap_serviced'), safeToUse: f('ap_safeToUse')
                };
            });

            const fv = n => (certForm.querySelector(`[name="${n}"]`) || {}).value || '';
            const payload = {
                certType: 'CP12',
                installer: {
                    engineer: fv('ins_engineer'), company: fv('ins_company'),
                    address: fv('ins_address'), address2: fv('ins_address2'),
                    postcode: fv('ins_postcode'), telephone: fv('ins_telephone'),
                    gasSafeReg: fv('ins_gasSafeReg'), idCardNo: fv('ins_idCardNo')
                },
                jobAddress: {
                    name: fv('job_name'), address: fv('job_address'), address2: fv('job_address2'),
                    address3: fv('job_address3'), postcode: fv('job_postcode'), telephone: fv('job_telephone')
                },
                customer: {
                    name: fv('cust_name'), company: fv('cust_company'), address: fv('cust_address'),
                    address2: fv('cust_address2'), postcode: fv('cust_postcode'),
                    telephone: fv('cust_telephone'), mobile: fv('cust_mobile')
                },
                appliances,
                defects: [0,1,2,3,4,5].map(i => fv('defect_' + i)).filter(Boolean),
                checks: {
                    emergencyControl: fv('chk_emergencyControl'),
                    gasTightness: fv('chk_gasTightness'),
                    pipeworkVisual: fv('chk_pipeworkVisual'),
                    equipotentialBonding: fv('chk_equipotentialBonding'),
                    waterQuality: fv('chk_waterQuality'),
                    labelsWarningNotice: fv('chk_labelsWarningNotice'),
                    coFitted: fv('chk_coFitted'), coTested: fv('chk_coTested'),
                    smokeFitted: fv('chk_smokeFitted'), smokeTested: fv('chk_smokeTested')
                },
                comments: fv('comments'),
                nextInspection: fv('nextInspection'),
                date: fv('date'),
                issuedBy: fv('issuedBy'),
                receivedBy: fv('receivedBy')
            };

            const res = await fetch(API + '/api/installer/certificates', {
                method: 'POST',
                headers: headers(true),
                body: JSON.stringify(payload)
            });

            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                throw new Error(err.error || 'Server error ' + res.status);
            }

            const blob = await res.blob();
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'CP12-certificate.pdf';
            document.body.appendChild(a);
            a.click();
            setTimeout(() => { URL.revokeObjectURL(url); a.remove(); }, 2000);

            $('certSuccess').style.display = 'flex';
            setTimeout(() => { $('certSuccess').style.display = 'none'; }, 5000);
            loadCertificates();
        } catch (err) {
            $('certError').textContent = err.message || 'Failed to generate certificate';
            $('certError').style.display = 'block';
        }

        btn.disabled = false;
        btn.textContent = 'Issue Certificate & Download PDF';
    });
}

async function loadCertificates() {
    const list = $('certificatesList');
    if (!list) return;
    try {
        const res = await fetch(API + '/api/installer/certificates', { headers: headers() });
        if (res.status === 401) { clearSession(); return showLogin(); }
        const data = await res.json();
        renderCertificates(data.certificates || []);
    } catch {
        list.innerHTML = '<p class="empty-state">Could not load certificates.</p>';
    }
}

function renderCertificates(certs) {
    const list = $('certificatesList');
    if (!list) return;
    if (certs.length === 0) {
        list.innerHTML = '<p class="empty-state">No certificates issued yet.</p>';
        return;
    }
    list.innerHTML = certs.slice().reverse().map(c => `
        <div class="finding-item" style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;">
            <div>
                <strong>CP12 #${escapeHtml(c.certNo)}</strong>
                <span style="margin-left:8px;color:#666;font-size:13px">${escapeHtml(c.customerName || '—')}</span>
                <p style="margin:3px 0 0;font-size:12px;color:#888">${escapeHtml(c.jobAddress || '')} &mdash; ${new Date(c.issuedAt).toLocaleDateString('en-GB')} &mdash; by ${escapeHtml(c.installerId)}</p>
            </div>
            <button class="btn btn-sm btn-outline" onclick="downloadCertPDF('${c.id}', '${c.certNo}')">Download PDF</button>
        </div>
    `).join('');
}

async function downloadCertPDF(id, certNo) {
    try {
        const res = await fetch(API + '/api/installer/certificates/' + id + '/pdf', { headers: headers() });
        if (!res.ok) { alert('Could not download certificate'); return; }
        const blob = await res.blob();
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `CP12-${certNo}.pdf`;
        document.body.appendChild(a);
        a.click();
        setTimeout(() => { URL.revokeObjectURL(url); a.remove(); }, 2000);
    } catch { alert('Download failed'); }
}

// ---- INIT ----
checkSession();
initApplianceRows();
