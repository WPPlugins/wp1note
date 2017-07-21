<?php
$errorMessage		= '';
$successMessage 	= '';
$language           = "";
$details = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}options WHERE `option_name` = 'wp1Note_language'");
if (count($details) > 0) {            
	$language = $details[0]->option_value;
}

if(file_exists(plugin_dir_path(__FILE__).'language/'.$language.'.php')){
	include('language/'.$language.'.php');
	$mainheading                = $label['mainheading'];
	$mainsubheading             = $label['mainsubheading'];
	$mainLabel                  = $label['mainLabel'];
    $areYouSureToDelete         = $label['areYouSureToDelete'];
	$contentupdatedSuccessfully = $label['contentupdatedSuccessfully'];
	$save                       = $label['save'];
}else{
	$mainheading                = "wp1Note Manager";
	$mainsubheading             = "Please save your details in the bellow editor.";
	$mainLabel                  = "Content details";
    $areYouSureToDelete         = "Are you sure to delete detsils";
	$contentupdatedSuccessfully = "Content updated successfully";
	$save                       = "Save";
}
$content_details = '';
if (isset($_POST['add'])) {
    $content_details = ($_POST['content_details']);
	$content_details = $content_details;
                $update = "UPDATE {$wpdb->prefix}options SET 
                         `option_value` =\"$content_details\" WHERE `option_name` = 'wp1Note_contents'
					   ";
     $wpdb->query($update);
            $successMessage = $contentupdatedSuccessfully;  
}
?>
<?php
	$details = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}options WHERE `option_name` = 'wp1Note_contents'");
	if (count($details) > 0) {            
		$content_details = $details[0]->option_value;
	}
	
function get_current_user_role() {
	global $wp_roles;
	$current_user = wp_get_current_user();
	$roles = $current_user->roles;
	$role = array_shift($roles);
	return isset($wp_roles->role_names[$role]) ? translate_user_role($wp_roles->role_names[$role] ) : false;
}
$userRole = get_current_user_role();
$showSave = 1;

$settings           = "";
$details = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}options WHERE `option_name` = 'wp1Note_settings'");
if (count($details) > 0) {            
	$settings = $details[0]->option_value;
}
$settingsArray = explode(',',$settings);
$administrator_visibility   = $settingsArray[0];
$subscriber_visibility      = $settingsArray[1];
$editor_visibility          = $settingsArray[2];
$author_visibility          = $settingsArray[3];
$contributor_visibility     = $settingsArray[4];

if($administrator_visibility == 1 && $userRole=='Administrator'){
	$showSave = 1;
}else if($subscriber_visibility == 1 && $userRole=='Subscriber'){
	$showSave = 1;
}else if($editor_visibility == 1 && $userRole=='Editor'){
	$showSave = 1;
}else if($author_visibility == 1 && $userRole=='Author'){
	$showSave = 1;
}else if($contributor_visibility == 1 && $userRole=='Contributor'){
	$showSave = 1;
}else if($administrator_visibility == 2 && $userRole=='Administrator'){
	$showSave = 2;
}else if($subscriber_visibility == 2 && $userRole=='Subscriber'){
	$showSave = 2;
}else if($editor_visibility == 2 && $userRole=='Editor'){
	$showSave = 2;
}else if($author_visibility == 2 && $userRole=='Author'){
	$showSave = 2;
}else if($contributor_visibility == 2 && $userRole=='Contributor'){
	$showSave = 2;
}else{
	$showSave = 0;
}
?>

<?php if($showSave ==2){ echo '<div class="wrap"><h2>Access Denied</h2><p>You do not have sufficient permissions to access this page.Please contact administrator for more details.</p></div>';exit;}?>
<div class="wrap">    
    <h2><?php echo $mainheading;?></h2>
    <p><?php echo $mainsubheading;?></p>
    <p id="add_slide_error" style="color:#ff0000"></p>
    <?php if ($errorMessage != '') echo'<p style="color:#ff0000">' . $errorMessage . '</p>'; ?>
    <?php if ($successMessage != '') echo'<p style="color:#008000">' . $successMessage . '</p>'; ?>
    <form method="post" action="" enctype="multipart/form-data">
        <?php wp_nonce_field('update-options'); ?>
        <table width="80%">
            <tr valign="top">
                <th scope="row" style="text-align:right;"><?php echo $mainLabel;?></th>
                <td >
                    <?php the_editor($content_details, 'content_details'); ?>
                </td>
            </tr>
            <?php
			if($showSave){
			?>
            <tr valign="top">
                <th scope="row" style="text-align:right;"></th>
                <td >                    
                    <input style="width:100px;" name="add" type="submit" value="<?php echo $save; ?>" onclick="return validateForm()"/>
                </td>
            </tr>
            <?php }?>
        </table>
    </form>    
</div>
<script>
    function validateForm(){
        var err=0;       
        var content_details = document.getElementById('content_details').value;        
		content_details = content_details.replace(/^\s+|\s+$/g,'')
        if(content_details == '' &&confirm('<?php echo $areYouSureToDelete;?>')){            
            err=1;
        }
        if(err==1)
        {
            return false;   
        }
        else{
            return true;
        }
    }
</script>