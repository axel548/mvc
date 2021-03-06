<?php
class Usuarios extends Controlador{

    public function __construct(){
        $this->modeloUsuario = $this->modelo('modeloUsuarios');
        $this->Helper = $this->modelo('Helpers');
    }
    public function index(){
        $this->vista('usuarios/index');
    }
    public function inicio(){
        if ($this->checarLogueo()) {
			redirect('inicio');
		}else{
            $this->vista('usuarios/index');
		}
    }
    public function registro(){
        $this->vista('usuarios/registro');  
    }
    public function login(){
        if ($this->checarLogueo()) {
			redirect('inicio');
		}else{
            $this->vista('usuarios/login');
		}
    }
    public function slider(){
        if(isset($_SESSION['id_usuario']) && $_SESSION['usr']->rol=="1"){
            $this->vista('inicio/index');
        }else{
            $imgRecup = $this->modeloUsuario->imagRp();
            $data = [
                'imagRe' => $imgRecup
            ];
            $this->vista('usuarios/slider', $data);
        }
    }
    public function productosrandom(){
        $gtCategori = $this->modeloUsuario->getCategorias();
        $gtProduc = $this->Helper->getRamdon(6);
        $data = [
            'GetCateg' => $gtCategori,
            'GetProduc' => $gtProduc
        ];
        $this->vista('usuarios/productos-random', $data);
    }
    public function contacto(){
        if(isset($_SESSION['id_usuario']) && $_SESSION['usr']->rol=="1"){
            $this->vista('inicio/index');
        }else{
            $this->vista('usuarios/contacto');
        }
    }
    public function registrarse(){
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $data = [
            'nombre' => trim($_POST['nombre']),
            'correo' => trim($_POST['correo']),
            'pass' => trim($_POST['pass'])
        ];
        $data['pass'] = password_hash($data['pass'], PASSWORD_DEFAULT);
        if ($this->modeloUsuario->altaDeUsuario($data)) {
            $this->vista('usuarios/login');
        }else{
            die('No se pudo registrar');
        }
    }
    public function iniciarSesion(){
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $data = [
            'correo' => trim($_POST['correo']),
            'pass' => trim($_POST['pass'])
        ];
        $usuario_logueado = $this->modeloUsuario->login($data['correo'], $data['pass']);
        //cambia
        if ($usuario_logueado) {
            $this->crearSesionDeUsuario($usuario_logueado);
        }else {
            $this->vista('usuarios/login');
        }
    }
    public function crearSesionDeUsuario($user){       
            $_SESSION['id_usuario'] = $user->id;
            $id_usr = $user->id;
            $usrbusc = $this->modeloUsuario->buscarUsuario($id_usr);
            $data = [
                'usuario' => $usrbusc
            ];
            foreach($data['usuario'] as $_SESSION['usr']){
            }
            redirect('alertas/entro');
        //redirect('publicaciones');
    }
    public function salir(){
        unset($_SESSION['id_usuario']);
        unset($_SESSION['usr']);
        session_destroy();
        redirect('usuarios/login');
    }
    public function checarLogueo(){
        if (isset($_SESSION['id_usuario'])) {
            return true;
        }else {
            return false;
        }
    }
    public function verCategoria($id){
        $gtCategori = $this->modeloUsuario->getCategorias();
        $verCateg = $this->modeloUsuario->vercat($id);
        $getall = $this->Helper->getAllCategoria($id);
        $data = [
            'GetCateg' => $gtCategori,
            'ver' => $verCateg,
            'getall' => $getall
        ];
        $this->vista('usuarios/categproduc', $data);
    }
    public function getProduct($id){
        $getProduc = $this->Helper->getProduct($id);
        $data = [
            'producto' => $getProduc
        ];
        $this->vista('usuarios/info_producto', $data);
    }
}
?>