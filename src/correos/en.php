<?php
require_once dirname(__FILE__).'/../clases/Conector.php';
require_once dirname(__FILE__).'/../clases/Mailer.php';
$sql = "select u.usuario, u.nombres, u.apellidos, u.telefono, u.correo, fp.ficha, concat(p.nivel,' en ',p.nombre) as formacion from usuario as u, ficha_usuario as fu, encuesta_usuario as eu, ficha_programa as fp, programa as p where u.id=fu.id_usuario and eu.id_ficha = fu.id and eu.estado = 'P' and month(eu.fecha_asignacion)>6 and fp.id = fu.id_ficha and fp.id_programa = p.id and length(u.correo)>4  group by u.correo order by fp.ficha asc limit 40 offset 0;";
$usuarios = Conector::ejecutarQuery($sql);
$mail = new Mailer();
$mail->setAsunto('Encuesta de satisfacción proceso de formación profesional integral');
$mensaje = '<p>Cordial saludo</p><p>Estimados aprendices</p><br><p style="text-align:justify">Por medio de este correo el Grupo de Gestión de Formación Profesional Integral del Centro Internacional de Producción Limpia -  Lope les informa que como ustedes ya están a punto de culminar su etapa lectiva, les hemos asignado la tarea de responder la siguiente encuesta: <strong>Encuesta de satisfacción proceso de formación profesional integral</strong>, que tiene como objetivo principal conocer las opiniones de los aprendices sobre la formación técnica y áreas transversales (habilidades blandas) en todo su proceso formativo.</p><p style="text-align:justify">Dicha encuesta se encuentra disponible en el <a href="https://www.desarrolloslope.com/encuestas" target="_blank">sistema de información de encuestas</a> donde cada aprendiz deberá iniciar sesión y proceder a dar solución a dicha encuesta. Si no conocen sus credenciales de acceso, pueden acceder a <a href="https://desarrolloslope.com/encuestas/?contenido=src/identidad.php" target="_blank">este link</a> para verificar su identidad.</p><p style="text-align:justify">Le sugerimos que cada uno realice esta tarea lo más pronto posible, ya que es un requisito para finalizar su etapa lectiva.</p><p style="text-align:justify">Si tiene alguna duda sobre este tema o tiene inconvenientes con el aplicativo, por favor comuníquese al correo misionallope@misena.edu.co</p><br><p>Agradecemos su atención.</p>';
$html = '<table>';
$html .= '<thead><tr><th>Usuario</th><th>Nombres</th><th>Apellidos</th><th>Teléfono</th><th>Correo electrónico</th><th>Ficha</th><th>Programa de formación</th></tr></thead>';
$html .= '<tbody>';
$mail->setMensaje($mensaje,true);
foreach ($usuarios as $u) {
    $html .= '<tr>';
    $html .= '<td>'.$u['usuario'].'</td>';
    $html .= '<td>'.$u['nombres'].'</td>';
    $html .= '<td>'.$u['apellidos'].'</td>';
    $html .= '<td>'.$u['telefono'].'</td>';
    $html .= '<td>'.$u['correo'].'</td>';
    $html .= '<td>'.$u['ficha'].'</td>';
    $html .= '<td>'.$u['formacion'].'</td>';
    $html .= '</tr>';
    $mail->addEmail($u['correo']);
}
$html .= '</tbody></table>';
$mail->addCopia('luna.juandavid95@gmail.com','BCC');
$mail->enviar();
