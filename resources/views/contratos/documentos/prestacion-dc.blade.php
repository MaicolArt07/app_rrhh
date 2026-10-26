{{-- Contrato de Prestación de Servicios — DELTA CARGO S.R.L. (formato: contratos/Junio´26 - Contrato de Prestación de Servicios). --}}
@extends('contratos.documentos.layout', ['formato' => 'prestacion', 'm' => ['arriba' => 72, 'derecha' => 63, 'abajo' => 78, 'izquierda' => 64, 'logo_arriba' => -55, 'logo_derecha' => 5]])

@section('contenido')
<div class="poppins p10">

<p class="titulo" style="margin-top:17pt"><u>CONTRATO DE PRESTACIÓN DE SERVICIOS</u></p>

<p>Conste por el presente contrato de prestación de servicios (en adelante, el <b>“Contrato”</b>), que puede elevarse a público a solo reconocimiento de firmas a solicitud de cualesquiera de las partes que intervienen, de común acuerdo y con plena capacidad, conforme al Art. 519 del código civil, se establece el presente contrato, al tenor y contenido de las siguientes cláusulas:</p>

<p style="margin-bottom:0"><b><u>PRIMERA. DE LAS PARTES:</u></b><br>Intervienen en el presente contrato las siguientes partes:</p>
<p>&nbsp;</p>

<p class="sangria"><b>1.1. DELTA CARGO S.R.L.</b>, empresa legalmente constituida con Matrícula de Comercio y Número de Identificación Tributaria Nro. 1025565029, de la ciudad de Santa Cruz de la Sierra, debidamente representada legalmente por el señor <b>Luis Fernando Ortíz Reque,</b> con cédula de identidad N° 4720436 según consta por Escritura Pública de Poder No. N° 439/2025 del 21 de octubre del 2025, otorgada en la Notaría No. 20 del Distrito Judicial de Santa Cruz, en adelante simplemente denominado “<b>CONTRATANTE”.</b></p>

<p class="sangria"><b>1.2. {{ $t['nombre'] }}</b>, mayor de edad, hábil por derecho, con C.I. N° {{ $t['ci_completo'] }} denominado en lo posterior a los fines del presente documento como <b>“PRESTADOR DE SERVICIOS”</b>.</p>

<p class="sangria">1.3. Las partes descritas anteriormente, se podrán denominar PARTES cuando se haga referencia a ambas, o como PARTE cuando se haga referencia a una de ellas.</p>

<p><b><u>SEGUNDA. OBJETO:</u></b><br>Por el presente contrato, el <b>PRESTADOR DE SERVICIOS</b>, se compromete y obliga, a prestar el servicio de “<b>{{ $c['cargo'] }}</b>”.</p>

<p><b><u>TERCERA. NATURALEZA JURÍDICA DEL CONTRATO:</u></b><br>La ejecución del presente contrato de servicios se rige por las estipulaciones del Capítulo V del título ll Parte Segunda del Libro Tercero del Código Civil. Queda claramente establecido que las obligaciones correspondientes a los aportes sociales de cualquier índole están a cuenta y cargo del <b>PRESTADOR DE SERVICIOS</b> y en el marco de lo dispuesto por el Decreto Supremo 107/2009 de 1ro. de mayo de 2009, el <b>PRESTADOR DE SERVICIOS</b> se obliga a dar cumplimiento a toda obligación sociolaboral que le corresponda. En consecuencia, no existe obligación para el <b>CONTRATANTE</b> de responder por el pago de las cargas sociales emergentes de una relación laboral ni a favor del <b>PRESTADOR DE SERVICIOS</b>, ni de las personas que éste pudiera contratar para la ejecución de los Servicios.</p>

<p><b><u>CUARTA. HONORARIOS PROFESIONALES Y FORMA DE PAGO:</u></b><br>El pago por la presente relación de servicios será de <b>Bs.- {{ $c['monto'] }} ({{ $c['literal_titulo'] }} {{ $c['centavos'] }}/100 BOLIVIANOS)</b> por el servicio brindado, el cual será a requerimiento del CONTRATANTE. EL <b>PRESTADOR DE SERVICIO</b> deberá emitir factura por los pagos recibidos, de lo contrario <b>EL CONTRATANTE</b> actuará como sustituto en calidad de Agente de Retención de dicha Obligación, en mérito a lo establecido en el artículo 25, parágrafo 1, de la Ley 2492.</p>

