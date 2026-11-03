<?php
/**
 * ==========================================================================
 * ParkControl · database/seeds/002_catalogos.php
 * ==========================================================================
 * Catálogo inicial de marcas y modelos de vehículos. Idempotente:
 * INSERT IGNORE sobre las claves únicas de cada tabla.
 * ==========================================================================
 */

/** @var PDO $pdo */
$pdo = Database::getConexion();

$catalogo = [
    'Toyota'     => ['Yaris', 'Corolla', 'Hilux', 'RAV4'],
    'Chevrolet'  => ['Sail', 'Spark', 'Onix', 'D-Max'],
    'Kia'        => ['Rio', 'Sportage', 'Morning', 'Soluto'],
    'Hyundai'    => ['Accent', 'Tucson', 'Grand i10', 'Elantra'],
    'Nissan'     => ['Versa', 'Kicks', 'Navara', 'March'],
    'Suzuki'     => ['Swift', 'Vitara', 'Baleno', 'Jimny'],
    'Ford'       => ['Fiesta', 'Ranger', 'EcoSport', 'Focus'],
    'Volkswagen' => ['Gol', 'Polo', 'Amarok', 'Tiguan'],
    'Peugeot'    => ['208', '301', '3008', 'Partner'],
    'Mitsubishi' => ['L200', 'ASX', 'Outlander', 'Mirage'],
];

$insertarMarca = $pdo->prepare('INSERT IGNORE INTO marca (marca) VALUES (?)');
$buscarMarca = $pdo->prepare('SELECT idMarca FROM marca WHERE marca = ? LIMIT 1');
$insertarModelo = $pdo->prepare('INSERT IGNORE INTO modelo (idMarca, modelo) VALUES (?, ?)');

foreach ($catalogo as $marca => $modelos) {
    $insertarMarca->execute([$marca]);
    $buscarMarca->execute([$marca]);
    $idMarca = (int) $buscarMarca->fetchColumn();

    if ($idMarca === 0) {
        continue;
    }

    foreach ($modelos as $modelo) {
        $insertarModelo->execute([$idMarca, $modelo]);
    }
}

echo '  Catálogo de marcas y modelos cargado (' . count($catalogo) . " marcas).\n";
