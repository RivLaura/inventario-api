# Sistema de Inventario Multicanal con Laravel

Sistema de gestión de inventario desarrollado con **Laravel y PHP**, que integra diferentes tecnologías de comunicación mediante APIs REST, GraphQL, WebSockets y gRPC.

El proyecto permite administrar productos, consultar información, actualizar existencias y transmitir cambios en tiempo real, utilizando una arquitectura basada en servicios compartidos.

## Tecnologías utilizadas

- **PHP y Laravel:** desarrollo del backend.
- **REST API:** operaciones CRUD sobre productos.
- **GraphQL (Lighthouse):** consultas y mutaciones personalizadas.
- **WebSockets (Laravel Reverb):** comunicación en tiempo real.
- **gRPC:** comunicación mediante Protocol Buffers.
- **Node.js:** implementación del servidor gRPC.
- **Python:** cliente gRPC de prueba.
- **SQLite / MySQL:** persistencia de datos.
- **JavaScript y Blade:** interfaz de inventario.
- **Composer y npm:** administración de dependencias.

## Funcionalidades principales

### API REST
Permite gestionar productos mediante operaciones HTTP:

| Método | Endpoint | Descripción |
|---|---|---|
| GET | `/api/productos` | Listar productos |
| GET | `/api/productos/{id}` | Consultar un producto |
| POST | `/api/productos` | Crear un producto |
| PUT/PATCH | `/api/productos/{id}` | Actualizar un producto |
| DELETE | `/api/productos/{id}` | Eliminar un producto |

### GraphQL
Permite realizar consultas y mutaciones mediante el endpoint `/graphql`.

Operaciones disponibles:
- `productos`: consultar todos los productos.
- `producto`: consultar un producto por ID.
- `crearProducto`: registrar un nuevo producto.
- `actualizarStock`: modificar las existencias.

### WebSockets con Laravel Reverb
Implementa comunicación en tiempo real mediante eventos.

Cuando se crea o actualiza un producto, se emite el evento `ProductoActualizado`, que puede ser recibido por los clientes conectados al canal `inventario`.

La interfaz de demostración está disponible en `/inventario`.

### gRPC
Implementa un servicio gRPC con Node.js para consultar productos registrados en Laravel.

El contrato se define en `grpc/producto.proto` y proporciona la operación `ObtenerProducto`.

Incluye clientes de prueba desarrollados en Node.js y Python.

## Arquitectura del proyecto

La lógica de negocio se centraliza en `ProductoService`, permitiendo que REST y GraphQL reutilicen las mismas operaciones.

```text
Clientes REST ──────┐
                    ├── ProductoService
Clientes GraphQL ───┘         │
                              ├── Eloquent / Base de datos
                              │
                              └── ProductoActualizado
                                         │
                                   Laravel Reverb
                                         │
                                  Clientes WebSocket

Cliente gRPC
     │
Servidor Node.js
     │
Endpoint interno protegido
     │
Laravel / Eloquent
```

## Instalación y configuración

### Requisitos

- PHP y Composer compatibles con la versión de Laravel instalada.
- Node.js y npm.
- SQLite o MySQL.
- Python 3 para ejecutar el cliente gRPC opcional.
- Git.

### 1. Clonar el repositorio

```bash
git clone https://github.com/RivLaura/inventario-api.git
cd inventario-api
```

### 2. Instalar dependencias

```bash
composer install
npm install
```

### 3. Configurar Laravel

Crear el archivo de entorno a partir del ejemplo:

```powershell
Copy-Item .env.example .env
```

Generar la clave de la aplicación:

```bash
php artisan key:generate
```

Configurar la conexión a la base de datos en `.env`.

Para SQLite:

```env
DB_CONNECTION=sqlite
```

Crear el archivo `database/database.sqlite` si todavía no existe.

### 4. Ejecutar migraciones

```bash
php artisan migrate
```

### 5. Configurar la comunicación gRPC

Agregar una clave privada local en `.env`:

```env
GRPC_INTERNAL_KEY=una-clave-local-privada
```

Configurar la misma clave como variable de entorno del proceso Node.js que ejecuta el servidor gRPC.

No publicar claves reales en el repositorio.

### 6. Ejecutar los servicios

Abrir terminales independientes en la raíz del proyecto:

**Terminal 1 — Laravel**

```bash
php artisan serve
```

**Terminal 2 — Reverb**

```bash
php artisan reverb:start
```

**Terminal 3 — Trabajador de colas**

```bash
php artisan queue:work
```

**Terminal 4 — Vite**

```bash
npm run dev
```

**Terminal 5 — gRPC (PowerShell)**

```powershell
cd grpc
npm install
$env:GRPC_INTERNAL_KEY='una-clave-local-privada'
node server.cjs
```

## Ejemplos de uso

### Crear un producto con REST

```http
POST /api/productos
Content-Type: application/json
Accept: application/json
```

```json
{
  "nombre": "Teclado",
  "precio": 25000,
  "stock": 12
}
```

### Consultar productos con GraphQL

```graphql
query {
  productos {
    id
    nombre
    precio
    stock
  }
}
```

### Actualizar stock con GraphQL

```graphql
mutation {
  actualizarStock(id: 1, stock: 15) {
    id
    nombre
    stock
  }
}
```

### Probar el cliente gRPC

Con Laravel y el servidor gRPC en ejecución, abrir otra terminal dentro de `grpc/`:

```bash
node client.cjs
```

Para utilizar el cliente Python, instalar `grpcio` y `grpcio-tools`, generar los archivos necesarios a partir de `producto.proto` y ejecutar:

```bash
python client.py
```

## Pruebas del inventario en tiempo real

1. Iniciar Laravel, Reverb, el trabajador de colas y Vite.
2. Abrir `http://127.0.0.1:8000/inventario` en dos pestañas.
3. Crear un producto mediante REST o GraphQL si todavía no existe ninguno.
4. Presionar el botón para incrementar el stock.
5. Verificar que ambas pestañas reflejen la actualización.

## Consideraciones

Este proyecto fue desarrollado con fines académicos y de aprendizaje.

La configuración está orientada a un entorno local. Para utilizarlo en producción sería necesario reforzar la autenticación, la protección de canales, el manejo de concurrencia del stock y la seguridad de las comunicaciones.

## Autoría

**María Laura Riveros**

Estudiante de Tecnicatura Universitaria en Programación.

GitHub: [RivLaura](https://github.com/RivLaura)

## Propósito académico

Proyecto práctico orientado a comprender e implementar diferentes mecanismos de comunicación entre aplicaciones, reutilización de lógica de negocio, persistencia de información y sincronización en tiempo real.