# Sistema de Gestión de Órdenes

Sistema web desarrollado con Laravel y Vue.js para la gestión de órdenes de compra, clientes y productos, con generación de reportes en PDF y Excel.

## 🚀 Tecnologías Utilizadas

### Backend
- **Laravel 12** - Framework PHP
- **PHP 8.2+** - Lenguaje de programación
- **MySQL/SQLite** - Base de datos
- **Laravel Sanctum** - Autenticación API
- **mPDF** - Generación de reportes PDF
- **Maatwebsite Excel** - Generación de reportes Excel

### Frontend
- **Vue.js 3** - Framework JavaScript
- **Vue Router** - Enrutamiento SPA
- **Vite** - Build tool y servidor de desarrollo
- **Tailwind CSS 4** - Framework CSS
- **Axios** - Cliente HTTP
- **SweetAlert2** - Alertas y notificaciones

## 📋 Funcionalidades

- ✅ Gestión completa de órdenes (CRUD)
- ✅ Gestión de clientes
- ✅ Gestión de productos con categorías
- ✅ Control de inventario automático
- ✅ Generación de reportes en PDF
- ✅ Generación de reportes en Excel
- ✅ Autenticación de usuarios con Sanctum
- ✅ Interfaz SPA moderna con Vue.js
- ✅ Validaciones en tiempo real
- ✅ Alertas interactivas

## 🛠️ Instalación

### Requisitos Previos
- PHP >= 8.2
- Composer
- Node.js >= 18
- NPM o Yarn
- MySQL

### Pasos de Instalación

1. **Clonar el repositorio**
```bash
git clone https://gitlab.com/SINOEP/test_angel_aviles.git
cd test_angel_aviles
```

2. **Instalar dependencias de PHP**
```bash
composer install
```

3. **Instalar dependencias de Node.js**
```bash
npm install
```

4. **Configurar el archivo de entorno**
```bash
cp .env.example .env
```

5. **Generar la clave de aplicación**
```bash
php artisan key:generate
```

6. **Configurar la base de datos**

Edita el archivo `.env` y configura tu base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_base_datos
DB_USERNAME=usuario
DB_PASSWORD=contraseña
```

O para SQLite:
```env
DB_CONNECTION=sqlite
```

7. **Ejecutar las migraciones y seeders**
```bash
php artisan migrate --seed
```

8. **Compilar los assets del frontend**
```bash
npm run build
```

## 🚀 Ejecución

### Modo Desarrollo

Opción 1 - Dos terminales separadas:

**Terminal 1 - Servidor Laravel:**
```bash
php artisan serve
```

**Terminal 2 - Servidor Vite:**
```bash
npm run dev
```

Opción 2 - Un solo comando (recomendado):
```bash
composer dev
```

La aplicación estará disponible en: `http://localhost:8000`

### Modo Producción

1. **Compilar assets para producción**
```bash
npm run build
```

2. **Configurar el servidor web** (Apache/Nginx) apuntando al directorio `public/`

## 📊 Estructura de la Base de Datos

### Tablas Principales

- **users** - Usuarios del sistema
- **customers** - Clientes
- **products** - Productos (con stock y categoría)
- **orders** - Órdenes de compra
- **order_items** - Ítems de cada orden

## 🔐 Autenticación

El sistema utiliza Laravel Sanctum para autenticación API. 

**Usuario por defecto:**
- Email: `test@example.com`
- Password: `password`

## 📁 Endpoints API Principales

```
POST   /api/login                    - Iniciar sesión
POST   /api/logout                   - Cerrar sesión
GET    /api/me                       - Obtener usuario autenticado

GET    /api/customers                - Listar clientes
GET    /api/products                 - Listar productos

GET    /api/orders                   - Listar órdenes
POST   /api/orders                   - Crear orden
GET    /api/orders/{id}              - Ver orden
PUT    /api/orders/{id}              - Actualizar orden
DELETE /api/orders/{id}              - Eliminar orden

GET    /api/orders/report/pdf        - Generar reporte PDF
GET    /api/orders/report/excel      - Generar reporte Excel
```

## 🧪 Testing

```bash
php artisan test
```

O usando composer:
```bash
composer test
```

## 📝 Notas Adicionales

- El sistema valida automáticamente el stock disponible antes de crear/actualizar órdenes
- Los reportes incluyen totales y subtotales calculados automáticamente
- La interfaz es completamente responsive
- Se incluyen validaciones tanto en frontend como backend

## 👨‍💻 Autor

Angel Aviles
