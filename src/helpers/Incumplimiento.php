<?php

class Incumplimiento {
    private $id_encuesta;
    private $id_programa;
    private $id_ficha;
    private $sql;

    public function __construct($id_encuesta,$id_programa = null,$id_ficha = null) {
        $this->sql = "select u.usuario, u.nombres, u.apellidos, u.telefono, u.correo, fp.ficha, fp.modalidad from encuesta_usuario as eu, ficha_usuario as fu, usuario as u, ficha_programa as fp where eu.id_encuesta = $id_encuesta and eu.estado = 'P' and eu.id_ficha = fu.id and u.id = fu.id_usuario and fp.id = fu.id_ficha";
        $this->id_encuesta = $id_encuesta;
        $this->id_programa = $id_programa;
        $this->id_ficha = $id_ficha;
        $this->sql .= $this->get_filtro();
    }

    private function get_filtro() {
        $filtro = '';
        if($this->id_programa!=null) $filtro .= " and fp.id_programa = $this->id_programa order by fp.modalidad asc, fp.ficha desc";
        else $filtro .= " and fp.id = $this->id_ficha";
        return $filtro;
    }

    public function get_array() {
        $datos = Conector::ejecutarQuery($this->sql);
        $array = $virtual = $presencial = [];
        foreach($datos as $objeto) {
            if($objeto['modalidad']==='P') array_push($presencial,$objeto);
            else array_push($virtual,$objeto);
        }
        if(count($presencial)>0) {
            $presencial = ['modalidad' => 'Presencial', 'usuarios' => $presencial];
            array_push($array,$presencial);
        }
        if(count($virtual)>0) {
            $virtual = ['modalidad' => 'Virtual', 'usuarios' => $virtual];
            array_push($array,$virtual);
        }
        return $array;
    }

    public function get_header_html() {
        $encuesta = new Encuesta('id', $this->id_encuesta);
        $html = $subtitulo = null;
        if($this->id_programa!=null) {
            $programa = new Programa('id', $this->id_programa);
            $subtitulo = $programa->getNombre_completo();
        } else {
            $ficha = new FichaPrograma('id', $this->id_ficha);
            $subtitulo = $ficha->getNombre();
            $html .= '<tr><th>Gestor</th><td colspan="5">'.$ficha->getGestor().'</td></tr>';
            $html .= '<tr><th>Correo Gestor</th><td colspan="5">'.$ficha->getCorreo().'</td></tr>';
        }
        return '<thead><tr><th colspan="6">Incumplimiento en diligenciar encuesta</th></tr>
                    <tr><th colspan="6">'.$encuesta->getNombre().'</th></tr>
                    <tr><th colspan="6">'.$subtitulo.'</th></tr>'.$html.'
                    <tr><th>Identificación</th><th>Nombres</th><th>Apellidos</th><th>Teléfono</th><th>Correo</th><th>Ficha</th></tr></thead>';
    }
}