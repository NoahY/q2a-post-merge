<?php

/*
	Plugin Name: Merge
	Plugin URI: https://github.com/NoahY/q2a-merge
	Plugin Description: Provides merging capabilities
	Plugin Version: 0.1a
	Plugin Date: 2011-10-15
	Plugin Author: NoahY
	Plugin Author URI: http://www.question2answer.org/qa/user/NoahY
	Plugin License: GPLv2
	Plugin Minimum Question2Answer Version: 1.4
*/


	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../../');
		exit;
	}

	qa_register_plugin_layer('qa-merge-layer.php', 'Merge Layer');
	
	qa_register_plugin_module('module', 'qa-php-widget.php', 'qa_merge_admin', 'Merge Admin');


/*
	Omit PHP closing tag to help avoid accidental output
*/
