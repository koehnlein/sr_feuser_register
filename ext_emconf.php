<?php

########################################################################
# Extension Manager/Repository config file for ext "sr_feuser_register".
#
# Auto generated 21-03-2012 11:26
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Front End User Registration',
	'description' => 'A self-registration variant of Kasper Skårhøj\'s Front End User Admin extension.',
	'category' => 'plugin',
	'shy' => 0,
	'dependencies' => 'cms,felogin,saltedpasswords,rsaauth,static_info_tables,div2007',
	'conflicts' => 'germandates,rlmp_language_detection,newloginbox,kb_md5fepw,srfeuserregister_t3secsaltedpw',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => 0,
	'uploadfolder' => 1,
	'createDirs' => 'uploads/tx_srfeuserregister',
	'modify_tables' => 'fe_users',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author' => 'Stanislas Rolland / Franz Holzinger',
	'author_email' => 'franz@ttproducts.de',
	'author_company' => 'jambage.com',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'version' => '2.7.0',
	'_md5_values_when_last_written' => 'a:54:{s:9:"ChangeLog";s:4:"096f";s:16:"contributors.txt";s:4:"d12f";s:21:"ext_conf_template.txt";s:4:"6da4";s:12:"ext_icon.gif";s:4:"ad8f";s:17:"ext_localconf.php";s:4:"b926";s:14:"ext_tables.php";s:4:"f1dd";s:14:"ext_tables.sql";s:4:"3a13";s:13:"locallang.xml";s:4:"ee5f";s:16:"locallang_db.xml";s:4:"9f44";s:7:"tca.php";s:4:"aad1";s:45:"control/class.tx_srfeuserregister_control.php";s:4:"e3fc";s:50:"control/class.tx_srfeuserregister_control_main.php";s:4:"987d";s:46:"control/class.tx_srfeuserregister_setfixed.php";s:4:"ef46";s:14:"doc/manual.sxw";s:4:"9e77";s:48:"hooks/class.tx_srfeuserregister_hooksHandler.php";s:4:"0425";s:45:"hooks/class.tx_srfeuserregister_hooks_cms.php";s:4:"e912";s:38:"lib/class.tx_srfeuserregister_auth.php";s:4:"7cab";s:38:"lib/class.tx_srfeuserregister_conf.php";s:4:"5775";s:39:"lib/class.tx_srfeuserregister_email.php";s:4:"4e07";s:38:"lib/class.tx_srfeuserregister_lang.php";s:4:"fc4a";s:44:"lib/class.tx_srfeuserregister_lib_tables.php";s:4:"cb13";s:37:"lib/class.tx_srfeuserregister_tca.php";s:4:"509a";s:43:"marker/class.tx_srfeuserregister_marker.php";s:4:"306c";s:47:"model/class.tx_srfeuserregister_controldata.php";s:4:"7c26";s:40:"model/class.tx_srfeuserregister_data.php";s:4:"9cf8";s:49:"model/class.tx_srfeuserregister_model_feusers.php";s:4:"f719";s:50:"model/class.tx_srfeuserregister_model_setfixed.php";s:4:"c509";s:52:"model/class.tx_srfeuserregister_model_table_base.php";s:4:"6843";s:39:"model/class.tx_srfeuserregister_url.php";s:4:"0d5e";s:58:"model/field/class.tx_srfeuserregister_model_field_base.php";s:4:"ab6f";s:63:"model/field/class.tx_srfeuserregister_model_field_usergroup.php";s:4:"9292";s:37:"pi1/class.tx_srfeuserregister_pi1.php";s:4:"3565";s:48:"pi1/class.tx_srfeuserregister_pi1_adodb_time.php";s:4:"5652";s:42:"pi1/class.tx_srfeuserregister_pi1_base.php";s:4:"9413";s:50:"pi1/class.tx_srfeuserregister_pi1_urlvalidator.php";s:4:"4ff4";s:23:"pi1/flexform_ds_pi1.xml";s:4:"abb2";s:19:"pi1/icon_delete.gif";s:4:"f914";s:21:"pi1/internal_link.gif";s:4:"12b9";s:32:"pi1/internal_link_new_window.gif";s:4:"402a";s:17:"pi1/locallang.xml";s:4:"38bb";s:36:"pi1/tx_srfeuserregister_htmlmail.css";s:4:"0570";s:42:"pi1/tx_srfeuserregister_htmlmail_xhtml.css";s:4:"f65b";s:41:"pi1/tx_srfeuserregister_pi1_css_tmpl.html";s:4:"bd10";s:38:"pi1/tx_srfeuserregister_pi1_sample.txt";s:4:"297e";s:37:"pi1/tx_srfeuserregister_pi1_terms.txt";s:4:"1cac";s:37:"pi1/tx_srfeuserregister_pi1_tmpl.tmpl";s:4:"201d";s:28:"scripts/jsfunc.updateform.js";s:4:"a9aa";s:18:"scripts/rsaauth.js";s:4:"ec71";s:31:"static/css_styled/constants.txt";s:4:"44c4";s:27:"static/css_styled/setup.txt";s:4:"5806";s:30:"static/old_style/constants.txt";s:4:"bc62";s:30:"static/old_style/editorcfg.txt";s:4:"cfad";s:26:"static/old_style/setup.txt";s:4:"41f4";s:42:"view/class.tx_srfeuserregister_display.php";s:4:"e247";}',
	'constraints' => array(
		'depends' => array(
			'php' => '5.2.0-0.0.0',
			'typo3' => '4.5.0-4.7.99',
			'cms' => '',
			'felogin' => '',
			'saltedpasswords' => '',
			'rsaauth' => '',
			'adodb' => '',
			'static_info_tables' => '2.0.5-',
			'div2007' => '0.8.2-',
		),
		'conflicts' => array(
			'germandates' => '0.0.0-1.0.1',
			'rlmp_language_detection' => '0.0.0-1.2.99',
			'newloginbox' => '',
			'kb_md5fepw' => '',
			'srfeuserregister_t3secsaltedpw' => '',
			'patch1822' => '',
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
);

?>