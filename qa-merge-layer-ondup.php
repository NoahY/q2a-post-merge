<?php

	class qa_html_theme_layer extends qa_html_theme_base {

		function doctype() {
			if(qa_post_text('merge_from') && qa_get_logged_in_level() >= QA_USER_LEVEL_ADMIN) {
				$merged = qa_merge_do_merge();
				if($merged === true)
					qa_redirect(qa_q_request(qa_post_text('merge_to'),null),array('merged'=>qa_post_text('merge_from')));
				else {
					$error1 = $merged[0];
					$error2 = $merged[1];
					$this->content['error'] = "Error merging posts.";
				}
				
			}
			qa_html_theme_base::doctype();
		}

		// override q_view_clear to add merge-button
		function q_view_clear() {
			
			// call default method output
			qa_html_theme_base::q_view_clear();
			
			// return if not admin!
			if (qa_get_logged_in_level() < QA_USER_LEVEL_ADMIN) {
				return;
			}
			
			// check if question is duplicate
			$closed = (@$this->content['q_view']['raw']['closedbyid'] !== null);
			if($closed) {
				// check if duplicate
				$duplicate = qa_db_read_one_value( qa_db_query_sub('SELECT postid FROM `^posts` 
																		WHERE `postid` = #
																		AND `type` = "Q"
																		;', $this->content['q_view']['raw']['closedbyid']), true );
				if($duplicate) {
					$this->output('<div id="mergeDup" style="margin:10px 0 0 120px;padding:5px 10px;background:#FCC;border:1px solid #AAA;"><h3>Merge Duplicate:</h3>');
					
					// form output
					$this->output('
<FORM METHOD="POST">
<TABLE>
	<TR>
		<TD CLASS="qa-form-tall-label">
			From: &nbsp;
			<INPUT NAME="merge_from" id="merge_from" TYPE="text" VALUE="'.$this->content['q_view']['raw']['postid'].'" CLASS="qa-form-tall-number">
			&nbsp; To: &nbsp;
			<INPUT NAME="merge_to" id="merge_to" TYPE="text" VALUE="'.$this->content['q_view']['raw']['closedbyid'].'" CLASS="qa-form-tall-number">
		</TD>
	</TR>
	<TR>
		<TD CLASS="qa-form-tall-label">
		Text to show when redirecting from merged question:
		</TD>
	</TR>
	<TR>
		<TD CLASS="qa-form-tall-label">
		<INPUT NAME="merge_question_merged" id="merge_question_merged" TYPE="text" VALUE="'.qa_opt('merge_question_merged').'" CLASS="qa-form-tall-text">
		</TD>
	</TR>
	<TR>
		<TD style="text-align:right;">
			<INPUT NAME="merge_question_process" VALUE="Merge" TITLE="" TYPE="submit" CLASS="qa-form-tall-button qa-form-tall-button-0">
		</TD>

	</TR>
	
</TABLE>
</FORM>				');
					$this->output('</div>');
				}
			}
			
		}
		
	} // end layer class

/*
        Omit PHP closing tag to help avoid accidental output
*/