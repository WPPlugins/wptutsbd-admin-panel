<?php
global $wpdb;
if(isset($_POST['reset_wptutsbd_settings'])) $Options=$this->getOptions(true);
else $Options=$this->getOptions();
if(isset($_POST['reset_wptutsbd_settings'])) {
delete_option($this->OptionsName);
delete_option($wpdb->prefix.'wptutsbd_admin_dashboard_widget_registered');
?>
<div class="updated"><p><strong><?php _e('Reset successfully',self::LANG);?>.</strong></p></div>
<?php
}
if (isset($_POST['save_wptutsbd_settings'])) {
    $settings=array();
    if(isset($_POST['settings'])){
        foreach($_POST['settings'] as $key => $value){
            $settings[$key]=$value;
        }
        $Options['settings'] = $settings;
        update_option($this->OptionsName, $Options);
    }
    ?>
    <div class="updated"><p><strong><?php _e('Settings Updated',self::LANG);?>.</strong></p></div>
<?php
}
if(isset($_POST['reset_settings'])){
    unset($Options['settings']);
    update_option($this->OptionsName, $Options);
    ?>
    <div class="updated"><p><strong><?php _e('Reset ok',self::LANG);?>.</strong></p></div>
<?php
}
$color = (isset($Options['settings']['color']))?$Options['settings']['color']:0;
$custom_css=(isset($Options['settings']['custom_css']))?$Options['settings']['custom_css']:'';
$admin_logo_image = (isset($Options['settings']['admin_logo_image']))?$Options['settings']['admin_logo_image']:'';
$admin_logo_text = (isset($Options['settings']['admin_logo_text']))?$Options['settings']['admin_logo_text']:'';
$admin_logo_url = (isset($Options['settings']['admin_logo_url']))?$Options['settings']['admin_logo_url']:'';
$admin_footer_text = (isset($Options['settings']['admin_footer_text']))?$Options['settings']['admin_footer_text']:'';
$admin_footer_version = (isset($Options['settings']['admin_footer_version']))?$Options['settings']['admin_footer_version']:1;
?>
<div class="wrap">
    <div id="icon-options-general" class="icon32"><br></div>
    <h2><?php _e('Settings',self::LANG);?></h2>

    <form action="" method="post">
        <h4><?php _e('Color',self::LANG);?></h4>
        <table class="form-table" id="wptutsbd-admin-icons-table">
            <tbody>
            <tr valign="top">
                <th scope="row"><?php _e('Choose color',self::LANG);?></th>
                <td id="front-static-pages">

                        <p>
                                <label for="admin_color1"><?php _e('Default',self::LANG);?></label>
                                <input type="radio" id="admin_color1" class="tog" value="0" <?php checked($color,0);?> name="settings[color]">

                        </p>
                        <p>
                                <label for="admin_color2"><?php _e('Red',self::LANG);?></label>
                                <input type="radio" id="admin_color2" class="tog" value="red" <?php checked($color,'red');?> name="settings[color]">

                        </p>
                        <p>
                                <label for="admin_color3"><?php _e('Yellow',self::LANG);?></label>
                                <input type="radio" id="admin_color3" class="tog" value="yellow" <?php checked($color,'yellow');?> name="settings[color]">

                        </p>
                        <p>
                                <label for="admin_color4"><?php _e('Green',self::LANG);?></label>
                                <input type="radio" id="admin_color4" class="tog" value="green" <?php checked($color,'green');?> name="settings[color]">

                        </p>
                        <p>
                                <label for="admin_color5"><?php _e('Purple',self::LANG);?></label>
                                <input type="radio" id="admin_color5" class="tog" value="purple" <?php checked($color,'purple');?> name="settings[color]">

                       

                </td>
                
            </tr>
            </tbody>
             </p>

                       
                       <p> Custom color is available on The pro version. Please Visit <a href="http://wptutsbd.com/custom_dashboard_plugin">WTB Admin panel Site</a>. to purchase the Pro version .
</P>
        </table>
        <h4><?php _e('Admin logo',self::LANG);?></h4>
        
       <table class="wptutsbd-admin-form form-table">
            <tbody>
           <tr valign="top">
                <th scope="row"><label for="admin_logo_image"><?php _e('Admin Logo Text',self::LANG);?></label></th>
                <td>
                    <input type="text" class="regular-text" id="admin_logo_image" value="<?php echo $admin_logo_text;?>" name="settings[admin_logo_text]">

                </td>
            </tr>
            

            </tbody>
        </table>
         <p>Admin/ Dashboard Top logo Branding is available on The pro version. Please Visit <a href="http://wptutsbd.com/custom_dashboard_plugin">WTB Admin panel Site</a>. to purchase the Pro version .
</P>
        <h4><?php _e('Footer Settings',self::LANG);?></h4>
        <table class="wptutsbd-admin-form form-table">
            <tbody>
           
            
           <tr valign="top">
                <th scope="row"><label for="admin_footer_version"><?php _e('Footer Version',self::LANG);?></label></th>
                <td>
                    <input type="checkbox" id="admin_footer_version" value="1" name="settings[admin_footer_version]" <?php checked($admin_footer_version,1);?>>
                <p> Footer Text Branding is available on The pro version. Please Visit <a href="http://wptutsbd.com/custom_dashboard_plugin">WTB Admin panel Site</a>. to purchase the Pro version .
</P>
                </td>

            </tr> 
           
            </tbody>
        </table>
        
        <p class="submit">

            <input type="submit" name="save_wptutsbd_settings" class="button button-primary"  value="<?php _e('Save Changes',self::LANG);?>">
            <input type="submit" name="reset_settings" class="button button-secondary"  value="<?php _e('Reset',self::LANG);?>">
        </p>
        <input type="submit" name="reset_wptutsbd_settings" class="button button-secondary"  value="<?php _e('Reset All Settings',self::LANG);?>">
    </form>
</div>