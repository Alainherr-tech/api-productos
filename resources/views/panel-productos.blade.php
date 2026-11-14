<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h1 class="text-primary mb-4">Gestión de Productos</h1>

    <div id="alerta"></div>

    {{-- Formulario --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white" id="form-titulo">Agregar Nuevo Producto</div>
        <div class="card-body">
            <form id="form-producto" class="row g-3 align-items-end">
                <input type="hidden" id="producto-id">
                <div class="col-md-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" id="nombre" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <input type="text" id="descripcion" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="precio" class="form-label">Precio</label>
                    <input type="number" step="0.01" min="0" id="precio" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label for="stock" class="form-label">Stock</label>
                    <input type="number" min="0" id="stock" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success w-100" id="btn-guardar">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Lista --}}
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">Lista de Productos</div>
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody id="tabla-productos"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const API = '/api/productos';
    const headers = {'Content-Type': 'application/json', 'Accept': 'application/json'};
    const form = document.getElementById('form-producto');
    const campos = ['nombre', 'descripcion', 'precio', 'stock'];
    let productos = [];

    const escapar = (t) => String(t ?? '').replace(/[&<>"']/g, c => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));

    function alerta(mensaje, tipo = 'success') {
        document.getElementById('alerta').innerHTML =
            `<div class="alert alert-${tipo} alert-dismissible fade show">${escapar(mensaje)}
             <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
    }

    async function cargar() {
        const res = await fetch(API, {headers});
        productos = await res.json();
        document.getElementById('tabla-productos').innerHTML = productos.map(p => `
            <tr>
                <td>${p.id}</td>
                <td>${escapar(p.nombre)}</td>
                <td>${escapar(p.descripcion)}</td>
                <td>$${p.precio}</td>
                <td>${p.stock}</td>
                <td>
                    <button class="btn btn-warning btn-sm" onclick="editar(${p.id})">Editar</button>
                    <button class="btn btn-danger btn-sm" onclick="eliminar(${p.id})">Eliminar</button>
                </td>
            </tr>`).join('');
    }

    function editar(id) {
        const p = productos.find(x => x.id === id);
        document.getElementById('producto-id').value = p.id;
        campos.forEach(c => document.getElementById(c).value = p[c] ?? '');
        document.getElementById('form-titulo').textContent = 'Editar Producto #' + p.id;
        window.scrollTo({top: 0, behavior: 'smooth'});
    }

    function reiniciarForm() {
        form.reset();
        document.getElementById('producto-id').value = '';
        document.getElementById('form-titulo').textContent = 'Agregar Nuevo Producto';
    }

    async function eliminar(id) {
        if (!confirm('¿Eliminar este producto?')) return;
        const res = await fetch(`${API}/${id}`, {method: 'DELETE', headers});
        const data = await res.json();
        alerta(data.message, res.ok ? 'success' : 'danger');
        cargar();
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('producto-id').value;
        const body = {
            nombre: document.getElementById('nombre').value,
            descripcion: document.getElementById('descripcion').value,
            precio: document.getElementById('precio').value,
            stock: document.getElementById('stock').value,
        };
        const res = await fetch(id ? `${API}/${id}` : API, {
            method: id ? 'PUT' : 'POST', headers, body: JSON.stringify(body),
        });
        const data = await res.json();
        if (res.ok) {
            alerta(data.message);
            reiniciarForm();
            cargar();
        } else {
            const detalle = data.errors ? ' ' + Object.values(data.errors).flat().join(' ') : '';
            alerta((data.message || 'Error') + detalle, 'danger');
        }
    });

    cargar();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
