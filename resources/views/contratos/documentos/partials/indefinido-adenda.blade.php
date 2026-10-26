{{--
    Adenda de confidencialidad, protección de información y no contacto al contrato indefinido.
    $variante = 'dc' (Times 10 pt, como el original de DELTA CARGO S.R.L) | 'dx' (Poppins 9 pt, DELTAEXPRESS S.A.).
--}}
<div class="salto"></div>
<div class="{{ $variante === 'dc' ? 'times t10' : 'poppins p9' }}">

<p class="titulo" style="margin-bottom:1.4em">ADENDA DE CONFIDENCIALIDAD, PROTECCIÓN DE INFORMACIÓN Y NO CONTACTO AL CONTRATO INDIVIDUAL DE TRABAJO POR TIEMPO INDEFINIDO</p>

<p>Conste por la presente Adenda al Contrato Individual de Trabajo por Tiempo Indefinido (en adelante, la "Adenda"), que forma parte integrante del contrato laboral suscrito entre las partes, celebrado al amparo de la Ley General del Trabajo, su Decreto Reglamentario y demás disposiciones laborales vigentes del Estado Plurinacional de Bolivia.</p>

<p style="margin-bottom:0"><b>PRIMERA. DE LAS PARTES</b><br>Intervienen en la presente Adenda las siguientes partes:</p>
@if ($variante === 'dc')
<p><b>1.1- DELTA CARGO S.R.L,</b> empresa legalmente constituida de acuerdo con las leyes del Estado Plurinacional de Bolivia, con Matrícula de Comercio No. 1025565029, NIT. No. 1025565029, con domicilio legal en la ciudad de Santa Cruz de la Sierra, Estado Plurinacional de Bolivia y dirección comercial sobre la Nueva Carretera Warnes Urbanización Trapero, Zona Nor Este, representada en el presente acto por el Sr. <b>LUIS FERNANDO ORTIZ REQUE,</b> con Cédula de Identidad No. 4720436 Santa Cruz, en su condición de Representante Legal según Poder General de Administración No. 439/2025, otorgado por ante la Notaría de Fe Pública de Primera Clase No. 20 del Distrito Judicial de Santa Cruz, a cargo de la Notaria Dra. Carmen Jacqueline Pereyra Saucedo, en lo sucesivo y a los fines del presente contrato se denominará simplemente como el EMPLEADOR.</p>

<p><b>1.2. {{ $t['nombre'] }},</b> mayor de edad, hábil por derecho, con Cédula de Identidad N° {{ $t['ci_completo'] }}, con {{ $t['domicilio'] }}, quien en adelante se denominará <b>"EL TRABAJADOR"</b>.</p>
@else
<p><b>1.1. DELTAEXPRESS S.A.</b>, empresa legalmente constituida conforme a las leyes del Estado Plurinacional de Bolivia, con Matrícula de Comercio N° 406496, NIT N° 373968026, con domicilio legal en la ciudad de Santa Cruz de la Sierra, representada legalmente por el señor <b>Luis Fernando Ortíz Reque</b>, con Cédula de Identidad N° 4720436 Santa Cruz, en su calidad de Representante Legal, en adelante denominada simplemente <b>"EL EMPLEADOR"</b>.</p>

<p><b>1.2. {{ $t['nombre'] }},</b> mayor de edad, hábil por derecho, con Cédula de Identidad N° {{ $t['ci_completo'] }}, con domicilio en {{ $t['domicilio'] }}, quien en adelante se denominará <b>"EL TRABAJADOR"</b>.</p>
@endif

<p style="margin-bottom:0"><b>SEGUNDA. OBJETO</b></p>
<p>La presente Adenda tiene por objeto incorporar al Contrato Individual de Trabajo obligaciones específicas y reforzadas de confidencialidad, protección de información y no contacto, con el fin de resguardar los intereses comerciales, operativos, tecnológicos y reputacionales del EMPLEADOR, tanto durante la vigencia de la relación laboral como con posterioridad a su terminación por cualquier causa.</p>

