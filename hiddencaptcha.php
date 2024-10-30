<?php
/*
Plugin Name: Hidden captcha
Plugin URI: http://www.elplugin.com/hidden-captcha/
Description: Captcha oculto para los usuarios que bloquea a los robots que pretenden incluir spam. Protege el acceso de login, registro y formulario de password perdido.
Author: elPlugin
Author URI: http://www.elplugin.com/
Version: 1.0
License: GPLv2
*/


//menu de opciones
add_action('admin_menu','elPlugin_menu_principal');
add_action('admin_menu','elPlugin_menu_hidden_captcha');

//menu principal el Plugin
if (! function_exists('elPlugin_menu_principal')){
    function elPlugin_menu_principal(){    
        add_menu_page('elPlugin','elPlugin','read','elPlugin_menu','elPlugin_pagina_opciones', WP_CONTENT_URL."/plugins/hidden-captcha/images/icon_16.png");
        
        wp_register_style('elPlugin_css_Style', plugins_url( 'css/style.css', __FILE__ ) );
		wp_enqueue_style('elPlugin_css_Style' );        
    }
}

//menu del plugin
if (! function_exists('elPlugin_menu_hidden_captcha')){
    function elPlugin_menu_hidden_captcha(){
        add_submenu_page('elPlugin_menu', 'Hidden captcha', 'Hidden captcha', 'read', 'hidden_captcha_opciones','elPlugin_hidden_captcha_pagina');
    }
}

//pagina opciones elPlugin
if (! function_exists('elPlugin_pagina_opciones')){
	function elPlugin_pagina_opciones(){
		echo "<h2>el<span class='elplugin_azul'>P</span>lugin</h2>";
		echo "<p>Actualmente elPlugin dispone de los siguientes plugins:</p>";	
		echo "<p><b>Hidden captcha</b></p>";		
	}
}

//pagina opciones hidden captcha
if (! function_exists('elPlugin_hidden_captcha_pagina')){
	function elPlugin_hidden_captcha_pagina(){
		echo "<div class='wrap'><h2>Hidden captcha</h2></div>";
		
		echo "Hidden captcha es la solución que permite evitar el spam, sin molestar al usuario con incomodos captchas. Este plugin incluye un captcha oculto para que cualquier robot que intente insertar spam no logre realizarlo.";
		
		echo "<div id='msgverde'>";
			echo "<p>En este momento \"Hidden captcha\" te está protegiendo contra el spam en los siguientes formularios:</p>";
			echo "<p>Inicio de sesión<br/>Registro de usuarios<br/>Olvido de contraseña<br/>Comentarios</p>";
		echo "</div>";
	}
}


add_action('init','elPlugin_hidden_captcha_register_session');

//inicio de session
if (! function_exists('elPlugin_hidden_captcha_register_session')){
	function elPlugin_hidden_captcha_register_session(){
		if(!session_id()){
			@session_start();
		}
	}
}
    
add_action('comment_form', 'elPlugin_hidden_captcha_add');
add_action('comment_post', 'elPlugin_hidden_captcha_comment_post');
add_action('register_form', 'elPlugin_hidden_captcha_add');
add_action('register_post', 'elPlugin_hidden_captcha_form_post');
add_action('login_form', 'elPlugin_hidden_captcha_add');
add_filter('login_redirect', 'elPlugin_hidden_captcha_login_redirect', 10, 3);
add_action('lostpassword_form', 'elPlugin_hidden_captcha_add' );
add_action('lostpassword_post', 'elPlugin_hidden_captcha_form_post');


//añadidmos captcha oculto
if (! function_exists('elPlugin_hidden_captcha_add')){
	function elPlugin_hidden_captcha_add($id){    
		if(!session_id()){
			@session_start();
		}
		
		//borramos las variables    
		unset($_SESSION['elPlugin_hidden_captcha_inicio']);
		unset($_SESSION['elPlugin_hidden_captcha_cargado']);    

		$r1 = rand(1,50000);
		$r2 = rand(1,50000);
		if ($r1 == $r2){
			$r2++;
		}
		
		$_SESSION['elPlugin_hidden_captcha_primero'] = $r2;
		$_SESSION['elPlugin_hidden_captcha_numPet'] = 5;
		
		echo "<img src=\"".plugin_dir_url( __FILE__ )."image.php?i=".$r1."\" />";    
		echo "<img src=\"".plugin_dir_url( __FILE__ )."image.php?i=".$r2."\" />";
	}
}


//comprobar captcha al enviar un comentario dentro de un post
if (! function_exists('elPlugin_hidden_captcha_comment_post')){
	function elPlugin_hidden_captcha_comment_post($id){    
		if (session_id() == ""){
			@session_start();
		}
		if(!isset($_SESSION['elPlugin_hidden_captcha_cargado'])){
			wp_set_comment_status($id, 'spam');
		}else{
			wp_set_comment_status($id, 'hold');
		}
	}
}


//comprobar captcha al enviar el formulario (registro, olvido de clave...)
if (! function_exists('elPlugin_hidden_captcha_form_post')){
	function elPlugin_hidden_captcha_form_post(){    
		if (session_id() == ""){
			@session_start();
		}
		if(!isset($_SESSION['elPlugin_hidden_captcha_cargado'])){
			wp_die("Error captcha");
		}
	}
}


//comprobar captcha al enviar el formulario de login
if (! function_exists('elPlugin_hidden_captcha_login_redirect')){
	function elPlugin_hidden_captcha_login_redirect($url){    
		if (session_id() == ""){
			@session_start();
		}
		if(!isset($_SESSION['elPlugin_hidden_captcha_cargado'])){
			wp_clear_auth_cookie();
			return $_SERVER["REQUEST_URI"];
		}else{
			return $url;
		}
	}
}


?>