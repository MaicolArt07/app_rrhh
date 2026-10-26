{{--
    Cuerpo del Contrato Individual de Trabajo por Plazo Indefinido.
    $variante = 'dc' (contratos/Agosto´26 - Contrato Indefinido, DELTA CARGO S.R.L)
              | 'dx' (contratos/Junio´26 - Contrato Indefinido - DX, DELTAEXPRESS SA).
    Las diferencias entre ambos originales se conservan tal cual.
--}}
@php
    $empresa = $variante === 'dc'
        ? ['nombre' => 'DELTA CARGO S.R.L', 'matricula' => '1025565029', 'nit' => '1025565029', 'poder' => '439/2025']
        : ['nombre' => 'DELTAEXPRESS SA', 'matricula' => '406496', 'nit' => '373968026', 'poder' => '440/2025'];
@endphp
<div class="times t12">

<p class="titulo" style="margin-bottom:1em">CONTRATO INDIVIDUAL DE TRABAJO POR PLAZO INDEFINIDO</p>

<p>Se celebra el presente Contrato Individual de Trabajo con carácter indefinido, de conformidad a las condiciones, términos y modalidades que se estipula en sus cláusulas y de acuerdo a las normas de la Ley General del Trabajo, Decreto Reglamentario y disposiciones conexas vigentes.</p>

<p style="margin-bottom:0"><b>PRIMERA: (DE LAS PARTES)</b>. Son partes intervinientes en la celebración del presente contrato, las siguientes:</p>
<p><b>1.- {{ $empresa['nombre'] }}</b>, empresa legalmente constituida de acuerdo con las leyes del Estado Plurinacional de Bolivia, con Matrícula de Comercio No. {{ $empresa['matricula'] }}, NIT. No. {{ $empresa['nit'] }}, con domicilio legal en la ciudad de Santa Cruz de la Sierra, Estado Plurinacional de Bolivia y dirección comercial sobre la Nueva Carretera Warnes Urbanización Trapero, Zona Nor Este, <span class="t11">representada en el presente acto por el Sr. <b>LUIS FERNANDO ORTIZ REQUE,</b> con Cédula de Identidad No. 4720436 Santa Cruz, en su condición de Representante Legal según Poder General de Administración No. {{ $empresa['poder'] }}, otorgado por ante la Notaría de Fe Pública de Primera Clase No. 20 del Distrito Judicial de Santa Cruz, a cargo de la Notaria Dra. Carmen Jacqueline Pereyra Saucedo, en lo sucesivo y a los fines del presente contrato se denominará simplemente como el EMPLEADOR.</span></p>

<p class="t11"><b>2.- {{ $t['nombre_mayus'] }}</b> con CI {{ $t['ci_completo'] }}, estado civil {{ $t['estado_civil'] }}, profesional en {{ $t['profesion'] }} y residencia en {{ $t['domicilio'] }}, de esta ciudad, que en adelante se denominará como EL TRABAJADOR.</p>

<p><b>SEGUNDA: (OBJETO).</b> EL EMPLEADOR conviene en contratar a EL TRABAJADOR, para que preste sus servicios como <b>{{ $c['cargo'] }},</b> comprometiéndose EL TRABAJADOR a cumplir con todas las responsabilidades inherentes a este cargo, conforme a las directrices que reciba del EMPLEADOR y de los niveles jerárquicos, ajustándose a toda norma legal aplicable a esta relación laboral. EL TRABAJADOR, desempeñará sus funciones con dependencia directa del {{ $c['dependencia'] }}, así como de los funcionarios intermedios que ésta le señale. Así mismo EL TRABAJADOR desempeñará sus funciones en la sucursal central de la empresa ubicada en la ciudad de Santa Cruz de la Sierra, Nueva Carretera a Warnes Urbanización Trapero, Zona Nor Este.</p>

<p><b>TERCERA: (NATURALEZA DEL CONTRATO).-</b> La naturaleza jurídica del presente contrato, es de carácter estrictamente laboral, regulado por los artículos 5 y 6 de la Ley General del Trabajo, concordante con los Artículos 5, 6, y 7 de su Decreto Reglamentario y demás disposiciones vigentes sobre la materia.</p>

