@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Users</h1>
    <button class="btn btn-primary" id="btn-create">New User</button>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0" id="users-table">
            <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Display Name</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="user-modal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="user-form">
            <div class="modal-header">
                <h5 class="modal-title">User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-2">
                <input type="hidden" name="id">
                <div class="col-12">
                    <label class="form-label">Identity ID</label>
                    <input class="form-control" name="identity_id" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Username</label>
                    <input class="form-control" name="username" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Email</label>
                    <input class="form-control" name="email" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Display Name</label>
                    <input class="form-control" name="display_name" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Status</label>
                    <input class="form-control" name="status" value="active">
                </div>
                <div class="col-12">
                    <div class="alert alert-danger d-none mb-0" id="errors"></div>
                </div>
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
    const apiBase = @json($apiBase ?? '/api/users');
    const tbody = document.querySelector('#users-table tbody');
    const form = document.querySelector('#user-form');
    const errors = document.querySelector('#errors');
    const modal = bootstrap.Modal.getOrCreateInstance(document.querySelector('#user-modal'));

    async function load() {
        const res = await fetch(apiBase);
        const json = await res.json();
        const rows = (json.data || []).map(item => `
            <tr>
                <td>${item.username}</td>
                <td>${item.email}</td>
                <td>${item.display_name}</td>
                <td>${item.status || ''}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-secondary me-1" data-edit='${JSON.stringify(item)}'>Edit</button>
                    <button class="btn btn-sm btn-outline-danger" data-delete="${item.id}">Delete</button>
                </td>
            </tr>`).join('');
        tbody.innerHTML = rows || '<tr><td colspan="5" class="text-center text-muted py-3">No users found.</td></tr>';
    }

    document.querySelector('#btn-create').addEventListener('click', () => {
        form.reset();
        form.elements.id.value = '';
        form.elements.status.value = 'active';
        errors.classList.add('d-none');
        modal.show();
    });

    tbody.addEventListener('click', (event) => {
        const edit = event.target.closest('[data-edit]');
        if (edit) {
            const item = JSON.parse(edit.dataset.edit);
            form.reset();
            for (const [key, value] of Object.entries(item)) {
                if (form.elements.namedItem(key) && value !== null) {
                    form.elements.namedItem(key).value = value;
                }
            }
            errors.classList.add('d-none');
            modal.show();
            return;
        }

        const del = event.target.closest('[data-delete]');
        if (del && confirm('Delete this user?')) {
            fetch(`${apiBase}/${del.dataset.delete}`, { method: 'DELETE' }).then(load);
        }
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        errors.classList.add('d-none');

        const fd = new FormData(form);
        const id = fd.get('id');
        const payload = Object.fromEntries([...fd.entries()].filter(([key]) => key !== 'id'));
        const url = id ? `${apiBase}/${id}` : apiBase;
        const method = id ? 'PUT' : 'POST';
        const res = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload),
        });

        if (!res.ok) {
            const err = await res.json();
            errors.textContent = JSON.stringify(err.errors || err.message || 'Request failed');
            errors.classList.remove('d-none');
            return;
        }

        modal.hide();
        load();
    });

    load();
})();
</script>
@endpush