<p style="margin-bottom:0"><b>TERCERA. DEFINICIONES</b><br>Para efectos de la presente Adenda se entenderá por:</p>
<p><b>"Información Confidencial":</b> toda información técnica, comercial, financiera, estratégica o de cualquier otra naturaleza a la que EL TRABAJADOR tenga acceso durante la relación laboral, incluyendo sin limitación: sistemas de trabajo, métodos, procesos internos, documentación, archivos, registros, listas de clientes y proveedores, contratos, precios, tarifas, márgenes comerciales, rutas logísticas, estrategias comerciales, software, bases de datos, información financiera, credenciales de acceso, claves de API, variables de entorno, tokens de autenticación, secretos de configuración, modelos de negocio, información relacionada con la plataforma SaaS y cualquier otro material cuya divulgación pueda afectar los intereses del EMPLEADOR.</p>

<p><b>"Credenciales y Secretos de Sistema":</b> claves de acceso, contraseñas, tokens, certificados digitales, variables de entorno y cualquier otro mecanismo de autenticación o configuración utilizado para acceder, administrar o modificar los sistemas del EMPLEADOR.</p>

<p><b>"Código Propietario":</b> todo código fuente, scripts, módulos, librerías, configuraciones, desarrollos, mejoras o adaptaciones creadas por EL/LA TRABAJADOR/A en cumplimiento de sus funciones laborales o utilizando recursos, infraestructura, información o herramientas del EMPLEADOR.</p>

<p style="margin-bottom:0"><b>CUARTA. CONFIDENCIALIDAD Y PROTECCIÓN DE INFORMACIÓN</b></p>
<p><b>4.1.</b> EL TRABAJADOR no podrá revelar, divulgar, facilitar, proporcionar ni poner a disposición de terceros, directa o indirectamente, Información Confidencial del EMPLEADOR durante la vigencia de la relación laboral ni con posterioridad a su terminación. Esta obligación comprende toda la información física o digital a la que acceda en razón de sus funciones.</p>

<p><b>4.2.</b> EL TRABAJADOR se obliga especialmente a no proporcionar, ceder, facilitar, vender, permutar, transferir, copiar ni divulgar, por ningún mecanismo, de forma gratuita u onerosa, en el territorio nacional o fuera de éste, ninguno de los elementos descritos, sea en beneficio propio o de terceros. El incumplimiento de esta obligación generará la responsabilidad de reconocer todos los daños y perjuicios ocasionados al <b>EMPLEADOR</b>.</p>

<p><b>4.3</b>. EL TRABAJADOR adoptará todas las medidas técnicas y organizativas necesarias para proteger la Información Confidencial, incluyendo: a) No almacenar información en dispositivos personales o no autorizados. b) Acceder únicamente a la información necesaria para el desempeño de sus funciones. c) Reportar inmediatamente cualquier incidente, brecha o acceso no autorizado y, en ningún caso, después de transcurridas veinticuatro (24) horas desde que tenga conocimiento del hecho. d) No realizar copias, respaldos o exportaciones sin autorización expresa del EMPLEADOR. e) Cumplir con todas las políticas internas de seguridad de la información, incluyendo aquellas implementadas para el cumplimiento de controles bajo certificaciones SOC 1 Tipo II u otras equivalentes.</p>

<p style="margin-bottom:0"><b>QUINTA. CREDENCIALES DE SISTEMA Y PROPIEDAD INTELECTUAL</b></p>
<p><b>5.1.</b> Las Credenciales y Secretos de Sistema son de uso exclusivo para el desempeño de las funciones laborales. Queda expresamente prohibido, compartir credenciales, almacenarlas en texto plano o repositorios no autorizados, reutilizarlas en otros proyectos, o conservar copias una vez concluida la relación contractual. Al concluir la relación laboral EL TRABAJADOR deberá devolver inmediatamente toda la Información Confidencial, equipos, documentos, dispositivos, credenciales y cualquier activo físico o digital perteneciente al EMPLEADOR.</p>

<p><b>5.2.</b> Todo desarrollo, software, documentación técnica, adaptación, mejora, configuración o modificación creada por EL TRABAJADOR dentro del ejercicio de sus funciones laborales o utilizando recursos del EMPLEADOR será de propiedad exclusiva de éste, conforme a la Ley N° 1322 de Derechos de Autor y el Convenio de Berna. EL TRABAJADOR reconoce que los derechos patrimoniales sobre dichos desarrollos corresponden exclusivamente al EMPLEADOR, comprometiéndose a no reutilizarlos ni explotarlos para beneficio propio o de terceros.</p>