<p><b>CUARTA: (PLAZO).</b> El presente contrato es suscrito a plazo indefinido, por lo cual se establece una duración permanente en tanto exista la relación laboral entre el EMPLEADOR y EL TRABAJADOR, cuya extinción se dará únicamente en caso de existir acuerdo entre las partes, renuncia del trabajador, o por las causales de despidos establecida en la Ley General del Trabajo.</p>

<p><b>QUINTA: (PERIODO DE PRUEBA).</b> Las partes contratantes acuerdan que los tres primeros meses de trabajo, a partir de la fecha de vigencia de este Contrato, vale decir, a partir del @if ($variante === 'dc')<b>{{ $c['inicio'] }},</b>@else{{ $c['inicio'] }},@endif serán considerados como periodo de prueba, conforme a lo dispuesto por el Artículo 13 de la Ley General del Trabajo.</p>

<p style="margin-bottom:0"><b>SEXTA: (SALARIO). -</b> EL TRABAJADOR, como retribución a su trabajo recibirá un salario mensual de <b>Bs. {{ $c['monto'] }}.- ({{ $c['literal_mayus'] }} CON {{ $c['centavos'] }}/100 BOLIVIANOS)</b>, monto al cual se le deducirán las retenciones, aportes e impuestos conforme a Ley.</p>
<p>Este monto será pagado ya sea mediante depósito en la cuenta bancaria que EL TRABAJADOR designe o por cualquier otro medio acordado entre las Partes.</p>

@if ($variante === 'dc')
<p><b>SÉPTIMA: (EXCLUSIÓN DE OTRAS ACTIVIDADES).</b> EL TRABAJADOR no podrá realizar trabajos con empresas relacionadas al transporte de carga o al desarrollo o comercialización de software similar o competencia del ofertado por la empresa</p>
@else
<p><b>SÉPTIMA: (EXCLUSIÓN DE OTRAS ACTIVIDADES).</b> EL TRABAJADOR, no podrá dedicarse a ocupaciones iguales o distintas a las que se obliga por este contrato y que sean ajenas a las actividades del empleador durante el tiempo que trabaje en la empresa.</p>
@endif

<p><b>OCTAVA: (JORNADA LABORAL).</b> La Jornada efectiva de trabajo se someterá a todo lo estipulado en la Ley General del Trabajo, particularmente en sus artículos 46 y siguientes, la cual será no excederá de 8 horas por día y de 48 horas por semana para el caso de los hombres y 40 horas por semana para el caso de las mujeres, es así que se cita el sgte. horario de trabajo: de <b>LUNES A VIERNES 8:00 AM A 16:00 PM</b></p>

<p><b>NOVENA: (PROCEDIMIENTOS, POLÍTICAS Y NORMAS DE LA EMPRESA)</b> EL TRABAJADOR declara conocer los procedimientos, políticas y normas de LA EMPRESA, haberlas leído y comprendido en su totalidad. En consecuencia, EL TRABAJADOR acepta someterse al cumplimiento de estas políticas y normas.</p>

<p><b>DÉCIMA: (RESPONSABILIDADES). -</b> EL TRABAJADOR, asume la responsabilidad de cumplir con las tareas y responsabilidades especificadas para este cargo en el respectivo Manual de Funciones, garantizando un buen desempeño en la empresa y el cumplimiento de todas las normas y reglamentaciones de la misma. Para dicho fin, el Manual de Funciones, forma parte del presente contrato, así como las Comunicaciones Internas y otros instrumentos de comunicación y organización de funciones propias de la empresa.</p>

<p style="margin-bottom:0"><b>Las principales funciones del puesto serán:</b></p>
<ul>
    @foreach ($c['funciones'] as $funcion)
        <li>{{ $funcion['texto'] }}
            @if ($funcion['sub'])
                <ul class="sub">
                    @foreach ($funcion['sub'] as $sub)
                        <li>{{ $sub }}</li>
                    @endforeach
                </ul>
            @endif
        </li>
    @endforeach
</ul>

