{{-- Contrato Individual de Trabajo por Plazo Indefinido â€” DELTA CARGO S.R.L (formato: contratos/AgostoÂ´26 - Contrato Indefinido). --}}
@extends('contratos.documentos.layout', ['formato' => 'indefinido', 'm' => ['arriba' => 114, 'derecha' => 82, 'abajo' => 72, 'izquierda' => 85, 'logo_arriba' => -47, 'logo_derecha' => 5]])

@section('contenido')
    @include('contratos.documentos.partials.indefinido-cuerpo', ['variante' => 'dc'])
    @include('contratos.documentos.partials.indefinido-adenda', ['variante' => 'dc'])
@endsection
