const API_URL = 'controllers/api.php';

// ============================================
// CLASE DE UTILS
// ============================================
class Utils {
    static escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    static showSpinner(containerId) {
        document.getElementById(containerId).innerHTML =
            '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
    }

    static showError(containerId, message) {
        document.getElementById(containerId).innerHTML =
            `<p class="text-danger">${message}</p>`;
    }
}

// ============================================
// GESTOR DE MONEDAS (API EXTERNA)
// ============================================
class CurrencyManager {
    constructor() {
        this.rates = { EUR: 1 };
        this.currentCurrency = 'EUR';
        this.currencies = ['EUR', 'USD', 'GBP', 'JPY'];
    }

    async fetchRates() {
        try {
            // API Externa: Frankfurter (Para meter diferentes monedas)
            const res = await fetch('https://api.frankfurter.app/latest?from=EUR&to=USD,GBP,JPY');
            const data = await res.json();
            this.rates = { EUR: 1, ...data.rates };
            return true;
        } catch (err) {
            console.error('Error fetching rates:', err);
            return false;
        }
    }

    convert(amountInEur) {
        const rate = this.rates[this.currentCurrency] || 1;
        return (amountInEur * rate).toFixed(2);
    }

    getSymbol() {
        const symbols = { EUR: '€', USD: '$', GBP: '£', JPY: '¥' };
        return symbols[this.currentCurrency] || '';
    }
}

// ============================================
// GESTOR DEL DASHBOARD
// ============================================
class DashboardManager {
    async load() {
        try {
            const res = await fetch(`${API_URL}?action=get_stats`);
            const stats = await res.json();

            // Cargo tasas si no están cargadas
            if (Object.keys(app.currencyManager.rates).length === 1) {
                await app.currencyManager.fetchRates();
            }

            this.render(stats);
        } catch (err) {
            console.error(err);
            Utils.showError('admin-content', 'Error cargando estadísticas');
        }
    }

    render(stats) {
        const currency = app.currencyManager.currentCurrency;
        const symbol = app.currencyManager.getSymbol();
        const revenue = app.currencyManager.convert(stats.total_revenue || 0);

        const content = document.getElementById('admin-content');

        // MAP para generar opciones de moneda
        const currencyOptions = app.currencyManager.currencies.map(c =>
            `<option value="${c}" ${c === currency ? 'selected' : ''}>${c}</option>`
        ).join('');

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
                            <div class="d-flex justify-content-center align-items-center mb-2">
                                <h2 class="mb-0 me-2" id="revenueDisplay">${revenue}${symbol}</h2>
                            </div>
                            <select class="form-select form-select-sm bg-success text-white border-white w-50 mx-auto" 
                                    onchange="app.updateCurrency(this.value)">
                                ${currencyOptions}
                            </select>
                            <p class="mb-0 mt-1">Ingresos</p>
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
                            <button class="btn btn-primary me-2" onclick="app.loadSection('products'); setTimeout(() => app.productManager.openModal(), 300);">
                                + Nuevo Producto
                            </button>
                            <button class="btn btn-outline-light" onclick="app.loadSection('orders')">
                                Ver Pedidos
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
}

// ============================================
// GESTOR DE USUARIOS
// ============================================
class UserManager {
    constructor() {
        this.initListeners();
    }

    initListeners() {
        const form = document.getElementById('userForm');
        if (form) {
            const newForm = form.cloneNode(true);
            form.parentNode.replaceChild(newForm, form);
            newForm.addEventListener('submit', (e) => this.handleSubmit(e));
        }
    }

    async fetchUsers() {
        try {
            const res = await fetch(`${API_URL}?action=get_users`);
            const users = await res.json();
            this.render(users);
        } catch (err) {
            console.error(err);
            Utils.showError('admin-content', 'Error cargando usuarios');
        }
    }

