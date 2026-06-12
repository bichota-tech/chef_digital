-- =============================================================================
-- Base de Datos para "Chef Digital"
-- =============================================================================
-- Este script crea la base de datos y sus tablas normalizadas con claves foráneas,
-- restricciones e índices de optimización para un catálogo de recetas profesional.

CREATE DATABASE IF NOT EXISTS `chef_digital` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `chef_digital`;

-- 1. Tabla: chefs
-- Almacena la información de los chefs creadores de las recetas.
CREATE TABLE IF NOT EXISTS `chefs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) NOT NULL,
    `biografia` TEXT NULL,
    `f_nac` DATE NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabla: recetas
-- Almacena las recetas del catálogo. Tiene relación de uno a muchos con chefs (un chef tiene muchas recetas).
CREATE TABLE IF NOT EXISTS `recetas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `titulo` VARCHAR(150) NOT NULL,
    `portada` VARCHAR(255) NOT NULL, -- URL de la imagen de portada
    `anio` INT NOT NULL,
    `chef_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_recetas_chefs`
        FOREIGN KEY (`chef_id`) REFERENCES `chefs` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabla: ingredientes
-- Almacena los ingredientes independientes y sus calorías asociadas.
CREATE TABLE IF NOT EXISTS `ingredientes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) NOT NULL,
    `calorias` INT NOT NULL COMMENT 'Calorías por cada 100 gramos/unidades estándar',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tabla: receta_ingrediente (Tabla Pivote Muchos-a-Muchos)
-- Conecta recetas e ingredientes, registrando la cantidad específica de cada ingrediente en cada receta.
CREATE TABLE IF NOT EXISTS `receta_ingrediente` (
    `receta_id` INT NOT NULL,
    `ingrediente_id` INT NOT NULL,
    `cantidad` VARCHAR(50) NOT NULL COMMENT 'Ej: 200g, 2 cucharadas, 1 pieza',
    PRIMARY KEY (`receta_id`, `ingrediente_id`),
    CONSTRAINT `fk_pivot_receta`
        FOREIGN KEY (`receta_id`) REFERENCES `recetas` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_pivot_ingrediente`
        FOREIGN KEY (`ingrediente_id`) REFERENCES `ingredientes` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- Inserción de Registros de Prueba (Mínimo 5 por tabla)
-- =============================================================================

-- Inserción en tabla: chefs
INSERT INTO `chefs` (`nombre`, `biografia`, `f_nac`) VALUES
('Ferran Adrià', 'Considerado uno de los mejores chefs del mundo, pionero de la gastronomía molecular y alma del mítico restaurante elBulli.', '1962-05-14'),
('Gordon Ramsay', 'Reconocido chef británico, multipremiado con estrellas Michelin y famoso por su perfeccionismo y presencia televisiva.', '1966-11-08'),
('Massimo Bottura', 'Chef italiano innovador, propietario de Osteria Francescana, restaurante galardonado con tres estrellas Michelin.', '1962-09-30'),
('Gastón Acurio', 'Chef, escritor y empresario peruano, principal promotor de la difusión de la gastronomía peruana en el mundo.', '1967-10-30'),
('Dominique Crenn', 'Primera chef mujer en los Estados Unidos en recibir tres estrellas Michelin en su restaurante Atelier Crenn en San Francisco.', '1965-02-03');

-- Inserción en tabla: recetas
INSERT INTO `recetas` (`titulo`, `portada`, `anio`, `chef_id`) VALUES
('Esferificación de Olivas Verdes', 'https://images.unsplash.com/photo-1541014741259-df5290db5785?auto=format&fit=crop&w=800&q=80', 2003, 1),
('Solomillo Wellington Clásico', 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=800&q=80', 1993, 2),
('La Parte Crujiente de la Lasaña', 'https://images.unsplash.com/photo-1574894709920-11b28e7367e3?auto=format&fit=crop&w=800&q=80', 2012, 3),
('Ceviche Clásico Peruano', 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?auto=format&fit=crop&w=800&q=80', 1998, 4),
('Tarta de Manzana Moderna', 'https://images.unsplash.com/photo-1621574539437-4b7cb63120b8?auto=format&fit=crop&w=800&q=80', 2015, 5);

-- Inserción en tabla: ingredientes
INSERT INTO `ingredientes` (`nombre`, `calorias`) VALUES
('Aceituna verde líquida (Zumo)', 115),
('Alginato de sodio (Gelificante)', 0),
('Solomillo de ternera', 250),
('Masa de hojaldre', 558),
('Pasta para lasaña', 130),
('Queso Parmigiano Reggiano', 431),
('Filete de Corvina (Pescado)', 104),
('Limo o Limón (Zumo)', 30),
('Cebolla morada', 40),
('Manzanas Granny Smith', 52);

-- Inserción en tabla: receta_ingrediente (relación muchos a muchos)
INSERT INTO `receta_ingrediente` (`receta_id`, `ingrediente_id`, `cantidad`) VALUES
-- Receta 1: Esferificación de Olivas
(1, 1, '200 ml'),
(1, 2, '2 gramos'),

-- Receta 2: Solomillo Wellington
(2, 3, '800 gramos'),
(2, 4, '1 lámina'),

-- Receta 3: Lasaña Crujiente
(3, 5, '6 láminas'),
(3, 6, '150 gramos'),

-- Receta 4: Ceviche Clásico
(4, 7, '500 gramos'),
(4, 8, '100 ml'),
(4, 9, '1 pieza'),

-- Receta 5: Tarta de Manzana
(5, 10, '4 piezas'),
(5, 4, '1 lámina');
