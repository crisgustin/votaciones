<?php
class Paginacion {
    private $count;
    private $archivo;
    public $rxp;

    public function __construct($count, $rxp, $archivo) {
        $this->count = $count;
        $this->archivo = $archivo;
        $this->rxp = $rxp;
    }
    
    public function getEncabezado($pagina) {
        $html = '<label>Página '.$pagina.' de '.$this->total_paginas().'</label>';
        //$html .= '<label class="sm">Página '.$pagina.' de '.$this->total_paginas().'</label>';
        return $html;
    }
    
    private function total_paginas() {
        return $this->count>$this->rxp?ceil($this->count/$this->rxp):1;
    }
    
    public function registro_inicial($pagina) {
        return ($pagina-1)*$this->rxp;
    }
    
    private function registro_final($pagina) {
        return $pagina%$this->rxp>0?$this->count:($pagina*$this->rxp);
    }
    
    public function getPaginasHTML($pagina) {
        $html = '<ul class="pagination">'; $n = 1;
        $btn_primero=$btn_anterior=$btn_siguiente=$btn_ultimo='';
        if($this->total_paginas()>4) {
            if($pagina>1) {
                $btn_anterior = '<li><a href="?contenido='.$this->archivo.'&pag='.($pagina-1).'" title="Anterior"><span class="fa fa-chevron-left"></span></a></li>';
                if($pagina>4) {
                    $btn_primero = '<li><a href="?contenido='.$this->archivo.'" title="Primer página"><span class="fa fa-chevron-left"></span><span class="fa fa-chevron-left"></span></a></li>';
                    $n = $pagina-3;
                }
            }
            if($pagina<$this->total_paginas()) {
                $btn_ultimo = '<li><a href="?contenido='.$this->archivo.'&pag='.$this->total_paginas().'" title="Última página"><span class="fa fa-chevron-right"></span><span class="fa fa-chevron-right"></span></a></li>';
                $btn_siguiente = '<li><a href="?contenido='.$this->archivo.'&pag='.($pagina+1).'" title="Siguiente"><span class="fa fa-chevron-right"></span></a></li>';
            }
        }
        $html .= $btn_primero.$btn_anterior;
        $a = $this->total_paginas()>4?4:$this->total_paginas();
        for ($i = 0; $i < $a; $i++) {
            $active = $pagina!=$n?'':' class="active"';
             $html .= '<li'.$active.'><a href="?contenido='.$this->archivo.'&pag='.$n.'">'.$n.'</a></li>';
             $n++;
        }$html .= $btn_siguiente.$btn_ultimo.'</ul>';
        return $html;
    }
}