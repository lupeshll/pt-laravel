<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>CRUD Productos</title>

    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
</head>

<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <header class="md-8">
            <h1 class="text-3x1 font-bold text-gray-800 text-center">Gestión de productos</h1>
        </header>
        <div id="notification" class="hidden mb-4 p-4 rounded-lg text-center font-medium">
            <!-- Las notificaciones se mostrarán aquí -->
        </div>

        <!-- Formulario de creación/edición de productos -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 id="form-title" class="text-xl font-semibold text-gray-700 mb-4">Crear nuevo producto</h2>
            <form id='product-form' class="space-y-4">
                <input type="hidden" id="product-id" value="">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre del
                            producto *</label>
                        <input type="text" id="name" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:right-2 focus:right-blue-500 focus:border-transparent"
                            placeholder="Ingrese nombre del producto">
                    </div>
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Precio *</label>
                        <input type="number" id="price" step="0.01" min="0" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:right-2 focus:right-blue-500 focus:border-transparent"
                            placeholder="0.00">
                    </div>

                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descripción del
                        producto</label>
                    <textarea id="description" name="description" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:right-2 focus:right-blue-500 focus:border-transparent"
                        placeholder="Ingrese una descripción (opcional)"></textarea>

                </div>
                <div class="flex gap-2">
                    <button type="submit" id="btn-submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200">Guardar</button>
                    <button type="button" id="btn-cancel" onclick="cancelEdit()"
                        class="hidden bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-lg transition duration-200 ">Cancelar</button>
                </div>

            </form>
        </div>

        <!-- Tabla de productos -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-700">Lista de productos</h2>
            </div>
            <div class="overflow-x-auto ">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-500 uppercase tracking-wider">ID
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-500 uppercase tracking-wider">Nombre
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-500 uppercase tracking-wider">Precio
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-500 uppercase tracking-wider">
                                Descripción</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-500 uppercase tracking-wider">Fecha
                                creación
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody id="products-table">
                        <!-- Las filas de productos se agregarán aquí dinámicamente -->
                    </tbody>
                </table>
            </div>
            <div id="no-products" class="hidden g-8 text-center text-gray-500">
                No hay productos registrados. Crea uno nuevo usando el formulario de arriba.
            </div>
        </div>
    </div>


    <script>
        const API_URL = '/api/products';
        document.addEventListener('DOMContentLoaded', function() {
            //Cargar productos al iniciar
            loadProducts();

        });

        //Manejos de envió de formulario
        document.getElementById('product-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const productId = document.getElementById('product-id').value;
            if (productId) {
                updateProduct(productId);
            } else {
                createProduct();
            }
        });

        //
        async function loadProducts() {
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
            }
        }

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
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${product.id}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${product.name}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$${parseFloat(product.price).toFixed(2)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${product.description || ''}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatDate(product.created_at)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                        <button onclick="editProduct(${product.id})" class="text-indigo-600 hover:text-indigo-900 mr-4">Editar</button>
                        <button onclick="deleteProduct(${product.id})" class="text-red-600 hover:text-red-900">Eliminar</button>
                    </td>
                </tr>
            `).join('');
        }

        //crear producto
        async function createProduct() {
            const data = getFormData();

            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(
                        data
                    )
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
            }
        }

        //EDITAR-cargar datos en el formulario
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

                    document.getElementById('product-form').scrollIntoView({
                        behavior: 'smooth'
                    });
                } else {
                    showNotification(result.message, 'error');
                }

            } catch (error) {
                showNotification('Error al cargar producto: ' + error.message, 'error');
            }
        }

        //Actualizar producto
        async function updateProduct(id) {
            const data = getFormData();

            try {
                const response = await fetch(`${API_URL}/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(
                        data
                    )
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
            }
        }

        //Eliminar producto
        async function deleteProduct(id) {
            if (!confirm('¿Estás seguro de que deseas eliminar este producto?')) {
                return;
            }

            try {
                const response = await fetch(`${API_URL}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json'
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

        //Cancelar edición
        function cancelEdit() {
            resetForm();
        }

        //Obtener datos del formulario
        function getFormData() {
            return {
                name: document.getElementById('name').value.trim(),
                price: parseFloat(document.getElementById('price').value),
                description: document.getElementById('description').value.trim()
            };
        }

        //Resetear formulario
        function resetForm() {
            document.getElementById('product-form').reset();
            document.getElementById('product-id').value = '';

            document.getElementById('form-title').textContent = 'Crear nuevo producto';
            document.getElementById('btn-submit').textContent = 'Guardar';
            document.getElementById('btn-cancel').classList.add('hidden');
        }

        // Mostrar notificaciones
        function showNotification(message, type) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = 'mb-4 p-4 rounded-lg text-center font-medium';
            if (type === 'success') {
                notification.classList.add('bg-green-100', 'text-green-800');
            } else if (type === 'error') {
                notification.classList.add('bg-red-100', 'text-red-800');
            }
            notification.classList.remove('hidden');
            setTimeout(() => {
                notification.classList.add('hidden');
            }, 4000);
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

        //
        function scapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>

</body>


</html>