<p style="margin-bottom:0.6em"><b>DÉCIMA PRIMERA: (RESPONSABILIDADES ADICIONALES DEL TRABAJADOR).</b> EL TRABAJADOR se compromete a:</p>
<ol>
    <li>Trabajar con eficiencia, puntualidad, responsabilidad, honradez, esmero y lealtad, obedeciendo las instrucciones de la Gerencia General y de los niveles jerárquicos correspondientes.</li>
    <li>Cumplir las normas disciplinarias y disposiciones de trabajo determinadas por la organización.</li>
    <li>Observar buena conducta moral adecuada al trabajo.</li>
    <li>Respetar a sus superiores y compañeros de trabajo.</li>
    <li>Involucrarse en todos los temas relacionados a su área, sin perjuicio de otros ajenos al área que les sean solicitados. La cultura de trabajo de nuestra empresa requiere el máximo compromiso de parte de todos nuestros trabajadores, independiente del cargo, jerarquía o experiencia.</li>
</ol>

<p style="margin-bottom:0.6em"><b>DÉCIMA SEGUNDA: (OBLIGACIONES LABORALES)</b> Son obligaciones de EL TRABAJADOR, desde el momento de su ingreso hasta la terminación de su relación contractual con la empresa, de manera enunciativa y no limitativa:</p>
<ol>
    <li>Conocer y cumplir todas las normas e instrucciones relacionadas con la organización y funcionamiento de la empresa.</li>
    <li>Prestar su servicio en el lugar y puesto de trabajo que se le asigne, debiendo hacerlo con responsabilidad, obedeciendo instrucciones, eficiencia, lealtad, esmero y dedicación en procura de un adecuado desempeño.</li>
    <li>Acatar y cumplir asignaciones, cambios de responsabilidades, órdenes de transferencia de una sección a otra, así como también los cambios de horario que la empresa estime conveniente para su operación.</li>
    <li>Conservar los equipos de trabajo, materiales, insumos, e implementos que estén a su cargo, así como preservar cuidadosamente los documentos, registros de todo tipo, archivos y cualquier otra pertenencia, según normas y procedimientos que establezca la empresa.</li>
    <li>Guardar respeto y consideración a sus superiores y colegas de trabajo, observando siempre una conducta y comportamiento encuadrados dentro de la moral y las buenas costumbres.</li>
    <li>Puntualidad en la asistencia cumpliendo estrictamente los horarios asignados, roles y turnos de trabajo establecidos, así como registrar diariamente sus horarios de ingreso y salida.</li>
    <li>Comunicar inmediatamente a sus superiores cualquier desperfecto o mal funcionamiento de los equipos que estén bajo su manejo y responsabilidad.</li>
    <li>Cuidar los bienes y enseres que la empresa le facilite para el desempeño de sus funciones, siendo responsable por toda pérdida o daño a los bienes que la empresa le entregue en custodia, quedando obligada al resarcimiento del daño ocasionado por dolo, culpa o negligencia que le sean imputables.</li>
    <li>Asistir a los cursos de capacitación, actualización y entrenamiento para los que fuere convocada.</li>
    <li>Guardar absoluta reserva sobre aspectos relacionados a los sistemas de trabajo, administración y organización de la empresa y no divulgar información confidencial de ninguna naturaleza o secretos relativos a la seguridad de la empresa, que le fueren confiados en razón a su cargo, tanto a personas externas a la empresa, como a compañeros de trabajo que no estuvieran autorizados a conocerla.</li>
    <li>Dar a los clientes excelente atención, respeto, cortesía y amabilidad.</li>
    <li>Observar con absoluta rigurosidad el aseo e higiene personal, así como mantener el orden y limpieza de su área de trabajo.</li>
    <li>Comunicar a Recursos Humanos cualquier cambio de domicilio o número telefónico y cualquier información personal relevante, inmediatamente se produzcan estos cambios.</li>
    <li>Orientar, entrenar y enseñar al personal bajo su dependencia, para el mejor desempeño de sus funciones.</li>
    <li>Portar su licencia de conducir y mantenerla vigente al conducir o emplear vehículos de la empresa.</li>
    <li>Cumplir las instrucciones emanadas de la Gerencia General, Gerencia de área e inmediato superior.</li>
    <li>De acuerdo a lo dispuesto por la ley 1599 de 18 de octubre de 1994 todo trabajador(a) que sufra acoso sexual en su fuente de trabajo, debe denunciar tal hecho a su Supervisor, Gerencia de área o Recursos Humanos, para que se adopten las acciones legales correspondientes.</li>
    <li>Reportar todas las faltas e incumplimientos a las normas y procedimientos de la Empresa, que sean de su conocimiento.</li>
