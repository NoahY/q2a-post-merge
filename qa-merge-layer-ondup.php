<?php

	class qa_html_theme_layer extends qa_html_theme_base {

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
				$closedByQu = qa_db_read_one_value( qa_db_query_sub('SELECT postid FROM `^posts` 
																		WHERE `postid` = #
																		AND `type` = "Q"
																		;', $this->content['q_view']['raw']['closedbyid']), true );
				if(isset($closedByQu)) {
					$showDup = '<div id="mergeDup" style="width:310px;height:100px;margin:10px 0 0 120px;padding:5px 10px;background:#FCC;border:1px solid #AAA;"><h3>Merge Duplicate:</h3>';
					$this->output($showDup);
					
					// form output
					$this->output('
<FORM METHOD="POST" ACTION="http://www.gute-mathe-fragen.de/admin/plugins#b2a5fe693b0d7119bd240e9e4142d789">
<TABLE>
	<TR>
		<TD CLASS="qa-form-tall-label">
			From &nbsp;
			<INPUT NAME="merge_from" id="merge_from" TYPE="text" VALUE="'.$this->content['q_view']['raw']['postid'].'" CLASS="qa-form-tall-number">
		</TD>
		
		<TD CLASS="qa-form-tall-label">
		To &nbsp;
		<INPUT NAME="merge_to" id="merge_to" TYPE="text" VALUE="'.$this->content['q_view']['raw']['closedbyid'].'" CLASS="qa-form-tall-number">
		</TD>
		
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