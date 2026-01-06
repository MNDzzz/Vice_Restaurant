const API_URL = 'controllers/api.php';

// Caché de categorías actual
let categoriesCache = [];

document.addEventListener('DOMContentLoaded', () => {
    loadCategories();
    loadSection('dashboard');
});

// ============================================
// NAVEGACIÓN DE SECCIONES
// ============================================

function loadSection(section) {
    const content = document.getElementById('admin-content');
    const title = document.getElementById('section-title');

    // Actualizo el botón activo
    document.querySelectorAll('.list-group-item').forEach(btn => btn.classList.remove('active'));
    document.getElementById(`btn-${section}`)?.classList.add('active');

    // Muestro cargando
    content.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';

    switch (section) {
        case 'dashboard':
            title.innerText = 'Dashboard';
            loadDashboard();
            break;
        case 'users':
            title.innerText = 'Gestión de Usuarios';
            fetchUsers();
            break;
        case 'orders':
            title.innerText = 'Gestión de Pedidos';
            fetchOrders();
            break;
        case 'products':
            title.innerText = 'Gestión de Productos';
            fetchProducts();
            break;
        default:
            content.innerHTML = '<p class="text-muted">Sección no encontrada</p>';
    }
}

// ============================================
// DASHBOARD
// ============================================

