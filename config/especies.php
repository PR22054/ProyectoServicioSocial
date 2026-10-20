<?php

// Datos fijos de los formularios oficiales de especies municipales
return [
    // Codigo de formulario que lleva el encabezado de cada reporte PDF
    'codigos' => [
        'libro'         => 'TESO-008-FORM',
        'bodega'        => 'TESO-010-FORM',
        'realizaciones' => 'TESO-002-CONT',
        'saldos'        => 'TESO-004-CONT',
        'otros'         => 'TESO-000-FORM',   // existencias por distrito, traslados, mensual y consolidado anual
    ],

    // Version de cada formulario (los demas reportes: 001) y fecha de vigencia de todos
    'versiones' => [
        'libro'         => '001',
        'bodega'        => '000',
        'realizaciones' => '001',
        'saldos'        => '000',
    ],
    'vigencia' => '11/11/2025',

    'firmas' => [
        'tesorera' => ['nombre' => env('ESPECIES_FIRMA_TESORERIA', 'LIC. DELMY MARILIN MURILLOS JERÓNIMO'), 'cargo' => 'TESORERA MUNICIPAL'],
    ],
];
