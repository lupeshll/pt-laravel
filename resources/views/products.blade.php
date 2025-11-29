<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>CRUD Productos</title>

    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <header class="md-8">
        <h1>Gestión de productos</h1>
    </header>
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 id="form-title" class="text-xl font-semibold text-gray-700 mb-4">Crear nuevo producto</h2>
        <form id='product-form' class="space-y-4">
            <input type="hidden" id="product-id" value="">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre del
                        producto *</label>
                    <input type="text" id="name" required>
                </div>
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Precio *</label>
                    <input type="number" id="price" step="0.01" min="0" required>
                </div>

            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descripción del
                    producto</label>
                <textarea id="description" name="description" rows="3"></textarea>

            </div>
            <div class="flex gap-2">
                <button type="submit" id="btn-submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200">Guardar</button>
                <button type="button" id="btn-cancel" onclick="cancelEdit()"
                    class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-lg transition duration-200 hidden">Cancelar</button>
            </div>

        </form>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-500 uppercase tracking-wider">Nombre
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-500 uppercase tracking-wider">Precio
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-500 uppercase tracking-wider">
                            Descripción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-500 uppercase tracking-wider">Fecha
                            creación
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-500 uppercase tracking-wider">Acciones
                        </th>
                    </tr>
                </thead>
                <tbody id="products-table">
                    <!-- Las filas de productos se agregarán aquí dinámicamente -->
                </tbody>
            </table>
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
    </script>

</body>

</html>