    render(users) {
        let html = `
            <button class="btn btn-primary mb-3" onclick="app.userManager.openModal()">+ Nuevo Usuario</button>
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
        } else {
            // MAP
            html += users.map(u => {
                const roleBadge = {
                    'superadmin': 'bg-danger',
                    'admin': 'bg-warning text-dark',
                    'user': 'bg-secondary'
                }[u.role] || 'bg-secondary';

                const statusBadge = u.is_active == 1 ? 'bg-success' : 'bg-danger';
                const statusText = u.is_active == 1 ? 'Activo' : 'Inactivo';

                const toggleAdminBtn = IS_SUPERADMIN && u.role !== 'superadmin' ?
                    `<button class="btn btn-sm btn-outline-warning" onclick="app.userManager.toggleAdmin(${u.id})" title="Cambiar rol">
                        ${u.role === 'admin' ? '⬇️' : '⬆️'}
                    </button>` : '';

                const actions = u.role !== 'superadmin' ? `
                    <button class="btn btn-sm btn-info" onclick="app.userManager.editUser(${u.id})">✏️</button>
                    <button class="btn btn-sm btn-${u.is_active == 1 ? 'secondary' : 'success'}" 
                            onclick="app.userManager.toggleActive(${u.id})" title="${u.is_active == 1 ? 'Desactivar' : 'Activar'}">
                        ${u.is_active == 1 ? '🔒' : '🔓'}
                    </button>
                    ${toggleAdminBtn}
                    <button class="btn btn-sm btn-danger" onclick="app.userManager.deleteUser(${u.id})">🗑️</button>
                ` : '<span class="text-muted">Protegido</span>';

                return `
                    <tr>
                        <td>${u.id}</td>
                        <td>${Utils.escapeHtml(u.name)}</td>
                        <td>${Utils.escapeHtml(u.email)}</td>
                        <td><span class="badge ${roleBadge}">${u.role}</span></td>
                        <td><span class="badge ${statusBadge}">${statusText}</span></td>
                        <td>${actions}</td>
                    </tr>
                `;
            }).join('');
        }

        html += '</tbody></table></div>';
        document.getElementById('admin-content').innerHTML = html;
    }

    openModal(user = null) {
        document.getElementById('userModalTitle').innerText = user ? 'Editar Usuario' : 'Nuevo Usuario';
        document.getElementById('userId').value = user?.id || '';
        document.getElementById('userName').value = user?.name || '';
        document.getElementById('userEmail').value = user?.email || '';
        document.getElementById('userPassword').value = '';
        document.getElementById('userRole').value = user?.role || 'user';
        document.getElementById('userActive').checked = user ? user.is_active == 1 : true;

        new bootstrap.Modal(document.getElementById('userModal')).show();
    }

    async editUser(id) {
        try {
            const res = await fetch(`${API_URL}?action=get_user&id=${id}`);
            const user = await res.json();
            if (user && !user.error) {
                this.openModal(user);
            } else {
                alert('Error: ' + (user.error || 'Usuario no encontrado'));
            }
        } catch (err) {
            console.error(err);
            alert('Error de conexión');
        }
    }

    async handleSubmit(e) {
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
                this.fetchUsers();
            } else {
                alert('Error: ' + (result.error || 'No se pudo guardar'));
            }
        } catch (err) {
            console.error(err);
            alert('Error de conexión');
        }
    }

    async deleteUser(id) {
        if (!confirm('¿Seguro que quieres eliminar este usuario?')) return;
        this.performAction('delete_user', { id });
    }

    async toggleAdmin(id) {
        if (!confirm('¿Cambiar el rol de administrador de este usuario?')) return;
        this.performAction('toggle_admin', { id });
    }

    async toggleActive(id) {
        this.performAction('toggle_active', { id });
    }

    async performAction(action, data) {
        try {
            const res = await fetch(`${API_URL}?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await res.json();
            if (result.success) {
                this.fetchUsers();
            } else {
                alert('Error: ' + (result.error || 'Acción fallida'));
            }
        } catch (err) {
            console.error(err);
        }
    }
}

// ============================================
// GESTOR DE PRODUCTOS
// ============================================
class ProductManager {
    constructor() {
        this.categoriesCache = [];
        this.initListeners();
    }

    initListeners() {
        const form = document.getElementById('productForm');
        if (form) {
            const newForm = form.cloneNode(true);
            form.parentNode.replaceChild(newForm, form);
            newForm.addEventListener('submit', (e) => this.handleSubmit(e));
        }
    }

    async loadCategories() {
        try {
            const res = await fetch(`${API_URL}?action=get_categories`);
            this.categoriesCache = await res.json();
        } catch (err) {
            console.error('Error loading categories:', err);
        }
    }