</ol>

<p><b>DÉCIMA TERCERA: (CONFIDENCIALIDAD Y USO DE LA INFORMACIÓN)</b>: EL TRABAJADOR se compromete a guardar absoluta reserva y no divulgar a terceros ninguna <b>Información Confidencial</b> de la EMPRESA, sea técnica o comercial, a la que acceda durante la vigencia del contrato. Esta incluye, sin limitarse, datos sobre costos, precios, clientes, proveedores, salarios, estrategias comerciales, software, métodos, procesos y activos digitales. EL TRABAJADOR utilizará dicha información <b>únicamente</b> en la medida necesaria para proporcionar los servicios u objetivos de la EMPRESA , y se obliga a tomar todas las medidas necesarias para preservar los activos digitales a su cargo, evitando la fuga de información.</p>

<p><b>DÉCIMA CUARTA: (SANCIONES).-</b> Si EL TRABAJADOR no llega a cumplir con lo anteriormente mencionado, estará sujeto a recibir las siguientes sanciones:<br>El exponer información confidencial de la empresa, se considera una falta grave, se cuantificará el daño ocasionado a la empresa tomando en cuenta diferentes factores, como tiempo invertido, imagen comercial. Dependiendo de la falta puede derivar en una llamada de atención en el file hasta la desvinculación laboral.</p>

<p style="margin-bottom:0.6em"><b>DÉCIMA QUINTA: (PROHIBICIONES)</b> Se prohíbe a EL TRABAJADOR, desde el momento de su ingreso hasta la terminación de su relación contractual con la empresa, de manera enunciativa y no limitativa:</p>
<ol>
    <li>Presentarse al trabajo en estado inconveniente para realizar sus labores, lo cual será considerado como inasistencia injustificada.</li>
    <li>Internar elementos prohibidos en las instalaciones y dependencias de la empresa.</li>
    <li>Acosar o agredir física o verbalmente a colegas o incitar a la agresión en cualquier instalación de la empresa.</li>
    <li>Abandonar el puesto de trabajo sin autorización del superior inmediato.</li>
    <li>Sustraer documentos, herramientas, materiales u otros objetos de la empresa sin la debida autorización.</li>
    @if ($variante === 'dc')
        <li>Realizar trabajos ajenos a sus responsabilidades dentro del horario laboral si son incompatibles con la relación laboral, en particular en empresas relacionadas al transporte de carga o al desarrollo o comercialización de software similar o competencia del ofertado por la empresa</li>
    @else
        <li>Realizar trabajos ajenos a sus responsabilidades dentro del horario laboral o fuera del horario si son incompatibles con la relación laboral.</li>
    @endif
    <li>Distraerse en labores no relacionadas con el trabajo durante el horario laboral.</li>
    <li>Presentarse al trabajo con enfermedades infectocontagiosas, lo cual se considerará una falta grave.</li>
    <li>Realizar el trabajo sin compromiso, incitar a la desobediencia o revelar secretos de trabajo que comprometan la eficiencia o calidad de los productos de la empresa.</li>
    <li>Realizar proselitismo político o religioso dentro de la empresa.</li>
    <li>Permitir el ingreso de personas ajenas a la empresa sin la debida autorización de su superior, supervisor o la Gerencia General.</li>
    <li>Alterar el control de asistencia, incluyendo marcar entradas o salidas de otros trabajadores.</li>
    <li>Conducir o usar vehículos de la empresa sin autorización o para fines no autorizados, o sin portar la licencia correspondiente.</li>
    <li>Transportar personas no autorizadas en vehículos de la empresa.</li>
    <li>Realizar actividades que pongan en riesgo la seguridad propia, de los compañeros o de la empresa.</li>
    <li>Usar indebidamente materiales, herramientas o equipos de la empresa para beneficio personal o de terceros, salvo autorización.</li>
    <li>Hacer uso indebido de medios de comunicación y equipos informáticos, descargar material no autorizado o instalar software sin autorización.</li>
    <li>Revelar información confidencial o aspectos relacionados con sistemas de trabajo, administración, organización o seguridad de la empresa.</li>
    <li>Incurrir en acoso sexual, conforme a la Convención Interamericana para Prevenir, Sancionar y Erradicar la Violencia contra la Mujer.</li>
    <li>Encubrir faltas o incumplimientos a las normas y procedimientos de la empresa.</li>