<p><b><u>QUINTA. DESARROLLO DEL SERVICIO:</u></b><br><b>El CONTRATANTE</b>, con la facultad que le confiere el Art. 738 del Código Civil, podrá valorar de manera periódica el <b>SERVICIO</b> realizado por el <b>PRESTADOR DE SERVICIOS</b> a efectos de establecer el cumplimiento de sus obligaciones derivadas del presente contrato.</p>

<p><b><u>SEXTA. OBLIGACIONES DEL PRESTADOR DE SERVICIOS:</u></b><br><b>6.1.</b> Realizar su prestación de servicio, de manera eficaz y responsable.<br><b>6.2.</b> Emitir la correspondiente factura por los servicios prestados.</p>

<p><b><u>SÉPTIMA. OBLIGACIONES DEL CONTRATANTE:</u></b><br>7.2. Realizar el pago de honorarios profesionales correspondiente.</p>

<p><b><u>OCTAVA. RESOLUCIÓN, RESCISIÓN Y/O FINALIZACIÓN DEL CONTRATO:</u></b><br>El presente contrato podrá ser rescindido, resuelto y/o finalizado conforme a los siguientes términos:</p>

<p><b>8.1.</b> Al sólo vencimiento del plazo acordado en el presente instrumento quedará automáticamente resuelto de hecho, no pudiendo operar tácita reconducción alguna.</p>

<p><b>8.2</b>. Por <b>EL CONTRATANTE</b>, en cualquier momento y por medio de comunicación expresa escrita que exprese su decisión de rescindir el presente contrato, sin necesidad de requerimiento judicial o extrajudicial alguno, sin que ello pueda generar derecho para solicitar reclamación o indemnización alguna, sino simplemente la liquidación de saldos que pudiera existir entre PARTES hasta el último día de prestación efectiva del servicio.</p>

<p><b>8.3.</b> Por <b>EL PRESTADOR DE SERVICIOS</b>, con una anticipación de 15 (quince días) por medio de comunicación expresa escrita que exprese su decisión de rescindir el presente contrato, sin necesidad de requerimiento judicial o extrajudicial alguno.</p>

<p><b>8.4.</b> Por imposibilidad o causal sobreviviente que hicieran inviable el <b>SERVICIO</b> acordado, sea por desastres naturales, revoluciones, conmoción civil, alzamientos armados, reyertas o cualquier otra que sea ajeno a las partes, caso en el cual la resolución operará de puro derecho debiendo proceder a la liquidación de saldos que hubiera entre <b>PARTES</b> hasta el último día de prestación efectiva del <b>SERVICIO</b>, sin generar ninguna otra responsabilidad para ninguna de ellas.</p>

<p><b>8.5.</b> Por alguna resolución, norma o instrucción de cualquier naturaleza emitida por autoridad competente que de alguna forma limite o prohíba las actividades desarrolladas por cualquier de las <b>PARTES</b>, en cuyo caso la resolución procederá a requerimiento de la <b>PARTE</b> impedida, sin que ello le origine obligación alguna a favor de la otra <b>PARTE</b>.</p>

<p><b>8.6.</b> En caso de incumplimiento por el <b>PRESTADOR DE SERVICIOS</b> a cualquiera de las cláusulas del presente contrato, <b>EL CONTRATANTE</b> podrá requerir optativamente al <b>PRESTADOR DE SERVICIOS</b> el cumplimiento del contrato en un plazo no mayor a 2 (dos) días o, en su defecto, la resolución del mismo sin necesidad de intervención judicial alguna conforme lo dispone el Art. 569 del Código Civil Boliviano.</p>

