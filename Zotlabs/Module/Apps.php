<?php
namespace Zotlabs\Module;


use \Zotlabs\Lib as Zlib;

class Apps extends \Zotlabs\Web\Controller {

	function get() {

		nav_set_selected('Apps');
	
		if(argc() == 2 && argv(1) == 'edit')
			$mode = 'edit';
		else
			$mode = 'list';

		$_SESSION['return_url'] = \App::$query_string;
	
		$apps = array();
	
		if(local_channel()) {
			Zlib\Apps::import_system_apps();
			$syslist = array();
			$list = Zlib\Apps::app_list(local_channel(), (($mode == 'edit') ? true : false), $_GET['cat']);
			if($list) {
				foreach($list as $x) {
					$syslist[] = Zlib\Apps::app_encode($x);
				}
			}
			Zlib\Apps::translate_system_apps($syslist);
		}
		else
			$syslist = Zlib\Apps::get_system_apps(true);

		usort($syslist,'Zotlabs\\Lib\\Apps::app_name_compare');
	
	//	logger('apps: ' . print_r($syslist,true));
	
		foreach($syslist as $app) {
			$apps[] = Zlib\Apps::app_render($app,$mode);
		}

		return replace_macros(get_markup_template('myapps.tpl'), array(
			'$sitename' => get_config('system','sitename'),
			'$cat' => ((array_key_exists('cat',$_GET) && $_GET['cat']) ? escape_tags($_GET['cat']) : ''),
			'$title' => t('Apps'),
			'$apps' => $apps,
			'$authed' => ((local_channel()) ? true : false),
			'$manage' => t('Manage apps'),
			'$create' => (($mode == 'edit') ? t('Create new app') : '')
		));
	
	}
	
}
