<?php
/**
 * TechCare Soporte TI - Enrutador Raíz
 * Redirige la ejecución hacia el módulo 03-Desarrollo
 */

chdir(__DIR__ . '/03-Desarrollo');
require_once __DIR__ . '/03-Desarrollo/index.php';