    updateCategorySelect() {
        const select = document.getElementById('prodCategory');
        if (!select) return;

        select.innerHTML = '<option value="">Sin categoría</option>';
        this.categoriesCache.forEach(cat => {
            select.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
        });
    }

    async fetchProducts() {
        try {
            if (this.categoriesCache.length === 0) await this.loadCategories();
            const res = await fetch(`${API_URL}?action=get_products`);
            const products = await res.json();
            this.render(products);
        } catch (err) {
            console.error(err);
            Utils.showError('admin-content', 'Error cargando productos');
        }
    }

    render(products) {
        let html = `
            <button class="btn btn-primary mb-3" onclick="app.productManager.openModal()">+ Nuevo Producto</button>
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
        } else {
            // MAP
            html += products.map(p => `
                <tr>
                    <td>${p.id}</td>
                    <td><img src="${Utils.escapeHtml(p.image)}" alt="${Utils.escapeHtml(p.name)}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;"></td>
                    <td>${Utils.escapeHtml(p.name)}</td>
                    <td><small>${Utils.escapeHtml((p.description || '').substring(0, 50))}${p.description?.length > 50 ? '...' : ''}</small></td>
                    <td><strong>${parseFloat(p.price).toFixed(2)}€</strong></td>
                    <td>${Utils.escapeHtml(p.category_name || 'Sin categoría')}</td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="app.productManager.editProduct(${p.id})">✏️</button>
                        <button class="btn btn-sm btn-danger" onclick="app.productManager.deleteProduct(${p.id})">🗑️</button>
                    </td>
                </tr>
            `).join('');
        }

        html += '</tbody></table></div>';
        document.getElementById('admin-content').innerHTML = html;
    }

    openModal(product = null) {
        this.updateCategorySelect();
        document.getElementById('productModalTitle').innerText = product ? 'Editar Producto' : 'Nuevo Producto';
        document.getElementById('prodId').value = product?.id || '';
        document.getElementById('prodName').value = product?.name || '';
        document.getElementById('prodDescription').value = product?.description || '';
        document.getElementById('prodPrice').value = product?.price || '';
        document.getElementById('prodImage').value = product?.image || '';
        document.getElementById('prodCategory').value = product?.category_id || '';

        new bootstrap.Modal(document.getElementById('productModal')).show();
    }

    async editProduct(id) {
        try {
            const res = await fetch(`${API_URL}?action=get_product&id=${id}`);
            const product = await res.json();
            if (product) {
                this.openModal(product);
            }
        } catch (err) {
            console.error(err);
        }
    }

    async handleSubmit(e) {
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
                this.fetchProducts();
            } else {
                alert('Error: ' + (result.error || 'No se pudo guardar'));
            }
        } catch (err) {
            console.error(err);
            alert('Error de conexión');
        }
    }

    async deleteProduct(id) {
        if (!confirm('¿Seguro que quieres eliminar este producto?')) return;

        try {
            const res = await fetch(`${API_URL}?action=delete_product`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            });
            const result = await res.json();

            if (result.success) {
                this.fetchProducts();
            } else {
                alert('Error: ' + (result.error || 'No se pudo eliminar'));
            }
        } catch (err) {
            console.error(err);
        }
    }
}

// ============================================
// GESTOR DE PEDIDOS
// ============================================
class OrderManager {
    async fetchOrders() {
        try {
            const res = await fetch(`${API_URL}?action=get_orders`);
            const orders = await res.json();
            this.render(orders);
        } catch (err) {
            console.error(err);
            Utils.showError('admin-content', 'Error cargando pedidos');
        }
    }

    render(orders) {
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
        } else {
            // MAP
            html += orders.map(o => {
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

                return `
                    <tr>
                        <td>#${o.id}</td>
                        <td>${Utils.escapeHtml(o.user_name || 'Anónimo')}<br><small class="text-muted">${Utils.escapeHtml(o.user_email || '')}</small></td>
                        <td><strong>${parseFloat(o.total).toFixed(2)}€</strong></td>
                        <td><span class="badge ${statusBadge}">${statusText}</span></td>
                        <td><small>${new Date(o.created_at).toLocaleString('es-ES')}</small></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="app.orderManager.viewOrder(${o.id})">👁️</button>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                    Estado
                                </button>
                                <ul class="dropdown-menu dropdown-menu-dark">
                                    <li><a class="dropdown-item" href="#" onclick="app.orderManager.updateStatus(${o.id}, 'pending')">⏳ Pendiente</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="app.orderManager.updateStatus(${o.id}, 'completed')">✅ Completado</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="app.orderManager.updateStatus(${o.id}, 'cancelled')">❌ Cancelado</a></li>
                                </ul>
                            </div>
                            <button class="btn btn-sm btn-danger" onclick="app.orderManager.deleteOrder(${o.id})">🗑️</button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        html += '</tbody></table></div>';
        document.getElementById('admin-content').innerHTML = html;
    }

