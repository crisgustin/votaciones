<?php
session_start();
require_once 'src/clases/Usuario.php';
if(isset($_SESSION['usuario'])) Usuario::cerrarSesion();