const apiUrl = `${window.location.pathname}?api=1`;
const userBody = document.getElementById('user-tbody');
const pagination = document.getElementById('pagination');
const form = document.getElementById('user-form');
const status = document.getElementById('status');
const submitBtn = document.getElementById('submit-btn');
const resetBtn = document.getElementById('reset-btn');
const countPill = document.getElementById('count-pill');
const addBtn = document.getElementById('add-btn');
const modal = document.getElementById('user-modal');
const fields = {
    id: document.getElementById('user-id'),
    name: document.getElementById('name'),
    email: document.getElementById('email'),
    password: document.getElementById('password')
};

function setStatus(message, tone = 'muted') {
    status.textContent = message;
    status.classList.toggle('text-error', tone === 'error');
}

function openModal() {
    if (typeof modal.showModal === 'function') {
        modal.showModal();
    } else {
        modal.setAttribute('open', 'true');
    }
}

function closeModal() {
    if (typeof modal.close === 'function') {
        modal.close();
    } else {
        modal.removeAttribute('open');
    }
}

function setMode(mode) {
    if (mode === 'edit') {
        submitBtn.textContent = 'Update user';
    } else {
        submitBtn.textContent = 'Create user';
        fields.id.value = '';
    }
}

function clearForm() {
    form.reset();
    setMode('create');
}

const pageSize = 5;
let currentPage = 1;
let allUsers = [];

function renderPagination(totalPages) {
    pagination.innerHTML = '';
    if (totalPages <= 1) {
        return;
    }

    const prevButton = document.createElement('button');
    prevButton.className = 'btn btn-sm app-pagination';
    prevButton.textContent = 'Previous';
    prevButton.disabled = currentPage === 1;
    prevButton.addEventListener('click', () => {
        currentPage = Math.max(1, currentPage - 1);
        renderUsers(allUsers);
    });
    pagination.appendChild(prevButton);

    for (let page = 1; page <= totalPages; page += 1) {
        const pageButton = document.createElement('button');
        pageButton.className = page === currentPage
            ? 'btn btn-sm app-pagination-active'
            : 'btn btn-sm app-pagination';
        pageButton.textContent = page;
        pageButton.addEventListener('click', () => {
            currentPage = page;
            renderUsers(allUsers);
        });
        pagination.appendChild(pageButton);
    }

    const nextButton = document.createElement('button');
    nextButton.className = 'btn btn-sm app-pagination';
    nextButton.textContent = 'Next';
    nextButton.disabled = currentPage === totalPages;
    nextButton.addEventListener('click', () => {
        currentPage = Math.min(totalPages, currentPage + 1);
        renderUsers(allUsers);
    });
    pagination.appendChild(nextButton);
}

