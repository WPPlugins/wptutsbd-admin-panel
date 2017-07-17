<?php
global $wpdb;
$dw= get_option($wpdb->prefix.'wptutsbd_admin_dashboard_widget_registered');
if($dw==false){
    $dw=array('dashboard_right_now','dashboard_plugins','dashboard_plugins','dashboard_quick_press','dashboard_recent_drafts',);
    $d=get_option("dashboard_widget_options");
    if(is_array($d))
        foreach($d as $key =>$v)
            array_push($dw,$key);
}

$Options = $this->getOptions();
if (isset($_POST['save_dashboard_icons']))
{
    $dashboard_icons=array();
    if(isset($_POST['dashboard_icons'])){

        foreach($_POST['dashboard_icons'] as $key => $value){
            $icon=(isset($value["icon"]))?$value["icon"]:'';
            $show=(isset($value["show"]))? $value["show"]:0;
            $dashboard_icons[$key]['icon']=$icon;
            $dashboard_icons[$key]['show']=$show;

        }
        $Options['dashboard_icons'] = $dashboard_icons;
        update_option($this->OptionsName, $Options);
    }
?>
    <div class="updated"><p><strong><?php _e('Settings Updated',self::LANG);?>.</strong></p></div>
<?php
}
if(isset($_POST['reset_dashboard_icons'])){
    unset($Options['dashboard_icons']);
    update_option($this->OptionsName, $Options);
    ?>
    <div class="updated"><p><strong><?php _e('Reset Ok',self::LANG);?>.</strong></p></div>
<?php
}
?>
<div class="wrap">
    <div class="icon32" id="icon-tools"><br></div>
    <h2><?php _e('Dashboard Icons',self::LANG);?></h2>
    <h5><?php _e('Note: If you could not see some dashboard widgets, please enter to Dashboard Home then return and set icons for them!',self::LANG);?></h5>
    <form action="" method="post">

        <div class="clearfix">
            <table id="wptutsbd-admin-icons-table" class="wptutsbd-table-left form-table" >
                <tbody>
                <tr valign="top">
                    <td scope="row" class="wptutsbd-admin-dashboard-icon-show">Show</td>
                    <td>Name</td>
                    <td>Icon</td>
                </tr>
                <?php


                foreach($dw as $key){
                    $name = ucwords(str_replace(array("dashboard","_"),array(""," "),$key));
                    if($key=='dashboard_primary') $name='Wordpress Blog';
                    if($key=='dashboard_secondary') $name="Other Wordpress News";
                    $show=(isset($Options['dashboard_icons'][strip_tags($key)]['show']))? $Options['dashboard_icons'][strip_tags($key)]['show']:1;
                    $val=(isset($Options['dashboard_icons'][strip_tags($key)]['icon']))? $Options['dashboard_icons'][strip_tags($key)]['icon']:'';
                    ?>

                    <tr valign="top">

                        <td scope="row" class="wptutsbd-admin-dashboard-icon-show"><input type="checkbox" value="1" name="dashboard_icons[<?php echo strip_tags($key);?>][show]" <?php checked($show,1);?>></td>
                        <td><label for="<?php echo $key;?>"><?php echo $name;?></label></td>
                        <td><a href="#"><i class="md-icon wptutsbd-admin-<?php echo strip_tags($key);?> icon-star"></i><input type="hidden" class="regular-text" value="<?php echo $val;?>" id="<?php echo $key;?>" name="dashboard_icons[<?php echo strip_tags($key);?>][icon]"></a></td>
                    </tr>
                <?php
                }
                ?>

                </tbody>
            </table>
            <?php include("icons_list.php");?>
        </div>

        <p class="submit">
            <input type="submit" value="<?php _e('Save Changes',self::LANG);?>" class="button button-primary" id="save_dashboard_icons" name="save_dashboard_icons">
            <input type="submit" value="<?php _e('Reset',self::LANG);?>" class="button button-primary" name="reset_dashboard_icons">
        </p>
    </form>


</div>