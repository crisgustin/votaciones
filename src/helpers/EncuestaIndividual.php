<?php

class EncuestaIndividual {
    private $asignacion;
    private $encuesta;
    private $ficha;
    private $aprendiz;

    public function __construct($id_encuesta = null) {
        $this->asignacion = new EncuestaUsuario('encuesta_usuario.id',$id_encuesta);
        $this->encuesta = $this->asignacion->getEncuesta();
        $this->ficha = $this->asignacion->getFicha_usuario();
        $this->aprendiz = $this->ficha->getUsuario();
        $this->ficha = $this->ficha->getFicha_programa();
    }

    public function get_array() {
        $rus = RespuestaUsuario::getListaEnObjetos("id_encuesta = {$this->asignacion->getId()}");
        $data = [];
        foreach ($rus as $ru) {
            $pregunta = $ru->getPregunta();
            $categoria = $pregunta->getCategoria();
            $rp = $ru->getRespuestaPregunta();
            $respuesta = $pregunta->getTipo()=='RA'?$ru->getTexto():$rp->getTexto();
            $obj = ['categoria' => $categoria->getNombre(), 'enunciado' => $pregunta->getEnunciado(), 'respuesta' => $respuesta];
            array_push($data,$obj);
        }
        return $data;
    }

    public function get_header_html() {
        return '<thead>
            <tr><th colspan="4">Resultado de Encuesta</th></tr>
            <tr><th colspan="4">'.$this->encuesta->getNombre().'</th></tr>
            <tr><th colspan="4">'.$this->encuesta->getObjetivo().'</th></tr>
            <tr><th>Ficha</th><td colspan="3">'.$this->ficha->getNombre().' - '.$this->ficha->getNombreModalidad().'</td></tr>
            <tr><th>Aprendiz</th><td colspan="3">'.$this->aprendiz->getUsuario().' - '.$this->aprendiz->getNombres_completos().'</td></tr>
            <tr><th>Fecha Asignación</th><td>'.$this->asignacion->getFecha_asignacion().'</td><th>Fecha Diligenciamiento</th><td>'.$this->asignacion->getFecha_presentacion().'</td></tr>
            <tr><td colspan="4"></td></tr>
        </thead>';
    }

    public function get_body_html() {
        $html = '<tbody>';
        $html .= '<tr>';
        $html .= '<th colspan="3">Pregunta</th>';
        $html .= '<th>Respuesta</th>';
        $html .= '<th></th>';
        $html .= '</tr>';
        $data = $this->get_array();
        $categoria = '';
        foreach ($data as $obj) {
            if($categoria!=$obj['categoria']&&$obj['categoria']!='') $html .= '<tr><th colspan="4">'.$obj['categoria'].'</th></tr>';
            $html .= '<tr>';
            $html .= '<td colspan="3">'.$obj['enunciado'].'</td>';
            $html .= '<td>'.$obj['respuesta'].'</td>';
            $html .= '</tr>';
            $categoria = $obj['categoria'];
        }
        $html .= '</tbody>';
        return $html;
    }

    public function get_table_html($exportable=false) {
        $css = $exportable?' class="table table-hover table-condensed table-stripped"':'';
        return '<table'.$css.'>'.$this->get_header_html().$this->get_body_html().'</table>';
    }

    public function get_aprendiz() {
        return $this->aprendiz;
    }
}