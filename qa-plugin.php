<?php

/*
	Plugin Name: Merge
	Plugin URI: https://github.com/NoahY/q2a-merge
	Plugin Update Check URI: https://github.com/NoahY/q2a-merge/raw/master/qa-plugin.php
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

	// merge button on duplicate question page (only for admin)
	qa_register_plugin_layer('qa-merge-layer-ondup.php', 'Merge Button for Duplicate Question');

/*
	Omit PHP closing tag to help avoid accidental output
*/