<p style="margin-bottom:0"><b>8.7</b>. Asimismo, el presente contrato podrá resolverse de hecho por parte del <b>CONTRATANTE</b> sin ningún tipo de comunicación previa, menos requerimiento judicial o extrajudicial alguno, si el <b>PRESTADOR DE SERVICIOS</b> incurriese en alguna de las siguientes circunstancias:</p>
<table style="width:100%; border-collapse:collapse; margin-bottom:1em">
    <tr><td style="width:40pt; vertical-align:top">8.7.1</td><td style="text-align:justify">Cesión o transferencia, total o parcial, legal o convencional, judicial o extrajudicial, de los términos del presente contrato, salvo si ello fuera expresamente aceptado previamente y por escrito por el <b>CONTRATANTE</b></td></tr>
    <tr><td style="vertical-align:top">8.7.2</td><td style="text-align:justify">Por la existencia de alguna disposición e manada de autoridad competente que motiven la pérdida de los títulos, licencias, autorizaciones ú otros que pudiera requerir el <b>PRESTADOR DE SERVICIOS</b> para la realización del <b>SERVICIO</b>.</td></tr>
</table>

<p>En caso de resolución del contrato por las causales señaladas en los incisos 8.7.1 y/o 8.7.2 del presente Contrato, el <b>PRESTADOR DE SERVICIOS</b> deberá pagar los daños y perjuicios ocasionados al <b>CONTRATANTE</b>.</p>

<p><b><u>NOVENA. PLAZO DE LA PRESTACIÓN DE SERVICIOS:</u></b><br>El plazo del presente contrato será desde el <b>{{ $c['inicio'] }} hasta el {{ $c['fin'] }}</b>, impostergablemente; no procediendo la tácita reconducción. En caso de que las <b>PARTES</b> quieran ampliar el plazo señalado, tendrán necesariamente que suscribir un nuevo contrato.</p>

<p><b><u>DÉCIMA. GARANTÍA:</u></b><br><b>EL PRESTADOR DE SERVICIOS</b> garantiza el cumplimiento de su obligación con todos sus bienes presentes y futuros.</p>

<p><b><u>DÉCIMA PRIMERA. IMPOSIBILIDAD DE CESIÓN:</u></b><br>Se acuerda entre partes que el <b>PRESTADOR DE SERVICIOS</b> no podrá ceder los derechos ni obligaciones que adquiere en virtud al presente contrato, salvo autorización y conformidad expresa previa y por escrito del <b>CONTRATANTE.</b></p>

<p><b><u>DÉCIMA SEGUNDA. VISITAS A LAS SUCURSALES DEL CONTRATANTE:</u></b><br>Para una mejor ejecución de sus servicios, EL PRESTADOR DE SERVICIOS podrá asistir a cualquiera de las sucursales del CONTRATANTE que se encuentre más cercana a su lugar de residencia o de los clientes, todo con el objeto de realizar la debida presentación, recojo de los servicios que ofrece EL CONTRATANTE, o en su defecto para hacer uso de las instalaciones del CONTRATANTE para recabar información de datos o participar de capacitaciones que este pueda ofrecerle, sin que su presencia pueda interpretarse como una sujeción a órdenes o instrucciones, ni como la obligación de cumplir un horario, por lo que los horarios que el PRESTADOR DE SERVICIOS puede asistir a las sucursales del CONTRATANTE, son de horas 08:00 a 16:30 de Lunes a Viernes.</p>

<p><b><u>DÉCIMA TERCERA. NOTIFICACIONES, CONTACTOS Y COMUNICACIONES:</u></b><br>Para notificaciones, las partes acuerdan proporcionar los siguientes datos de contacto y comunicaciones:</p>

<p style="text-align:left"><b>Por el CONTRATANTE:</b><br>
<b>Nombre del responsable:</b> Recursos Humanos - DeltaX<br>
<b>No. de Celular</b>: 77663615<br>
<b>Correo Electrónico:</b> asaavedra@deltax.la<br>
<b>Dirección:</b> Nueva Carretera a Warnes Urb. Trapero</p>

<p style="text-align:left"><b>Por el PRESTADOR DE SERVICIOS:</b><br>
<b>Nombre</b>: {{ $t['nombre'] }}<br>
<b>No. de Celular</b>: {{ $t['celular'] }}<br>
<b>Correo Electrónico</b>: {{ $t['email'] }}<br>
<b>Dirección</b>: {{ $t['domicilio'] }}</p>