async function loadDashboard() {
    try {
        const res = await fetch(`${API_URL}?action=get_stats`);
        const stats = await res.json();

        const content = document.getElementById('admin-content');
        content.innerHTML = `
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-dark h-100">
                        <div class="card-body text-center">
                            <h2 class="mb-0">${stats.total_users || 0}</h2>
                            <p class="mb-0">Usuarios</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-dark h-100">
                        <div class="card-body text-center">
                            <h2 class="mb-0">${stats.total_orders || 0}</h2>
                            <p class="mb-0">Pedidos Totales</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-dark h-100">
                        <div class="card-body text-center">
                            <h2 class="mb-0">${stats.pending_orders || 0}</h2>
                            <p class="mb-0">Pedidos Pendientes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body text-center">
                            <h2 class="mb-0">${parseFloat(stats.total_revenue || 0).toFixed(2)}€</h2>
                            <p class="mb-0">Ingresos</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card bg-secondary">
                        <div class="card-header">📦 Productos</div>
                        <div class="card-body">
                            <h3>${stats.total_products || 0}</h3>
                            <p class="text-muted mb-0">productos en el menú</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-secondary">
                        <div class="card-header">⚡ Acciones Rápidas</div>
                        <div class="card-body">
                            <button class="btn btn-primary me-2" onclick="loadSection('products'); setTimeout(openProductModal, 300);">
                                + Nuevo Producto
                            </button>
                            <button class="btn btn-outline-light" onclick="loadSection('orders')">
                                Ver Pedidos
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    } catch (err) {
        console.error(err);
        document.getElementById('admin-content').innerHTML = '<p class="text-danger">Error cargando estadísticas</p>';
    }
}

// ============================================
// CATEGORÍAS
// ============================================

async function loadCategories() {
    try {
        const res = await fetch(`${API_URL}?action=get_categories`);
        categoriesCache = await res.json();
        updateCategorySelect();
    } catch (err) {
        console.error('Error loading categories:', err);
    }
}

function updateCategorySelect() {
    const select = document.getElementById('prodCategory');
    if (!select) return;

    select.innerHTML = '<option value="">Sin categoría</option>';
    categoriesCache.forEach(cat => {
        select.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
    });
}

// ============================================
// GESTIÓN DE USUARIOS
// ============================================

async function fetchUsers() {
    try {
        const res = await fetch(`${API_URL}?action=get_users`);
        const users = await res.json();
        renderUsers(users);
    } catch (err) {
        console.error(err);
        document.getElementById('admin-content').innerHTML = '<p class="text-danger">Error cargando usuarios</p>';
    }
}

function renderUsers(users) {
    let html = `
        <button class="btn btn-primary mb-3" onclick="openUserModal()">+ Nuevo Usuario</button>
        <div class="table-responsive">
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
    `;

    if (users.length === 0) {
        html += '<tr><td colspan="6" class="text-center text-muted">No hay usuarios</td></tr>';
    }

    users.forEach(u => {
        const roleBadge = {
            'superadmin': 'bg-danger',
            'admin': 'bg-warning text-dark',
            'user': 'bg-secondary'
        }[u.role] || 'bg-secondary';

        const statusBadge = u.is_active == 1 ? 'bg-success' : 'bg-danger';
        const statusText = u.is_active == 1 ? 'Activo' : 'Inactivo';

        // Solo muestro el botón de cambiar admin para superadmin
        const toggleAdminBtn = IS_SUPERADMIN && u.role !== 'superadmin' ?
            `<button class="btn btn-sm btn-outline-warning" onclick="toggleAdmin(${u.id})" title="Cambiar rol">
                ${u.role === 'admin' ? '⬇️' : '⬆️'}
            </button>` : '';

        html += `
            <tr>
                <td>${u.id}</td>
                <td>${escapeHtml(u.name)}</td>
                <td>${escapeHtml(u.email)}</td>
                <td><span class="badge ${roleBadge}">${u.role}</span></td>
                <td><span class="badge ${statusBadge}">${statusText}</span></td>
                <td>
                    ${u.role !== 'superadmin' ? `
                        <button class="btn btn-sm btn-info" onclick="editUser(${u.id})">✏️</button>
                        <button class="btn btn-sm btn-${u.is_active == 1 ? 'secondary' : 'success'}" 
                                onclick="toggleActive(${u.id})" title="${u.is_active == 1 ? 'Desactivar' : 'Activar'}">
                            ${u.is_active == 1 ? '🔒' : '🔓'}
                        </button>
                        ${toggleAdminBtn}
                        <button class="btn btn-sm btn-danger" onclick="deleteUser(${u.id})">🗑️</button>
                    ` : '<span class="text-muted">Protegido</span>'}
                </td>
            </tr>
        `;
    });

    html += '</tbody></table></div>';
    document.getElementById('admin-content').innerHTML = html;
}

function openUserModal(user = null) {
    document.getElementById('userModalTitle').innerText = user ? 'Editar Usuario' : 'Nuevo Usuario';
    document.getElementById('userId').value = user?.id || '';
    document.getElementById('userName').value = user?.name || '';
    document.getElementById('userEmail').value = user?.email || '';
    document.getElementById('userPassword').value = '';
    document.getElementById('userRole').value = user?.role || 'user';
    document.getElementById('userActive').checked = user ? user.is_active == 1 : true;

    new bootstrap.Modal(document.getElementById('userModal')).show();
}

async function editUser(id) {
    try {
        const res = await fetch(`${API_URL}?action=get_user&id=${id}`);
        const user = await res.json();
        if (user && !user.error) {
            openUserModal(user);
        } else {
            alert('Error: ' + (user.error || 'Usuario no encontrado'));
        }
    } catch (err) {
        console.error(err);
        alert('Error de conexión');
    }
}

document.getElementById('userForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const id = document.getElementById('userId').value;
    const data = {
        name: document.getElementById('userName').value,
        email: document.getElementById('userEmail').value,
        password: document.getElementById('userPassword').value,
        role: document.getElementById('userRole').value,
        is_active: document.getElementById('userActive').checked ? 1 : 0
    };

    if (id) data.id = id;

    const action = id ? 'update_user' : 'create_user';

    try {
        const res = await fetch(`${API_URL}?action=${action}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();

        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
            fetchUsers();
        } else {
            alert('Error: ' + (result.error || 'No se pudo guardar'));
        }
    } catch (err) {
        console.error(err);
        alert('Error de conexión');
    }
});

async function deleteUser(id) {
    if (!confirm('¿Seguro que quieres eliminar este usuario?')) return;

    try {
        const res = await fetch(`${API_URL}?action=delete_user`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });
        const result = await res.json();

        if (result.success) {
            fetchUsers();
        } else {
            alert('Error: ' + (result.error || 'No se pudo eliminar'));
        }
    } catch (err) {
        console.error(err);
    }
}

async function toggleAdmin(id) {
    if (!confirm('¿Cambiar el rol de administrador de este usuario?')) return;

    try {
        const res = await fetch(`${API_URL}?action=toggle_admin`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });
        const result = await res.json();

        if (result.success) {
            fetchUsers();
        } else {
            alert('Error: ' + (result.error || 'No se pudo cambiar rol'));
        }
    } catch (err) {
        console.error(err);
    }
}

