{{--
    Base de los contratos descargables. Reproduce el formato de los PDF originales
    de la carpeta contratos/: hoja, márgenes, logo DELTA X en cada página y fuentes.

    Variables: $formato = 'prestacion' (A4, Poppins) | 'indefinido' (Carta, Times).
               $m = márgenes medidos en el original [arriba, derecha, abajo, izquierda] y
                    posición del logo [logo_arriba, logo_derecha] relativa al área de texto.
--}}
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    @font-face { font-family: 'Poppins'; font-weight: normal; font-style: normal; src: url('{{ resource_path('contratos/fonts/Poppins-Regular.ttf') }}') format('truetype'); }
    @font-face { font-family: 'Poppins'; font-weight: bold; font-style: normal; src: url('{{ resource_path('contratos/fonts/Poppins-Bold.ttf') }}') format('truetype'); }
    @font-face { font-family: 'Poppins'; font-weight: normal; font-style: italic; src: url('{{ resource_path('contratos/fonts/Poppins-Italic.ttf') }}') format('truetype'); }
    @font-face { font-family: 'Poppins'; font-weight: bold; font-style: italic; src: url('{{ resource_path('contratos/fonts/Poppins-BoldItalic.ttf') }}') format('truetype'); }

    @page { margin: {{ $m['arriba'] }}pt {{ $m['derecha'] }}pt {{ $m['abajo'] }}pt {{ $m['izquierda'] }}pt; }
    .logo { position: fixed; top: {{ $m['logo_arriba'] }}pt; right: {{ $m['logo_derecha'] }}pt; width: {{ $formato === 'prestacion' ? 156 : 130.4 }}pt; }

    body { margin: 0; color: #000; }
    p { margin: 0 0 1em 0; text-align: justify; }
    b { font-weight: bold; }
    u { text-decoration: underline; }
    .centro { text-align: center; }
    .titulo { text-align: center; font-weight: bold; margin-bottom: 1.6em; }
    .salto { page-break-before: always; }
    .sangria { margin-left: 18pt; }
    ul, ol { margin: 0 0 1em 0; padding-left: 28pt; }
    li { text-align: justify; margin: 0; }
    ul.sub { margin: 0; padding-left: 22pt; list-style-type: circle; }
    .firmas { width: 100%; margin-top: 70pt; border-collapse: collapse; }
    .firmas td { width: 50%; text-align: center; vertical-align: top; padding: 0 6pt; }

    /* Familias de letra de cada documento.
       El generador multiplica el interlineado de Poppins por 1,65 (alto de la fuente):
       0.909 equivale al interlineado 1,5 del original (15 pt en 10 pt). */
    .poppins { font-family: 'Poppins', sans-serif; }
    .times { font-family: 'Times New Roman', Times, serif; }
    .p10 { font-size: 10pt; line-height: 0.909; }
    .p95 { font-size: 9.5pt; line-height: 0.909; }
    .p9 { font-size: 9pt; line-height: 0.909; }
    .t12 { font-size: 12pt; line-height: 13.8pt; }
    .t11 { font-size: 11pt; line-height: 12.7pt; }
    .t10 { font-size: 10pt; line-height: 11.5pt; }
</style>
</head>
<body>
    <img class="logo" src="{{ resource_path($formato === 'prestacion' ? 'contratos/img/logo-horizontal.png' : 'contratos/img/logo-cuadrado.png') }}" alt="DELTA X">
    @yield('contenido')
</body>
</html>
