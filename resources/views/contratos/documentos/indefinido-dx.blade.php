{{-- Contrato Individual de Trabajo por Plazo Indefinido â€” DELTAEXPRESS SA (formato: contratos/JunioÂ´26 - Contrato Indefinido - DX). --}}
@extends('contratos.documentos.layout', ['formato' => 'indefinido', 'm' => ['arriba' => 142, 'derecha' => 82, 'abajo' => 72, 'izquierda' => 85, 'logo_arriba' => -75, 'logo_derecha' => 5]])

@section('contenido')
    @include('contratos.documentos.partials.indefinido-cuerpo', ['variante' => 'dx'])
    @include('contratos.documentos.partials.indefinido-adenda', ['variante' => 'dx'])
@endsection
