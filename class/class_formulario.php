<?php

/**
 * 
 * Clase Formulario para la Web
 * 
 * Funciones:
 * 
 * validar_formulario() => Verifar y detectar problemas en el formulario (Privada)
 * procesar_mensaje() => Validar el mensaje puesto en el formulario (Privada)
 * procesar_formulario() => Validar formulario, procesar mensaje e insertarlo a la BD (Publica)
 * 
 * **/
 
class formulario
{
    private $nombre, $email, $titulo, $mensaje, $check_submit;
    private $error = array();
    private $conexion = null;
    private $select = null;
    
    function __construct($nombre, $email, $titulo, $mensaje, $check_submit)
    {
        $this->nombre = $nombre;
        $this->email = $email;
        $this->titulo = $titulo;
        $this->mensaje = $mensaje;
        $this->check_submit = $check_submit;
        
        //Evitar llamado directo desde el navegador
        if(!$this->check_submit)
        {
            die("ERROR!, acceso denegado!");
        }
        
        //Conectando y seleccionando a la base de datos
        $this->conexion = @mysql_connect('localhost', '', '');
        
        if(!$this->conexion)
        {
            $this->error['db'] = 'Error conectando a la Base de Datos';
        }
        
        $this->select = @mysql_select_db('md_web', $this->conexion);
        
        if(!$this->select)
        {
            $this->error['select_db'] = 'Error seleccionando a la Base de Datos';
        }
    }
    
    private function validar_formulario()
    {
        //Verificamos campos vacios
        if(empty($this->nombre) || empty($this->email) || empty($this->titulo) || empty($this->mensaje))
        {
            $this->error['vacios'] = 'Debe llenar TODOS los datos solicitados!';
        }
        
        //Contamos la longitud de titulo, nombre y mensaje
        $count_nombre = strlen($this->nombre);
        $count_titulo = strlen($this->titulo);
        $count_mensaje = strlen($this->mensaje);
        
        //El nombre solo puede llevar letras!
        if(!preg_match('/^[A-Za-z]+$/', $this->nombre) && !empty($this->nombre))
        {
            $this->error['nombre_solo_letras'] = "El nombre solo puede llevar letras!";
        }
        
            
        //Validando email
        if(preg_match('/^([A-Za-z0-9])*@([A-Za-z\.]+[A-Za-z])$/', $this->email) && !empty($this->email))
        {
            //Hasta que no se me ocurra como arreglar la expresion regular, hare este parche todo chapucero xD
            //Basicamente, buscara que haya al menos un . en el email
            if(!strrpos($this->email, '.'))
            {
                $this->error['email_invalido'] = "Email incorrecto!";
            }
        }
        else if(!empty($this->email))
        {
            $this->error['email_invalido'] = "Email incorrecto!";
        }
        
        //El titulo solo puede llevar letras!
        if(!preg_match('/^[A-Za-z]+$/', $this->titulo) && !empty($this->titulo))
        {
            $this->error['titulo_con_numeros'] = "El titulo <b>SOLO</b> debe tener letras <b>NO</b> numeros!";
        }
        
        //Titulo no puede ser mayor a 100 caracteres
        if($count_titulo > 100)
        {
            $this->error['titulo_muy_largo'] = "Titulo supera los 100 caracteres";
        }
        
        //Nombre no puede ser mayor a 60 caracteres
        if($count_nombre > 60)
        {
            $this->error['nombre_muy_largo'] = "Nombre supera los 60 caracteres";
        }
        
        if($count_mensaje > 60000)
        {
            $this->error['mensaje_muy_largo'] = 'Mensaje supera los 60000 caracteres';
        }
        
        //Contamos la cantidad de errores y si es mayor a cero ,mostramos errores y retornamos false, si no retornamos true
        $count_errores = count($this->error);
            
        if($count_errores > 0)
        {
            foreach($this->error as $er)
            {
                echo $er.'<br />';
            }
            
            return false;
        }
        
        return true;
    }
    
    private function escape_strings($msg)
    {
        return (mysql_real_escape_string($msg, $this->conexion));
    }
    
    public function procesar_formulario()
    {
        if($this->validar_formulario() != false)
        {
            //Sanitizo los strings con mysql_real_escape_string
            $this->nombre = $this->escape_strings($this->nombre);
            $this->email = $this->escape_strings($this->email);
            $this->titulo = $this->escape_strings($this->titulo);
            $this->mensaje = $this->escape_strings($this->mensaje);
            
            $id = '';
            
            //Insertamos en la base de datos
            $to_insert =
            "INSERT INTO contacto(ID, titulo, Nombre, Email, Mensaje)
             VALUES ('$id', '$this->titulo', '$this->nombre', '$this->email', '$this->mensaje')";
             
            $insert = @mysql_query($to_insert, $this->conexion);
            
            if(!$insert)
            {
                //die(mysql_error()); Debug!
                exit("Error!, porfavor intente nuevamente mas tarde!, gracias :) ");
            }
            else
            {
                define('OPERACION_REALIZADA', true);
                require('./../../confirmacion.php');
            }
        }
    }
}

?>