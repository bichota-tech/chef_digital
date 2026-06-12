<?php
/**
 * Funciones de Negocio y Seguridad de Datos
 * 
 * Contiene todas las operaciones de consulta a la base de datos de Chef Digital.
 * Implementa de manera estricta el uso de Sentencias Preparadas (Prepared Statements)
 * para mitigar ataques de inyección SQL (SQL Injection).
 */

require_once __DIR__ . '/../config/db.php';

/**
 * =============================================================================
 * NOTA DE SEGURIDAD: ¿Por qué usamos Sentencias Preparadas (Prepared Statements)?
 * =============================================================================
 * 
 * La Inyección SQL ocurre cuando datos proporcionados por el usuario se concatenan
 * directamente en una cadena de consulta SQL, permitiendo a un atacante alterar la estructura
 * lógica de dicha consulta para saltarse controles de seguridad o acceder a datos no autorizados.
 * 
 * Al usar Sentencias Preparadas con PDO (ej. $pdo->prepare() y bindParam/execute):
 * 
 * 1. Separación de Lógica y Datos: La base de datos compila la estructura de la consulta SQL
 *    usando "marcadores de posición" (ej. :id o :busqueda) antes de insertar los datos reales.
 * 2. Inmunidad a Caracteres Especiales: Incluso si el atacante ingresa valores maliciosos como 
 *    "1' OR '1'='1" o "1; DROP TABLE recetas;", el motor de la base de datos tratará estos
 *    datos estrictamente como cadenas de texto literales (strings) y no como comandos ejecutables.
 * 3. Tipado Seguro: PDO valida internamente que los valores coincidan con los tipos esperados,
 *    añadiendo una capa adicional de saneamiento.
 */

/**
 * Recupera todas las recetas del catálogo realizando un JOIN con la tabla chefs
 * para obtener el nombre del chef creador. Opcionalmente filtra por búsqueda.
 * 
 * @param string $busqueda Término opcional para filtrar por título de receta o nombre del chef.
 * @return array Colección de recetas encontradas
 */
function obtenerRecetasConChefs(string $busqueda = ''): array {
    $db = obtenerConexion();
    
    // Consulta base con JOIN para cumplir el requisito académico
    $sql = "SELECT r.id, r.titulo, r.portada, r.anio, r.chef_id, c.nombre AS chef_nombre
            FROM recetas r
            INNER JOIN chefs c ON r.chef_id = c.id";
            
    // Si se proporciona un término de búsqueda, agregamos la cláusula WHERE de forma segura.
    if (!empty($busqueda)) {
        // Usamos marcadores de posición nominados (:termino) en lugar de concatenar $busqueda.
        $sql .= " WHERE r.titulo LIKE :termino OR c.nombre LIKE :termino";
    }
    
    // Ordenamos por año de creación de la receta de forma descendente
    $sql .= " ORDER BY r.anio DESC";
    
    try {
        $stmt = $db->prepare($sql);
        
        if (!empty($busqueda)) {
            // El comodín '%' se añade al valor de búsqueda, no a la consulta SQL directamente.
            $paramBusqueda = "%" . $busqueda . "%";
            // Vinculamos el parámetro de manera segura, indicando que se trata de una cadena de texto (PARAM_STR)
            $stmt->bindParam(':termino', $paramBusqueda, PDO::PARAM_STR);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error en obtenerRecetasConChefs: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene la información detallada de una receta específica por su ID.
 * 
 * @param int $id ID único de la receta
 * @return array|null Datos de la receta o null si no se encuentra
 */
function obtenerRecetaPorId(int $id): ?array {
    $db = obtenerConexion();
    
    $sql = "SELECT r.id, r.titulo, r.portada, r.anio, r.chef_id, 
                   c.nombre AS chef_nombre, c.biografia AS chef_biografia, c.f_nac AS chef_f_nac
            FROM recetas r
            INNER JOIN chefs c ON r.chef_id = c.id
            WHERE r.id = :id";
            
    try {
        $stmt = $db->prepare($sql);
        // Vinculamos como entero (PARAM_INT) para asegurar que el motor interprete el dato numéricamente
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $resultado = $stmt->fetch();
        return $resultado ? $resultado : null;
    } catch (PDOException $e) {
        error_log("Error en obtenerRecetaPorId: " . $e->getMessage());
        return null;
    }
}

/**
 * Obtiene los ingredientes asociados a una receta (Relación Muchos a Muchos).
 * Resuelve la tabla pivote receta_ingrediente e ingredientes.
 * 
 * @param int $recetaId ID de la receta
 * @return array Listado de ingredientes con su cantidad y calorías
 */
function obtenerIngredientesDeReceta(int $recetaId): array {
    $db = obtenerConexion();
    
    $sql = "SELECT i.nombre, i.calorias, ri.cantidad
            FROM receta_ingrediente ri
            INNER JOIN ingredientes i ON ri.ingrediente_id = i.id
            WHERE ri.receta_id = :receta_id";
            
    try {
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':receta_id', $recetaId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error en obtenerIngredientesDeReceta: " . $e->getMessage());
        return [];
    }
}

/**
 * Calcula las calorías totales estimadas de una receta basándose en sus ingredientes.
 * 
 * @param array $ingredientes Lista de ingredientes obtenidos de la base de datos
 * @return int Calorías totales acumuladas
 */
function calcularCaloriasTotales(array $ingredientes): int {
    $total = 0;
    foreach ($ingredientes as $ingrediente) {
        $total += $ingrediente['calorias'];
    }
    return $total;
}
