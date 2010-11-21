<?php

if(!class_exists('formulario'))
{
    require('./class/class_formulario.php');
}

$formulario = new formulario($_POST['nombre'], $_POST['email'], $_POST['titulo_mensaje'], $_POST['mensaje'], $_POST['submit_check']);

$formulario->procesar_formulario();

?>
