<?php
	require_once '../clases/Conector.php';
	require_once '../clases/TipoEmergencia.php';
	//$html = '<option value="">Seleccione el Estado del Item</option>';
	if($_POST['tinci']){
		$html = '<option value="">Seleccione el Estado del Item</option>';
		$datos=TipoEmergencia::getListaEnObjetos("idEventoPrincipal=".$_POST['tinci']." order by idTipoEmergencia asc ");
		foreach($datos as $dato){
			$html .= '<option value="'.$dato->getIdTipoEmergencia().'">'.$dato->getTipoEmergencia().'</option>';
		}
		echo $html;
	}