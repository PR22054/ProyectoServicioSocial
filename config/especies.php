<?php

// Datos fijos de los formularios oficiales de especies municipales
return [
    'libro' => [
        'codigo'   => 'TESO-004-CONT',
        'version'  => '001',
        'vigencia' => '11/11/2025',
    ],

    'firmas' => [
        'tesorera' => ['nombre' => env('ESPECIES_FIRMA_TESORERIA', 'LIC. DELMY MARILIN MURILLOS JERÓNIMO'), 'cargo' => 'TESORERA MUNICIPAL'],
    ],
];
