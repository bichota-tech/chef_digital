# Chef Digital - Catálogo de Recetas Gourmet 🍳💻

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-777bb4.svg?style=flat-square&logo=php)](https://www.php.net/)
[![Database](https://img.shields.io/badge/MySQL-8.0%2B-4479a1.svg?style=flat-square&logo=mysql)](https://www.mysql.com/)
[![Aesthetic](https://img.shields.io/badge/UI--UX-Premium--Glassmorphism-orange.svg?style=flat-square)](#-características-clave)

**Chef Digital** es una aplicación web responsiva diseñada como catálogo de recetas gourmet de alta cocina. Cumple con los requisitos académicos de programación en entorno servidor y cuenta con estándares profesionales de arquitectura, seguridad y UI/UX, ideal para presentarse en un portafolio de desarrollo de software de alto nivel.

---

## 🚀 Características Clave

- **Arquitectura Modular (DRY)**: Código limpio y desacoplado estructurado en `/config`, `/includes`, `/assets` y el directorio raíz.
- **Base de Datos Normalizada (3FN)**: Relaciones sólidas de uno a muchos y muchos a muchos, resolviendo eficientemente la asociación entre recetas, chefs e ingredientes.
- **Seguridad Garantizada**: Implementación de **Sentencias Preparadas (Prepared Statements)** mediante PDO para anular cualquier vector de ataque por inyección SQL.
- **Diseño de Interfaz Premium**: Interfaz en modo oscuro con estética híbrida *Glassmorphism*, transiciones animadas fluidas, y enfoque Mobile-First.
- **Accesibilidad Web (A11y)**: Jerarquía estructurada de títulos (H1-H6), textos descriptivos alternativos en portadas de recetas, y compatibilidad con navegadores/lectores de pantalla.

---

## 🛠️ Requisitos Técnicos

- Servidor local compatible con PHP 8.0 o superior (XAMPP, WampServer, Laragon, o Docker).
- Servidor de base de datos MySQL / MariaDB.
- Extensión `pdo_mysql` habilitada en PHP.

---

## 📥 Instrucciones de Instalación

1. **Clonar el proyecto** en tu directorio web (ej. `htdocs` en XAMPP o `www` en Wamp):

   ```bash
   git clone https://github.com/tu-usuario/chef_digital.git
   ```

2. **Configurar la Base de Datos**:
   - Accede a tu administrador de base de datos (phpMyAdmin o CLI).
   - Importa el archivo [`chef_digital.sql`](chef_digital.sql) ubicado en la raíz del proyecto. Este creará la base de datos `chef_digital` e insertará registros de prueba automatizados.

3. **Verificar Conexión en el Backend**:
   - Edita el archivo de configuración [`config/db.php`](config/db.php) si tu servidor MySQL utiliza un puerto, usuario o contraseña diferente.

     ```php
     define('DB_USER', 'tu_usuario');
     define('DB_PASS', 'tu_contrasena');
     ```

4. **Correr Localmente**:
   - Abre tu navegador web y navega a `http://localhost/chef_digital/index.php`.

---

## 🌐 Despliegue en la Nube (Render & PlanetScale)

Este proyecto está preparado para ejecutarse en la nube utilizando **Render** para el servidor web PHP y **PlanetScale** para la base de datos MySQL serverless.

### 1. Base de Datos en PlanetScale

- Crea una base de datos en [PlanetScale](https://planetscale.com/).
- Importa las tablas y los registros de prueba ejecutando el script [`chef_digital.sql`](chef_digital.sql) desde la consola web o mediante tu cliente de base de datos preferido.
- Obtén las credenciales de conexión en formato de variables de entorno o cadena de conexión (`DATABASE_URL`).

### 2. Alojamiento PHP en Render

- Crea un nuevo **Web Service** en [Render](https://render.com/) enlazado a tu repositorio Git de GitHub o GitLab.
- Render detectará automáticamente el archivo `composer.json` en la raíz del proyecto y configurará el entorno como un servicio **PHP**.
- En la pestaña **Environment** de Render, añade las siguientes variables de entorno:

| Variable | Valor Recomendado / Ejemplo | Descripción |
| :--- | :--- | :--- |
| `DATABASE_URL` | `mysql://user:password@host/dbname` | Cadena de conexión rápida provista por PlanetScale. |
| `DB_SSL_CA` | `/etc/ssl/certs/ca-certificates.crt` | Ruta interna de certificados de Render para conexiones SSL. |

> [!IMPORTANT]
> PlanetScale requiere **conexiones TLS/SSL obligatorias**. La base de datos no permitirá conexiones inseguras sin cifrar. El archivo [`config/db.php`](config/db.php) está preparado para leer `DB_SSL_CA` y adjuntar automáticamente la clave de seguridad `PDO::MYSQL_ATTR_SSL_CA` y la verificación de servidor correspondientes en la inicialización de PDO.

---

## 📊 Diseño de la Base de Datos (E-R Diagram)

El esquema de base de datos está normalizado para optimizar consultas relacionales complejas y mantener la integridad de los datos.

```mermaid
erDiagram
    CHEFS ||--o{ RECETAS : "crea"
    RECETAS ||--|{ RECETA_INGREDIENTE : "contiene"
    INGREDIENTES ||--|{ RECETA_INGREDIENTE : "usado_en"

    CHEFS {
        int id PK
        string nombre
        text biografia
        date f_nac
    }

    RECETAS {
        int id PK
        string titulo
        string portada
        int anio
        int chef_id FK
    }

    INGREDIENTES {
        int id PK
        string nombre
        int calorias
    }

    RECETA_INGREDIENTE {
        int receta_id PK, FK
        int ingrediente_id PK, FK
        string cantidad
    }
```

---

## 🔒 Seguridad: Mitigación de SQL Injection

En este proyecto se ha implementado seguridad por diseño mediante el uso de **PDO** (PHP Data Objects) con sentencias preparadas.

### ¿Por qué es seguro?

Al concatenar variables del usuario en un string SQL, un atacante puede inyectar código malicioso para alterar la lógica de la base de datos.
Al utilizar `PDO::prepare()` y vincular parámetros con `bindParam()`:

1. **Precompilación**: La base de datos compila la estructura de la consulta antes de recibir los valores reales.
2. **Tratamiento como cadenas literales**: Los parámetros del usuario se envían por canales de datos separados, obligando al motor SQL a interpretarlos estrictamente como strings de texto y nunca como comandos ejecutables.

*Ejemplo práctico implementado en [`includes/funciones.php`](includes/funciones.php):*

```php
$stmt = $db->prepare("SELECT * FROM recetas WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
```

---

## 🎨 Sugerencias para un Portafolio de Alto Nivel

Si vas a presentar este proyecto ante reclutadores o en tu web de portafolio, considera las siguientes pautas de documentación:

1. **Añade capturas de pantalla / GIFs de la UI**: Muestra el diseño adaptable en móvil y escritorio.
2. **Describe las Heurísticas de Nielsen aplicadas**:
   - *Visibilidad del estado del sistema*: Búsquedas dinámicas y pantallas claras de "No se encontraron resultados".
   - *Prevención de errores*: Validación estricta del parámetro `id` en `receta.php` (redireccionando en caso de datos no válidos).
   - *Estética y diseño minimalista*: Uso equilibrado de tipografía y colores que no saturan al usuario.
3. **Explica la escalabilidad**: Menciona cómo el uso de la arquitectura modular (DRY) facilita añadir funcionalidades futuras (como paneles de administración para añadir recetas o registro de usuarios) con mínimo esfuerzo de refactorización.