</ol>

<p>Se prohíbe todo acto de racismo y toda forma de discriminación ya sea de parte del empleado y/o empleador, de conformidad a la Ley No. 045 del 08 de octubre de 2010. Así también, de acuerdo a lo dispuesto por la Ley 1599 de 18 de octubre de 1994 todo trabajador(a) que sufra acoso sexual en su fuente de trabajo, debe denunciar tal hecho a su Supervisor, Gerencia de área o Recursos Humanos, para que se adopten las acciones legales correspondientes.</p>

<p><b>DÉCIMA SEXTA: (VACACIONES).</b> EL TRABAJADOR tendrá derecho a vacaciones anuales de acuerdo a Ley. La empresa elaborará anualmente los turnos de vacaciones, y cualquier solicitud formulada por EL TRABAJADOR dependerá para su aceptación de los requerimientos operacionales y necesidades de LA EMPRESA.</p>

<p style="margin-bottom:0"><b>DÉCIMA SÉPTIMA: (TERMINACIÓN).-</b> LA EMPRESA podrá dar por terminado el presente contrato de trabajo sin derecho a desahucio ni indemnización por tiempo de servicios, cuando EL TRABAJADOR incurra en alguna de las causales establecidas en el Artículo 16 de la Ley General del Trabajo y el Artículo 9 de su Decreto Reglamentario.</p>
<p style="margin-bottom:0">Asimismo, en aplicación del Decreto Supremo Nº 28699, durante el periodo de prueba la empresa podrá rescindir unilateralmente este contrato si el desempeño de EL TRABAJADOR no satisface las necesidades de la empresa.</p>
<p>Fuera de este periodo, la terminación del contrato deberá sujetarse a las disposiciones establecidas por la normativa laboral vigente.</p>

<p><b>DÉCIMA OCTAVA: (HEREDEROS).</b> Para efectos legales y dando cumplimiento a lo establecido en el Artículo 7 del Decreto Reglamentario a la Ley General del Trabajo, EL TRABAJADOR reconoce y declara que en caso de fallecimiento, sus herederos son:</p>
@foreach ($c['herederos'] as $heredero)
    <p style="margin:0 0 {{ $loop->last ? '1em' : '0.3em' }} 18pt">-&nbsp;&nbsp;&nbsp;&nbsp;{{ $heredero }}</p>
@endforeach

<p>En este sentido EL TRABAJADOR reconoce y acepta que previo el cumplimiento de las formalidades y obligaciones legales correspondientes, la empresa podrá pagar sus beneficios que le pudieran corresponder a sus herederos indicados anteriormente, salvo disposición contraria de autoridad judicial competente mediante sentencia ejecutoriada.</p>

<p><b>DÉCIMA NOVENA: (ACEPTACIÓN).</b> Las partes que suscriben el presente contrato se someten a su fiel y estricto cumplimiento, en señal de lo cual lo suscriben en tres ejemplares de igual valor legal, mismo que será refrendado por la Inspectoría de Trabajo.</p>

<p class="centro" style="margin-top:1.5em">Santa Cruz, {{ $c['inicio'] }}</p>

<table class="firmas">
    <tr>
        <td>{{ $t['nombre'] }}<br><b>TRABAJADOR</b></td>
        <td>Luis Fernando Ortiz Reque<br><b>REPRESENTANTE LEGAL</b><br><b>{{ $empresa['nombre'] }}</b></td>
    </tr>
</table>

</div>
