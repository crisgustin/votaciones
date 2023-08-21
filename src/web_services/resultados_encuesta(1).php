<?php
require_once dirname(__FILE__) . './../clases/Conector.php';
require_once dirname(__FILE__) . './../clases/Filter.php';
require_once dirname(__FILE__) . './../clases/NivelFormacion.php';
require_once dirname(__FILE__) . './../clases/Programa.php';
require_once dirname(__FILE__) . './../clases/FichaPrograma.php';
require_once dirname(__FILE__) . './../clases/Coordinacion.php';
require_once dirname(__FILE__) . './../clases/RespuestaPregunta.php';
if (isset($_POST['id_encuesta'])) {
    $mensaje = 'Mostrando resultados de todos los programas y fichas en general';
    $array = $preguntas = $respuestas = array();
    $sql_count = "select count(eu.estado) as completas";
    $from_count = 'from encuesta_usuario as eu';
    $where_count = "where eu.estado='F' and eu.id_encuesta = {$_POST['id_encuesta']}";
    $sql = 'select p.id, p.tipo, p.enunciado, ru.id_respuesta, count(ru.id_respuesta) as cantidad, ru.texto';
    $from = 'from pregunta as p, respuesta_usuario as ru, encuesta_usuario as eu';
    $where = "where p.id = ru.id_pregunta and p.id_encuesta = eu.id_encuesta and p.id_encuesta = {$_POST['id_encuesta']} and eu.id = ru.id_encuesta and eu.estado = 'F'";
    $group_by = 'group by ru.id_respuesta, ru.texto';
    $order = 'order by p.id asc';
    if(isset($_POST['id_ficha'])||isset($_POST['id_programa'])||isset($_POST['coord'])||isset($_POST['nivel'])) {
        $from .= ', ficha_usuario as fu, ficha_programa as fp';
        $from_count .= ', ficha_usuario as fu, ficha_programa as fp';
        $where_count .= " and eu.id_ficha = fu.id and fu.id_ficha = fp.id";
        $where .= " and eu.id_ficha = fu.id and fu.id_ficha = fp.id";
        if(isset($_POST['id_ficha'])){
            $ficha = new FichaPrograma('id', $_POST['id_ficha']);
            $mensaje = 'Mostrando resultados de la ficha '.$ficha->getNombre();
            $where .= " and fu.id_ficha = {$_POST['id_ficha']}";
            $where_count .= " and fu.id_ficha = {$_POST['id_ficha']}";
        } elseif(isset($_POST['coord'])) {
            $coord = new Coordinacion('id',$_POST['coord']);
            $mensaje = 'Mostrando resultados de los programas que pertenecen a la coordinación '.$coord->getNombre();
            $where .= " and fp.coordinacion = '{$_POST['coord']}'";
            $where_count .= " and fp.coordinacion = '{$_POST['coord']}'";
        } elseif(isset($_POST['id_programa'])||isset($_POST['nivel'])) {
            $from .= ', programa as pr';
            $from_count .= ', programa as pr';
            $where_count .= " and fp.id_programa = pr.id";
            $where .= " and fp.id_programa = pr.id";
            if(isset($_POST['id_programa'])) {
                $programa = new Programa('id',$_POST['id_programa']);
                $mensaje = 'Mostrando resultados del programa '.$programa->getNombre();
                $where_count .= " and fp.id_programa = {$_POST['id_programa']}";
                $where .= " and fp.id_programa = {$_POST['id_programa']}";
            } else {
                $nivel = new NivelFormacion('id',$_POST['nivel']);
                $mensaje = 'Mostrando resultados de los programas con nivel de formación'.$nivel->getNombre();
                $where_count .= " and pr.nivel = '{$_POST['nivel']}'";
                $where .= " and pr.nivel = '{$_POST['nivel']}'";
            }
        }
    }
    if(isset($_POST['f_inicio'])&&isset($_POST['f_fin'])) {
        $mensaje .= ' desde '.$_POST['f_inicio'].' hasta '.$_POST['f_fin'];
        $where .= " and eu.fecha_presentacion between '{$_POST['f_inicio']}' and '{$_POST['f_fin']}'";
        $where_count .= " and eu.fecha_presentacion between '{$_POST['f_inicio']}' and '{$_POST['f_fin']}'";
    }
    $sql_count = $sql_count.' '.$from_count.' '.$where_count.';';
    $completas = Conector::ejecutarQuery($sql_count)[0]['completas'];
    $sql_count = str_replace(['completas',"'F'"], ['faltantes',"'P'"], $sql_count);
    $faltantes = Conector::ejecutarQuery($sql_count)[0]['faltantes'];
    $sql = $sql.' '.$from.' '.$where.' '.$group_by.' '.$order.';';
    /*
        select p.id, p.tipo, p.enunciado, ru.id_respuesta, count(ru.id_respuesta) as cantidad, ru.texto from pregunta as p, respuesta_usuario as ru, encuesta_usuario as eu, ficha_usuario as fu where p.id = ru.id_pregunta and p.id_encuesta = 1 and eu.id = ru.id_encuesta and eu.id_ficha = fu.id and p.id_encuesta = eu.id_encuesta and eu.estado='F' and fu.id_ficha = 20 group by ru.id_respuesta, ru.texto order by p.id asc;
     *      */
    $resultado = Conector::ejecutarQuery($sql);
    if(is_array($resultado)) {
        $ids = array();
        $i = 0;
        foreach ($resultado as $pregunta) {
            if(!in_array($pregunta['id'],$ids)){
                array_push($ids, $pregunta['id']);
                $pregunta = Filter::clear_array($pregunta,['texto','cantidad','id_respuesta']);
                $pregunta['respuestas'] = array();
                foreach ($resultado as $respuesta) {
                    if($respuesta['id']==$pregunta['id']) {
                        $texto='';
                        if($pregunta['tipo']!='RA') {
                            $obj = new RespuestaPregunta('id', $respuesta['id_respuesta']);
                            $texto = $obj->getTexto();
                        }$respuesta['respuesta'] = $texto;
                        $respuesta = Filter::clear_array($respuesta,['id','enunciado','tipo','id_respuesta']);
                        array_push($pregunta['respuestas'],$respuesta);
                    }
                } array_push($preguntas,$pregunta);
            }
        }
        if(count($preguntas)===0) $mensaje = 'No se encontró ningún resultado';
        $conteo=['faltantes'=>$faltantes,'completas'=>$completas];
        $array['conteo'] = $conteo;
        $array['mensaje'] = $mensaje;
        $array['preguntas'] = $preguntas;
        $array = json_encode($array);
        print_r($array);
    }
}