<p>Cualquier notificación realizada a los correos electrónicos, números de celular y direcciones proporcionadas por las partes se entenderá por bien hecha y válida legalmente. Cualquier cambio que efectúen las partes a los datos arriba proporcionados, será debidamente puesto en conocimiento a la otra parte en un plazo máximo de 24 horas.</p>

<p><b><u>DÉCIMA CUARTA. CONFIDENCIALIDAD:</u></b><br><b>14.1. El PRESTADOR DE SERVICIOS</b> no podrá en ningún momento proporcionar a terceros, directa o indirectamente, por si o interpósita persona, información confidencial así como aspectos relativos a los sistemas de trabajo, administración, organización y/o seguridad del <b>CONTRATANTE</b> y en general de la documentación, archivos, planes, programas, proyectos, códigos de desactivación de alarmas, códigos computarizados de seguridad, archivos de contabilidad y otros que conozca, se entere o tome conocimiento en virtud de la celebración de este contrato.</p>

<p><b>14.2</b>. Asimismo siendo que en virtud al presente contrato el <b>PRESTADOR DE SERVICIOS</b>, tendrá la posibilidad de conocer nuevos sistemas de trabajo, métodos, técnicas, descubrimientos, innovaciones, ideas, conceptos ú otros, relacionados con la actividad del <b>CONTRATANTE</b>, teniendo además la posibilidad de acceder a documentación, archivos, registros, precios de productos, proveedores y demás material del <b>CONTRATANTE</b> así como a la instalación, manejo y manipulación de diversos softwares, aspectos todos ellos que por estar vinculados a derechos de autor y propiedad intelectual se hace necesario su absoluto resguardo, reserva y confidencialidad, el <b>PRESTADOR DE SERVICIOS</b> se compromete a guardar absoluta reserva y confidencialidad sobre los mismos obligándose especialmente a no proporcionar, ceder, facilitar, vender, permutar, transferir, donar, obsequiar, copiar ú otros, a ninguna persona, naturales o jurídicas, por ningún mecanismo, sea en forma gratuita u onerosa, en el territorio nacional o fuera de éste, ninguno de los elementos descritos, sea en beneficio propio o de terceros, comprometiéndose caso contrario a reconocer los daños y perjuicios que pudiera ocasionar con ello al <b>CONTRATANTE.</b></p>

<p><b><u>DÉCIMA QUINTA. INEXISTENCIA DE EXCLUSIVIDAD, DEPENDENCIA Y SUBORDINACIÓN:</u></b><br>Se hace constar que el prestador de servicios no tiene carácter de exclusividad, ni dependencia ni subordinación con el Contratante siendo libre de desarrollar sus actividades a favor de otras personas, naturales o jurídicas, en cuanto no afecten a los servicios contratados y no haya conflicto de intereses.</p>

<p style="margin-bottom:0"><b><u>DÉCIMA SEXTA. SOLUCIÓN DE CONTROVERSIAS:</u></b><br>Las Partes expresan que los términos del presente Contrato y las obligaciones que de él emergen, se encuentran bajo la jurisdicción de las leyes y autoridades bolivianas. Todo litigio, discrepancia, cuestión y reclamación resultante de la ejecución o interpretación del presente Contrato o relacionado con él, directa o indirectamente, se someterá previamente a la negociación directa entre las Partes.</p>
<p style="margin-bottom:0">Si agotada la negociación entre Partes o expirado el plazo máximo de 10 (Diez) días calendario, la controversia no fuese resuelta amigablemente, la misma se resolverá definitivamente mediante arbitraje en el marco de la Ley de Conciliación y Arbitraje.</p>
<p style="margin-bottom:0">El arbitraje se sujetará a las autoridades, reglas y al procedimiento contenido en el Reglamento de Arbitraje del Centro de Conciliación y Arbitraje de la CAINCO. Igualmente, las Partes hacen constar expresamente su compromiso de cumplir el Laudo Arbitral que se dicte, renunciando en la medida permitida por Ley, a cualquier tipo de recurso contra el mismo.</p>
<p>Los costos emergentes del proceso de arbitraje serán asumidos en su totalidad por la parte que resulte perdedora. En caso de que se pudiera llegar a una conciliación antes de emitirse el Laudo Arbitral, los costos en los que se hubieran incurrido serán cubiertos por ambas partes en iguales porcentajes (50%).</p>