function renderUsers(users) {
    const sortedUsers = [...users].sort((a, b) => Number(a.id) - Number(b.id));
    const totalPages = Math.max(1, Math.ceil(sortedUsers.length / pageSize));
    if (currentPage > totalPages) {
        currentPage = totalPages;
    }
    const startIndex = (currentPage - 1) * pageSize;
    const pageUsers = sortedUsers.slice(startIndex, startIndex + pageSize);

    userBody.innerHTML = '';
    countPill.textContent = `${sortedUsers.length} user${sortedUsers.length === 1 ? '' : 's'}`;

    if (!sortedUsers.length) {
        userBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-slate-300">No users yet. Add someone to see them appear here.</td>
            </tr>
        `;
        pagination.innerHTML = '';
        return;
    }

    pageUsers.forEach((user) => {
        const displayName = user.fullname || user.name || 'Unnamed user';
        const row = document.createElement('tr');
        row.className = 'text-base-content';
        row.dataset.password = user.password || '';
        const maskedPassword = user.password ? '********' : '-';
        row.innerHTML = `
            <td>${user.id}</td>
            <td class="font-medium text-base-content">${displayName}</td>
            <td>${user.email}</td>
            <td>
                <div class="flex items-center gap-2">
                    <span class="font-mono text-base-content" data-password>${maskedPassword}</span>
                    <button class="btn btn-xs btn-ghost" type="button" data-action="toggle-password" aria-label="Show password">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4">
                            <path fill="currentColor" d="M12 5c5.05 0 9.09 3.15 10.5 7-1.41 3.85-5.45 7-10.5 7S2.91 15.85 1.5 12C2.91 8.15 6.95 5 12 5zm0 2c-3.54 0-6.75 2.06-8.14 5 1.39 2.94 4.6 5 8.14 5s6.75-2.06 8.14-5C18.75 9.06 15.54 7 12 7zm0 2.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5z"/>
                        </svg>
                    </button>
                </div>
            </td>
            <td>
                <button class="btn btn-sm btn-outline" data-action="edit" data-id="${user.id}">Edit</button>
            </td>
        `;
        userBody.appendChild(row);
    });

    renderPagination(totalPages);
}

async function loadUsers() {
    setStatus('Loading users...');
    try {
        const response = await fetch(apiUrl);
        const data = await response.json();
        allUsers = Array.isArray(data) ? data : [];
        renderUsers(allUsers);
        setStatus('Directory synced.');
    } catch (error) {
        setStatus('Unable to load users. Check the API.', 'error');
    }
}

async function submitUser(event) {
    event.preventDefault();
    const payload = {
        id: fields.id.value || undefined,
        name: fields.name.value.trim(),
        email: fields.email.value.trim(),
        password: fields.password.value
    };

    const isEdit = Boolean(payload.id);
    const method = isEdit ? 'PUT' : 'POST';
    setStatus(isEdit ? 'Updating user...' : 'Creating user...');

    try {
        const response = await fetch(apiUrl, {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.message || 'Request failed');
        }
        if (isEdit && window.Swal) {
            Swal.fire({
                icon: 'success',
                title: 'User updated',
                text: 'Changes saved successfully.'
            });
        }
        setStatus(isEdit ? 'User updated.' : 'User created.');
        clearForm();
        closeModal();
        await loadUsers();
    } catch (error) {
        setStatus(error.message || 'Something went wrong.', 'error');
    }
}

userBody.addEventListener('click', (event) => {
    const toggleButton = event.target.closest('button[data-action="toggle-password"]');
    if (toggleButton) {
        const row = toggleButton.closest('tr');
        const passwordCell = row.querySelector('[data-password]');
        const isShowing = toggleButton.dataset.visible === 'true';
        const passwordValue = row.dataset.password || '';
        if (isShowing) {
            passwordCell.textContent = passwordValue ? '********' : '-';
            toggleButton.setAttribute('aria-label', 'Show password');
            toggleButton.dataset.visible = 'false';
            toggleButton.innerHTML = `
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4">
                    <path fill="currentColor" d="M12 5c5.05 0 9.09 3.15 10.5 7-1.41 3.85-5.45 7-10.5 7S2.91 15.85 1.5 12C2.91 8.15 6.95 5 12 5zm0 2c-3.54 0-6.75 2.06-8.14 5 1.39 2.94 4.6 5 8.14 5s6.75-2.06 8.14-5C18.75 9.06 15.54 7 12 7zm0 2.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5z"/>
                </svg>
            `;
        } else {
            passwordCell.textContent = passwordValue || '-';
            toggleButton.setAttribute('aria-label', 'Hide password');
            toggleButton.dataset.visible = 'true';
            toggleButton.innerHTML = `
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4">
                    <path fill="currentColor" d="M2.1 3.51L3.51 2.1l18.39 18.39-1.41 1.41-3.12-3.12A10.9 10.9 0 0 1 12 19c-5.05 0-9.09-3.15-10.5-7a11.6 11.6 0 0 1 4.39-5.4L2.1 3.51zm6.1 6.1a3 3 0 0 0 4.2 4.2l-4.2-4.2zm7.69 7.69-1.77-1.77a4.96 4.96 0 0 1-6.64-6.64L5.6 7.11A9.51 9.51 0 0 0 3.6 12c1.39 2.94 4.6 5 8.4 5 1.33 0 2.59-.25 3.89-.7zM8.11 5.6A9.44 9.44 0 0 1 12 5c5.05 0 9.09 3.15 10.5 7a11.7 11.7 0 0 1-3.3 4.34l-2.02-2.02a4.96 4.96 0 0 0-6.5-6.5L8.11 5.6z"/>
                </svg>
            `;
        }
        return;
    }

    const button = event.target.closest('button[data-action="edit"]');
    if (!button) return;

    const row = button.closest('tr');
    const cells = row.querySelectorAll('td');
    const id = button.dataset.id;
    const name = cells[1].textContent;
    const email = cells[2].textContent;

    fields.id.value = id;
    fields.name.value = name;
    fields.email.value = email;
    fields.password.value = '';
    setMode('edit');
    openModal();
});

addBtn.addEventListener('click', () => {
    clearForm();
    setMode('create');
    openModal();
});

modal.addEventListener('click', (event) => {
    if (event.target === modal) {
        closeModal();
    }
});

form.addEventListener('submit', submitUser);
resetBtn.addEventListener('click', clearForm);

loadUsers();
