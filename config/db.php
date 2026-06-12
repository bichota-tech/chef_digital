<?php
/**
 * Configuración de la Base de Datos y Conexión PDO
 * 
 * Este archivo gestiona la conexión segura a la base de datos MySQL utilizando PDO (PHP Data Objects).
 * Soporta configuración dinámica a través de variables de entorno, ideal para despliegues
 * en la nube como Render y bases de datos serverless como PlanetScale.
 */

// =============================================================================
// CONFIGURACIÓN MANUAL (Para desarrollo local o Hostings como InfinityFree)
// Si tu servidor NO utiliza variables de entorno, edita los siguientes valores:
// =============================================================================
define('CONF_DB_HOST', 'sql102.infinityfree.com');      // InfinityFree: sqlXXX.infinityfree.com (ver en tu panel)
define('CONF_DB_PORT', 3306);
define('CONF_DB_USER', 'if0_42164592');           // InfinityFree: tu usuario de base de datos (if0_XXXXXXX)
define('CONF_DB_PASS', 'sj9GpGGmuIHyEH');               // InfinityFree: tu contraseña de cPanel
define('CONF_DB_NAME', 'if0_42164592_db_chef');    // InfinityFree: el nombre de base de datos creada (if0_XXXXX_chef_digital)

/**
 * Obtiene una instancia de conexión PDO a la base de datos.
 * Utiliza variables de entorno si están disponibles (producción/Render/PlanetScale),
 * o las constantes manuales definidas arriba.
 * 
 * @return PDO Instancia de la conexión
 * @throws PDOException Si la conexión falla
 */
function obtenerConexion(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        // 1. Detectar configuración desde variables de entorno
        $url = getenv('DATABASE_URL');
        
        if (!empty($url)) {
            // Intentar parsear la URL de la base de datos (común en proveedores de hosting)
            $dbparts = parse_url($url);
            
            $host    = $dbparts['host'] ?? CONF_DB_HOST;
            $port    = $dbparts['port'] ?? CONF_DB_PORT;
            $user    = $dbparts['user'] ?? CONF_DB_USER;
            $pass    = $dbparts['pass'] ?? CONF_DB_PASS;
            $dbname  = isset($dbparts['path']) ? ltrim($dbparts['path'], '/') : CONF_DB_NAME;
        } else {
            // Cargar variables individuales o usar valores por defecto locales (XAMPP/Laragon)
            $host    = getenv('DB_HOST') ?: CONF_DB_HOST;
            $port    = getenv('DB_PORT') ?: CONF_DB_PORT;
            $user    = getenv('DB_USER') ?: CONF_DB_USER;
            $pass    = getenv('DB_PASS') !== false ? getenv('DB_PASS') : (getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : CONF_DB_PASS);
            $dbname  = getenv('DB_NAME') ?: CONF_DB_NAME;
        }
        
        $charset = 'utf8mb4';
        
        // Data Source Name (DSN)
        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
        
        // Opciones predeterminadas de PDO
        $opciones = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        // 2. Soporte para conexión SSL (Requerido por PlanetScale)
        // Se puede configurar mediante la variable de entorno DB_SSL_CA (ej: /etc/ssl/certs/ca-certificates.crt en Render)
        $ssl_ca = getenv('DB_SSL_CA');
        if (!empty($ssl_ca)) {
            $opciones[PDO::MYSQL_ATTR_SSL_CA] = $ssl_ca;
            // Opcionalmente, verificar certificado del servidor si la plataforma lo exige
            $opciones[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
        }

        try {
            $pdo = new PDO($dsn, $user, $pass, $opciones);
        } catch (PDOException $e) {
            // En producción registramos internamente y lanzamos mensaje genérico
            error_log("Error de conexión a la base de datos: " . $e->getMessage());
            throw new Exception("Error interno del servidor. No se pudo establecer conexión segura.");
        }
    }

    return $pdo;
}

