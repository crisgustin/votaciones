<?php
error_reporting(0);
$nombres=trim($usuario_logueado->getNombres().' '.$usuario_logueado->getApellidos());
$nombres=strtoupper($nombres);



switch($usuario_logueado->getId_rol()) {

    case 1: case 2:
        $html ='<div><h3>hola mundo</h3></div>';
        $html ='<div style="background-color:#646464">';
        $html .= '<br><center><h3 style="color:white"><b>Bienvenido(a): '.$nombres.'</b></h3></center><br>';
        $html .= '</div>';
        $html .= '</br>';
        $votado=Conector::ejecutarQuery("select * from votacion where idVotante=".$usuario_logueado->getId());
        if($votado[0][0]!=null){
            $html .= '<center><h2 style="color:red"><b>USTED YA HA VOTADO</b></h3></center>';
        }
        


    break;
    
    default:
        $html = '<div class="img-main"><div><p class="text-center" style="color:#000000;font-weight:bold;font-size:20px">SIRE 119 - SISTEMA DE REGISTRO DE EMERGENCIAS</p><img src="imagenes/logo.png"/></div></div>';break;

}

echo $html;