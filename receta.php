<?php
/**
 * Chef Digital - Detalle de Receta
 * 
 * Muestra toda la información de una receta individual,
 * su creador (chef), y los ingredientes asociados (relación N:M).
 */

require_once __DIR__ . '/includes/funciones.php';

// Validar y sanear el ID recibido por GET
$receta_id = 0;
if (isset($_GET['id'])) {
    $receta_id = (int)$_GET['id'];
}

// Obtener la receta usando sentencias preparadas
$receta = obtenerRecetaPorId($receta_id);

// Redirigir al inicio si la receta no existe o el ID no es válido
if (!$receta) {
    header('Location: index.php');
    exit;
}

// Obtener los ingredientes asociados a esta receta (Relación Muchos a Muchos)
$ingredientes = obtenerIngredientesDeReceta($receta_id);
$calorias_totales = calcularCaloriasTotales($ingredientes);

// Título de la página dinámico
$pagina_titulo = $receta['titulo'];
$nav_activo = 'recetas';

// Incluir cabecera
require_once __DIR__ . '/includes/header.php';
?>

<!-- Enlace de regreso con rol semántico -->
<div class="back-btn-area">
    <a href="index.php" class="back-link" aria-label="Volver al catálogo de recetas">
        <i class="fa-solid fa-arrow-left-long"></i> Volver al Catálogo
    </a>
</div>

<!-- Estructura del Detalle de Receta -->
<article class="recipe-detail-container" aria-labelledby="recipe-title">
    <div class="recipe-detail-grid">
        
        <!-- Columna Izquierda: Galería e Información General -->
        <section class="recipe-visuals" aria-label="Imagen y metadatos de la receta">
            <div class="recipe-gallery">
                <img 
                    src="<?php echo htmlspecialchars($receta['portada'], ENT_QUOTES, 'UTF-8'); ?>" 
                    alt="Fotografía de presentación de <?php echo htmlspecialchars($receta['titulo'], ENT_QUOTES, 'UTF-8'); ?>" 
                    class="recipe-detail-img"
                >
            </div>
            
            <!-- Tarjetas de Metadatos -->
            <div class="recipe-meta-cards">
                <div class="meta-card">
                    <span class="meta-card-value"><?php echo (int)$receta['anio']; ?></span>
                    <span class="meta-card-label">Año de Creación</span>
                </div>
                <div class="meta-card">
                    <span class="meta-card-value"><?php echo $calorias_totales; ?> kcal</span>
                    <span class="meta-card-label">Calorías Totales</span>
                </div>
            </div>
        </section>
        
        <!-- Columna Derecha: Contenido de la Receta e Ingredientes -->
        <section class="recipe-info" aria-label="Detalles de la receta e ingredientes">
            <h1 id="recipe-title" class="recipe-header-title">
                <?php echo htmlspecialchars($receta['titulo'], ENT_QUOTES, 'UTF-8'); ?>
            </h1>
            
            <p class="recipe-chef-tag">
                <i class="fa-solid fa-cookie-bite" aria-hidden="true"></i> 
                Creado por el Chef: <strong><?php echo htmlspecialchars($receta['chef_nombre'], ENT_QUOTES, 'UTF-8'); ?></strong>
            </p>

            <!-- Sección de Ingredientes (Relación Muchos a Muchos) -->
            <div class="ingredients-card">
                <h2 class="recipe-section-title">Ingredientes Requeridos</h2>
                <?php if (!empty($ingredientes)): ?>
                    <ul class="ingredients-list">
                        <?php foreach ($ingredientes as $ingrediente): ?>
                            <li>
                                <div class="ingrediente-info">
                                    <span class="ingrediente-nombre">
                                        <?php echo htmlspecialchars($ingrediente['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                    <span class="ingrediente-calorias">
                                        (<?php echo (int)$ingrediente['calorias']; ?> kcal/100g)
                                    </span>
                                </div>
                                <span class="ingrediente-cantidad">
                                    <?php echo htmlspecialchars($ingrediente['cantidad'], ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">No se han registrado ingredientes para esta receta.</p>
                <?php endif; ?>
            </div>

            <!-- Ficha del Chef Creador -->
            <div class="chef-profile-card">
                <h2 class="recipe-section-title">Sobre el Chef</h2>
                <div class="chef-profile-header">
                    <div class="chef-avatar-placeholder" aria-hidden="true">
                        <?php echo mb_substr($receta['chef_nombre'], 0, 1); ?>
                    </div>
                    <div>
                        <h3 class="chef-profile-name"><?php echo htmlspecialchars($receta['chef_nombre'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p class="chef-profile-meta">
                            Fecha de nac: <?php echo date('d/m/Y', strtotime($receta['chef_f_nac'])); ?>
                        </p>
                    </div>
                </div>
                <p class="chef-profile-bio">
                    <?php echo htmlspecialchars($receta['chef_biografia'], ENT_QUOTES, 'UTF-8'); ?>
                </p>
            </div>
            
        </section>

    </div>
</article>

<?php
// Incluir pie de página
require_once __DIR__ . '/includes/footer.php';
?>