async function toggleActive(id) {
    try {
        const res = await fetch(`${API_URL}?action=toggle_active`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });
        const result = await res.json();

        if (result.success) {
            fetchUsers();
        } else {
            alert('Error: ' + (result.error || 'No se pudo cambiar estado'));
        }
    } catch (err) {
        console.error(err);
    }
}

// ============================================
// GESTIÓN DE PRODUCTOS
// ============================================

async function fetchProducts() {
    try {
        const res = await fetch(`${API_URL}?action=get_products`);
        const products = await res.json();
        renderProducts(products);
    } catch (err) {
        console.error(err);
        document.getElementById('admin-content').innerHTML = '<p class="text-danger">Error cargando productos</p>';
    }
}

function renderProducts(products) {
    let html = `
        <button class="btn btn-primary mb-3" onclick="openProductModal()">+ Nuevo Producto</button>
        <div class="table-responsive">
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Categoría</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
    `;

    if (products.length === 0) {
        html += '<tr><td colspan="7" class="text-center text-muted">No hay productos</td></tr>';
    }

    products.forEach(p => {
        html += `
            <tr>
                <td>${p.id}</td>
                <td><img src="${escapeHtml(p.image)}" alt="${escapeHtml(p.name)}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;"></td>
                <td>${escapeHtml(p.name)}</td>
                <td><small>${escapeHtml((p.description || '').substring(0, 50))}${p.description?.length > 50 ? '...' : ''}</small></td>
                <td><strong>${parseFloat(p.price).toFixed(2)}€</strong></td>
                <td>${escapeHtml(p.category_name || 'Sin categoría')}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="editProduct(${p.id})">✏️</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteProduct(${p.id})">🗑️</button>
                </td>
            </tr>
        `;
    });

    html += '</tbody></table></div>';
    document.getElementById('admin-content').innerHTML = html;
}

function openProductModal(product = null) {
    updateCategorySelect();
    document.getElementById('productModalTitle').innerText = product ? 'Editar Producto' : 'Nuevo Producto';
    document.getElementById('prodId').value = product?.id || '';
    document.getElementById('prodName').value = product?.name || '';
    document.getElementById('prodDescription').value = product?.description || '';
    document.getElementById('prodPrice').value = product?.price || '';
    document.getElementById('prodImage').value = product?.image || '';
    document.getElementById('prodCategory').value = product?.category_id || '';

    new bootstrap.Modal(document.getElementById('productModal')).show();
}

async function editProduct(id) {
    try {
        const res = await fetch(`${API_URL}?action=get_product&id=${id}`);
        const product = await res.json();
        if (product) {
            openProductModal(product);
        }
    } catch (err) {
        console.error(err);
    }
}

