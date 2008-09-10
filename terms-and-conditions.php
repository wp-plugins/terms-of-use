<?php
/*
Plugin Name: Terms and Conditions
Plugin URI: 
Description:This Plugin allows you to add custom tags to wordpress .
Version: 0.1
Author: Levi Putna
Author 
*/

/*  Copyright 2006  Levi Putna  (email : levi.putna@ozblog.com.au)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//setup the plugin root depending upon usage in Wordress or Wordpress MU
if(function_exists('get_current_site')){
  define('_TACPATH', ABSPATH . 'wp-content/mu-plugins/terms-and-conditions/');
}else{
  define('_TACPATH', ABSPATH . 'wp-content/plugins/terms-and-conditions/');
}

//Initialize a few settings
if(function_exists('get_current_site')){
  if(get_site_option('terms_and_conditions_version') != '0.2'){terms_and_conditions_install();}
}else{
  if(get_option('terms_and_conditions_version') != '0.2'){terms_and_conditions_install();}
}

//add a hook into the admin header to check if the user has agreed to the terms and conditions.
add_action('admin_head', 'terms_and_conditions_check');

// Hook for adding admin menus
add_action('admin_menu', 'mt_add_pages');

//@function - action function for above hook
function mt_add_pages() {

  if(function_exists('get_current_site')){
    // Add a submenu to wpmu settings:
    add_submenu_page('wpmu-admin.php', 'Terms & Conditions', 'Terms & Conditions', 10, 'terms_and_conditions', 'terms_and_conditions_settings');
  }else{
    // Add a submenu to wp settings:
    add_submenu_page('options-general.php', 'Terms & Conditions', 'Terms & Conditions', 10, 'terms_and_conditions', 'terms_and_conditions_settings');
  }

}

//@function - displays and save the page settings
function terms_and_conditions_settings() {

  // if the settings were changes save them
	if($_POST){
		$file = fopen(_TACPATH . 'terms-and-conditions.txt', 'w');
		fwrite($file, $_POST['terms_and_conditions']);
		fclose($file);
		
		$file = fopen(_TACPATH . 'privacy-policy.txt', 'w');
		fwrite($file, $_POST['privacy_policy']);
		fclose($file);
		
		$file = fopen(_TACPATH . 'welcome.txt', 'w');
		fwrite($file, $_POST['welcome']);
		fclose($file);
		
		if(function_exists('get_current_site')){
      update_site_option('terms_and_conditions_sitename', $_POST['site-name']);
    }else{
      update_option('terms_and_conditions_sitename', $_POST['site-name']);
    }
  
	}
	
	//Retrieve all the setting
	$terms_and_conditions = stripslashes(file_get_contents(_TACPATH . 'terms-and-conditions.txt'));
	$privacy_policy       = stripslashes(file_get_contents(_TACPATH . 'privacy-policy.txt'));
  $welcome              = stripslashes(file_get_contents(_TACPATH . 'welcome.txt'));
	
	if(function_exists('get_current_site')){
    $site_name = get_site_option('terms_and_conditions_sitename');
  }else{
    $site_name = get_option('terms_and_conditions_sitename');
  }

  if(!$site_name){
    $site_name = get_option('blogname');
  }

	// current url
  	$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

  //the settings page
?>
<div class="wrap">
	<h2>Terms & Conditions Settings</h2>

    <form action="<?php echo $url; ?>" method="post">
    	<table class="form-table">
        	<tbody>
    			<tr valign="top">
					<th scope="row">
						<label for="blogdescription">Website Name</label>
                    </th>
				<td>
					<input id="site-name" type="text" size="45" value="<?php echo $site_name; ?>" style="width: 95%;" name="site-name"/>
					<p>Website Name will replace the <b>%%WEBSITENAME%%</b> in Terms and Conditions, Privacy Policy and Welcome Message when desplayed to the usere.</p>
				</td>
         	</tr>
            <tr valign="top">
					<th scope="row">
						<label for="blogdescription">Terms and Conditions</label>
                    </th>
				<td>
                	<?php
					$fileperms = substr(sprintf('%o', fileperms(_TACPATH . 'terms-and-conditions.txt')), -4);
					if($fileperms != '0664'){
						echo '<p><b>WARNING : </b>terms-and-conditions.txt is not writable, any changes made will not be saved.</p>';
					}
					?>
					<textarea id="terms_and_conditions" class="code" style="width: 98%; font-size: 12px;" rows="10" cols="60" name="terms_and_conditions"><?php echo $terms_and_conditions; ?></textarea>
					<p></p>
				</td>
         	</tr>

        <tr valign="top">
					<th scope="row">
						<label for="blogdescription">Privacy Policy</label>
                    </th>
				<td>
                	<?php
					$fileperms = substr(sprintf('%o', fileperms(_TACPATH . 'privacy-policy.txt')), -4);
					if($fileperms != '0664'){
						echo '<p><b>WARNING : </b>privacy-policy .txt is not writable, any changes made will not be saved.</p>';
					}
					?>
					<textarea id="privacy_policy" class="code" style="width: 98%; font-size: 12px;" rows="10" cols="60" name="privacy_policy"><?php echo $privacy_policy ; ?></textarea>
					<p></p>
				</td>
         	</tr>
         	
         	<tr valign="top">
					<th scope="row">
						<label for="blogdescription">Welcome Message</label>
                    </th>
				<td>
                	<?php
					$fileperms = substr(sprintf('%o', fileperms(_TACPATH . 'welcome.txt')), -4);
					if($fileperms != '0664'){
						echo '<p><b>WARNING : </b>welcome.txt is not writable, any changes made will not be saved.</p>';
					}
					?>
					<textarea id="welcome" class="code" style="width: 98%; font-size: 12px;" rows="10" cols="60" name="welcome"><?php echo $welcome; ?></textarea>
					<p>This message is displayed after the user agrees to the terms and conditions.</p>
				</td>
         	</tr>
         	

         </tbody>
       </table>



    	<p class="submit">
			<input type="submit" value="Save Changes" name="Submit"/>
        </p>

    </form>
</div>
<?php
}

//@function - check is the user has agreed to the terms and conditions
function terms_and_conditions_check(){
    global $current_user;
    $userdata = get_userdata($current_user->ID);

		if(!$userdata->terms_and_conditions && !$_GET['page'] == 'terms-and-conditions/terms-and-conditions.php'){
	    
	    if(function_exists('get_current_site')){
        echo '<script type="text/javascript">window.location = "wpmu-admin.php?page=terms-and-conditions/terms-and-conditions.php"</script>';
      }else{
        echo '<script type="text/javascript">window.location = "admin.php?page=terms-and-conditions/terms-and-conditions.php"</script>';
      }
		        
		}
}

//@function - Initialize a few settings
function terms_and_conditions_install(){

  if(function_exists('get_current_site')){
    update_site_option('terms_and_conditions_version', '0.2');
    
    $root_name = get_site_option('blogname');
    update_site_option('terms_and_conditions_sitename', $root_name);
  }else{
    update_option('terms_and_conditions_version', '0.2');
    
    $root_name = get_option('blogname');
    update_option('terms_and_conditions_sitename', $root_name);
  }

	//try and make txt files writable
	@chmod(_TACPATH . 'terms-and-conditions.txt', '0664');
	@chmod(_TACPATH . 'privacy-policy.txt', '0664');
	@chmod(_TACPATH . 'welcome.txt', '0664');
}
?>
