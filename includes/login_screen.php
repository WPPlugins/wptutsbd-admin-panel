<?php
$Options = $this->getOptions();
if (isset($_POST['save_login_screen'])){
    $login_fields=array();
    if(isset($_POST['login_screen'])){
        foreach($_POST['login_screen'] as $key => $value)
            $login_fields[$key]=$value;
        $Options['login_screen'] = $login_fields;
        update_option($this->OptionsName, $Options);
    }
?>
<div class="updated"><p><strong><?php _e('Settings Updated',self::LANG);?></strong></p></div>
<?php
}
if(isset($_POST['reset_login_screen'])){
    unset($Options['login_screen']);
    update_option($this->OptionsName, $Options);
    ?>
    <div class="updated"><p><strong><?php _e('Reset Ok',self::LANG);?></strong></p></div>
<?php
}
?>
<div class="wrap">
    <div id="icon-options-general" class="icon32"><br></div>
    <h2><?php _e('Login Screen Setting',self::LANG);?></h2>
  <h5> Styling your login page and contorlling the default wordpress features is only available on The pro version. Please Visit <a href="http://wptutsbd.com/custom_dashboard_plugin">WTB Admin panel Site</a>. to purchase the Pro version .Buy the pro version and Rebrand your websites's backend . You will get a lot of cool features more on the Pro version . SO hurry up :) 
</h5>  
</div>