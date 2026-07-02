

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1"><?php echo e($entityLabel); ?></h1>
        <p class="text-muted mb-0">Administer <?php echo e(strtolower($entityLabel)); ?> through the Identity REST APIs.</p>
    </div>
    <button class="btn btn-primary" id="btn-create"><i class="bi bi-plus-lg me-1"></i> New <?php echo e(rtrim($entityLabel, 's')); ?></button>
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
                    <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($column); ?>"><?php echo e($column); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                    <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th><?php echo e(ucwords(str_replace('_', ' ', $column))); ?></th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center p-3 border-top">
            <span class="text-muted small" id="pagination-info">Loading...</span>
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
                <h5 class="modal-title" id="modal-title"><?php echo e($entityLabel); ?></h5>
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(() => {
    const apiBase = <?php echo json_encode($apiBase, 15, 512) ?>;
    const entityLabel = <?php echo json_encode($entityLabel, 15, 512) ?>;
    const singularLabel = entityLabel.endsWith('s') ? entityLabel.slice(0, -1) : entityLabel;
    const columns = <?php echo json_encode($columns, 15, 512) ?>;
    const fields = <?php echo json_encode($fields, 15, 512) ?>;
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

    function buildQuery() {
        const params = new URLSearchParams({ page: state.page, per_page: 15, sort: state.sort, direction: state.direction });
        if (state.search) params.set('search', state.search);
        if (state.status) params.set('status', state.status);
        return params.toString();
    }

    function toDisplay(value) {
        if (value === null || value === undefined || value === '') return '<span class="text-muted">-</span>';
        if (typeof value === 'object') return `<code>${JSON.stringify(value)}</code>`;
        return String(value);
    }

    function buildForm() {
        const controls = fields.map(field => {
            if (boolFields.has(field)) {
                return `<div class="col-md-6"><label class="form-label">${labelize(field)}</label><select class="form-select" name="${field}"><option value="1">Yes</option><option value="0">No</option></select></div>`;
            }
            if (jsonFields.has(field)) {
                return `<div class="col-12"><label class="form-label">${labelize(field)}</label><textarea class="form-control" rows="2" name="${field}">[]</textarea></div>`;
            }
            const type = field.endsWith('_at') ? 'datetime-local' : 'text';
            return `<div class="col-md-6"><label class="form-label">${labelize(field)}</label><input class="form-control" type="${type}" name="${field}"></div>`;
        }).join('');

        formFields.innerHTML = `
            <input type="hidden" name="id" id="field-id">
            ${controls}
            <div class="col-12"><div class="alert alert-danger d-none mb-0" id="form-errors"></div></div>
        `;
    }

    function renderPagination(meta) {
        paginationInfo.textContent = `Page ${meta.current_page || 1} of ${meta.last_page || 1} - ${meta.total || 0} total`;
        pagination.innerHTML = '';
        const last = meta.last_page || 1;
        const current = meta.current_page || 1;
        for (let page = Math.max(1, current - 2); page <= Math.min(last, current + 2); page++) {
            pagination.innerHTML += `<li class="page-item ${page === current ? 'active' : ''}"><a class="page-link" href="#" data-page="${page}">${page}</a></li>`;
        }
    }

    async function load() {
        tableBody.innerHTML = `<tr><td colspan="${columns.length + 1}" class="text-center py-4 text-muted">Loading...</td></tr>`;
        const res = await fetch(`${apiBase}?${buildQuery()}`);
        const json = await res.json();
        const rows = (json.data || []).map(item => `
            <tr>
                ${columns.map(column => `<td>${toDisplay(item[column])}</td>`).join('')}
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
            for (const field of fields) {
                const input = form.elements.namedItem(field);
                if (!input) continue;
                if (boolFields.has(field)) {
                    input.value = item[field] ? '1' : '0';
                } else if (jsonFields.has(field)) {
                    input.value = JSON.stringify(item[field] ?? []);
                } else {
                    input.value = item[field] ?? '';
                }
            }
            document.querySelector('#modal-title').textContent = `Edit ${singularLabel}`;
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

        const fd = new FormData(form);
        const id = fd.get('id');
        const payload = {};

        for (const field of fields) {
            const raw = fd.get(field);
            if (raw === null) continue;
            const value = String(raw).trim();
            if (value === '') continue;
            if (boolFields.has(field)) {
                payload[field] = value === '1';
            } elseif (jsonFields.has(field)) {
                try {
                    payload[field] = JSON.parse(value);
                } catch {
                    payload[field] = [];
                }
            } else {
                payload[field] = value;
            }
        }

        const res = await fetch(id ? `${apiBase}/${id}` : apiBase, {
            method: id ? 'PUT' : 'POST',
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\AxiomOS\axiomos-core\modules\Identity\Resources\views/crud/index.blade.php ENDPATH**/ ?>