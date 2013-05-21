<?php

/*
	Plugin Name: Merge
	Plugin URI: https://github.com/NoahY/q2a-merge
	Plugin Update Check URI: https://raw.github.com/NoahY/q2a-merge/master/qa-plugin.php
	Plugin Description: Provides merging capabilities
	Plugin Version: 0.2
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

	function qa_merge_do_merge() {
	                                
		qa_opt('merge_question_merged',qa_post_text('merge_question_merged'));
		
		$from = (int)qa_post_text('merge_from');
		$to = (int)qa_post_text('merge_to');
		
		$titles = qa_db_read_all_assoc(
			qa_db_query_sub(
				"SELECT postid,title,acount FROM ^posts WHERE postid IN (#,#)",
				qa_post_text('merge_from'),qa_post_text('merge_to')
			)
		);
		if(count($titles) != 2) {
			$error1 = null;
			$error2 = null;
			if(empty($titles)) {
				$error1 = 'Post not found.';
				$error2 = $error1;
			}
			else if($titles[0]['postid'] == $from){
				$error2 = 'Post not found.';
			}
			else if($titles[0]['postid'] == $to){
				$error1 = 'Post not found.';
			}
			else $error1 = 'unknown error.';
			return array($error1,$error2);
		}
		else {
			
			$acount = (int)$titles[0]['acount']+(int)$titles[1]['acount'];
			
			$text = '<div class="qa-content-merged"> '.str_replace('^post',qa_path(qa_q_request((int)qa_post_text('merge_to'), ($titles[0]['postid'] == $to?$titles[0]['title']:$titles[1]['title'])), null, qa_opt('site_url')),qa_opt('merge_question_merged')).' </div>';
			
			qa_db_query_sub(
				"UPDATE ^posts SET parentid=# WHERE parentid=#",
				$to, $from
			);
			
			qa_db_query_sub(
				"UPDATE ^posts SET acount=# WHERE postid=#",
				$acount,$to
			);

			qa_db_query_sub(
				'CREATE TABLE IF NOT EXISTS ^postmeta (
				meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				post_id bigint(20) unsigned NOT NULL,
				meta_key varchar(255) DEFAULT \'\',
				meta_value longtext,
				PRIMARY KEY (meta_id),
				KEY post_id (post_id),
				KEY meta_key (meta_key)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8'
			);			
			
			qa_db_query_sub(
				"INSERT INTO ^postmeta (post_id,meta_key,meta_value) VALUES (#,'merged_with',#)",
				$from,$to
			);                                
			
			require_once QA_INCLUDE_DIR.'qa-app-posts.php';
			qa_post_delete($from);
			return true;
		}
	
	}

/*
	Omit PHP closing tag to help avoid accidental output
*/