    async viewOrder(id) {
        try {
            const res = await fetch(`${API_URL}?action=get_order&id=${id}`);
            const order = await res.json();

            if (order) {
                document.getElementById('orderIdDisplay').innerText = order.id;

                // MAP para items
                const itemsRows = (order.items || []).map(item => `
                    <tr>
                        <td>${Utils.escapeHtml(item.product_name || 'Producto eliminado')}</td>
                        <td>${item.quantity}</td>
                        <td>${parseFloat(item.price).toFixed(2)}€</td>
                        <td>${(item.quantity * item.price).toFixed(2)}€</td>
                    </tr>
                `).join('');

                const itemsHtml = `<table class="table table-dark table-sm">
                    <thead><tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr></thead>
                    <tbody>${itemsRows}</tbody>
                </table>`;

                document.getElementById('orderDetails').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Cliente:</strong> ${Utils.escapeHtml(order.user_name || 'Anónimo')}</p>
                            <p><strong>Email:</strong> ${Utils.escapeHtml(order.user_email || 'N/A')}</p>
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

    async updateStatus(id, status) {
        try {
            const res = await fetch(`${API_URL}?action=update_order_status`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, status })
            });
            const result = await res.json();

            if (result.success) {
                this.fetchOrders();
            } else {
                alert('Error: ' + (result.error || 'No se pudo actualizar'));
            }
        } catch (err) {
            console.error(err);
        }
    }

    async deleteOrder(id) {
        if (!confirm('¿Seguro que quieres eliminar este pedido?')) return;

        try {
            const res = await fetch(`${API_URL}?action=delete_order`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            });
            const result = await res.json();

            if (result.success) {
                this.fetchOrders();
            } else {
                alert('Error: ' + (result.error || 'No se pudo eliminar'));
            }
        } catch (err) {
            console.error(err);
        }
    }
}

// ============================================
// APLICACIÓN PRINCIPAL
// ============================================
class AdminApp {
    constructor() {
        this.dashboardManager = new DashboardManager();
        this.userManager = new UserManager();
        this.productManager = new ProductManager();
        this.orderManager = new OrderManager();
        this.currencyManager = new CurrencyManager(); // API Externa

        this.init();
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.productManager.loadCategories(); // Precargar categorías

            // localStorage: Recupero la última sección visitada o voy al dashboard
            const lastSection = localStorage.getItem('adminSection') || 'dashboard';
            this.loadSection(lastSection);
        });
    }

    loadSection(section) {
        const title = document.getElementById('section-title');

        // Actualizo el botón activo
        document.querySelectorAll('.list-group-item').forEach(btn => btn.classList.remove('active'));
        document.getElementById(`btn-${section}`)?.classList.add('active');

        // localStorage: Guardo la sección actual
        localStorage.setItem('adminSection', section);

        // Muestro spinner
        Utils.showSpinner('admin-content');

        switch (section) {
            case 'dashboard':
                title.innerText = 'Dashboard';
                this.dashboardManager.load();
                break;
            case 'users':
                title.innerText = 'Gestión de Usuarios';
                this.userManager.fetchUsers();
                break;
            case 'orders':
                title.innerText = 'Gestión de Pedidos';
                this.orderManager.fetchOrders();
                break;
            case 'products':
                title.innerText = 'Gestión de Productos';
                this.productManager.fetchProducts();
                break;
            default:
                document.getElementById('admin-content').innerHTML = '<p class="text-muted">Sección no encontrada</p>';
        }
    }

    updateCurrency(currency) {
        this.currencyManager.currentCurrency = currency;
        // Recargo solo el dashboard sin llamar a la API de nuevo
        this.dashboardManager.load();
    }
}

// Instancia global para ser accesible desde el HTML (onclick)
const app = new AdminApp();
