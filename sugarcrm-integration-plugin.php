<?php
/*
Plugin Name: SugarCRM Integration Plugin
Plugin URI: http://sugarcrm-integration.bitweise.net/
Description: This plugin integrates your SugarCRM Admin Panel to your Wordpress Admin Panel.
Version: 1.3
Author: bitweise.NET
Author URI: http://www.bitweise.net
*/

add_action('admin_menu', 'sugarcrmIntegrationMenu');

function sugarcrmIntegrationMenu() {
	// add_menu_page('page_title, menu_title, capability, menu_slug, function, icon_url, position');
	add_menu_page('SugarCRM', 'SugarCRM', 10, 'sugarcrm_panel', 'sugarcrmPanel');
	// add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
	add_submenu_page('sugarcrm_panel', 'Panel', 'Panel', 10, 'sugarcrm_panel', 'sugarcrmPanel');
	add_submenu_page('sugarcrm_panel', 'Settings', 'Settings', 10, 'sugarcrm_settings', 'sugarcrmSettings');
}


function sugarcrmPanel() {

	// lay the foundations
	$sugarcrm_options = get_option("plugin_sugarcrm_options");

	// define sugarEntry 
	if (!defined('sugarEntry')) define('sugarEntry', true);
	
	// Setup SugarCRM options
	$options = array(
	"location" => $sugarcrm_options['url'] .'soap.php',
	"uri" => $sugarcrm_options['url'],
	"trace" => 1
	);
	
	// user authentication array
	$user_auth = array(
	"user_name" => $sugarcrm_options['username'],
	"password" => $sugarcrm_options['password'],
	"version" => '.01'
	);
	
	// connect via SOAP
	$soapClient = new SoapClient(NULL, $options);
	
	// Login to SugarCRM
	$response = $soapClient->login($user_auth,"admin");
	$sugarcrm_session_id = $response->id;
	
	// activate seamless login
	$result = $soapClient->seamless_login($sugarcrm_session_id);

	echo '<iframe style="width:100%; height: 1400px;" src="'.$sugarcrm_options['url'].'index.php?module=Home&action=index&MSID='.$sugarcrm_session_id.'" scrolling="no" frameborder="0" ></iframe>';
}


function sugarcrmSettings() {

	$sugarcrm_options = get_option("plugin_sugarcrm_options");
	
	if (!is_array( $sugarcrm_options )) { // Pruefe ob variable KEIN array ist
		$sugarcrm_options = array(
			'url' => '', // schublade url = standard wert
			'username' => '', // schublade username = standard wert
			'password' => '' // schublade password = standard wert
		);
	} // fertig
	
	// wenn form abgeschickt
	if ($_POST['crm-settings-save']) { // daten uebergeben in...
	
		// Check if url of server ends with "/" if not it will be corrected automatically
		$tmp = htmlspecialchars($_POST['crm_url']);
		$check = substr($tmp, -1);
		if ($check != "/") { $sugarcrm_url = $tmp . "/"; } else { $sugarcrm_url = $tmp; }
	
		$sugarcrm_options['url'] = $sugarcrm_url; //htmlspecialchars($_POST['crm_url']); // variable
		$sugarcrm_options['username'] = htmlspecialchars($_POST['crm_user']); // variable
		if ($_POST['crm_pwd'] != '') { $sugarcrm_options['password'] = md5(htmlspecialchars($_POST['crm_pwd'])); } // variable
		update_option("plugin_sugarcrm_options", $sugarcrm_options); // in option
	}
?>

<div class="crm-settings-panel">
<h2>SugarCRM Integration Plugin</h2>
This plugin allows you to integrate your existing SugarCRM to your Wordpress admin panel.<br />
<br />
<b>Explanation:</b><br />
<br />
<ol>
<li>Enter the path to your SugarCRM installation and your login data below.</li>
<li>Click on the save button below.</li>
<li>Visit the SugarCRM panel on the left sidebar.</li>
<li>Enjoy!</li>
</ol>
<br />
  <h3>SugarCRM Settings</h3>
  <div class="crmform">
    <div class="metabox-prefs">
      <form action="" method="post">
        <label for="crm_url">URL : </label>
        <input type="text" name="crm_url" id="crm_url" value="<?php echo ($sugarcrm_options['url']); ?>" size="81" />
		<i>e.g. "http://www.bitweise.net/sugarcrm/" (don't forget the "/" at the end)</i>
        <br />
        <label for="crm_user">Username : </label>
        <input type="text" name="crm_user" id="crm_user" value="<?php echo ($sugarcrm_options['username']); ?>" size="25" /> 
		<i>e.g. "admin"</i>
        <br />
        <label for="crm_pwd">Password : </label>
        <input type="password" name="crm_pwd" id="crm_pwd" size="25" />
		<i>e.g. "secret"</i>
        <br />
        <input type="submit" name="crm-settings-save" id="crm-settings-save" value="Save" class="button" />
        <!--<input type="hidden" name="redirect_to" value="blub.php" />-->
      </form>
    </div>
  </div>
	<br /><i>The entries have to be done without the quotation marks ("..."). The password entry won't be displayed but you might enter a new one.</i>
</div>
<?php 
}

function include_sugarcrm() { 
	// do something
}
?>