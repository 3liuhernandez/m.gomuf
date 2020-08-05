<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace app\models;

use app\models as Model;
use Ocrend\Kernel\Helpers as Helper;
use Ocrend\Kernel\Models\Models;
use Ocrend\Kernel\Models\IModels;
use Ocrend\Kernel\Models\ModelsException;
use Ocrend\Kernel\Models\Traits\DBModel;
use Ocrend\Kernel\Router\IRouter;

/**
 * Modelo Home
 */
class Home extends Models implements IModels {
    use DBModel;

    /**
     * Libro
     * 
     * @return string
     */

    CONST FILE = 'assets/app/doc/REENCUENTRO CON LAS NUEVAS GENERACIONES POST COVID 19.pdf';

    /**
     * Envía un mensaje de contacto
     * 
     * @return array
     */
    public function contact(){
        try {
            global $http, $config;
            $name = $http->request->get('name');
            $email = $http->request->get('email');
            $msj = $http->request->get('msj');
            
            # Verificar campos vacíos
            if(Helper\Functions::e($name, $email, $msj)){
                throw new ModelsException('Debes llenar todos los campos.');
            }

            # Verificar email
            if ( !Helper\Strings::is_email($email) ) {
                throw new ModelsException('El email debe tener un formto válido.');
            }

            # Cuerpo del menaje
            $HTML = 'Has recibido un nuevo mensaje de contacto.
                <br />
                <br />
                <ul>
                    <li><b>Nombre: </b>'.$name.'</li>
                    <li><b>Email: </b><a href="mailto:'.$email.'">'.$email.'</a></li>
                </ul>
                <br />
                <br />
                '.$msj;

            # Enviar email
            $email_send = Helper\Emails::send(["contacto.gomuf@gmail.com" => "GOMUF"],array(
                # Título del mensaje
                '{{title}}' => 'Mensaje de contacto - ' . $config['build']['name'],
                # Url de logo
                '{{url_logo}}' => $config['build']['url'],
                # Logo
                '{{logo}}' => $config['mailer']['logo'],
                # Contenido del mensaje
                '{{content}} ' => $HTML,
                # Copyright
                '{{copyright}}' => '&copy; '.date('Y') .' <a href="'.$config['build']['url'].'">'.$config['build']['name'].'</a> - Todos los derechos reservados.'
              ),1);

            return array('success' => 1, 'message' => 'Mensaje enviado con éxito.');
        } catch (ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }


    /**
     * Guarda a un suscriptor e inicia la descarga del libro
     * 
     * return array
     */
    public function download(){
        try {
            global $http, $config;
            $name = $http->request->get('name');
            $email = $http->request->get('email');
            $msj = $http->request->get('msj');

            # Verificar campos vacíos
            if(Helper\Functions::e($name, $email, $msj)){
                throw new ModelsException('Debes llenar todos los campos.');
            }

            # Verificar email
            if ( !Helper\Strings::is_email($email) ) {
                throw new ModelsException('El email debe tener un formto válido.');
            }

            $email = $this->db->scape($email);
            # Verificar si no existe el email no guardamos
            if (false == $this->db->select('id_suscriptor', 'suscripcion', null, "email = '$email'", 1)) {
                
                # Insertar registro
                $this->db->insert('suscripcion', array(
                    'name' => $name,
                    'email' => $email,
                    'message' => $msj
                ));
            }else{
                $this->db->query("UPDATE suscripcion SET download = download + 1 WHERE email = '$email' LIMIT 1");
            }


            return array('success' => 1, 'message' => 'Su libro se descargará en breve.', 'file' => self::FILE);
        } catch (ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }

    /**
     * __construct()
    */
    public function __construct(IRouter $router = null) {
        parent::__construct($router);
		$this->startDBConexion();
    }
}