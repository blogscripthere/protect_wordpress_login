<?php
/**
 * @package Protect Your WordPress Login
 * @version 1.0
 */
/*
Plugin Name: ScriptHere's Protect Your WordPress Login.
Plugin URI: https://github.com/blogscripthere/protect_wordpress_login
Description:  It's a simple plugin for choosing a key,value pair(i.e in a query string add key and value ex: ?key=value) and accessing the login page.
Author: Narendra Padala
Author URI: https://in.linkedin.com/in/narendrapadala
Text Domain: sh
Version: 1.0
Last Updated: 27/01/2018
*/

/**
 * define here what ever key would you like to use in your wp-login page
 * http://yoursite.com/?your_key=your_value
 */
define('SH_CUSTOM_KEY','your_key');
/**
 * define here what ever key would you like to use in your wp-login page
 * http://yoursite.com/?your_key=your_value
 */
define('SH_CUSTOM_VAL','your_value');

/**
 * Before displaying the login form, check the availability and validity of the login key.
 */
function sh_show_login_form_callback(){
    /**
     * check if you are sending a login and password,
     * this means that the form has been displayed.
     * Therefore, the login key has been verified.
     */
    if(!empty($_REQUEST['log'])){ return; }
    /**
     * Some common Wordpress actions should be allowed.
     * For example, log if you're logged out,
     * postpass used to display password-protected posts.
     */
    $valid_actions = array('logout', 'postpass');
    /**
     * Check valid actions
     */
    if(isset($_REQUEST['action']) && in_array($_REQUEST['action'], $valid_actions)){
        return;
    }
    /**
     * Check if the key is provided in the query string or not
     */
    if(isset($_REQUEST[SH_CUSTOM_KEY]) && $_REQUEST[SH_CUSTOM_KEY] == SH_CUSTOM_VAL){
        return;
    }
    /**
     * Finally, show error message
     */
    die("Your not authorized to access this page...!");
}

/**
 * Overwrite login page hook
 */
add_action('login_init', 'sh_show_login_form_callback');

/**
 * This will add a hidden field to the login form.
 * Send the password before logging in and review it before validating.
 */
function sh_add_hidden_field_callback(){
    /**
     * Set, hidden field
     */
    echo '<input type="hidden" name="'.SH_CUSTOM_KEY.'" value="'.SH_CUSTOM_VAL.'"/>';
}

/**
 * Inject the hidden field login form hook
 */
add_action('login_form', 'sh_add_hidden_field_callback');


/**
 * This method is called before the user is authenticated.
 * In this case, confirm that the key is provided from the login form.
 * Anyone can send a request to POST and try to log in without using the login form.
 * Anyway, that's what the robot does.
 */
function sh_login_authenticate_callback(){
    /**
     * Check, If you have not sent the login form, please release it
     */
    if(empty($_REQUEST['log'])){
        return;
    }
    /**
     * Check, Is the key given for the check?
     */
    if(!isset($_REQUEST[SH_CUSTOM_KEY])){
        //show error message
        die("Your not authorized to access this page...!");
    }
    /**
     * Check, Confirm the validity of the posted key
     */
    if(isset($_REQUEST[SH_CUSTOM_KEY])&& $_REQUEST[SH_CUSTOM_KEY] != SH_CUSTOM_VAL ){
        //show error message
        die("Your not authorized to access this page...!");
    }
}
/**
 * Confirm that the login key is provided on the POST login request hook
 */
add_action('wp_authenticate', 'sh_login_authenticate_callback');
