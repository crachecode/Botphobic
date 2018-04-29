<?php

/**
 * Copyright (C) 2018 Crachecode
 * created by Timothée Crozat
 * https://www.crachecode.net
 * License: http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 */

// Make sure no one attempts to run this script "directly"
if (!defined('PUN'))
	exit;

// Load the admin_plugin_example.php language file
//require PUN_ROOT.'lang/'.$admin_language.'/admin_plugin_example.php';

// Tell admin_loader.php that this is indeed a plugin and that it is loaded
define('PUN_PLUGIN_LOADED', 1);

function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
	$pieces = [];
	$max = mb_strlen($keyspace, '8bit') - 1;
	for ($i = 0; $i < $length; ++$i) {
		$pieces []= $keyspace[rand(0, $max)];
	}
	return implode('', $pieces);
}

// Store the config
if (isset($_POST['process_form']))
{
	$botphobic_salt = random_str(16);
	$botphobic_timestamp = isset($_POST['botphobic_timestamp']);
	$botphobic_honeypot = isset($_POST['botphobic_honeypot']);
	$botphobic_javascript = isset($_POST['botphobic_javascript']);
	$botphobic_cookies = isset($_POST['botphobic_cookies']);
	$botphobic_message = isset($_POST['botphobic_message']) ? pun_trim($_POST['botphobic_message']) : 'Sorry, there was an error. Please try with a different browser.';

	foreach (compact('botphobic_salt', 'botphobic_timestamp', 'botphobic_honeypot', 'botphobic_javascript', 'botphobic_cookies', 'botphobic_message') as $key => $value)
	{
		if (isset($pun_config[$key]))
			$db->query('UPDATE '.$db->prefix.'config SET conf_value = \''.$db->escape($value).'\' WHERE conf_name = \''.$db->escape($key).'\'') or error('Unable to update config value for '.$key, __FILE__, __LINE__, $db->error());
		else
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\''.$db->escape($key).'\', \''.$db->escape($value).'\')') or error('Unable to store config value for '.$key, __FILE__, __LINE__, $db->error());
	}
	// Regenerate the config cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require PUN_ROOT.'include/cache.php';
	generate_config_cache();
	redirect('admin_loader.php?plugin=AP_Botphobic.php', 'Settings saved successfully. Redirecting...');
}
// Display the admin navigation menu
generate_admin_menu($plugin);

?>

<div class="blockform">
	<h2><span>Botphobic</span></h2>
	<div class="box">
		<form id="recaptcha" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
			<div class="inform">
				<fieldset>
					<legend>Tests</legend>
					<div class="infldset">
						<table class="aligntop" cellspacing="0">
							<tr>
								<th scope="row">Encrypted timestamp<br>(requires OpenSSL on server side)</th>
								<td>
									<input type="checkbox" name="botphobic_timestamp" <?php if (isset($pun_config['botphobic_timestamp']) && $pun_config['botphobic_timestamp']) echo 'checked' ?> />
								</td>
							</tr>
							<tr>
								<th scope="row">Honeypot</th>
								<td>
									<input type="checkbox" name="botphobic_honeypot" <?php if (isset($pun_config['botphobic_honeypot']) && $pun_config['botphobic_honeypot']) echo 'checked' ?> />
								</td>
							</tr>
							<tr>
								<th scope="row">Javascript</th>
								<td>
									<input type="checkbox" name="botphobic_javascript" <?php if (isset($pun_config['botphobic_javascript']) && $pun_config['botphobic_javascript']) echo 'checked' ?> />
								</td>
							</tr>
							<tr>
								<th scope="row">Cookies</th>
								<td>
									<input type="checkbox" name="botphobic_cookies" <?php if (isset($pun_config['botphobic_cookies']) && $pun_config['botphobic_cookies']) echo 'checked' ?> />
								</td>
							</tr>
						</table>
					</div>
				</fieldset>
				<fieldset>
					<legend>Options</legend>
					<div class="infldset">
						<table class="aligntop" cellspacing="0">
							<tr>
								<th scope="row">Error message</th>
								<td>
									<textarea name="botphobic_message" cols="60" rows="3"><?php
										if (isset($pun_config['botphobic_message']) && $pun_config['botphobic_message']) echo $pun_config['botphobic_message'];
										else { ?>Sorry, there was an error. Please try with a different browser.<?php } ?></textarea>
								</td>
							</tr>
						</table>
					</div>
			</div>
			<p class="submitend"><input type="submit" name="process_form" value="Save" /></p>
		</form>
	</div>
</div>
