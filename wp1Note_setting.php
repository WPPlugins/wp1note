<?php
$errorMessage 		= "";
$successMessage     = "";
$language           = "";
$details = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}options WHERE `option_name` = 'wp1Note_language'");
if (count($details) > 0) {            
	$language = $details[0]->option_value;
}

if (isset($_POST['update'])) {
    $language = $_POST['language'];
}
if(file_exists(plugin_dir_path(__FILE__).'language/'.$language.'.php')){
	include('language/'.$language.'.php');
	$heading                     = $label['heading'];
	$subHeading                  = $label['subHeading'];
	$feildNameOne                = $label['feildNameOne'];
	$select                      = $label['select'];
	$pleaseSelectLanguage        = $label['pleaseSelectLanguage'];
	$settingsUpdatedSuccessfully = $label['settingsUpdatedSuccessfully'];
	$update                      = $label['update'];
}else{
	$heading                     = "Update Settings";
	$subHeading                  = "Please update language.";
	$feildNameOne                = "Language";
	$select                      = "Select";
	$pleaseSelectLanguage        = "Please select language";
	$settingsUpdatedSuccessfully = "Settings updated successfully";
	$update                      = "Update";
}



if (isset($_POST['update'])) {
    $language = $_POST['language'];
	$settings = $_POST['type_1'].','.$_POST['type_2'].','.$_POST['type_3'].','.$_POST['type_4'].','.$_POST['type_5'];
    if ($language == '' ) {
        $errorMessage = $pleaseSelectLanguage;
    } 
    if ($errorMessage == '') {    
		$wpdb->query("UPDATE {$wpdb->prefix}options SET option_value ='$language' where option_name = 'wp1Note_language'");
		$wpdb->query("UPDATE {$wpdb->prefix}options SET option_value ='$settings' where option_name = 'wp1Note_settings'");		     
        $successMessage = $settingsUpdatedSuccessfully;
    }
}

$details = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}options WHERE `option_name` = 'wp1Note_language'");
if (count($details) > 0) {            
	$language = $details[0]->option_value;
}

////////////
$settings           = "";
$details = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}options WHERE `option_name` = 'wp1Note_settings'");
if (count($details) > 0) {            
	$settings = $details[0]->option_value;
}
////////////
?>
<div class="wrap">
    <h2><?php echo $heading;?></h2>
    <p><?php echo $subHeading;?></p>
    <p id="settings_error" style="color:#ff0000"></p>
    <?php if ($errorMessage != '') echo'<p style="color:#ff0000">' . $errorMessage . '</p>'; ?>
    <?php if ($successMessage != '') echo'<p style="color:#008000">' . $successMessage . '</p>'; ?>
    <form method="post" action="" enctype="multipart/form-data">
        <?php wp_nonce_field('update-options'); ?>
        <table width="80%">
            <tr valign="top">
                <th width="20%" scope="row" style="text-align:right;"><?php echo $feildNameOne;?><b> *</b></th>
                <td width="60%">
                	<select name="language" type="text" id="language">
                    <option value=""><?php echo $select;?></option>
                    <?php
					if ($handle = opendir(plugin_dir_path(__FILE__) .'language/')) {
						echo "Directory handle: $handle\n";
						echo "Entries:\n";
					
						while (false !== ($entry = readdir($handle))) {
							if ($entry != "." && $entry != "..") {
								$entryname = reset(explode('.',$entry));								
								?>
								<option <?php if (isset($language)){ if($entryname == $language){?> selected="selected"<?php }}?> value='<?php echo $entryname;?>'><?php echo $entryname;?></option>
                                <?php 
							}
						}
						closedir($handle);
					}
					?>
                </td>
            </tr> 
            <?php 
			$settingsArray = explode(',',$settings);
			for($t=1;$t<6;$t++){?>
            <tr valign="top">
                <th width="20%" scope="row" style="text-align:right;"><?php echo $label['type_'.$t];?><b> *</b></th>
                <td width="60%">
                	<select name="type_<?php echo $t;?>" type="text" id="language">
                    	<option <?php if($settingsArray[$t-1] ==0){?> selected="selected" <?php }?> value="0">Read only</option>
                        <option <?php if($settingsArray[$t-1] ==1){?> selected="selected" <?php }?> value="1">Read and Write</option>
                        <option <?php if($settingsArray[$t-1] ==2){?> selected="selected" <?php }?> value="2">No access</option>
                    </select>
                </td>
            </tr> 
            <?php }?>    
        </table>
        <p style="padding-left:20%;">
            <input style="width:100px;" name="update" type="submit" value="<?php echo $update; ?>" onclick="return validatewp1NoteSetting()"/>
        </p>
    </form>
</div>
<script>
    function validatewp1NoteSetting(){
        var err=0;
        var language = document.getElementById('language').value;   
        
        if(language == ''){
            document.getElementById('settings_error').innerHTML=<?php echo $pleaseSelectLanguage;?>;            
            err=1;
        }
        else
        {
            document.getElementById('settings_error').innerHTML='';
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
