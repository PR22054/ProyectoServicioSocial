<?php

// Datos fijos de los formularios oficiales de especies municipales
return [
    // Codigo de formulario que lleva el encabezado de cada reporte PDF
    'codigos' => [
        'libro'         => 'TESO-008-FORM',
        'bodega'        => 'TESO-010-FORM',
        'realizaciones' => 'TESO-002-FORM',
        'saldos'        => 'TESO-004-FORM',
        'otros'         => 'TESO-000-FORM',   // existencias por distrito, traslados, mensual y consolidado anual
    ],

    'libro' => [
        'version'  => '001',
        'vigencia' => '11/11/2025',
    ],

    'firmas' => [
        'tesorera' => ['nombre' => env('ESPECIES_FIRMA_TESORERIA', 'LIC. DELMY MARILIN MURILLOS JERÓNIMO'), 'cargo' => 'TESORERA MUNICIPAL'],
    ],
];