<p><b><u>DÉCIMA SÉPTIMA. CONFORMIDAD:</u></b><br>Ambas PARTES, declaramos nuestra plena y absoluta conformidad a todas y cada una de las cláusulas precedentes, manifestando que las mismas corresponden a nuestra fiel y real voluntad no habiendo mediado vicio de consentimiento alguno, firmando al pie en constancia en triple ejemplar.</p>

<p>Santa Cruz, {{ $c['inicio'] }}.</p>

<table class="firmas">
    <tr>
        <td>{{ $t['nombre'] }}<br>C.I - {{ $t['ci_completo'] }}<br><b>PRESTADOR DE SERVICIOS</b></td>
        <td>Luis Fernando Ortíz Reque<br>C.I - 4720436<br><b>CONTRATANTE</b></td>
    </tr>
</table>

{{-- ======================= ADENDA ======================= --}}
<div class="salto"></div>

<p class="titulo"><u>ADENDA DE CONFIDENCIALIDAD Y NO CONTACTO</u> <u>AL CONTRATO DE PRESTACIÓN DE SERVICIOS</u></p>

<p>Conste por la presente Adenda al Contrato de Prestación de Servicios (en adelante, la “Adenda”), que puede elevarse a público a solo reconocimiento de firmas a solicitud de cualesquiera de las partes, de común acuerdo y con plena capacidad, conforme al Art. 519 del Código Civil, la misma que forma parte integrante del Contrato de Prestación de Servicios suscrito entre las partes.</p>

<p><b><u>PRIMERA. DE LAS PARTES:</u></b></p>
<p>Intervienen en la presente Adenda las siguientes partes:</p>

<p class="sangria"><b>1.1. DELTA CARGO S.R.L.</b>, empresa legalmente constituida con Matrícula de Comercio y Número de Identificación Tributaria No. 1025565029, de la ciudad de Santa Cruz de la Sierra, debidamente representada legalmente por el señor <b>LUIS FERNANDO ORTIZ REQUE</b>, con cédula de identidad N° 4720436, según consta por Escritura Pública de Poder No. 439/2025 de 21 de Octubre de 2025, otorgada en la Notaría No. 20 del Distrito Judicial de Santa Cruz, en adelante simplemente denominado “<b>CONTRATANTE</b>”.</p>

<p class="sangria"><b>1.2. <u>{{ $t['nombre'] }}</u></b>, mayor de edad, hábil por derecho, con C.I. N° {{ $t['ci_completo'] }}, denominado en lo posterior como “<b>PRESTADOR DE SERVICIOS</b>”.</p>

<p><b><u>SEGUNDA. OBJETO:</u></b></p>
<p>La presente Adenda tiene por objeto incorporar al Contrato de Prestación de Servicios suscrito entre las partes, obligaciones específicas y reforzadas de confidencialidad, no contacto y protección de información, con el fin de resguardar los intereses comerciales, operativos y reputacionales del <b>CONTRATANTE</b>, tanto durante la vigencia del contrato como con posterioridad a su terminación por cualquier causa.</p>

<p><b><u>TERCERA. CONFIDENCIALIDAD:</u></b></p>
<p>14.1. El <b>PRESTADOR DE SERVICIOS</b> no podrá en ningún momento proporcionar a terceros, directa o indirectamente, por sí o interpósita persona, información confidencial así como aspectos relativos a los sistemas de trabajo, administración, organización y/o seguridad del <b>CONTRATANTE</b> y en general de la documentación, archivos, planes, programas, proyectos, códigos de desactivación de alarmas, códigos computarizados de seguridad, archivos de contabilidad y otros que conozca, se entere o tome conocimiento en virtud de la celebración de este contrato.</p>

