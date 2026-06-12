<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Chef Digital - Tu catálogo de recetas gourmet y de alta cocina. Descubre recetas exclusivas de los mejores chefs del mundo.">
    <title><?php echo isset($pagina_titulo) ? htmlspecialchars($pagina_titulo) . ' | Chef Digital' : 'Chef Digital | Catálogo de Recetas Gourmet'; ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome para iconos de accesibilidad y UI (opcional, cargado desde CDN público) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Estilos de Chef Digital -->
    <?php
    // Construir la URL base de forma dinámica para que funcione en cualquier hosting
    // (InfinityFree, localhost, Render, etc.) sin importar la ruta del archivo actual.
    $protocolo  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host       = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script_dir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    $base_url   = $protocolo . '://' . $host . $script_dir;
    ?>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/styles.css">
</head>
<body>
    <a href="#contenido-principal" class="sr-only-focusable">Saltar al contenido principal</a>

    <!-- Header Semántico -->
    <header class="app-header">
        <div class="header-container">
            <div class="logo-area">
                <a href="index.php" class="logo-link" aria-label="Volver al inicio de Chef Digital">
                    <span class="logo-icon"><i class="fa-solid fa-utensils"></i></span>
                    <span class="logo-text">Chef <span class="accent-text">Digital</span></span>
                </a>
            </div>
            
            <nav class="app-navigation" aria-label="Navegación principal">
                <ul class="nav-menu">
                    <li><a href="index.php" class="nav-link <?php echo (!isset($nav_activo) || $nav_activo === 'inicio') ? 'active' : ''; ?>"><i class="fa-solid fa-house"></i> Inicio</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main id="contenido-principal" class="app-main-content">
