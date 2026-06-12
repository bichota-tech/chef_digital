<?php
/**
 * Chef Digital - Página Principal (Catálogo de Recetas)
 * 
 * Muestra el catálogo de recetas haciendo un JOIN con los chefs.
 * Permite buscar recetas en tiempo real de forma segura.
 */

// Incluir archivos necesarios
require_once __DIR__ . '/includes/funciones.php';

// Inicializar variables de control de la UI
$pagina_titulo = 'Catálogo de Recetas';
$nav_activo = 'inicio';

// Capturar el término de búsqueda de forma segura
$termino_busqueda = '';
if (isset($_GET['q']) && is_string($_GET['q'])) {
    // Trim para quitar espacios innecesarios
    $termino_busqueda = trim($_GET['q']);
}

// Obtener las recetas a través de la función que implementa prepared statements
$recetas = obtenerRecetasConChefs($termino_busqueda);

// Incluir cabecera HTML5
require_once __DIR__ . '/includes/header.php';
?>

<!-- Sección Hero / Introducción -->
<section class="hero-section" aria-labelledby="hero-title">
    <div class="hero-badge">
        <i class="fa-solid fa-star"></i> Catálogo Exclusivo
    </div>
    <h1 id="hero-title" class="hero-title">Descubre el Arte de la <br>Alta Cocina</h1>
    <p class="hero-subtitle">
        Una selección exclusiva de recetas creadas por los chefs más influyentes y premiados del mundo.
    </p>
</section>

<!-- Sección de Filtros y Búsqueda -->
<section class="filter-section" aria-label="Búsqueda de recetas">
    <!-- Formulario con método GET para permitir compartir enlaces de búsqueda -->
    <form action="index.php" method="GET" class="search-form" role="search">
        <div class="search-input-wrapper">
            <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input 
                type="search" 
                name="q" 
                id="search-q"
                value="<?php echo htmlspecialchars($termino_busqueda, ENT_QUOTES, 'UTF-8'); ?>" 
                placeholder="Buscar receta o chef..." 
                class="search-input"
                aria-label="Buscar recetas por título o chef"
            >
        </div>
        <button type="submit" class="search-button">
            <span>Buscar</span>
            <i class="fa-solid fa-arrow-right"></i>
        </button>
    </form>
</section>

<!-- Sección Catálogo de Recetas (Grilla de Tarjetas) -->
<section class="catalog-section" aria-labelledby="catalog-title">
    <h2 id="catalog-title" class="sr-only-focusable">Recetas Disponibles</h2>
    
    <div class="recipes-grid">
        <?php if (!empty($recetas)): ?>
            <?php foreach ($recetas as $receta): ?>
                <!-- Tarjeta de Receta Individual (Artículo Semántico) -->
                <article class="recipe-card" aria-labelledby="receta-titulo-<?php echo $receta['id']; ?>">
                    <div class="card-image-area">
                        <!-- Portada con etiqueta alt descriptiva para accesibilidad -->
                        <img 
                            src="<?php echo htmlspecialchars($receta['portada'], ENT_QUOTES, 'UTF-8'); ?>" 
                            alt="Fotografía de la receta: <?php echo htmlspecialchars($receta['titulo'], ENT_QUOTES, 'UTF-8'); ?>" 
                            class="card-image"
                            loading="lazy"
                        >
                        <!-- Año de creación de la receta como insignia -->
                        <span class="card-badge"><?php echo (int)$receta['anio']; ?></span>
                    </div>
                    
                    <div class="card-content">
                        <h3 id="receta-titulo-<?php echo $receta['id']; ?>" class="card-title">
                            <?php echo htmlspecialchars($receta['titulo'], ENT_QUOTES, 'UTF-8'); ?>
                        </h3>
                        
                        <div class="card-chef">
                            <i class="fa-solid fa-user-tie" aria-hidden="true"></i>
                            <span>Chef: <strong><?php echo htmlspecialchars($receta['chef_nombre'], ENT_QUOTES, 'UTF-8'); ?></strong></span>
                        </div>
                        
                        <div class="card-footer">
                            <!-- Enlace accesible e interactivo para ver los detalles -->
                            <a 
                                href="receta.php?id=<?php echo (int)$receta['id']; ?>" 
                                class="card-link"
                                aria-label="Ver ingredientes y detalles de la receta <?php echo htmlspecialchars($receta['titulo'], ENT_QUOTES, 'UTF-8'); ?>"
                            >
                                <span>Ver Receta</span>
                                <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Estado vacío si no se encuentran recetas -->
            <div class="no-results">
                <div class="no-results-icon">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <h3 class="no-results-title">No se encontraron recetas</h3>
                <p class="no-results-text">
                    Prueba buscando con otros términos o limpia el filtro para ver todo el catálogo.
                </p>
                <?php if (!empty($termino_busqueda)): ?>
                    <div style="margin-top: 1.5rem;">
                        <a href="index.php" class="search-button" style="display: inline-flex; width: auto;">Ver Todo el Catálogo</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
// Incluir pie de página HTML5
require_once __DIR__ . '/includes/footer.php';
?>