<p style="margin-bottom:0"><b>SEXTA. NO CONTACTO POSTERIOR A LA TERMINACIÓN DE LA RELACIÓN LABORAL</b></p>
<p>Durante un periodo de doce (12) meses computables desde la terminación de la relación laboral, cualquiera sea la causa, EL TRABAJADOR se compromete: a) No contactar directa ni indirectamente a clientes, proveedores, socios comerciales o aliados estratégicos del EMPLEADOR con fines comerciales que impliquen el aprovechamiento de la Información Confidencial obtenida durante la relación laboral o que puedan ocasionar perjuicio económico, operativo o reputacional. b) No utilizar bases de datos, listas de clientes, proveedores, tarifas, contratos, estrategias comerciales o cualquier otra Información Confidencial obtenida durante la relación laboral. c) No inducir ni facilitar la desvinculación de trabajadores o colaboradores del EMPLEADOR para beneficio propio o de terceros competidores. Las obligaciones previstas en esta cláusula no constituyen una prohibición general para que EL TRABAJADOR ejerza su profesión u oficio, sino únicamente una restricción respecto del uso de Información Confidencial y de las relaciones comerciales desarrolladas por el EMPLEADOR.</p>

<p style="margin-bottom:0"><b>SÉPTIMA. MARCO LEGAL APLICABLE</b><br>La presente Adenda se regirá por:</p>
<ul>
    <li>Ley General del Trabajo.</li>
    <li>Decreto Reglamentario de la Ley General del Trabajo.</li>
    <li>Ley N° 1322 de Derechos de Autor.</li>
    <li>Ley N° 164 de Telecomunicaciones y Tecnologías de Información.</li>
    <li>Código Civil Boliviano, en lo aplicable.</li>
    <li>Código Penal Boliviano.</li>
    <li>Convenio de Berna para la Protección de las Obras Literarias y Artísticas.</li>
    <li>Acuerdo ADPIC/TRIPS de la Organización Mundial del Comercio, en materia de secretos comerciales.</li>
</ul>

<p style="margin-bottom:0"><b>OCTAVA. CONSECUENCIAS DEL INCUMPLIMIENTO</b></p>
<p><b>8.1.</b> El incumplimiento de cualquiera de las obligaciones previstas en la presente Adenda constituirá una falta grave durante la relación laboral y facultará al EMPLEADOR a aplicar las medidas disciplinarias y laborales permitidas por la normativa vigente, sin perjuicio de iniciar las acciones civiles y/o penales que correspondan para reclamar los daños y perjuicios ocasionados, incluyendo daño emergente y lucro cesante cuando legalmente corresponda.</p>

<p><b>8.2.</b> Las obligaciones de confidencialidad, protección de Información Confidencial, propiedad intelectual y restricciones respecto del uso de información reservada subsistirán aun después de terminada la relación laboral mientras dicha información mantenga el carácter de confidencial o constituya secreto comercial.</p>

<p style="margin-bottom:0"><b>NOVENA. VIGENCIA DE LAS DEMÁS ESTIPULACIONES</b><br>Las demás estipulaciones del Contrato Individual de Trabajo permanecen inalteradas y plenamente vigentes.</p>
<p>La presente Adenda forma parte integrante del contrato laboral y deberá interpretarse conjuntamente con éste.</p>

<p style="margin-bottom:0"><b>DÉCIMA. CONFORMIDAD</b></p>
<p>Las partes manifiestan su plena conformidad con el contenido de la presente Adenda, declarando que su suscripción responde a su libre y expresa voluntad, sin que medie error, dolo, violencia o cualquier otro vicio del consentimiento, firmando en dos ejemplares de un mismo tenor para constancia.</p>

<p style="margin-top:2em">Santa Cruz, {{ $c['inicio_de'] }}.</p>

<table class="firmas">
    <tr>
        <td>{{ $t['nombre'] }}<br>C.I - {{ $t['ci_completo'] }}<br><b>EL TRABAJADOR</b></td>
        <td>Luis Fernando Ortíz Reque<br>C.I - 4720436 SC<br><b>Representante Legal</b><br><b>{{ $variante === 'dc' ? 'Delta Cargo S.R.L' : 'Deltaexpress S.A' }}</b></td>
    </tr>
</table>
</div>
