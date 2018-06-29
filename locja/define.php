<?
ini_set("default_charset","utf-8");

//define('EMPRESA_NOME','Complexo Marinho');

define('HOST_DEV','locja.rhizatech.com.br');
define('HOST_FINAL','acesso.locja.net');

//grant all privileges on db_nearfinderus.* to nearfinderus@'%' identified by 'D461231a@aa53sdKgufjhfe#qG57890PsJfI213213';

define('SITE_HOST', $_SERVER['HTTP_HOST'] );

define('IS_DEV', SITE_HOST == HOST_DEV);

if( IS_DEV ){
    define('DB_HOST', "localhost");
    define('DB_USER', "root");
    define('DB_PASSWORD', 'rhiza$102040');
    define('DB_DATABASE', "locja");
    
    //grant all privileges on locja.* to locjauser@'localhost' identified by '102040';
} else {
    
    //grant all privileges on locja.* to locjauser@'localhost' identified by 'uL%(*876unjhb*(k$aBdA51esFA31asdVEa9q$H';
    
    define('DB_HOST', "localhost");
    define('DB_USER', "locjauser");
    define('DB_PASSWORD', 'uL%(*876asdasdunajahab*a(k3$4a234B6dA4575456715687e679s6FA31asdVEa9q$H');
    define('DB_DATABASE', "locja");
}
/*
define('SMTP_HOST', 'smtp.mandrillapp.com');
define('SMTP_PORT', '587');
define('SMTP_USER', 'contato@rhizatech.com.br');
define('SMTP_PASSWORD', 'O3ysuQHrRGe7pvGC8WdLKQ');
*/

/*
define('SMTP_HOST', 'smtp.elasticemail.com');
define('SMTP_PORT', '2525');
define('SMTP_USER', '4114c399-c367-403f-bfa3-20286304d046');
define('SMTP_PASSWORD', '4114c399-c367-403f-bfa3-20286304d046');
*/

define('SMTP_HOST', 'smtp.elasticemail.com');
define('SMTP_PORT', '2525');
define('SMTP_USER', 'cccaaa@as.com');
define('SMTP_PASSWORD', 'xxx');


define('EMAIL_MONITORAMENTO', 'aa@aa.com.br');
//define('EMAIL_MONITORAMENTO', 'lucas@vold.com.br');

define('SITE_HTTP', "http://" . $_SERVER['HTTP_HOST'] . "/");
define('SITE_HTTP_STATIC', "http://" . $_SERVER['HTTP_HOST']);