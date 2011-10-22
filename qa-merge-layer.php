<?php

	class qa_html_theme_layer extends qa_html_theme_base {

	// theme replacement functions
		
		function doctype() {
			if(@$this->content['error'] == qa_lang_html('main/page_not_found') && preg_match('/^[0-9]+\//',$this->request) !== false) {
				$pid = preg_replace('/\/.*/','',$this->request);
				$merged = qa_db_read_one_assoc(
					qa_db_query_sub(
						"SELECT ^posts.postid as postid,^posts.title as title FROM ^postmeta, ^posts WHERE ^postmeta.meta_key='merged_with' AND ^postmeta.post_id=# AND ^posts.postid=^postmeta.meta_value",
						$pid
					), true
				);
				if($merged) {
					qa_redirect(qa_q_request($merged['postid'],$merged['title']),array('merged'=>$pid));
				}
			}
			else if(qa_get('merged')) {
				$this->content['error'] = str_replace('^post',qa_get('merged'),qa_opt('merge_question_merged'));
			}
			if(qa_post_text('ajax_merge_get_from')) {
				return;
			}
			qa_html_theme_base::doctype();
		}
		function html() {
			if(qa_post_text('ajax_merge_get_from')) {
				$posts = qa_db_read_all_assoc(
					qa_db_query_sub(
						"SELECT postid,title FROM ^posts WHERE postid IN (#,#)",
						qa_post_text('ajax_merge_get_from'),qa_post_text('ajax_merge_get_to')
					)
				);
				if($posts[0]['postid']==(int)qa_post_text('ajax_merge_get_from'))
					echo '{"from":"'.$posts[0]['title'].'","to":"'.$posts[1]['title'].'","from_url":"'.qa_path_html(qa_q_request((int)qa_post_text('ajax_merge_get_from'), $posts[0]['title']), null, qa_opt('site_url')).'","to_url":"'.qa_path_html(qa_q_request((int)qa_post_text('ajax_merge_get_to'), $posts[1]['title']), null, qa_opt('site_url')).'"}';
				else
					echo '{"from":"'.$posts[1]['title'].'","to":"'.$posts[0]['title'].'","from_url":"'.qa_path_html(qa_q_request((int)qa_post_text('ajax_merge_get_from'), $posts[1]['title']), null, qa_opt('site_url')).'","to_url":"'.qa_path_html(qa_q_request((int)qa_post_text('ajax_merge_get_to'), $posts[0]['title']), null, qa_opt('site_url')).'"}';
				return;
			}
			qa_html_theme_base::html();
		}
		
		
		function head_custom()
		{
			if($this->template == 'admin') {
				$this->output("
	<script>			
	function mergePluginGetPosts() {
		var from=jQuery('#merge_from').val();
		var to=jQuery('#merge_to').val();

		var dataString = 'ajax_merge_get_from='+from+'&ajax_merge_get_to='+to;  
		jQuery.ajax({  
		  type: 'POST',  
		  url: '".qa_self_html()."',  
		  data: dataString,  
		  dataType: 'json',  
		  success: function(json) {
				jQuery('#merge_from_out').html('Merging from: <a href=\"'+json.from_url+'\">'+json.from+'</a>');
				jQuery('#merge_to_out').html('To: <a href=\"'+json.to_url+'\">'+json.to+'</a>');
			} 
		});
		return false;
	}
	</script>");
			}	
			qa_html_theme_base::head_custom();
		}
	}

