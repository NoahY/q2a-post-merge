<?php

        class qa_merge_admin {

                function allow_template($template)
                {
                        return ($template!='admin');
                }

                function option_default($option) {
                    
                    switch($option) {
                        case 'merge_question_merged':
                            return 'Redirected from merged question ^post';
                        default:
                            return null;				
                    }
                    
                }

                function admin_form(&$qa_content)
                {

                //      Process form input

                        $ok = null;
                        $error1 = null;
                        $error2 = null;

                        if(qa_clicked('merge_question_process')) {
                                
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
                                    
                                    $ok = 'Posts merged.';
                                }
                        }

                //      Create the form for display
						
                        $fields = array();
						
                        $fields[] = array(
                                'label' => 'From',
                                'tags' => 'NAME="merge_from" id="merge_from"',
                                'type' => 'number',
                                'error' => $error1,
                        );
                        $fields[] = array(
                                'label' => 'To',
                                'tags' => 'NAME="merge_to" id="merge_to"',
                                'type' => 'number',
                                'error' => $error2,
                        );
                        $fields[] = array(
                                'value' => '<input type="button" onclick="mergePluginGetPosts()" value="show"><div id="merge_from_out"></div><div id="merge_to_out"></div>',
                                'type' => 'static',
                        );
                        $fields[] = array(
                                'type' => 'blank',
                        );
                        $fields[] = array(
                                'label' => 'Text to show when redirecting from merged question',
                                'tags' => 'NAME="merge_question_merged" id="merge_question_merged"',
                                'value' => qa_opt('merge_question_merged'),
                        );


                        return array(
                                'ok' => ($ok && !isset($error)) ? $ok : null,

                                'fields' => $fields,

                                'buttons' => array(
                                        array(
                                                'label' => 'Merge',
                                                'tags' => 'NAME="merge_question_process"',
                                        ),
                                ),
                        );
                }
        }


/*
        Omit PHP closing tag to help avoid accidental output
*/
