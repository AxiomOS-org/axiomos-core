

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Companies</h1>
        <p class="text-muted mb-0">Companies within organizations — powered by REST API</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#entityModal" id="btn-create">
        <i class="bi bi-plus-lg me-1"></i> New Company
    </button>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form class="row g-3 align-items-end" id="filters-form">
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="search" class="form-control" name="search" placeholder="Name or code…">
            </div>
            <div class="col-md-3">
                <label class="form-label">Organization</label>
                <select class="form-select" name="organization_id" id="filter-organization"></select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">All</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="suspended">Suspended</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Sort</label>
                <select class="form-select" name="sort">
                    <option value="name">Name</option>
                    <option value="code">Code</option>
                    <option value="created_at">Created</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">Apply</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="data-table">
                <thead class="table-light">
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Organization</th>
                    <th>Status</th>
                    <th>Country</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center p-3 border-top">
            <span class="text-muted small" id="pagination-info">Loading…</span>
            <nav><ul class="pagination pagination-sm mb-0" id="pagination"></ul></nav>
        </div>
    </div>
</div>

<div class="modal fade" id="entityModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" id="entity-form">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Company</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <input type="hidden" name="id" id="field-id">
                <div class="col-12">
                    <label class="form-label">Organization *</label>
                    <select class="form-select" name="organization_id" id="form-organization" required></select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Code *</label>
                    <input class="form-control" name="code" required maxlength="64">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Name *</label>
                    <input class="form-control" name="name" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="2"></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Country</label>
                    <input class="form-control" name="country" value="US" maxlength="8">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Currency</label>
                    <input class="form-control" name="currency" value="USD" maxlength="8">
                </div>
                <div class="col-12"><div class="alert alert-danger d-none" id="form-errors"></div></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(() => {
    const apiBase = '/api/companies';
    const orgApi = '/api/organizations';
    let orgMap = {};
    let state = { page: 1, search: '', status: '', organization_id: '', sort: 'name', direction: 'asc' };
    const tableBody = document.querySelector('#data-table tbody');
    const pagination = document.querySelector('#pagination');
    const paginationInfo = document.querySelector('#pagination-info');
    const form = document.querySelector('#entity-form');
    const modalEl = document.querySelector('#entityModal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    const formErrors = document.querySelector('#form-errors');

    function statusBadge(s) {
        const cls = s === 'active' ? 'badge-status-active' : (s === 'suspended' ? 'badge-status-suspended' : 'badge-status-inactive');
        return `<span class="badge ${cls}">${s}</span>`;
    }

    async function loadOrganizations() {
        const res = await fetch(`${orgApi}?page=1&per_page=100&sort=name`);
        const json = await res.json();
        orgMap = Object.fromEntries((json.data || []).map(o => [o.id, o.name]));
        const opts = '<option value="">All organizations</option>' + (json.data || []).map(o => `<option value="${o.id}">${o.name}</option>`).join('');
        document.querySelector('#filter-organization').innerHTML = opts;
        document.querySelector('#form-organization').innerHTML = (json.data || []).map(o => `<option value="${o.id}">${o.name}</option>`).join('');
    }

    function buildQuery() {
        const p = new URLSearchParams({ page: state.page, per_page: 15, sort: state.sort, direction: state.direction });
        if (state.search) p.set('search', state.search);
        if (state.status) p.set('status', state.status);
        if (state.organization_id) p.set('organization_id', state.organization_id);
        return p.toString();
    }

    async function load() {
        tableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">Loading…</td></tr>';
        const res = await fetch(`${apiBase}?${buildQuery()}`);
        const json = await res.json();
        tableBody.innerHTML = (json.data || []).map(item => `
            <tr>
                <td><code>${item.code}</code></td>
                <td><strong>${item.name}</strong></td>
                <td class="small text-muted">${orgMap[item.organization_id] || item.organization_id || ''}</td>
                <td>${statusBadge(item.status)}</td>
                <td>${item.country || ''}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-secondary me-1" data-edit='${JSON.stringify(item)}'><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-sm btn-outline-danger" data-delete="${item.id}"><i class="bi bi-trash"></i></button>
                </td>
            </tr>`).join('') || '<tr><td colspan="6" class="text-center py-4 text-muted">No records found.</td></tr>';
        const meta = json.meta || {};
        paginationInfo.textContent = `Page ${meta.current_page || 1} of ${meta.last_page || 1} — ${meta.total || 0} total`;
        pagination.innerHTML = '';
        for (let p = Math.max(1, (meta.current_page || 1) - 2); p <= Math.min(meta.last_page || 1, (meta.current_page || 1) + 2); p++) {
            pagination.innerHTML += `<li class="page-item ${p === meta.current_page ? 'active' : ''}"><a class="page-link" href="#" data-page="${p}">${p}</a></li>`;
        }
    }

    document.querySelector('#filters-form').addEventListener('submit', e => {
        e.preventDefault();
        const fd = new FormData(e.target);
        state = { ...state, search: fd.get('search') || '', status: fd.get('status') || '', organization_id: fd.get('organization_id') || '', sort: fd.get('sort') || 'name', page: 1 };
        load();
    });

    pagination.addEventListener('click', e => {
        const link = e.target.closest('[data-page]');
        if (!link) return;
        e.preventDefault();
        state.page = Number(link.dataset.page);
        load();
    });

    tableBody.addEventListener('click', e => {
        const editBtn = e.target.closest('[data-edit]');
        if (editBtn) {
            const item = JSON.parse(editBtn.dataset.edit);
            form.reset();
            document.querySelector('#modal-title').textContent = 'Edit Company';
            document.querySelector('#field-id').value = item.id;
            for (const [k, v] of Object.entries(item)) {
                const input = form.elements.namedItem(k);
                if (input && v !== null) input.value = v;
            }
            formErrors.classList.add('d-none');
            modal.show();
        }
        const delBtn = e.target.closest('[data-delete]');
        if (delBtn && confirm('Delete this company?')) {
            fetch(`${apiBase}/${delBtn.dataset.delete}`, { method: 'DELETE' }).then(() => load());
        }
    });

    document.querySelector('#btn-create').addEventListener('click', () => {
        form.reset();
        document.querySelector('#field-id').value = '';
        document.querySelector('#modal-title').textContent = 'New Company';
        formErrors.classList.add('d-none');
    });

    form.addEventListener('submit', async e => {
        e.preventDefault();
        formErrors.classList.add('d-none');
        const fd = new FormData(form);
        const id = fd.get('id');
        const payload = Object.fromEntries([...fd.entries()].filter(([k]) => k !== 'id'));
        const res = await fetch(id ? `${apiBase}/${id}` : apiBase, {
            method: id ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload),
        });
        if (!res.ok) {
            const err = await res.json();
            formErrors.textContent = JSON.stringify(err.errors || err.message || 'Validation failed');
            formErrors.classList.remove('d-none');
            return;
        }
        modal.hide();
        load();
    });

    loadOrganizations().then(() => load());
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\AxiomOS\axiomos-core\modules\Organization\Resources\views/companies/index.blade.php ENDPATH**/ ?>