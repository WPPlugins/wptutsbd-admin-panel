<?php

/*
Plugin Name: Wptutsbd Dashboard
Plugin URI: http://wptutsbd.com/custom_dashboard_plugin
Description: wptutsbd Admin/Dashboard Theme
Author: WPTutsBD
Version: 1.0
Author URI: http://WPtutsBD.com


  Copyright 2013  WPTUTSBD  (email : WPtutsbd@outlook.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/








define('SCRIPT_DEBUG', true);
if(class_exists("wptutsbd_admin_ui")){
    $wptutsbd_admin_ui= new wptutsbd_admin_ui();
}

class wptutsbd_admin_ui{
    const LANG = 'wptutsbd-admin';
    public $suffix;
    public $pluginURL;
    public $OptionsName = "wptutsbd-admin-option";
    public $color;
    public $admin_bar;
    public function __construct(){
        $option=$this->getOptions();
        if(isset($_POST['settings']['color'])) $this->color=$_POST['settings']['color'];
        else {

            $this->color=(isset($option['settings']['color']))?$option['settings']['color']:0;
        }
        if(isset($_POST['reset_wptutsbd_settings']) || isset($_POST['reset_settings'])) $this->color='0';

        $this->pluginURL = plugins_url().'/'.str_replace(basename( __FILE__ ),"",plugin_basename( __FILE__ ) );
        $this->suffix=defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
        add_action('init', array($this,'load_language'));

        if(is_admin()){
            add_action('init',array($this,'remove_default_stylesheets'));
            add_action('wp_before_admin_bar_render' , array( $this , 'admin_bar_default_load' ) , 1 );
            add_action('wp_before_admin_bar_render' , array( $this , 'update_admin_bar' ) , 100 );
            add_action('wp_dashboard_setup', array($this,'get_dashboard_widgets') ,100);
            add_action('wp_dashboard_setup', array($this,'hide_dashboard_widgets'),101);
            add_action( 'admin_menu', array($this,'register_wptutsbd_admin_menu' ));
            add_action('admin_print_styles', array($this,'loading_css'),19);
            add_action('admin_enqueue_scripts', array($this,'loading_js'),18);
            remove_action( 'wp_default_styles', 'wp_default_styles' );              // removes the default wp_default_styles function
            add_action( 'wp_default_styles', array($this,'wptutsbd_admin_wp_default_styles') ); // adds our customized wptutsbd_admin_wp_default_styles function
            add_action('admin_menu', array($this,'new_menus'),99);
            add_action('admin_head',array($this,'generate_dashboard_icons'),100);
            add_action('admin_head',array($this,'generate_menu_icons'),100);
            add_action('admin_footer',array($this,'generate_admin_logo'));
            add_action('admin_footer',array($this,'generate_custom_css'),100);
            add_filter('update_footer', array($this,'update_version_footer'), 100);
            add_filter('admin_footer_text', array($this,'update_admin_footer'));

        }
        else if($this->is_login()){
            if(isset($option['login_screen']))
                if(isset($option['login_screen']['active']) && $option['login_screen']['active']=='1'){
                    add_action('init',array($this,'remove_default_stylesheets'));
                    add_action('login_head', array($this,'generate_login_bg'),10);
                    add_filter('login_headertitle' , array( $this , 'login_logo_title' ) );
                    add_action('login_head' , array( $this , 'login_logo_image' ) );
                    add_filter('login_headerurl', array( $this , 'login_logo_url' ) );
                    add_filter('bloginfo',array($this,'login_logo_text'),10,2);
                    add_action('login_footer',array($this,'login_footer_text'));
                    // add script + css
                    add_action('login_enqueue_scripts', array($this,'wptutsbd_admin_login_css'));
                    add_action('login_head', array($this,'wptutsbd_admin_login_js'),100);
                    if(isset($option['login_screen']['lost_password']) && $option['login_screen']['lost_password']=='1')
                        add_filter( 'gettext', array($this,'remove_lostpassword_text' ));
                    if(isset($option['login_screen']['back_to']) && $option['login_screen']['back_to']=='1')
                        add_filter( 'gettext', array($this,'remove_backto_text' ));
                    if(isset($option['login_screen']['remember_me']) && $option['login_screen']['remember_me']=='1')
                        add_action('login_head' , array( $this , 'remove_remember_me' ),100 );
                }

        }


    }
    public function remove_default_stylesheets() {
        wp_deregister_style('wp-admin');
    }
    public function load_language() {
        $path = dirname(plugin_basename( __FILE__ )) . '/languages/';
        $loaded = load_plugin_textdomain( 'wptutsbd-admin', false, $path);

    }

    public function is_login() {
        return in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) );
    }

    public function loading_js(){
        wp_enqueue_script('jquery');
        wp_enqueue_media();
        wp_enqueue_script('media-upload');
        wp_deregister_script('admin-bar');
        wp_enqueue_script('admin-bar', $this->pluginURL.'assets/js/script.js', array("jquery"), null, false);
        wp_enqueue_script('wptutsbd-setting-script', $this->pluginURL.'assets/js/settings.js', array("jquery"), null, false);

    }
    public function loading_css(){

        // WPtutsBD admin Style
        wp_enqueue_style('wptutsbd-admin-font', $this->pluginURL."assets/fonts/css/font-awesome$this->suffix.css", false, '1.0');
        wp_enqueue_style('customized-wptutsbd', $this->pluginURL."assets/css/wptutsbd-style.css", false, '1.0');
        wp_enqueue_style('wptutsbd-admin-admin-bar', $this->pluginURL."assets/css/adminbar.css", false, '1.0');
        wp_enqueue_style('wptutsbd-admin-custom', $this->pluginURL."assets/css/wptutsbd-admin.css", false, '1.0');

        if($this->color!='0')
            wp_enqueue_style('wptutsbd-admin-color', $this->pluginURL."assets/css/colors/".$this->color.".css", false, '1.0');

        if ( is_rtl() )
            wp_enqueue_style('wptutsbd-admin-rtl', $this->pluginURL."assets/css/rtl.css", false, '1.0');

    }
    public function wptutsbd_admin_login_css(){
        echo '<link rel="stylesheet" id="wptutsbd-admin-login"  href="'.$this->pluginURL."assets/css/login-styles.css".'" type="text/css" media="all" />';
        if($this->color!='0')
            echo '<link rel="stylesheet" id="wptutsbd-admin-color"  href="'.$this->pluginURL."assets/css/colors/".$this->color.'.css" type="text/css" media="all" />';
    }
    public function wptutsbd_admin_login_js(){
        // admin login
        wp_enqueue_script('wptutsbd-admin-login', $this->pluginURL."assets/js/login.js", false, '1.0');
    }
    /** get list dash widget */
    public function get_dashboard_widgets() {
        global $wp_meta_boxes, $wpdb;
        $dw= get_option($wpdb->prefix.'wptutsbd_admin_dashboard_widget_registered');
        if (current_user_can('administrator') && is_array($wp_meta_boxes['dashboard'])) {
            if($dw==false) $id_registered_dash_widget=array();
            else $id_registered_dash_widget=$dw;
            foreach(array('normal','side','column3','column4') as $context){
                if(isset($wp_meta_boxes['dashboard'][$context]))
                    foreach ( array('high', 'sorted', 'core', 'default', 'low') as $priority ) {
                        if(isset($wp_meta_boxes['dashboard'][$context][$priority]))
                            foreach ( (array) $wp_meta_boxes['dashboard'][$context][$priority] as $box ) {
                                if(!in_array($box['id'],$id_registered_dash_widget))
                                    array_push($id_registered_dash_widget,$box['id']);
                            }
                    }
            }
            update_option($wpdb->prefix.'wptutsbd_admin_dashboard_widget_registered', $id_registered_dash_widget);
        }


    }
    public function hide_dashboard_widgets(){
        $Options = $this->getOptions();
        global $wp_meta_boxes;
        if (current_user_can('administrator') && is_array($wp_meta_boxes['dashboard'])) {

            foreach(array('normal','side','column3','column4') as $context){
                if(isset($wp_meta_boxes['dashboard'][$context]))
                    foreach ( array('high', 'sorted', 'core', 'default', 'low') as $priority ) {
                        if(isset($wp_meta_boxes['dashboard'][$context][$priority]))
                            foreach ( (array) $wp_meta_boxes['dashboard'][$context][$priority] as $box ) {

                                if(isset($Options['dashboard_icons'][$box['id']]['show']) && $Options['dashboard_icons'][$box['id']]['show']==0)
                                    remove_meta_box( $box['id'], 'dashboard', $priority );
                            }
                    }
            }

        }
    }
    /** option */
    public function getOptions() {
        $wptutsbdOptions=array();
        $Options = get_option($this->OptionsName);

        if (!empty($Options)) {
            foreach ($Options as $key => $option)
                $wptutsbdOptions[$key] = $option;
        }
        else update_option($this->OptionsName, $wptutsbdOptions);

        return $wptutsbdOptions;
    }

    /** admin menu */
    public function register_wptutsbd_admin_menu(){
        add_menu_page( 'Wptutsbd Admin', 'WTB Admin', 'manage_options', 'wptutsbd-admin-ui-settings', array($this,'wptutsbd_admin_ui_setting_page'), '' );
        add_submenu_page( 'wptutsbd-admin-ui-settings', 'Settings', 'Settings', 'manage_options', 'wptutsbd-admin-ui-settings', array($this,'wptutsbd_admin_ui_setting_page') );
        add_submenu_page( 'wptutsbd-admin-ui-settings', 'Admin Bar', 'Admin Bar', 'manage_options', 'wptutsbd-admin-bar', array($this,'wptutsbd_admin_bar') );
        add_submenu_page( 'wptutsbd-admin-ui-settings', 'Menu Icons', 'Menu Icons', 'manage_options', 'wptutsbd-admin-menu-icons', array($this,'wptutsbd_admin_menu_icons') );
        add_submenu_page( 'wptutsbd-admin-ui-settings', 'Dashboard Icons', 'Dashboard Icons', 'manage_options', 'wptutsbd-admin-dashboard-icons', array($this,'wptutsbd_admin_dashboard_icons') );
        add_submenu_page( 'wptutsbd-admin-ui-settings', 'Login Screen', 'Login Screen', 'manage_options', 'wptutsbd-admin-login-screen', array($this,'wptutsbd_admin_login_screen') );
    }

    public function wptutsbd_admin_ui_setting_page(){
        include("includes/settings.php");
    }
    /** menu icons */
    public function new_menus(){
        global $menu;
        //var_dump($menu);
        $new_menu=array();
        foreach($menu as $item){
            if($item[4]!='wp-menu-separator' && !preg_match("/separator/i",$item[4])){

                $name=$this->get_name($item[0]);
                //var_dump($item);
                if(preg_match("/menu-/i",$item[5])) $id=str_replace("menu-","menu-icon-",$item[5]);
                else{
                    $id=strtolower($name);
                    $id="menu-icon-".str_replace(" ","-",$id);
                }
                if(isset($item[4]) && $item[4]!=''){
                    if(!preg_match("/".$id."/i",$item[4])){
                        $item[4].=" ".$id;
                    }
                }else $item[4]=$id;

            }
            array_push($new_menu,$item);
        }
        $menu=$new_menu;
    }
    public function generate_menu_icons(){
        if(!isset($_POST['reset_menu_icons'])){
            global $menu;
            $ids=array();
            foreach($menu as $item)
                if($item[4]!='wp-menu-separator' && !preg_match("/separator/i",$item[4]) )
                {
                    $name=$this->get_name($item[0]);
                    if(preg_match("/menu-/i",$item[5])) $id=str_replace("menu-","menu-icon-",$item[5]);
                    else{
                        $id=strtolower($name);
                        $id="menu-icon-".str_replace(" ","-",$id);
                    }
                    array_push($ids,$id);
                }
            $Options = $this->getOptions();

            if(isset($_POST['menu_icons'])){
                $menu_icons=array();
                foreach($_POST['menu_icons'] as $key => $value)
                    $menu_icons[$key]=$value;
                $Options['menu_icons'] = $menu_icons;
            }

            $style="<style type=\"text/css\">\n";
            foreach($ids as $i){
                if(isset($Options['menu_icons'][$i]) && $Options['menu_icons'][$i]!='')
                    $style.=".".$i. " .wp-menu-image:before, .wptutsbd-admin-" .$i. ":before{content: \"\\".$Options['menu_icons'][$i]."\" !important;}\n";
            }
            $style.="</style>\n";
            echo $style;
        }

    }
    private function get_name($key){
        if(preg_match("/<span/",$key)){
            $key=explode("<",$key);
            return $key[0];
        }
        return strip_tags($key);

    }
    public function wptutsbd_admin_menu_icons() {
        include("includes/menu_icons.php");

    }
    /** dashboard icons */
    public function wptutsbd_admin_dashboard_icons() {
        include('includes/dashboard_icons.php');
    }

    public function generate_dashboard_icons(){
        $Options = $this->getOptions();
        if(isset($_POST['dashboard_icons'])){
            $dashboard_icons=array();
            foreach($_POST['dashboard_icons'] as $key => $value)
                $dashboard_icons[$key]=$value;
            $Options['dashboard_icons'] = $dashboard_icons;
        }
        $style="";
        if(isset($Options['dashboard_icons']) && is_array($Options['dashboard_icons'])){
            $style="<style type=\"text/css\">\n";
            foreach(array_keys($Options['dashboard_icons']) as $i){
                if(isset($Options['dashboard_icons'][$i]['icon']) && $Options['dashboard_icons'][$i]['icon']!='')
                    $style.="#".$i. " .hndle > span:before, .wptutsbd-admin-".$i. ":before {content: \"\\".$Options['dashboard_icons'][$i]['icon']."\" !important;}\n";
            }
            $style.="</style>\n";
        }
        echo $style;

    }

    /** end admin menu */
    /** admin style */
    public function wptutsbd_admin_wp_default_styles( &$styles ) {
        if ( ! $guessurl = site_url() )
            $guessurl = wp_guess_url();
        $styles->base_url = $guessurl;
        $styles->content_url = defined('WP_CONTENT_URL')? WP_CONTENT_URL : '';
        $styles->default_version = get_bloginfo( 'version' );
        $styles->text_direction = function_exists( 'is_rtl' ) && is_rtl() ? 'rtl' : 'ltr';
        $styles->default_dirs = array('/wp-admin/', '/wp-includes/css/');
        $rtl_styles = array( 'wp-admin', 'ie', 'media', 'admin-bar', 'customize-controls', 'media-views', 'wp-color-picker' );
        $no_suffix = array( 'farbtastic' );
        $styles->add( 'wp-admin', "/wp-admin/css/wp-admin$this->suffix.css" );
        $styles->add( 'ie', "/wp-admin/css/ie$this->suffix.css" );
        $styles->add_data( 'ie', 'conditional', 'lte IE 7' );
        $styles->add( 'colors', true, array('wp-admin', 'buttons') );
        $styles->add( 'media', "/wp-admin/css/media$this->suffix.css" );
        $styles->add( 'install', "/wp-admin/css/install$this->suffix.css", array('buttons') );
        $styles->add( 'thickbox', '/wp-includes/js/thickbox/thickbox.css', array(), '20121105' );
        $styles->add( 'farbtastic', '/wp-admin/css/farbtastic.css', array(), '1.3u1' );
        $styles->add( 'wp-color-picker', "/wp-admin/css/color-picker$this->suffix.css" );
        $styles->add( 'jcrop', "/wp-includes/js/jcrop/jquery.Jcrop.min.css", array(), '0.9.10' );
        $styles->add( 'imgareaselect', '/wp-includes/js/imgareaselect/imgareaselect.css', array(), '0.9.8' );
        $styles->add( 'admin-bar', "/wp-includes/css/admin-bar$this->suffix.css" );
        $styles->add( 'wp-jquery-ui-dialog', "/wp-includes/css/jquery-ui-dialog$this->suffix.css" );
        $styles->add( 'editor-buttons', "/wp-includes/css/editor$this->suffix.css" );
        $styles->add( 'wp-pointer', "/wp-includes/css/wp-pointer$this->suffix.css" );
        $styles->add( 'customize-controls', "/wp-admin/css/customize-controls$this->suffix.css", array( 'wp-admin', 'colors', 'ie' ) );
        $styles->add( 'media-views', "/wp-includes/css/media-views$this->suffix.css", array( 'buttons' ) );
        $styles->add( 'buttons', $this->pluginURL.'assets/css/buttons.css' );
        foreach ( $rtl_styles as $rtl_style ) {
            $styles->add_data( $rtl_style, 'rtl', true );
            if ( $this->suffix && ! in_array( $rtl_style, $no_suffix ) )
                $styles->add_data( $rtl_style, 'suffix', $this->suffix );
        }
    }

    /** Login Screen */
    public function generate_login_bg(){
        $Options=$this->getOptions();
        $img=(isset($Options['login_screen']['background']))?$Options['login_screen']['background']:'';
        $color=(isset($Options['login_screen']['bg-color']))?$Options['login_screen']['bg-color']:'';
        if($img!='' || $color!=''){
            $style="<style>\n";
            if($color!='') $style.="html {background-color:".$color.";}";
            $style.="body.login {\n";

            if($color!='') $style.="background-color:".$color.";\n";
            if($img!='') $style.="background-image:url('".$img."');\n";
            if(!empty($Options['login_screen']['bg-repeat'])) $style.="background-repeat:".$Options['login_screen']['bg-repeat'].";\n";
            if(!empty($Options['login_screen']['bg-position'])) $style.="background-position:".$Options['login_screen']['bg-position'].";\n";
            $style.="}\n";
            $style.="</style>";
            echo $style;
        }else echo '';

    }
    public function wptutsbd_admin_login_screen(){
        include('includes/login_screen.php');
    }
    public function login_logo_image() {
        $Options=$this->getOptions();
        if(isset($Options['login_screen']))
            if(!empty( $Options['login_screen']['image'] ) && $Options['login_screen']['image']!='' ) {
                $script="<script>\n";
                $script.="var link=\"".$Options['login_screen']['image']."\";\n";
                $script.="</script>";
                echo $script;
            }
    }

    public function login_logo_title(){
        $Options=$this->getOptions();
        $title = __( 'Powered by WordPress', self::LANG );
        if(isset($Options['login_screen']))
            if(!empty( $Options['login_screen']['title'] ) && $Options['login_screen']['title'] !='') {
                $title = strip_tags( $Options['login_screen']['title']  );

            }
        return $title;
    }

    public function login_logo_url(){
        $Options=$this->getOptions();
        $url = __( 'http://wordpress.org', self::LANG);
        if(isset($Options['login_screen']))
            if(!empty( $Options['login_screen']['url']  ) && $Options['login_screen']['url']!='') {
                $url = strip_tags( $Options['login_screen']['url']  );
            }
        return $url;
    }

    public function login_logo_text($content,$show){
        $Options=$this->getOptions();
        if(isset($Options['login_screen']['text']))
            if($Options['login_screen']['text']!='' && ($Options['login_screen']['image']=='' || empty( $Options['login_screen']['image'] )) ) {
                if ($show == 'name'){
                    $content = $Options['login_screen']['text'];
                }
            }
        return $content;
    }

    public function login_footer_text(){
        $Options=$this->getOptions();
        $text='';
        if(isset($Options['login_screen']))
            if(!empty( $Options['login_screen']['footer_text'] )) {

                $text = "<div id=\"footer_text\">".esc_html($Options['login_screen']['footer_text'])."</div>";

            }
        echo $text;
    }
    public function remove_lostpassword_text ( $text ) {
        if ($text == 'Lost your password?'){$text = '';}
        return $text;
    }
    public function remove_backto_text ( $text ) {
        if (preg_match("/Back to/i",$text)){$text = '';}
        return $text;
    }
    public function remove_remember_me(){
        $css="<style>\n";
        $css.=".forgetmenot { display:none; }";
        $css.="</style>";
        echo $css;
    }
    /*** admin logo **/
    public function generate_admin_logo(){
        $Options=$this->getOptions();
        $html='';
        if(!empty($Options['settings']['admin_logo_image']) || !empty($Options['settings']['admin_logo_text'])){
            $html="<div id=\"wptutsbd-admin-logo\">";
            $html.="<a href=\"".$Options['settings']['admin_logo_url']."\">";
            if($Options['settings']['admin_logo_image']!='')
                $html.="<img src=\"".$Options['settings']['admin_logo_image']."\">";
            $html.=$Options['settings']['admin_logo_text'];
            $html.="</a>";
            $html.="</div>\n";
        }
        echo $html;
    }
    /**Admin bar **/
    public function wptutsbd_admin_bar(){
        include("includes/admin_bar.php");
    }
    public function admin_bar_default_load( $wp_admin_bar ) {
        global $wp_admin_bar;
        $this->admin_bar = $wp_admin_bar->get_nodes();

    }
    public function admin_bar_filter_load() {
        $Default_bar = $this->admin_bar;
        $current_user = wp_get_current_user();
        $Delete_bar = array( "user-actions" , "wp-logo-external" , "top-secondary" , "my-sites-super-admin" , "my-sites-list" );
        foreach( $Delete_bar as $del_name ) {
            if( !empty( $Default_bar[$del_name] ) ) {
                unset( $Default_bar[$del_name] );
            }
        }
        foreach( $Default_bar as $node_id => $node ) {
            if( preg_match( "/blog-[0-9]/" , $node->parent ) ) {
                unset( $Default_bar[$node_id] );
            }
        }

        foreach( $Default_bar as $node_id => $node ) {
            if( $node->id == 'my-account' ) {
                $str = sprintf( __( 'Howdy, %1$s', self::LANG ) , '[user_name]' );
                $Default_bar[$node_id]->title = str_replace('Howdy, '.$current_user->display_name,$str,$Default_bar[$node_id]->title);
            } elseif( $node->id == 'user-info' ) {
                $str ='<span class="display-name">[user_name]</span>';
                $Default_bar[$node_id]->title = str_replace('<span class="display-name">'.$current_user->display_name.'</span>, ',$str,$Default_bar[$node_id]->title);
            } elseif( $node->id == 'logout' ) {
                $Default_bar[$node_id]->href = preg_replace( '/&amp(.*)/' , '' , $node->href );
            } elseif( $node->id == 'site-name' ) {
                $Default_bar[$node_id]->title = '[blog_name]';
            } elseif( $node->id == 'updates' ) {
                $Default_bar[$node_id]->title = '[update_total]';
            } elseif( $node->id == 'comments' ) {
                $Default_bar[$node_id]->title = '[comment_count]';
            }
        }

        $Filter_bar = array();
        $MainMenuIDs = array();

        foreach( $Default_bar as $node_id => $node ) {
            if( empty( $node->parent ) ) {
                $Filter_bar["left"]["main"][$node_id] = $node;
                $MainMenuIDs[$node_id] = "left";
                unset( $Default_bar[$node_id] );
            } elseif( $node->parent == 'top-secondary' ) {
                $Filter_bar["right"]["main"][$node_id] = $node;
                $MainMenuIDs[$node_id] = "right";
                unset( $Default_bar[$node_id] );
            }
        }

        foreach( $Default_bar as $node_id => $node ) {
            if( $node->parent == 'wp-logo-external' ) {
                $Default_bar[$node_id]->parent = 'wp-logo';
            } elseif( $node->parent == 'user-actions' ) {
                $Default_bar[$node_id]->parent = 'my-account';
            } elseif( $node->parent == 'my-sites-list' ) {
                $Default_bar[$node_id]->parent = 'my-sites';
            } else{
                if( !array_keys( $MainMenuIDs , $node->parent ) ) {
                    if( !empty( $Default_bar[$node->parent] ) ) {
                        $Default_bar[$node_id]->parent = $Default_bar[$node->parent]->parent;
                    }
                }
            }
        }

        foreach( $MainMenuIDs as $parent_id => $menu_type ) {

            foreach( $Default_bar as $node_id => $node ) {
                if( $node->parent == $parent_id ) {
                    $Filter_bar[$menu_type]["sub"][$node_id] = $node;
                    unset( $Default_bar[$node_id] );
                }


            }
        }

        return $Filter_bar;
    }

    public function update_admin_bar($wp_admin_bar){
        if(!isset($_POST['reset_admin_bar'])){
            $Options = $this->getOptions();
            if(isset($_POST['admin_bar'])){
                $admin_bar=array();
                foreach($_POST['admin_bar'] as $key => $value){
                    $href=(isset($value["href"]))?$value["href"]:'';
                    $show=(isset($value["show"]))?$value["show"]:0;
                    $title = (isset($value["title"]))?$value["title"]:'';
                    $admin_bar[$key]['href']=$href;
                    $admin_bar[$key]['show']=$show;
                    $admin_bar[$key]['title']=$title;
                }

                $Options['admin_bar'] = $admin_bar;
            }
            global $wp_admin_bar;
            $admin_bar = $wp_admin_bar->get_nodes();
            foreach($admin_bar as $node_id => $node){
                if(isset($Options['admin_bar'][$node_id]['show']) && $Options['admin_bar'][$node_id]['show']==0)
                    $wp_admin_bar->remove_menu($node_id);
                else{
                    $new_node=$wp_admin_bar->get_node($node_id);
                    if(!empty($Options['admin_bar'][$node_id]['title']) && $Options['admin_bar'][$node_id]['show']!=$node->title){
                        $title=$this->val_replace($Options['admin_bar'][$node_id]['title']);
                        $new_node->title=stripslashes($title);
                    }

                    if(isset($Options['admin_bar'][$node_id]['href']) && $Options['admin_bar'][$node_id]['href']!=$node->href)
                        $new_node->href=$Options['admin_bar'][$node_id]['href'];
                    $wp_admin_bar->remove_menu($node_id);
                    $wp_admin_bar->add_node($new_node);
                }

            }
        }

    }
    private function val_replace( $str ) {

        if( !empty( $str ) ) {

            $update_data = wp_get_update_data();
            $awaiting_mod = wp_count_comments();
            $awaiting_mod = $awaiting_mod->moderated;
            $current_user = wp_get_current_user();
            if( is_multisite() ) {
                $current_site = get_current_site();
            }
            if( strstr( $str , '[comment_count]') ) {
                if ( current_user_can('edit_posts') ) {
                    $str  = str_replace( '[comment_count]' , '<span class="ab-icon"></span><span id="ab-awaiting-mod" class="ab-label awaiting-mod pending-count count-[comment_count]">[comment_count_format]</span>' ,  $str );
                }
            }
            if( strstr( $str , '[update_total]') ) {
                if ( $update_data['counts']['total'] ) {
                    $str = str_replace( '[update_total]' , '<span class="ab-icon"></span><span class="ab-label">[update_total_format]</span>' ,  $str );
                }
            }
            if( strstr( $str , '[update_plugins]') ) {
                if ( $update_data['counts']['plugins'] ) {
                    $str  = str_replace( '[update_plugins]' , '[update_plugins_format]' , $str  );
                }
            }
            if( strstr( $str  , '[update_themes]') ) {
                if ( $update_data['counts']['themes'] ) {
                    $str  = str_replace( '[update_themes]' , '[update_themes_format]' ,  $str );
                }
            }

            if( strstr( $str , '[blog_url]') ) {
                $str = str_replace( '[blog_url]' , get_bloginfo( 'url' ) , $str );
            }
            if( strstr( $str , '[template_directory_uri]') ) {
                $str = str_replace( '[template_directory_uri]' , get_bloginfo( 'template_directory' ) , $str );
            }
            if( strstr( $str , '[stylesheet_directory_uri]') ) {
                $str = str_replace( '[stylesheet_directory_uri]' , get_stylesheet_directory_uri() , $str );
            }
            if( strstr( $str , '[blog_name]') ) {
                $str = str_replace( '[blog_name]' , get_bloginfo( 'name' ) , $str );
            }
            if( strstr( $str , '[update_total]') ) {
                $str = str_replace( '[update_total]' , $update_data["counts"]["total"] , $str );
            }
            if( strstr( $str , '[update_total_format]') ) {
                $str = str_replace( '[update_total_format]' , number_format_i18n( $update_data["counts"]["total"] ) , $str );
            }
            if( strstr( $str , '[update_plugins]') ) {
                $str = str_replace( '[update_plugins]' , $update_data["counts"]["plugins"] , $str );
            }
            if( strstr( $str , '[update_plugins_format]') ) {
                $str = str_replace( '[update_plugins_format]' , number_format_i18n( $update_data["counts"]["plugins"] ) , $str );
            }
            if( strstr( $str , '[update_themes]') ) {
                $str = str_replace( '[update_themes]' , $update_data["counts"]["themes"] , $str );
            }
            if( strstr( $str , '[update_themes_format]') ) {
                $str = str_replace( '[update_themes_format]' , number_format_i18n( $update_data["counts"]["themes"] ) , $str );
            }
            if( strstr( $str , '[comment_count]') ) {
                $str = str_replace( '[comment_count]' , $awaiting_mod , $str );
            }
            if( strstr( $str , '[comment_count_format]') ) {
                $str = str_replace( '[comment_count_format]' , number_format_i18n( $awaiting_mod ) , $str );
            }
            if( strstr( $str , '[user_name]') ) {
                $str = str_replace( '[user_name]' , '<span class="display-name">' . $current_user->display_name . '</span>' , $str );
            }

            if( is_multisite() ) {
                if( strstr( $str , '[site_name]') ) {
                    $str = str_replace( '[site_name]' , esc_attr( $current_site->site_name ) , $str );
                }
                if( strstr( $str , '[site_url]') ) {
                    $protocol = is_ssl() ? 'https://' : 'http://';
                    $str = str_replace( '[site_url]' , $protocol . esc_attr( $current_site->domain ) , $str );
                }
            }

        }

        return $str;
    }

   
    public function update_admin_footer ()
    {
        $text =__('<script>
<!--
document.write(unescape("Thank%20you%20for%20Using%20%3Ca%20href%3D%22http%3A//wptutsbd.com/custom_dashboard_plugin%22%3E%20WPtutsBD%20/%20WTB%20Admin%20panel%3C/a%3E"));
//-->
</script>.',self::LANG);
        if(!isset($_POST['reset_settings'])){
            $Options = $this->getOptions();
            if(!empty($Options['settings']['admin_footer_text']))
                $text = $Options['settings']['admin_footer_text'];
        }

        echo $text;


    }

    public function update_version_footer($upgrade){
        $Options=$this->getOptions();
        if(isset($_POST['reset_settings'])) return $upgrade;
        if(!isset($Options['settings']['admin_footer_version'])) echo '';
        elseif($Options['settings']['admin_footer_version']==1)
            return $upgrade;


    }
    /**Custom CSS**/
    public function generate_custom_css(){
        $text ='';
        if(!isset($_POST['reset_settings'])){
            $Options = $this->getOptions();
            if(isset($_POST['settings']['custom_css'])) $Options['settings']['custom_css']=$_POST['settings']['custom_css'];
            if(!empty($Options['settings']['custom_css'])){
                $text="<style>\n";
                $text.= $Options['settings']['custom_css']."\n";
                $text.="</style>\n";
            }
        }
        echo $text;
    }

}



?>