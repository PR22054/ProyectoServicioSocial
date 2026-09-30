<?php

// Datos fijos de los formularios oficiales de especies municipales
return [
    'libro' => [
        'codigo'   => 'TESO-008-FORM',
        'version'  => '001',
        'vigencia' => '11/11/2025',
    ],

    'firmas' => [
        'tesorera' => ['nombre' => env('ESPECIES_FIRMA_TESORERIA', 'LIC. DELMY MARILIN MURILLOS JERÓNIMO'), 'cargo' => 'TESORERA MUNICIPAL'],
        'alcalde'  => ['nombre' => env('ESPECIES_FIRMA_ALCALDE', 'DR. CARLOS ADELSO LANDAVERDE CARPIO'), 'cargo' => 'ALCALDE MUNICIPAL'],
    ],
];