<p>14.2. Asimismo siendo que en virtud al presente contrato el <b>PRESTADOR DE SERVICIOS</b>, tendrá la posibilidad de conocer nuevos sistemas de trabajo, métodos, técnicas, descubrimientos, innovaciones, ideas, conceptos u otros, relacionados con la actividad del <b>CONTRATANTE</b>, teniendo además la posibilidad de acceder a documentación, archivos, registros, precios de productos, proveedores y demás material del <b>CONTRATANTE</b> así como a la instalación, manejo y manipulación de diversos softwares, aspectos todos ellos que por estar vinculados a derechos de autor y propiedad intelectual se hace necesario su absoluto resguardo, reserva y confidencialidad, el <b>PRESTADOR DE SERVICIOS</b> se compromete a guardar absoluta reserva y confidencialidad sobre los mismos, obligándose especialmente a no proporcionar, ceder, facilitar, vender, permutar, transferir, donar, obsequiar, copiar u otros, a ninguna persona, naturales o jurídicas, por ningún mecanismo, sea en forma gratuita u onerosa, en el territorio nacional o fuera de éste, ninguno de los elementos descritos, sea en beneficio propio o de terceros, comprometiendo caso contrario a reconocer los daños y perjuicios que pudiera ocasionar con ello al <b>CONTRATANTE</b>.</p>

<p><b><u>CUARTA. NO CONTACTO Y PROTECCIÓN DE INFORMACIÓN POST-TERMINACIÓN:</u></b></p>

<p class="sangria"><b>15.1.</b> Con el objeto de proteger los intereses comerciales, operativos y reputacionales de <b>DELTA CARGO S.R.L</b>, el <b>PRESTADOR DE SERVICIOS</b> se compromete a no contactar directa ni indirectamente a clientes, proveedores, socios comerciales o aliados estratégicos del <b>CONTRATANTE</b> con fines comerciales, de competencia o que puedan generar perjuicio económico, reputacional u operativo, por un periodo de <b>veinticuatro (24) meses</b> computables desde la fecha de terminación del contrato, por cualquier causa.</p>

<p class="sangria"><b>15.2.</b> El <b>PRESTADOR DE SERVICIOS</b> se obliga a no utilizar, compartir, transferir ni divulgar bases de datos, listados de contactos, información de clientes o proveedores, tarifas, condiciones comerciales ni ningún otro dato al que haya tenido acceso en razón del contrato, con personas naturales o jurídicas ajenas al <b>CONTRATANTE</b>, en ningún formato ni medio, incluyendo medios digitales y redes sociales.</p>

<p class="sangria"><b>15.3.</b> El <b>PRESTADOR DE SERVICIOS</b> se compromete a no inducir ni facilitar la desvinculación de colaboradores o prestadores de servicios del <b>CONTRATANTE</b> para beneficio propio o de terceros competidores.</p>

<p class="sangria"><b>15.4.</b> El incumplimiento de cualquiera de las obligaciones establecidas en la presente cláusula facultará al <b>CONTRATANTE</b> a iniciar las acciones civiles y/o penales que correspondan conforme a la legislación boliviana vigente, incluyendo la Ley N° 164 de Telecomunicaciones y Tecnologías de Información, el Código Civil y el Código Penal, así como a reclamar la reparación del daño económico, reputacional y operativo causado, incluyendo lucro cesante y daño emergente.</p>

<p><b><u>QUINTA. VIGENCIA DE LAS DEMÁS ESTIPULACIONES:</u></b></p>
<p>Las demás estipulaciones del Contrato de Prestación de Servicios se mantienen inalteradas y vigentes. La presente Adenda forma parte integrante de dicho contrato y deberá ser interpretada en conjunto con éste conforme al Art. 519 del Código Civil.</p>

<p><b><u>SEXTA. CONFORMIDAD:</u></b></p>
<p>Ambas PARTES, declaramos nuestra plena y absoluta conformidad a todas y cada una de las cláusulas precedentes, manifestando que las mismas corresponden a nuestra fiel y real voluntad no habiendo mediado vicio de consentimiento alguno, firmando al pie en constancia en doble ejemplar. Santa Cruz, {{ $c['inicio'] }}.</p>

<table class="firmas">
    <tr>
        <td>{{ $t['nombre'] }}<br>C.I - {{ $t['ci'] }}<br><b>PRESTADOR DE SERVICIOS</b></td>
        <td>Luis Fernando Ortíz Reque<br>C.I - 4720436<br><b>CONTRATANTE</b></td>
    </tr>
</table>

</div>
@endsection
