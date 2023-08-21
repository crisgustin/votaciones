<?php
require_once '../clases/Conector.php';
require_once '../clases/Programa.php';
require_once '../clases/FichaPrograma.php';
require_once '../clases/NivelFormacion.php';
$programas=$fichas=array();
if(isset($_POST['texto'])) {
    $texto = $_POST['texto'];
    if(is_numeric($texto)) {
        $filtro = " ficha like '%$texto%' limit 10";
        $datos = FichaPrograma::getListaEnObjetos($filtro);
        $programa = new Programa(null, null);
        $i=0;
        foreach ($datos as $ficha) {
            $fichas[$i]['id'] = $ficha->getId();
            $fichas[$i]['ficha'] = $ficha->getFicha();
            $fichas[$i]['fecha_inicio'] = $ficha->getFecha_inicio();
            $fichas[$i]['fecha_fin'] = $ficha->getFecha_fin();
            $fichas[$i]['fin_lectiva'] = $ficha->getFin_lectiva();
            $fichas[$i]['gestor'] = $ficha->getGestor();
            $fichas[$i]['correo'] = $ficha->getCorreo();
            if($programa!=$ficha->getPrograma()){
                $programa = $ficha->getPrograma();
                $objeto = array();
                $objeto['id']=$programa->getId();
                $objeto['nivel']=$programa->getNivel()->getNombre();
                $objeto['nombre']=$programa->getNombre();
                $objeto['descripcion']=$programa->getDescripcion();
                $objeto['fichas'] = $fichas;
                array_push($programas,$objeto);
                $fichas=array();
            }
            $i++;
        }
    } else {
        //$nivel = isset($_POST['nivel'])?"nivel = '{$_POST['nivel']}'":'';
        $texto = strtolower($texto);
        $filtro = "lower(nombre) like '%$texto%' order by nombre asc limit 10";
        $datos = Programa::getListaEnObjetos($filtro);
        foreach ($datos as $programa) {
            $objeto = array();
            $objeto['id'] = $programa->getId();
            $objeto['nivel']=$programa->getNivel()->getNombre();
            $objeto['nombre']=$programa->getNombre();
            $objeto['descripcion']=$programa->getDescripcion();
            $fichas = array();
            $datos_fichas = FichaPrograma::getListaEnObjetos("id_programa = {$programa->getId()}");
            foreach ($datos_fichas as $ficha) {
                $objeto_ficha = array();
                $objeto_ficha['id'] = $ficha->getId();
                $objeto_ficha['ficha'] = $ficha->getFicha();
                $objeto_ficha['fecha_inicio'] = $ficha->getFecha_inicio();
                $objeto_ficha['fecha_fin'] = $ficha->getFecha_fin();
                $objeto_ficha['fin_lectiva'] = $ficha->getFin_lectiva();
                $objeto_ficha['gestor'] = $ficha->getGestor();
                $objeto_ficha['correo'] = $ficha->getCorreo();
                array_push($fichas,$objeto_ficha);
            } $objeto['fichas'] = $fichas;
            array_push($programas,$objeto);
        }
    }
}
print_r(json_encode($programas));