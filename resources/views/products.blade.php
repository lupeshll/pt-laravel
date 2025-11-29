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
                <tbody id="product-table-body">
                    <!-- Las filas de productos se agregarán aquí dinámicamente -->
                </tbody>
            </table>
        </div>
    </div>


</body>

</html>
