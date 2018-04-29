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

class addon_botphobic extends flux_addon
{
	function register($manager)
	{
		if ($this->is_configured())
		{
			$manager->bind('register_after_validation', array($this, 'hook_register_after_validation'));
			$manager->bind('register_before_submit', array($this, 'hook_register_before_submit'));
		}
	}
	function is_configured()
	{
		global $pun_config;
		return (isset ($pun_config['botphobic_salt']) && $pun_config['botphobic_salt']);
	}
	function hook_register_after_validation()
	{
		global $errors;
		global $pun_config;
		if (!$this->bp_verify())
		{
			$errors[] = $pun_config['botphobic_message'];
		}
	}
	function hook_register_before_submit()
	{
		global $pun_config;
		?>

		<?php
		if (isset($pun_config['botphobic_timestamp']) && $pun_config['botphobic_timestamp'])
		{
			$bp_salted = openssl_encrypt(time(), 'aes-256-cbc', $pun_config['botphobic_salt'], 0, substr(hash('md5', $pun_config['botphobic_salt']),0,16));
		?>
			<input type="hidden" name="abc" value="<?php echo $bp_salted ?>">
		<?php } ?>

		<?php
		if (isset($pun_config['botphobic_honeypot']) && $pun_config['botphobic_honeypot'])
		{
			?>
			<input type="text" name="contact" value="" style="position:fixed !important;left:9000px !important;" tabindex="-1" autocomplete="nope">
			<textarea name="comment" style="display:none !important;" tabindex="-1" autocomplete="nope"></textarea>
		<?php } ?>

		<?php
		if (isset($pun_config['botphobic_javascript']) && $pun_config['botphobic_javascript'])
		{
			$bp_salted = hash('md5', substr($pun_config['botphobic_salt'],0,8));
			?>
			<input type="hidden" name="def" value="" id="test_js">
			<script>
				var test_js = document.getElementById("test_js");
				test_js.value = "<?php echo $bp_salted ?>";
			</script>
		<?php } ?>

		<?php
		if (isset($pun_config['botphobic_cookies']) && $pun_config['botphobic_cookies'])
		{
			$bp_salted = hash('md5', substr($pun_config['botphobic_salt'],0,12));
			setcookie('ghi', $bp_salted, time() + (2 * 3600), "/");
		}
		?>

		<?php
	}
	function bp_verify()
	{
		global $pun_config;

		if (isset($pun_config['botphobic_timestamp']) && $pun_config['botphobic_timestamp'])
		{
			if (isset($_POST['abc'])){
				$bp_timestamp = openssl_decrypt($_POST['abc'], 'aes-256-cbc', $pun_config['botphobic_salt'], 0, substr(hash('md5', $pun_config['botphobic_salt']),0,16));
				if (!$bp_timestamp) return false;
				$bp_begin = time() - (2 * 3600);
				$bp_end = time() - 4;
				if ($bp_timestamp < $bp_begin || $bp_timestamp > $bp_end) return false;
			}
			else return false;
		}
		if (isset($pun_config['botphobic_honeypot']) && $pun_config['botphobic_honeypot'])
		{
			if (isset($_POST['contact']) && $_POST['contact']) return false;
			if (isset($_POST['comment']) && $_POST['comment']) return false;
		}
		if (isset($pun_config['botphobic_javascript']) && $pun_config['botphobic_javascript'])
		{
			if (isset($_POST['def'])){
				$bp_salted = hash('md5', substr($pun_config['botphobic_salt'], 0, 8));
				if ($_POST['def'] !== $bp_salted) return false;
			}
			else return false;
		}
		if (isset($pun_config['botphobic_cookies']) && $pun_config['botphobic_cookies'])
		{
			if(isset($_COOKIE['ghi'])) {
				$bp_salted = hash('md5', substr($pun_config['botphobic_salt'],0,12));
				if ($_COOKIE['ghi'] !== $bp_salted) return false;
			}
			else return false;
		}
		return true;
	}
}