<?php
ob_start();
session_start();
date_default_timezone_set('America/Bogota');
require_once 'src/clases/Conector.php';
require_once 'src/clases/TipoDocumento.php';
require_once 'src/clases/FontAwesome.php';
require_once 'src/clases/Usuario.php';
require_once 'src/clases/MenuRol.php';
require_once 'src/clases/Menu.php';
require_once 'src/clases/Rol.php';
$usuario_logueado = new Usuario(null, null);
$contenido = isset($_GET['contenido'])&&$_GET['contenido']=='src/identidad.php'? $_GET['contenido']:'inicio.php';$wrapper='';
if(isset($_SESSION['usuario'])) {
    $usuario_logueado = unserialize ($_SESSION['usuario']);
    if(isset($_GET['contenido'])) $contenido = $_GET['contenido'];
    $wrapper = ' class="wrapper"';
}
$menu = MenuRol::getMenuHTML($usuario_logueado);

$hidden = $menu!=null?'md:hidden':'';
$content_style = $usuario_logueado->getId()!=null?'style="background-color:rgba(255,255,255,0.5)"':'';
$toggle= $menu!=null?'<button type="button" class="navbar-toggle collapsed" onclick="toggleSidebar()"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>':'';
?>
<!DOCTYPE html>
<html lang="es"<?=$wrapper?>>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv='cache-control' content='no-cache'>
        <meta http-equiv='expires' content='0'>
        <meta http-equiv='pragma' content='no-cache'>
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <link rel="stylesheet" type="text/css" href="lib/css/bootstrap.min.css"/>
        <link rel="stylesheet" type="text/css" href="vendor/snapappointments/bootstrap-select/dist/css/bootstrap-select.min.css"/>
        <link rel="stylesheet" type="text/css" href="lib/fontawesome/css/font-awesome.min.css"/>
        <link rel="stylesheet" type="text/css" href="lib/css/script.css"/>
        <link rel="icon" type="image/png" href="imagenes/del.png"/>
        <script type="text/javascript" src="lib/js/jquery-3.5.1.min.js"></script>
        <script type="text/javascript" src="lib/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="lib/js/script.js"></script>
        <script type="text/javascript" src="lib/js/notificaciones.js"></script>
        <script type="text/javascript" src="vendor/snapappointments/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
        <script type="text/javascript" src="vendor/snapappointments/bootstrap-select/dist/js/i18n/defaults-es_ES.min.js"></script>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css" />
        <link rel="stylesheet" href="leaflet-extramarker/css/leaflet.extra-markers.min.css">
        <link rel="stylesheet" href="lib/css/main.css">
        <title>VOTACIONES</title>
    </head>
    <body>
        <div class="page">
            <div class="navbar navbar-sena navbar-fixed-top <?=$hidden?>">
                <div class="navbar-header">
                    <?=$toggle?>
                    <a href="/votaciones/" class="navbar-brand" data-placement="right" title="SISTEMA DE VOTACIONES ESCOLARES"><img class="img-logo"src="imagenes/del.png"/><span class="hidden-xs">VOTACIONES</span></a>
                </div>
                <ul class="navbar-menu nav navbar-nav">
                    <?= MenuRol::getMenuLogin($usuario_logueado) ?>
                </ul>
            </div>
                <?=$menu?>
            <div class="content scrollbar" <?=$content_style?>>
                <?php include $contenido ?>
            </div>
        </div>
        <div class="modal fade text-center" id="modal" role="dialog">
            <div class="modal-dialog"><div class="modal-content"></div></div>
        </div>
        <div class="footer">
            <div class="row">
                <div class="col col-md-8 col-sm-12 col-xs-12">
                    <p class="footer-title text-bold">SISTEMA DE VOTACIONES ESCOLARES</p>
                    <p class="footer-title text-bold">ESPECIALIZACION DE DESARROLLO DE SOFTWARE</p>
                    <p class="footer-title text-bold">UNIVERISIDAD UNIMINUTO</p>
                    <p class="footer-title text-bold">Líneas de atención:</p>
                    <p class="footer-title text-bold">PBX: 3012606846</p>
                </div>
                <div class="col col-md-4 col-sm-12 col-xs-12">
                    <!-- <img class="img-responsive footer-img" src="imagenes/iso.png"/> -->
                </div>
            </div>
        </div>
        <script type="text/javascript">$('[title]').tooltip();$('.navbar-toggle').tooltip('show');</script>
        <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
        <script>

            if (Notification.permission !=='granted') {
                Notification.requestPermission().then((result) => {
                    console.log(result);
                });
            }
            // Enable pusher logging - don't include this in production
            Pusher.logToConsole = true;

            let pusher = new Pusher('9bae97f75cc76bcaffe6', {cluster: 'us2'});
            //var pusher = new Pusher('9bae97f75cc76bcaffe6', {cluster: 'us2'});


            let channel = pusher.subscribe('my-channel');
            channel.bind('my-event', (data) => {
                console.log(data);
                if (data.role==<?=$usuario_logueado->getId_rol()?>) {
                    //$('.badge').text(data.count).removeClass('hidden');
                    //let html = '';
                    for (let notification of data.notifications) {
                        if (Notification.permission === 'granted') {
                            const push = new Notification(notification.title, { body: notification.message, icon: notification.image, requireInteraction:true});
                        }
                    }
                    //$('.dropdown-menu').html(html);
                }
            });

            /*$('.dropdown').on('show.bs.dropdown', () => {
                $('.badge').text('').addClass('hidden');
            });*/
        </script>
    </body>
</html>
<?php ob_end_flush(); ?>

