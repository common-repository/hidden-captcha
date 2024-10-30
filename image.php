<?php
if(!session_id()){
    @session_start();
}

//si tenemos la primera fase, completamos
if(isset($_SESSION['elPlugin_hidden_captcha_inicio'])){
    $_SESSION['elPlugin_hidden_captcha_cargado'] = true;    
    imagen();
}else{

    //si es el primero cargado creamos la variable
    if($_GET['i'] == $_SESSION['elPlugin_hidden_captcha_primero']){
        $_SESSION['elPlugin_hidden_captcha_inicio'] = true;                
        imagen();                            
    }else{
        $_SESSION['elPlugin_hidden_captcha_numPet']--;
        if ($_SESSION['elPlugin_hidden_captcha_numPet'] <= 0){            
            imagen();            
        }else{
            session_write_close();
            header('Location: http://'.$_SERVER['SERVER_NAME']."/".$_SERVER['REQUEST_URI']);    
        }
    }    
}

//imagen gif 1x1 pixel
function imagen(){
    header("content-type: image/gif");      
    echo base64_decode("R0lGODlhAQABAIAAAAAAAAAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==");
}

?>