document.getElementById('productForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const id = document.getElementById('prodId').value;
    const data = {
        name: document.getElementById('prodName').value,
        description: document.getElementById('prodDescription').value,
        price: document.getElementById('prodPrice').value,
        image: document.getElementById('prodImage').value || 'img/default-product.jpg',
        category_id: document.getElementById('prodCategory').value || null
    };

    if (id) data.id = id;

    const action = id ? 'update_product' : 'create_product';

    try {
        const res = await fetch(`${API_URL}?action=${action}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();

        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
            fetchProducts();
        } else {
            alert('Error: ' + (result.error || 'No se pudo guardar'));
        }
    } catch (err) {
        console.error(err);
        alert('Error de conexión');
    }
});

async function deleteProduct(id) {
    if (!confirm('¿Seguro que quieres eliminar este producto?')) return;

    try {
        const res = await fetch(`${API_URL}?action=delete_product`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });
        const result = await res.json();

        if (result.success) {
            fetchProducts();
        } else {
            alert('Error: ' + (result.error || 'No se pudo eliminar'));
        }
    } catch (err) {
        console.error(err);
    }
}

// ============================================
// GESTIÓN DE PEDIDOS
// ============================================

async function fetchOrders() {
    try {
        const res = await fetch(`${API_URL}?action=get_orders`);
        const orders = await res.json();
        renderOrders(orders);
    } catch (err) {
        console.error(err);
        document.getElementById('admin-content').innerHTML = '<p class="text-danger">Error cargando pedidos</p>';
    }
}

function renderOrders(orders) {
    let html = `
        <div class="table-responsive">
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
    `;

    if (orders.length === 0) {
        html += '<tr><td colspan="6" class="text-center text-muted">No hay pedidos</td></tr>';
    }

    orders.forEach(o => {
        const statusBadge = {
            'pending': 'bg-warning text-dark',
            'completed': 'bg-success',
            'cancelled': 'bg-danger'
        }[o.status] || 'bg-secondary';

        const statusText = {
            'pending': 'Pendiente',
            'completed': 'Completado',
            'cancelled': 'Cancelado'
        }[o.status] || o.status;

        html += `
            <tr>
                <td>#${o.id}</td>
                <td>${escapeHtml(o.user_name || 'Anónimo')}<br><small class="text-muted">${escapeHtml(o.user_email || '')}</small></td>
                <td><strong>${parseFloat(o.total).toFixed(2)}€</strong></td>
                <td><span class="badge ${statusBadge}">${statusText}</span></td>
                <td><small>${new Date(o.created_at).toLocaleString('es-ES')}</small></td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="viewOrder(${o.id})">👁️</button>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                            Estado
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            <li><a class="dropdown-item" href="#" onclick="updateOrderStatus(${o.id}, 'pending')">⏳ Pendiente</a></li>
                            <li><a class="dropdown-item" href="#" onclick="updateOrderStatus(${o.id}, 'completed')">✅ Completado</a></li>
                            <li><a class="dropdown-item" href="#" onclick="updateOrderStatus(${o.id}, 'cancelled')">❌ Cancelado</a></li>
                        </ul>
                    </div>
                    <button class="btn btn-sm btn-danger" onclick="deleteOrder(${o.id})">🗑️</button>
                </td>
            </tr>
        `;
    });

    html += '</tbody></table></div>';
    document.getElementById('admin-content').innerHTML = html;
}

async function viewOrder(id) {
    try {
        const res = await fetch(`${API_URL}?action=get_order&id=${id}`);
        const order = await res.json();

        if (order) {
            document.getElementById('orderIdDisplay').innerText = order.id;

            let itemsHtml = '<table class="table table-dark table-sm"><thead><tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr></thead><tbody>';

            order.items?.forEach(item => {
                itemsHtml += `
                    <tr>
                        <td>${escapeHtml(item.product_name || 'Producto eliminado')}</td>
                        <td>${item.quantity}</td>
                        <td>${parseFloat(item.price).toFixed(2)}€</td>
                        <td>${(item.quantity * item.price).toFixed(2)}€</td>
                    </tr>
                `;
            });

            itemsHtml += '</tbody></table>';

            document.getElementById('orderDetails').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Cliente:</strong> ${escapeHtml(order.user_name || 'Anónimo')}</p>
                        <p><strong>Email:</strong> ${escapeHtml(order.user_email || 'N/A')}</p>
                        <p><strong>Fecha:</strong> ${new Date(order.created_at).toLocaleString('es-ES')}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Total:</strong> <span class="text-success fs-4">${parseFloat(order.total).toFixed(2)}€</span></p>
                        <p><strong>Estado:</strong> ${order.status}</p>
                    </div>
                </div>
                <hr>
                <h6>Productos:</h6>
                ${itemsHtml}
            `;

            new bootstrap.Modal(document.getElementById('orderModal')).show();
        }
    } catch (err) {
        console.error(err);
    }
}

async function updateOrderStatus(id, status) {
    try {
        const res = await fetch(`${API_URL}?action=update_order_status`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, status })
        });
        const result = await res.json();

        if (result.success) {
            fetchOrders();
        } else {
            alert('Error: ' + (result.error || 'No se pudo actualizar'));
        }
    } catch (err) {
        console.error(err);
    }
}

async function deleteOrder(id) {
    if (!confirm('¿Seguro que quieres eliminar este pedido?')) return;

    try {
        const res = await fetch(`${API_URL}?action=delete_order`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });
        const result = await res.json();

        if (result.success) {
            fetchOrders();
        } else {
            alert('Error: ' + (result.error || 'No se pudo eliminar'));
        }
    } catch (err) {
        console.error(err);
    }
}

// ============================================
// UTILIDADES
// ============================================

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
