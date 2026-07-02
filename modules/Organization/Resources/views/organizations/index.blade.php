@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Organizations</h1>
        <p class="text-muted mb-0">Manage tenant organizations — powered by REST API</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#entityModal" id="btn-create">
        <i class="bi bi-plus-lg me-1"></i> New Organization
    </button>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form class="row g-3 align-items-end" id="filters-form">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="search" class="form-control" name="search" placeholder="Name or code…">
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
                    <option value="status">Status</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Direction</label>
                <select class="form-select" name="direction">
                    <option value="asc">Ascending</option>
                    <option value="desc">Descending</option>
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
                    <th>Country</th>
                    <th>Status</th>
                    <th>Currency</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center p-3 border-top">
            <span class="text-muted small" id="pagination-info">Loading…</span>
            <nav>
                <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
            </nav>
        </div>
    </div>
</div>

<div class="modal fade" id="entityModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" id="entity-form">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Organization</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <input type="hidden" name="id" id="field-id">
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
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Country</label>
                    <input class="form-control" name="country" value="US" maxlength="8">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Currency</label>
                    <input class="form-control" name="currency" value="USD" maxlength="8">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Timezone</label>
                    <input class="form-control" name="timezone" value="UTC">
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
@endsection

@push('scripts')
<script>
(() => {
    const apiBase = @json($apiBase ?? '/api/organizations');
    let state = { page: 1, search: '', status: '', sort: 'name', direction: 'asc' };
    const tableBody = document.querySelector('#data-table tbody');
    const pagination = document.querySelector('#pagination');
    const paginationInfo = document.querySelector('#pagination-info');
    const form = document.querySelector('#entity-form');
    const modalEl = document.querySelector('#entityModal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    const formErrors = document.querySelector('#form-errors');

    function statusBadge(status) {
        const cls = status === 'active' ? 'badge-status-active' : (status === 'suspended' ? 'badge-status-suspended' : 'badge-status-inactive');
        return `<span class="badge ${cls}">${status}</span>`;
    }

    function buildQuery() {
        const params = new URLSearchParams({ page: state.page, per_page: 15, sort: state.sort, direction: state.direction });
        if (state.search) params.set('search', state.search);
        if (state.status) params.set('status', state.status);
        return params.toString();
    }

    async function load() {
        tableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">Loading…</td></tr>';
        const res = await fetch(`${apiBase}?${buildQuery()}`);
        const json = await res.json();
        const rows = (json.data || []).map(item => `
            <tr>
                <td><code>${item.code}</code></td>
                <td><strong>${item.name}</strong><div class="small text-muted">${item.slug || ''}</div></td>
                <td>${item.country || ''}</td>
                <td>${statusBadge(item.status)}</td>
                <td>${item.currency || ''}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-secondary me-1" data-edit='${JSON.stringify(item)}'><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-sm btn-outline-danger" data-delete="${item.id}"><i class="bi bi-trash"></i></button>
                </td>
            </tr>`).join('');
        tableBody.innerHTML = rows || '<tr><td colspan="6" class="text-center py-4 text-muted">No records found.</td></tr>';
        renderPagination(json.meta || {});
    }

    function renderPagination(meta) {
        paginationInfo.textContent = `Showing page ${meta.current_page || 1} of ${meta.last_page || 1} — ${meta.total || 0} total`;
        pagination.innerHTML = '';
        const last = meta.last_page || 1;
        const current = meta.current_page || 1;
        for (let p = Math.max(1, current - 2); p <= Math.min(last, current + 2); p++) {
            pagination.innerHTML += `<li class="page-item ${p === current ? 'active' : ''}"><a class="page-link" href="#" data-page="${p}">${p}</a></li>`;
        }
    }

    document.querySelector('#filters-form').addEventListener('submit', e => {
        e.preventDefault();
        const fd = new FormData(e.target);
        state.search = fd.get('search') || '';
        state.status = fd.get('status') || '';
        state.sort = fd.get('sort') || 'name';
        state.direction = fd.get('direction') || 'asc';
        state.page = 1;
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
            document.querySelector('#modal-title').textContent = 'Edit Organization';
            document.querySelector('#field-id').value = item.id;
            for (const [k, v] of Object.entries(item)) {
                const input = form.elements.namedItem(k);
                if (input && v !== null) input.value = v;
            }
            formErrors.classList.add('d-none');
            modal.show();
        }
        const delBtn = e.target.closest('[data-delete]');
        if (delBtn && confirm('Delete this organization?')) {
            fetch(`${apiBase}/${delBtn.dataset.delete}`, { method: 'DELETE' }).then(() => load());
        }
    });

    document.querySelector('#btn-create').addEventListener('click', () => {
        form.reset();
        document.querySelector('#field-id').value = '';
        document.querySelector('#modal-title').textContent = 'New Organization';
        formErrors.classList.add('d-none');
    });

    form.addEventListener('submit', async e => {
        e.preventDefault();
        formErrors.classList.add('d-none');
        const fd = new FormData(form);
        const id = fd.get('id');
        const payload = Object.fromEntries([...fd.entries()].filter(([k]) => k !== 'id'));
        const method = id ? 'PUT' : 'POST';
        const url = id ? `${apiBase}/${id}` : apiBase;
        const res = await fetch(url, { method, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify(payload) });
        if (!res.ok) {
            const err = await res.json();
            formErrors.textContent = JSON.stringify(err.errors || err.message || 'Validation failed');
            formErrors.classList.remove('d-none');
            return;
        }
        modal.hide();
        load();
    });

    load();
})();
</script>
@endpush
