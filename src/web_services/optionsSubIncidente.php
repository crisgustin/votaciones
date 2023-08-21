<?php
	require_once '../clases/Conector.php';
	require_once '../clases/SubPpal.php';
	print_r($_POST['tipo_emergencia']);
	if($_POST['tipo_emergencia']){
		$html='';
		$datos=SubPpal::getListaEnObjetos("idTipoEmergencia=".$_POST['tipo_emergencia']);
		foreach($datos as $dato){
			$html .= '<option value="'.$dato->getId().'">'.$dato->getNombreSub().'</option>';
		}
		$html .= '<option value="58">NO APLICA</option>';
		echo $html;
	}