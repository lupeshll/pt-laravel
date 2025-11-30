<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CRUD Productos</title>

    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
</head>

<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <header class="mb-8 bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-3xl font-bold text-gray-800 text-center">Gestión de Productos</h1>
            <p class="text-gray-600 text-center mt-2">Administra tu inventario de productos</p>
        </header>

        <div id="notification" class="hidden mb-4 p-4 rounded-lg text-center font-medium">
            <!-- Las notificaciones se mostrarán aquí -->
        </div>

        <!-- Formulario de creación/edición de productos -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border border-gray-100">
            <h2 id="form-title" class="text-xl font-semibold text-gray-700 mb-4">Crear nuevo producto</h2>
            <form id='product-form' class="space-y-4">
                <input type="hidden" id="product-id" value="">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre del
                            producto *</label>
                        <input type="text" id="name" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Ingrese nombre del producto">
                    </div>
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Precio *</label>
                        <input type="number" id="price" step="0.01" min="0" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="0.00">
                    </div>
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descripción del
                        producto</label>
                    <textarea id="description" name="description" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Ingrese una descripción (opcional)"></textarea>
                </div>
                <div class="flex gap-2">
                    <button type="submit" id="btn-submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        Guardar
                    </button>
                    <button type="button" id="btn-cancel" onclick="cancelEdit()"
                        class="hidden bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-lg transition duration-200">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabla de productos -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-700">Lista de productos</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[600px]">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nombre
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Precio
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Descripción
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha creación
                            </th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody id="products-table">
                        <!-- Las filas de productos se agregarán aquí dinámicamente -->
                    </tbody>
                </table>
            </div>
            <div id="no-products" class="hidden p-8 text-center text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No hay productos</h3>
                <p class="mt-1 text-sm text-gray-500">Comienza creando un nuevo producto usando el formulario de arriba.
                </p>
            </div>
        </div>
    </div>

    <script>
        const API_URL = '/api/products';

        document.addEventListener('DOMContentLoaded', function() {
            loadProducts();
            document.getElementById('product-form').addEventListener('submit', handleFormSubmit);
        });

        function handleFormSubmit(e) {
            e.preventDefault();
            const productId = document.getElementById('product-id').value;
            if (productId) {
                updateProduct(productId);
            } else {
                createProduct();
            }
        }

        // Estado de carga
        function setLoading(loading) {
            const tableBody = document.getElementById('products-table');
            const submitBtn = document.getElementById('btn-submit');

            if (loading) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <div class="flex justify-center items-center">
                                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mr-2"></div>
                                <span>Cargando productos...</span>
                            </div>
                        </td>
                    </tr>
                `;
                submitBtn.disabled = true;
            } else {
                submitBtn.disabled = false;
            }
        }

        // Cargar productos
        async function loadProducts() {
            setLoading(true);
            try {
                const response = await fetch(API_URL);
                const result = await response.json();

                if (result.status === 200) {
                    renderProducts(result.data);
                } else {
                    showNotification(result.message, 'error');
                }
            } catch (error) {
                showNotification('Error al cargar productos: ' + error.message, 'error');
            } finally {
                setLoading(false);
            }
        }

        // Renderizar productos
        function renderProducts(products) {
            const tbody = document.getElementById('products-table');
            const noProducts = document.getElementById('no-products');

            if (!products || products.length === 0) {
                tbody.innerHTML = '';
                noProducts.classList.remove('hidden');
                return;
            }

            noProducts.classList.add('hidden');

            tbody.innerHTML = products.map(product => `
                <tr class="hover:bg-gray-50 border-b border-gray-200">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${product.id}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        ${escapeHtml(product.name)}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        $${parseFloat(product.price).toFixed(2)}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                        ${escapeHtml(product.description || '-')}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${formatDate(product.created_at)}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                        <button onclick="editProduct(${product.id})" 
                                class="text-indigo-600 hover:text-indigo-900 mr-4 font-medium transition duration-200">
                            Editar
                        </button>
                        <button onclick="deleteProduct(${product.id})" 
                                class="text-red-600 hover:text-red-900 font-medium transition duration-200">
                            Eliminar
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Obtener datos del formulario con validación
        function getFormData() {
            const name = document.getElementById('name').value.trim();
            const price = document.getElementById('price').value;

            if (!name) {
                showNotification('El nombre del producto es obligatorio', 'error');
                document.getElementById('name').focus();
                return null;
            }

            if (!price || parseFloat(price) <= 0) {
                showNotification('El precio debe ser mayor a 0', 'error');
                document.getElementById('price').focus();
                return null;
            }

            return {
                name: name,
                price: parseFloat(price),
                description: document.getElementById('description').value.trim()
            };
        }

        // Crear producto
        async function createProduct() {
            const data = getFormData();
            if (!data) return;

            const submitBtn = document.getElementById('btn-submit');
            const originalText = submitBtn.textContent;

            submitBtn.textContent = 'Guardando...';
            submitBtn.disabled = true;

            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.status === 200) {
                    showNotification('Producto creado exitosamente', 'success');
                    resetForm();
                    loadProducts();
                } else {
                    showNotification(result.message, 'error');
                }
            } catch (error) {
                showNotification('Error al crear producto: ' + error.message, 'error');
            } finally {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        }

        // Editar - cargar datos en el formulario
        async function editProduct(id) {
            try {
                const response = await fetch(`${API_URL}/${id}`);
                const result = await response.json();

                if (result.status === 200) {
                    const product = result.data;
                    document.getElementById('product-id').value = product.id;
                    document.getElementById('name').value = product.name;
                    document.getElementById('price').value = product.price;
                    document.getElementById('description').value = product.description || '';

                    document.getElementById('form-title').innerText = 'Editar producto';
                    document.getElementById('btn-submit').innerText = 'Actualizar';
                    document.getElementById('btn-cancel').classList.remove('hidden');

                    // Scroll suave al formulario
                    document.getElementById('product-form').scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                } else {
                    showNotification(result.message, 'error');
                }
            } catch (error) {
                showNotification('Error al cargar producto: ' + error.message, 'error');
            }
        }

        // Actualizar producto
        async function updateProduct(id) {
            const data = getFormData();
            if (!data) return;

            const submitBtn = document.getElementById('btn-submit');
            const originalText = submitBtn.textContent;

            submitBtn.textContent = 'Actualizando...';
            submitBtn.disabled = true;

            try {
                const response = await fetch(`${API_URL}/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.status === 200) {
                    showNotification('Producto actualizado exitosamente', 'success');
                    resetForm();
                    loadProducts();
                } else {
                    showNotification(result.message, 'error');
                }
            } catch (error) {
                showNotification('Error al actualizar producto: ' + error.message, 'error');
            } finally {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        }

        // Eliminar producto
        async function deleteProduct(id) {
            if (!confirm('¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer.')) {
                return;
            }

            try {
                const response = await fetch(`${API_URL}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (result.status === 200) {
                    showNotification('Producto eliminado exitosamente', 'success');
                    loadProducts();
                } else {
                    showNotification(result.message, 'error');
                }
            } catch (error) {
                showNotification('Error al eliminar producto: ' + error.message, 'error');
            }
        }

        // Cancelar edición
        function cancelEdit() {
            resetForm();
        }

        // Resetear formulario
        function resetForm() {
            document.getElementById('product-form').reset();
            document.getElementById('product-id').value = '';
            document.getElementById('form-title').textContent = 'Crear nuevo producto';
            document.getElementById('btn-submit').textContent = 'Guardar';
            document.getElementById('btn-cancel').classList.add('hidden');

            // Remover clases de error de los inputs
            const inputs = document.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                input.classList.remove('border-red-500');
            });
        }

        // Mostrar notificaciones mejoradas
        function showNotification(message, type) {
            const notification = document.getElementById('notification');

            const icons = {
                success: '✅',
                error: '❌'
            };

            notification.innerHTML = `
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="mr-2">${icons[type] || 'ℹ️'}</span>
                        <span>${message}</span>
                    </div>
                    <button onclick="this.parentElement.parentElement.classList.add('hidden')" 
                            class="ml-4 text-gray-500 hover:text-gray-700 text-lg font-bold">
                        ×
                    </button>
                </div>
            `;

            notification.className = 'mb-4 p-4 rounded-lg font-medium flex border';

            if (type === 'success') {
                notification.classList.add('bg-green-50', 'text-green-800', 'border-green-200');
            } else if (type === 'error') {
                notification.classList.add('bg-red-50', 'text-red-800', 'border-red-200');
            }

            notification.classList.remove('hidden');

            // Auto-ocultar después de 5 segundos solo para éxito
            if (type === 'success') {
                setTimeout(() => {
                    notification.classList.add('hidden');
                }, 5000);
            }
        }

        // Formatear fecha
        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // Escape HTML para seguridad
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>

</html>
