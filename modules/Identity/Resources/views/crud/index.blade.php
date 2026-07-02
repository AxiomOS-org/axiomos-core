@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">{{ $entityTitle }}</h1>
        <p class="text-muted mb-0">{{ $entityDescription }}</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#entityModal" id="btn-create">
        <i class="bi bi-plus-lg me-1"></i> New {{ rtrim($entityTitle, 's') }}
    </button>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form class="row g-3 align-items-end" id="filters-form">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="search" class="form-control" name="search" placeholder="Search records...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">All</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="suspended">Suspended</option>
                    <option value="expired">Expired</option>
                    <option value="revoked">Revoked</option>
                    <option value="recorded">Recorded</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Sort</label>
                <select class="form-select" name="sort">
                    <option value="created_at">Created At</option>
                    <option value="updated_at">Updated At</option>
                    <option value="status">Status</option>
                    <option value="code">Code</option>
                    <option value="name">Name</option>
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
                    @foreach (($config['columns'] ?? []) as $column)
                        <th>{{ $column['label'] }}</th>
                    @endforeach
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
                <h5 class="modal-title" id="modal-title">{{ rtrim($entityTitle, 's') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="field-id">
                <div class="row g-3" id="dynamic-form-fields"></div>
                <div class="alert alert-danger d-none mt-3" id="form-errors"></div>
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
    const apiBase = @json($apiBase ?? '/api/identities');
    const config = @json($config);
    let state = { page: 1, search: '', status: '', sort: 'created_at', direction: 'desc' };
    const tableBody = document.querySelector('#data-table tbody');
    const pagination = document.querySelector('#pagination');
    const paginationInfo = document.querySelector('#pagination-info');
    const form = document.querySelector('#entity-form');
    const formErrors = document.querySelector('#form-errors');
    const fieldsWrap = document.querySelector('#dynamic-form-fields');
    const modal = bootstrap.Modal.getOrCreateInstance(document.querySelector('#entityModal'));

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function renderFormFields() {
        const fields = config.fields || [];
        fieldsWrap.innerHTML = fields.map((field) => {
            const required = field.required ? 'required' : '';
            if (field.type === 'textarea') {
                return `<div class="col-md-6"><label class="form-label">${escapeHtml(field.label)}${field.required ? ' *' : ''}</label><textarea class="form-control" name="${escapeHtml(field.key)}" rows="2" ${required}></textarea></div>`;
            }
            if (field.type === 'select') {
                const options = (field.options || []).map((option) => `<option value="${escapeHtml(option)}">${escapeHtml(option)}</option>`).join('');
                return `<div class="col-md-6"><label class="form-label">${escapeHtml(field.label)}${field.required ? ' *' : ''}</label><select class="form-select" name="${escapeHtml(field.key)}" ${required}><option value="">Select...</option>${options}</select></div>`;
            }
            if (field.type === 'checkbox') {
                return `<div class="col-md-6 d-flex align-items-end"><div class="form-check"><input class="form-check-input" type="checkbox" value="1" name="${escapeHtml(field.key)}" id="field-${escapeHtml(field.key)}"><label class="form-check-label" for="field-${escapeHtml(field.key)}">${escapeHtml(field.label)}</label></div></div>`;
            }
            const type = field.type || 'text';
            return `<div class="col-md-6"><label class="form-label">${escapeHtml(field.label)}${field.required ? ' *' : ''}</label><input class="form-control" type="${escapeHtml(type)}" name="${escapeHtml(field.key)}" ${required}></div>`;
        }).join('');
    }

    function buildQuery() {
        const params = new URLSearchParams({ page: state.page, per_page: 15, sort: state.sort, direction: state.direction });
        if (state.search) params.set('search', state.search);
        if (state.status) params.set('status', state.status);
        return params.toString();
    }

    function renderCell(item, key) {
        const value = item[key];
        if (typeof value === 'boolean') return value ? 'Yes' : 'No';
        if (value === null || value === undefined || value === '') return '<span class="text-muted">—</span>';
        return escapeHtml(value);
    }

    function renderPagination(meta) {
        const current = meta.current_page || 1;
        const last = meta.last_page || 1;
        paginationInfo.textContent = `Page ${current} of ${last} — ${meta.total || 0} total`;
        pagination.innerHTML = '';
        for (let page = Math.max(1, current - 2); page <= Math.min(last, current + 2); page++) {
            pagination.innerHTML += `<li class="page-item ${page === current ? 'active' : ''}"><a class="page-link" href="#" data-page="${page}">${page}</a></li>`;
        }
    }

    async function load() {
        tableBody.innerHTML = `<tr><td colspan="${(config.columns || []).length + 1}" class="text-center py-4 text-muted">Loading...</td></tr>`;
        const response = await fetch(`${apiBase}?${buildQuery()}`);
        const json = await response.json();
        const rows = (json.data || []).map((item) => {
            const cells = (config.columns || []).map((column) => `<td>${renderCell(item, column.key)}</td>`).join('');
            return `<tr>${cells}<td class="text-end"><button class="btn btn-sm btn-outline-secondary me-1" data-edit='${JSON.stringify(item)}'><i class="bi bi-pencil"></i></button><button class="btn btn-sm btn-outline-danger" data-delete="${escapeHtml(item.id)}"><i class="bi bi-trash"></i></button></td></tr>`;
        }).join('');
        tableBody.innerHTML = rows || `<tr><td colspan="${(config.columns || []).length + 1}" class="text-center py-4 text-muted">No records found.</td></tr>`;
        renderPagination(json.meta || {});
    }

    function resetForm() {
        form.reset();
        form.elements.id.value = '';
        formErrors.classList.add('d-none');
        formErrors.textContent = '';
    }

    document.querySelector('#btn-create').addEventListener('click', () => {
        resetForm();
        document.querySelector('#modal-title').textContent = `New ${config.title.replace(/s$/, '')}`;
    });

    document.querySelector('#filters-form').addEventListener('submit', (event) => {
        event.preventDefault();
        const fd = new FormData(event.target);
        state.search = String(fd.get('search') || '');
        state.status = String(fd.get('status') || '');
        state.sort = String(fd.get('sort') || 'created_at');
        state.page = 1;
        load();
    });

    pagination.addEventListener('click', (event) => {
        const link = event.target.closest('[data-page]');
        if (!link) return;
        event.preventDefault();
        state.page = Number(link.dataset.page);
        load();
    });

    tableBody.addEventListener('click', (event) => {
        const editButton = event.target.closest('[data-edit]');
        if (editButton) {
            const item = JSON.parse(editButton.dataset.edit);
            resetForm();
            form.elements.id.value = item.id;
            for (const field of (config.fields || [])) {
                const input = form.elements.namedItem(field.key);
                if (!input) continue;
                if (field.type === 'checkbox') {
                    input.checked = Boolean(item[field.key]);
                } else if (item[field.key] !== null && item[field.key] !== undefined) {
                    input.value = item[field.key];
                }
            }
            document.querySelector('#modal-title').textContent = `Edit ${config.title.replace(/s$/, '')}`;
            modal.show();
            return;
        }

        const deleteButton = event.target.closest('[data-delete]');
        if (deleteButton && confirm('Delete this record?')) {
            fetch(`${apiBase}/${deleteButton.dataset.delete}`, { method: 'DELETE' }).then(() => load());
        }
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        formErrors.classList.add('d-none');

        const fd = new FormData(form);
        const id = fd.get('id');
        const payload = {};
        for (const field of (config.fields || [])) {
            const input = form.elements.namedItem(field.key);
            if (!input) continue;
            if (field.type === 'checkbox') {
                payload[field.key] = input.checked;
                continue;
            }
            const value = String(fd.get(field.key) ?? '').trim();
            if (value !== '') payload[field.key] = value;
        }

        const response = await fetch(id ? `${apiBase}/${id}` : apiBase, {
            method: id ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload),
        });

        if (!response.ok) {
            const error = await response.json();
            formErrors.textContent = JSON.stringify(error.errors || error.message || 'Validation failed');
            formErrors.classList.remove('d-none');
            return;
        }

        modal.hide();
        load();
    });

    renderFormFields();
    load();
})();
</script>
@endpush
@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">{{ $entityTitle }}</h1>
        <p class="text-muted mb-0">{{ $entityDescription }}</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#entityModal" id="btn-create">
        <i class="bi bi-plus-lg me-1"></i> New {{ rtrim($entityTitle, 's') }}
    </button>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form class="row g-3 align-items-end" id="filters-form">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="search" class="form-control" name="search" placeholder="Search records...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">All</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="suspended">Suspended</option>
                    <option value="expired">Expired</option>
                    <option value="revoked">Revoked</option>
                    <option value="recorded">Recorded</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Sort</label>
                <select class="form-select" name="sort">
                    <option value="created_at">Created At</option>
                    <option value="updated_at">Updated At</option>
                    <option value="status">Status</option>
                    <option value="code">Code</option>
                    <option value="name">Name</option>
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
                    @foreach (($config['columns'] ?? []) as $column)
                        <th>{{ $column['label'] }}</th>
                    @endforeach
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
                <h5 class="modal-title" id="modal-title">{{ rtrim($entityTitle, 's') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="field-id">
                <div class="row g-3" id="dynamic-form-fields"></div>
                <div class="alert alert-danger d-none mt-3" id="form-errors"></div>
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
    const apiBase = @json($apiBase ?? '/api/identities');
    const config = @json($config);
    let state = { page: 1, search: '', status: '', sort: 'created_at', direction: 'desc' };
    const tableBody = document.querySelector('#data-table tbody');
    const pagination = document.querySelector('#pagination');
    const paginationInfo = document.querySelector('#pagination-info');
    const form = document.querySelector('#entity-form');
    const formErrors = document.querySelector('#form-errors');
    const fieldsWrap = document.querySelector('#dynamic-form-fields');
    const modal = bootstrap.Modal.getOrCreateInstance(document.querySelector('#entityModal'));

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function renderFormFields() {
        const fields = config.fields || [];
        fieldsWrap.innerHTML = fields.map((field) => {
            const required = field.required ? 'required' : '';
            if (field.type === 'textarea') {
                return `<div class="col-md-6"><label class="form-label">${escapeHtml(field.label)}${field.required ? ' *' : ''}</label><textarea class="form-control" name="${escapeHtml(field.key)}" rows="2" ${required}></textarea></div>`;
            }
            if (field.type === 'select') {
                const options = (field.options || []).map((option) => `<option value="${escapeHtml(option)}">${escapeHtml(option)}</option>`).join('');
                return `<div class="col-md-6"><label class="form-label">${escapeHtml(field.label)}${field.required ? ' *' : ''}</label><select class="form-select" name="${escapeHtml(field.key)}" ${required}><option value="">Select...</option>${options}</select></div>`;
            }
            if (field.type === 'checkbox') {
                return `<div class="col-md-6 d-flex align-items-end"><div class="form-check"><input class="form-check-input" type="checkbox" value="1" name="${escapeHtml(field.key)}" id="field-${escapeHtml(field.key)}"><label class="form-check-label" for="field-${escapeHtml(field.key)}">${escapeHtml(field.label)}</label></div></div>`;
            }
            const type = field.type || 'text';
            return `<div class="col-md-6"><label class="form-label">${escapeHtml(field.label)}${field.required ? ' *' : ''}</label><input class="form-control" type="${escapeHtml(type)}" name="${escapeHtml(field.key)}" ${required}></div>`;
        }).join('');
    }

    function buildQuery() {
        const params = new URLSearchParams({ page: state.page, per_page: 15, sort: state.sort, direction: state.direction });
        if (state.search) params.set('search', state.search);
        if (state.status) params.set('status', state.status);
        return params.toString();
    }

    function renderCell(item, key) {
        const value = item[key];
        if (typeof value === 'boolean') {
            return value ? 'Yes' : 'No';
        }
        if (value === null || value === undefined || value === '') {
            return '<span class="text-muted">—</span>';
        }
        return escapeHtml(value);
    }

    function renderPagination(meta) {
        const current = meta.current_page || 1;
        const last = meta.last_page || 1;
        paginationInfo.textContent = `Page ${current} of ${last} — ${meta.total || 0} total`;
        pagination.innerHTML = '';
        for (let page = Math.max(1, current - 2); page <= Math.min(last, current + 2); page++) {
            pagination.innerHTML += `<li class="page-item ${page === current ? 'active' : ''}"><a class="page-link" href="#" data-page="${page}">${page}</a></li>`;
        }
    }

    async function load() {
        tableBody.innerHTML = `<tr><td colspan="${(config.columns || []).length + 1}" class="text-center py-4 text-muted">Loading...</td></tr>`;
        const response = await fetch(`${apiBase}?${buildQuery()}`);
        const json = await response.json();
        const rows = (json.data || []).map((item) => {
            const cells = (config.columns || []).map((column) => `<td>${renderCell(item, column.key)}</td>`).join('');
            return `<tr>${cells}<td class="text-end"><button class="btn btn-sm btn-outline-secondary me-1" data-edit='${JSON.stringify(item)}'><i class="bi bi-pencil"></i></button><button class="btn btn-sm btn-outline-danger" data-delete="${escapeHtml(item.id)}"><i class="bi bi-trash"></i></button></td></tr>`;
        }).join('');
        tableBody.innerHTML = rows || `<tr><td colspan="${(config.columns || []).length + 1}" class="text-center py-4 text-muted">No records found.</td></tr>`;
        renderPagination(json.meta || {});
    }

    function resetForm() {
        form.reset();
        form.elements.id.value = '';
        formErrors.classList.add('d-none');
        formErrors.textContent = '';
    }

    document.querySelector('#btn-create').addEventListener('click', () => {
        resetForm();
        document.querySelector('#modal-title').textContent = `New ${config.title.replace(/s$/, '')}`;
    });

    document.querySelector('#filters-form').addEventListener('submit', (event) => {
        event.preventDefault();
        const fd = new FormData(event.target);
        state.search = String(fd.get('search') || '');
        state.status = String(fd.get('status') || '');
        state.sort = String(fd.get('sort') || 'created_at');
        state.page = 1;
        load();
    });

    pagination.addEventListener('click', (event) => {
        const link = event.target.closest('[data-page]');
        if (!link) {
            return;
        }
        event.preventDefault();
        state.page = Number(link.dataset.page);
        load();
    });

    tableBody.addEventListener('click', (event) => {
        const editButton = event.target.closest('[data-edit]');
        if (editButton) {
            const item = JSON.parse(editButton.dataset.edit);
            resetForm();
            form.elements.id.value = item.id;
            for (const field of (config.fields || [])) {
                const input = form.elements.namedItem(field.key);
                if (!input) {
                    continue;
                }
                if (field.type === 'checkbox') {
                    input.checked = Boolean(item[field.key]);
                } else if (item[field.key] !== null && item[field.key] !== undefined) {
                    input.value = item[field.key];
                }
            }
            document.querySelector('#modal-title').textContent = `Edit ${config.title.replace(/s$/, '')}`;
            modal.show();
            return;
        }

        const deleteButton = event.target.closest('[data-delete]');
        if (deleteButton && confirm('Delete this record?')) {
            fetch(`${apiBase}/${deleteButton.dataset.delete}`, { method: 'DELETE' }).then(() => load());
        }
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        formErrors.classList.add('d-none');

        const fd = new FormData(form);
        const id = fd.get('id');
        const payload = {};
        for (const field of (config.fields || [])) {
            const input = form.elements.namedItem(field.key);
            if (!input) {
                continue;
            }
            if (field.type === 'checkbox') {
                payload[field.key] = input.checked;
                continue;
            }
            const value = String(fd.get(field.key) ?? '').trim();
            if (value !== '') {
                payload[field.key] = value;
            }
        }

        const response = await fetch(id ? `${apiBase}/${id}` : apiBase, {
            method: id ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload),
        });

        if (!response.ok) {
            const error = await response.json();
            formErrors.textContent = JSON.stringify(error.errors || error.message || 'Validation failed');
            formErrors.classList.remove('d-none');
            return;
        }

        modal.hide();
        load();
    });

    renderFormFields();
    load();
})();
</script>
@endpush
@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">{{ $entityLabel }}</h1>
        <p class="text-muted mb-0">Administer {{ strtolower($entityLabel) }} through the Identity REST APIs.</p>
    </div>
    <button class="btn btn-primary" id="btn-create"><i class="bi bi-plus-lg me-1"></i> New {{ rtrim($entityLabel, 's') }}</button>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form class="row g-3 align-items-end" id="filters-form">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="search" class="form-control" name="search" placeholder="Search records...">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">All</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="suspended">Suspended</option>
                    <option value="revoked">Revoked</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Sort</label>
                <select class="form-select" name="sort">
                    <option value="created_at">Created</option>
                    <option value="updated_at">Updated</option>
                    <option value="status">Status</option>
                    @foreach ($columns as $column)
                        <option value="{{ $column }}">{{ $column }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Direction</label>
                <select class="form-select" name="direction">
                    <option value="desc">Descending</option>
                    <option value="asc">Ascending</option>
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
                    @foreach ($columns as $column)
                        <th>{{ ucwords(str_replace('_', ' ', $column)) }}</th>
                    @endforeach
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

<div class="modal fade" id="entity-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" id="entity-form">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">{{ $entityLabel }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3" id="form-fields">
                <input type="hidden" name="id" id="field-id">
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
    const apiBase = @json($apiBase);
    const entityLabel = @json($entityLabel);
    const singularLabel = entityLabel.endsWith('s') ? entityLabel.slice(0, -1) : entityLabel;
    const columns = @json($columns);
    const fields = @json($fields);
    const boolFields = new Set(['is_primary', 'success']);
    const jsonFields = new Set(['scopes', 'metadata']);
    const state = { page: 1, search: '', status: '', sort: 'created_at', direction: 'desc' };

    const tableBody = document.querySelector('#data-table tbody');
    const pagination = document.querySelector('#pagination');
    const paginationInfo = document.querySelector('#pagination-info');
    const form = document.querySelector('#entity-form');
    const formFields = document.querySelector('#form-fields');
    const modal = bootstrap.Modal.getOrCreateInstance(document.querySelector('#entity-modal'));

    function labelize(name) {
        return name.replaceAll('_', ' ').replace(/\b\w/g, c => c.toUpperCase());
    }

    function statusBadge(status) {
        if (!status) return '';
        const safe = String(status).toLowerCase();
        let cls = 'badge-status-inactive';
        if (safe === 'active' || safe === 'success') cls = 'badge-status-active';
        if (safe === 'suspended' || safe === 'revoked' || safe === 'expired' || safe === 'failed') cls = 'badge-status-suspended';
        return `<span class="badge ${cls}">${safe}</span>`;
    }

    function buildQuery() {
        const params = new URLSearchParams({ page: state.page, per_page: 15, sort: state.sort, direction: state.direction });
        if (state.search) params.set('search', state.search);
        if (state.status) params.set('status', state.status);
        return params.toString();
    }

    function toDisplay(value, column) {
        if (column === 'status') return statusBadge(value);
        if (value === null || value === undefined || value === '') return '';
        if (typeof value === 'object') return `<code>${JSON.stringify(value)}</code>`;
        return String(value);
    }

    function buildForm() {
        const controls = fields.map(field => {
            const id = `field-${field}`;
            if (boolFields.has(field)) {
                return `
                    <div class="col-md-6">
                        <label class="form-label">${labelize(field)}</label>
                        <select class="form-select" name="${field}" id="${id}">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>`;
            }

            if (jsonFields.has(field)) {
                return `
                    <div class="col-12">
                        <label class="form-label">${labelize(field)}</label>
                        <textarea class="form-control" rows="2" name="${field}" id="${id}" placeholder='["example"]'></textarea>
                    </div>`;
            }

            const type = field.includes('date') || field.endsWith('_at') ? 'datetime-local' : 'text';
            return `
                <div class="col-md-6">
                    <label class="form-label">${labelize(field)}</label>
                    <input class="form-control" type="${type}" name="${field}" id="${id}">
                </div>`;
        }).join('');

        formFields.innerHTML = `
            <input type="hidden" name="id" id="field-id">
            ${controls}
            <div class="col-12"><div class="alert alert-danger d-none mb-0" id="form-errors"></div></div>
        `;
    }

    function normalizeForForm(field, value) {
        if (value === null || value === undefined) return '';
        if (boolFields.has(field)) return value ? '1' : '0';
        if (jsonFields.has(field)) return JSON.stringify(value ?? []);
        if (field.includes('date') || field.endsWith('_at')) {
            const date = new Date(value);
            if (Number.isNaN(date.getTime())) return '';
            return date.toISOString().slice(0, 16);
        }
        return String(value);
    }

    function formPayload() {
        const fd = new FormData(form);
        const id = fd.get('id');
        const payload = {};

        for (const [key, rawValue] of fd.entries()) {
            if (key === 'id') continue;
            const value = String(rawValue).trim();
            if (value === '') continue;

            if (boolFields.has(key)) {
                payload[key] = value === '1' || value.toLowerCase() === 'true';
                continue;
            }

            if (jsonFields.has(key)) {
                try {
                    payload[key] = JSON.parse(value);
                } catch {
                    payload[key] = [];
                }
                continue;
            }

            if (key.includes('date') || key.endsWith('_at')) {
                payload[key] = new Date(value).toISOString();
                continue;
            }

            payload[key] = value;
        }

        return { id, payload };
    }

    function renderPagination(meta) {
        paginationInfo.textContent = `Showing page ${meta.current_page || 1} of ${meta.last_page || 1} — ${meta.total || 0} total`;
        pagination.innerHTML = '';
        const last = meta.last_page || 1;
        const current = meta.current_page || 1;

        for (let page = Math.max(1, current - 2); page <= Math.min(last, current + 2); page++) {
            pagination.innerHTML += `<li class="page-item ${page === current ? 'active' : ''}"><a class="page-link" href="#" data-page="${page}">${page}</a></li>`;
        }
    }

    async function load() {
        tableBody.innerHTML = `<tr><td colspan="${columns.length + 1}" class="text-center py-4 text-muted">Loading…</td></tr>`;
        const res = await fetch(`${apiBase}?${buildQuery()}`);
        const json = await res.json();
        const rows = (json.data || []).map(item => `
            <tr>
                ${columns.map(column => `<td>${toDisplay(item[column], column)}</td>`).join('')}
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-secondary me-1" data-edit='${JSON.stringify(item)}'><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-sm btn-outline-danger" data-delete="${item.id}"><i class="bi bi-trash"></i></button>
                </td>
            </tr>`).join('');

        tableBody.innerHTML = rows || `<tr><td colspan="${columns.length + 1}" class="text-center py-4 text-muted">No records found.</td></tr>`;
        renderPagination(json.meta || {});
    }

    document.querySelector('#filters-form').addEventListener('submit', event => {
        event.preventDefault();
        const fd = new FormData(event.target);
        state.search = String(fd.get('search') || '');
        state.status = String(fd.get('status') || '');
        state.sort = String(fd.get('sort') || 'created_at');
        state.direction = String(fd.get('direction') || 'desc');
        state.page = 1;
        load();
    });

    pagination.addEventListener('click', event => {
        const link = event.target.closest('[data-page]');
        if (!link) return;
        event.preventDefault();
        state.page = Number(link.dataset.page);
        load();
    });

    document.querySelector('#btn-create').addEventListener('click', () => {
        form.reset();
        document.querySelector('#field-id').value = '';
        document.querySelector('#modal-title').textContent = `New ${singularLabel}`;
        document.querySelector('#form-errors').classList.add('d-none');
        modal.show();
    });

    tableBody.addEventListener('click', event => {
        const edit = event.target.closest('[data-edit]');
        if (edit) {
            const item = JSON.parse(edit.dataset.edit);
            form.reset();
            document.querySelector('#field-id').value = item.id;
            document.querySelector('#modal-title').textContent = `Edit ${singularLabel}`;

            for (const field of fields) {
                const input = form.elements.namedItem(field);
                if (!input) continue;
                input.value = normalizeForForm(field, item[field]);
            }

            document.querySelector('#form-errors').classList.add('d-none');
            modal.show();
            return;
        }

        const del = event.target.closest('[data-delete]');
        if (del && confirm(`Delete this ${singularLabel.toLowerCase()}?`)) {
            fetch(`${apiBase}/${del.dataset.delete}`, { method: 'DELETE' }).then(() => load());
        }
    });

    form.addEventListener('submit', async event => {
        event.preventDefault();
        const errorBox = document.querySelector('#form-errors');
        errorBox.classList.add('d-none');

        const { id, payload } = formPayload();
        const method = id ? 'PUT' : 'POST';
        const url = id ? `${apiBase}/${id}` : apiBase;
        const res = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload),
        });

        if (!res.ok) {
            const err = await res.json();
            errorBox.textContent = JSON.stringify(err.errors || err.message || 'Validation failed');
            errorBox.classList.remove('d-none');
            return;
        }

        modal.hide();
        load();
    });

    buildForm();
    load();
})();
</script>
@endpush
