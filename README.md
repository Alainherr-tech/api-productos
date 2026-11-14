# API de Productos – Laravel

API REST CRUD de productos hecha con Laravel, más un panel web (Bootstrap) que la consume.
Usa **SQLite**, que ya viene integrada en Laravel: no hace falta instalar ni configurar MySQL.

## Requisitos
- PHP 8.2 o superior
- Composer

## Instalación
```bash
git clone <URL-DEL-REPOSITORIO>
cd api-productos
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite      # en Windows: type nul > database\database.sqlite
php artisan migrate
php artisan serve
```

- Panel web: http://127.0.0.1:8000/panel-productos
- API: http://127.0.0.1:8000/api/productos

## Endpoints

| Método | Ruta                  | Descripción               | Código |
|--------|-----------------------|---------------------------|--------|
| GET    | `/api/productos`      | Listar productos          | 200    |
| GET    | `/api/productos/{id}` | Ver un producto           | 200 / 404 |
| POST   | `/api/productos`      | Crear producto            | 201 / 422 |
| PUT    | `/api/productos/{id}` | Actualizar (parcial ok)   | 200 / 404 / 422 |
| DELETE | `/api/productos/{id}` | Eliminar producto         | 200 / 404 |

### Ejemplo: crear (POST)
```json
{
  "nombre": "Laptop Gamer",
  "descripcion": "Laptop con 16GB RAM y 1TB SSD",
  "precio": 1500.50,
  "stock": 10
}
```
Respuesta `201 Created`:
```json
{
  "message": "Producto creado exitosamente",
  "data": { "nombre": "Laptop Gamer", "descripcion": "Laptop con 16GB RAM y 1TB SSD", "precio": 1500.5, "stock": 10, "updated_at": "...", "created_at": "...", "id": 2 }
}
```

### Ejemplo: actualizar (PUT `/api/productos/2`)
```json
{ "precio": 1350.00, "stock": 8 }
```
→ `200 OK` con `"message": "Producto actualizado exitosamente"`.

### Ejemplo: eliminar (DELETE `/api/productos/2`)
→ `200 OK` con `{ "message": "Producto eliminado exitosamente" }`.

## Validaciones
- `nombre`: obligatorio, texto, máx. 255
- `descripcion`: opcional, texto
- `precio`: obligatorio, numérico, ≥ 0
- `stock`: obligatorio, entero, ≥ 0

Si falla, responde `422` con `message` y `errors`.

## Estructura
- `database/migrations/2026_11_13_182706_create_productos_table.php`
- `app/Models/Producto.php`
- `app/Http/Controllers/ProductoController.php`
- `routes/api.php` y `routes/web.php`
- `resources/views/panel-productos.blade.php`

## Pruebas
Se probó con Thunder Client (VS Code): POST, PUT y DELETE sobre `http://127.0.0.1:8000/api/productos`.
