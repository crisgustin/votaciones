<?php

class MenuRol {

    private $id_rol;

    private $menus;

    

    public function __construct($id_rol, $menus) {

        $this->id_rol = $id_rol;

        $this->menus = $menus;

    }

    

    public function getId_rol() {

        return $this->id_rol;

    }

    

    public function getMenusEnID() {

        if($this->menus==null) {

            $datos = MenuRol::getDatos($this->id_rol);

            $datos = !is_array($datos)?array():$datos;

            for ($i = 0; $i < count($datos); $i++) {

                $this->menus[$i] = $datos[$i]['id_menu'];

            }

        } return $this->menus;

    }



    public function grabar() {

        $resultado = $this->eliminar();

        if(count($this->menus)>0) {

            $SQL = "insert into menu_rol (id_menu, id_rol) values ";

            foreach ($this->menus as $value) {

                $SQL.="($value, $this->id_rol),";

            } $SQL = trim($SQL, ',').';';

            $resultado =$resultado==''?Conector::ejecutarQuery($SQL):$resultado;

        } return $resultado;

    }

    

    public function eliminar() {

        $SQL = "delete from menu_rol where id_rol = $this->id_rol;";

        return Conector::ejecutarQuery($SQL);

    }



        private static function getDatos($filtro) {

        $filtro = $filtro!=null?" and $filtro":null;

        $SQL = "select m.id, mr.id_menu, mr.id_rol from menu_rol as mr, menu as m where mr.id_menu = m.id$filtro;";

        return Conector::ejecutarQuery($SQL);

    }

    

    public static function getMenus($filtro) {

        $datos = MenuRol::getDatos($filtro);

        $array = array();

        for ($i = 0; $i < count($datos); $i++) {

            $id_menu = $datos[$i]['id_menu'];

            $array[$i] = new Menu('id', $id_menu);

        }

        return $array;

    }

    

    public static function getMenuHTML($usuario) {

        $html='';

        $array = $menus_compuestos = [];

        $id_rol = $usuario->getId_rol();

        if($id_rol!=null) {

            $filtro = "id_rol = $id_rol order by id asc";

            $menus = MenuRol::getMenus($filtro);
            

            foreach ($menus as $menu) {

                if($menu->getId_menu()==null) {

                    $icono = $menu->getFontAwesome()->getIcono();

                    if($icono!=null) $icono = '<span class="'.$icono.' menu-icon"></span>';

                    $html = '<a href="?contenido='.$menu->getRuta().'" class="nav-link" title="'.$menu->getDescripcion().'">'.$icono.$menu->getNombre().'</a>';

                    array_push($array,$html);

                } else array_push($menus_compuestos,$menu->getId_menu());

            }

            $menus_compuestos = array_unique($menus_compuestos);

            foreach ($menus_compuestos as $id_menu) {

                $menu = new Menu('id', $id_menu);

                $icono = $menu->getFontAwesome()->getIcono();

                if($icono!=null) $icono = '<span class="'.$icono.' menu-icon"></span>';

                $html = '<div class="dropdown">';

                $html .= '<button type="button" class="nav-link" data-toggle="dropdown">'.$menu->getNombre().'<span class="fa fa-caret-down dropdown-caret"></span></button>';

                $html .= self::getSub_menus($id_rol,$id_menu);

                $html .= '</div>';

                array_push($array,$html);

            }

        }

        if(count($array)>0) {

            $i = 1; $html = '<div class="sidebar closed">';

            $html .= '<button type="button" class="close" onclick="toggleSidebar()">x</button>

                <a href="/votaciones"><img src="imagenes/del.png" alt="Encuestas SENA" style="display:block;width:150px;height:150px;margin-left:auto;margin-right:auto"></a>

                <label style="display:block;text-align:center">SISTEMA DE VOTACIONES ESCOLARES</label>

                <div class="dropdown text-center" style="margin:10px 0;">

                    <button type="button" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-user-circle"></span> '.$usuario->getNombres().' '.$usuario->getApellidos().'<span class="caret"></span></button>

                    <ul class="dropdown-menu">

                        <li><a onclick="cargar_contenido_modal(this.href)" href="src/cambiar_clave.php" data-toggle="modal" data-target="#modal"><span class="fa fa-lock menu-icon"></span>Cambiar contraseña</a></li>

                        <li><a onclick="cerrar_sesion()" href="#"><span class="fa fa-sign-out menu-icon"></span>Cerrar sesión</a></li>

                    </ul>

                </div>';

            foreach($array as $value) {

                $html .= $value;

            }
            $html .= '</div>';

        }

        return $html;

    }



    private static function getSub_menus($id_rol, $id_menu) {

        $menus = MenuRol::getMenus("id_rol = $id_rol and m.id_menu = $id_menu");

        $html = '';

        if(count($menus)>0) {

            $html .= '<ul class="dropdown-menu">';

            foreach ($menus as $menu) {

                $descripcion = $menu->getDescripcion();

                $title = $descripcion != null ? ' title="' . $descripcion . '"' : '';

                $icono = $menu->getFontAwesome()->getIcono();

                if($icono!=null) $icono = '<span class="'.$icono.' menu-icon"></span>';

                $html .= '<li><a href="?contenido='.$menu->getRuta().'"'.$title.'>'.$icono.$menu->getNombre().'</a></li>';

            }

            $html .= '</ul>';

        } return $html;

    }

    

    public static function getMenuLogin($usuario) {

        $html = '';

        if($usuario->getId_rol()!=null) {

            $nombre = '<span class="hidden-xs">'.$usuario->getNombres().'</span>';

            $html .= '<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"><span class="fa fa-user-circle-o menu-icon"></span>'.$nombre.'<span class="fa fa-caret-down dropdown-caret"></span></a>';

            $html .= '<ul class="dropdown-menu dropdown-menu-right">';

            $html .= '<li><a onclick="cargar_contenido_modal(this.href)" href="src/cambiar_clave.php" data-toggle="modal" data-target="#modal"><span class="fa fa-lock menu-icon"></span>Cambiar contraseña</a></li>';

            $html .= '<li><a onclick="cerrar_sesion()" href="#"><span class="fa fa-sign-out menu-icon"></span>Cerrar sesión</a></li>';

            $html .= '</ul></li>';

        } else {

            $html .= '<li><a href="login.php" onclick="cargar_contenido_modal(this.href)" title="Accede a la plataforma" data-toggle="modal" data-target="#modal"><span class="fa fa-sign-in menu-icon"></span>Iniciar sesión</a></li>';

        }

        return $html;

    }

}