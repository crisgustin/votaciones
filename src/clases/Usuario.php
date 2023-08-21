<?php

class Usuario {

    private $id;

    private $usuario;

    private $nombres;

    private $apellidos;

    private $clave;

    private $id_rol;

    
    public function __construct($campo, $valor) {

        if ($campo != null) {

            if (is_array($campo)) {

                foreach ($campo as $key => $value) $this->$key = $value;

            } else {

                $SQL = "select * from usuarios where $campo = $valor;";
                //print_r($SQL);

                $resultado = Conector::ejecutarQuery($SQL);

                if ($resultado!=null&&count($resultado)>0) {

                    foreach ($resultado[0] as $key => $value) $this->$key = $value;

                }

            }

        }

    }

    

    public function getId() {

        return $this->id;

    }


    public function getUsuario() {

        return $this->usuario;

    }

    

    public function getNombres() {

        return $this->nombres;

    }

    

    public function getNombres_completos() {

        return $this->nombres.' '.$this->apellidos;

    }



    public function getApellidos() {

        return $this->apellidos;

    }

    

    public function getClave() {

        return $this->clave;

    }

    

    public function getId_rol() {

        return $this->id_rol;

    }

    

    public function getRol() {

        return new Rol('id', $this->id_rol);

    }
    

    

    public function getTipo_documento() {

        return $this->usuario!=null?new TipoDocumento('id', substr($this->usuario,0,2)):new TipoDocumento(null, null);

    }

    

    public function getDocumento() {

        return $this->usuario!=null?substr($this->usuario, 2):null;

    }




    public function setId($id) {

        $this->id = $id;

    }



    public function setUsuario($usuario) {

        $this->usuario = $usuario;

    }

    

    public function setNombres($nombres) {

        $this->nombres = $nombres;

    }



    public function setApellidos($apellidos) {

        $this->apellidos = $apellidos;

    }



    public function setClave($clave) {

        $this->clave = md5($clave);

    }



    public function setId_rol($id_rol) {

        $this->id_rol = $id_rol;

    }


    public function setIdUsuario($idusuario){
        $this->idusuario=$idusuario;
    }
    

    public static function clave_aleatoria($length=10) {

        $abecedary = ['a','b','c','d','e','f','g','h','j','k','m','n','ñ','o','p','q','r','s','t','u','v','w','x','y','z'];

        $numbers = ['0','1','2','3','4','5','6','7','8','9'];

        $chars = ['+','-','*','/','&','%','$','='];

        $password = [];

        $i = 3;

        $index = rand(0,count($numbers)-1);

        array_push($password,$numbers[$index]);

        shuffle($numbers);

        $index = rand(0,count($numbers)-1);

        array_push($password,$numbers[$index]);



        $index = rand(0,count($chars)-1);

        array_push($password,$chars[$index]);

        shuffle($chars);

        $index = rand(0,count($chars)-1);

        array_push($password,$chars[$index]);



        $upper = false;

        while ($i<$length) {

            $index = rand(0,count($abecedary)-1);

            shuffle($abecedary);

            $letter = $upper?strtoupper($abecedary[$index]):$abecedary[$index];

            $upper = ! $upper;

            array_push($password,$letter);

            $i++;

        }

        shuffle($password);

        return implode($password);

    }



    public function grabar() {

        $resultado = Usuario::validar_usuario($this->usuario);

        if($resultado=='') {
            $SQL = "insert into usuarios (usuario, nombres, apellidos, clave, id_rol) values ('$this->usuario', '$this->nombres', '$this->apellidos', '$this->clave', '$this->id_rol');";
            //print_r($SQL);
            $resultado = Conector::ejecutarQuery($SQL);

            if($resultado=='') $this->id = $this->getLastInsertID();

        } return $resultado;

    }

    

    public function modificar() {

        $this->telefono = $this->telefono!=null?"'$this->telefono'":'null';

        $SQL = "update usuarios set usuario = '$this->usuario', nombres = '$this->nombres', apellidos = '$this->apellidos', id_rol = $this->id_rol  where id = $this->id;";
        //print_r($SQL);
        return Conector::ejecutarQuery($SQL);

    }

    

    public function eliminar() {

        $SQL = "delete from usuarios where id = $this->id;";

        return Conector::ejecutarQuery($SQL);

    }

    

    private function getLastInsertID() {

        $SQL = 'select max(id) as id from usuarios;';

        return Conector::ejecutarQuery($SQL)[0]['id'];

    }



    public static function getLista($filtro){

        if ($filtro != null) $filtro=" where $filtro";

        $SQL = "select * from usuarios$filtro;";

        return Conector::ejecutarQuery($SQL);

    }

    

    public static function getCount(){

        $SQL = "select count(id) as count from usuarios;";

        return Conector::ejecutarQuery($SQL)[0]['count'];

    }

    

    public static function getListaEnObjetos($filtro){

        $datos = Usuario::getLista($filtro);

        $datos = !is_array($datos)?array():$datos;

        $usuarios = array();

        for ($i = 0; $i < count($datos); $i++) {
            //print_r($datos[$i]);

            $usuarios[$i] = new Usuario($datos[$i], null);

        } return $usuarios;

    }

    

    public static function validarIngreso($usuario,$clave) {

        $respuesta = '';
        //print_r($usuario);

        $usuario = new Usuario('usuario', "'$usuario'");

        if($usuario->getUsuario()!=null) {

            if($usuario->getClave()==md5($clave)) {

                $respuesta .= 'ok';

                session_start();

                $_SESSION['usuario'] = serialize($usuario);

            } else $respuesta = 'clave incorrecta';

        } else $respuesta = 'usuario incorrecto';

        return $respuesta;

    }

    

    public static function cerrarSesion(){

        session_unset();

        session_destroy();

    }

    

    public static function validar_usuario($usuario) {

        $resultado = '';

        $objeto = new Usuario('usuario', "'$usuario'");

        if($objeto->getUsuario()!=null) $resultado='Ya hay un usuario con esta identificación';

        return $resultado;

    }

    public static function getOptionsHTML($predeterminado) {

        $html = '<option value="">Selecciona un Tipo de Proceso</option>';

        $filtro='id_rol=2';

        $datos = self::getListaEnObjetos($filtro);

        

        foreach ($datos as $objeto) {

            $selected = $predeterminado==$objeto->getId()?' selected':'';

            $html.='<option value="'.$objeto->getIdentificacion().'"'.$selected.'>'.$objeto->getNombres()." ".$objeto->getApellidos().'</option>';

        } return $html; 

    }



    public static function exists($usuario) {

        $data = self::getListaEnObjetos("usuario = '$usuario'");

        return count($data)>0;

    }

}