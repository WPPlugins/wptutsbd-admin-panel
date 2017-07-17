<?php
global $menu;
$Options = $this->getOptions();
if (isset($_POST['save_menu_icons']))
{
    $menu_icons=array();
    if(isset($_POST['menu_icons'])){
        foreach($_POST['menu_icons'] as $key => $value)
            $menu_icons[$key]=$value;
        $Options['menu_icons'] = $menu_icons;
        update_option($this->OptionsName, $Options);
    }
?>
<div class="updated"><p><strong><?php _e('Settings Updated',self::LANG);?></strong></p></div>
<?php
}
if (isset($_POST['reset_menu_icons'])){
    unset($Options['menu_icons']);
    update_option($this->OptionsName, $Options);
    ?>
    <div class="updated"><p><strong><?php _e('Reset Ok',self::LANG);?></strong></p></div>
<?php
}
?>
<div class="wrap">
    <div class="icon32" id="icon-tools"><br></div><h2><?php _e('Menu Icons',self::LANG);?></h2>
    <h5> This feature is only available on The pro version. Please Visit <a href="http://wptutsbd.com/custom_dashboard_plugin">WTB Admin panel Site</a>. to purchase the Pro version .Buy the pro version and Rebrand your websites's backend . You will get a lot of cool features more on the Pro version . SO hurry up :) 
</h5>
</div>