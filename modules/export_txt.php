<?php
if ( $_REQUEST[ 'sspcmd' ] == "aexport" ) {
	$path = str_replace( array( "\\", "/" ), array( MSDIRS, MSDIRS ), dirname( __FILE__ ) ) . MSDIRS . ".." . MSDIRS . "exports" . MSDIRS . $sid . "_" . $auid . ".txt";
	$output = '
' . $question . '

';
	foreach( $answers_text as $at ) {
$output .= '
' . $at->answertext . ' ' . $at->count;
	}
	if ( file_put_contents( $path, $output ) ) {
		$result = "success";
	}
	else {
		$result = __( 'Write error', MODAL_SURVEY_TEXT_DOMAIN );
	}	
}
else {
	$path = str_replace( array( "\\", "/" ), array( MSDIRS, MSDIRS ), dirname( __FILE__ ) ) . MSDIRS . "..". MSDIRS . "exports" . MSDIRS . $survey_exp[ 'id' ] . ".txt";
	$output = '
' . __( 'Survey ID:', MODAL_SURVEY_TEXT_DOMAIN ) . ' ' . $survey_exp['id'] . '
' . __( 'Survey Name:', MODAL_SURVEY_TEXT_DOMAIN ) . ' ' . $survey_exp['name'] . '
' . __( 'Export Time:', MODAL_SURVEY_TEXT_DOMAIN ) . ' ' . $survey_exp['export_time'];
if ( $personal ) {
$output .= '

' . __( 'User ID:', MODAL_SURVEY_TEXT_DOMAIN ) . ' ' . $survey_exp[ 'user_details' ]->autoid . '
' . __( 'Name:', MODAL_SURVEY_TEXT_DOMAIN ) . ' ' . ( $survey_exp[ 'user_details' ]->name ? $survey_exp[ 'user_details' ]->name : __( 'Anonymous', MODAL_SURVEY_TEXT_DOMAIN ) ) . '
' . __( 'Username:', MODAL_SURVEY_TEXT_DOMAIN ) . ' ' . ( $survey_exp[ 'user_details' ]->username ? $survey_exp[ 'user_details' ]->username : __( 'Not Specified', MODAL_SURVEY_TEXT_DOMAIN ) ) . '
' . __( 'Email:', MODAL_SURVEY_TEXT_DOMAIN ) . ' ' . $survey_exp[ 'user_details' ]->email;
if ( ! empty( $survey_exp[ 'user_details' ]->custom ) ) {
	foreach ( unserialize( $survey_exp[ 'user_details' ]->custom ) as $muc_index=>$muc ) {
$output .= '
' . ucfirst( strtolower( $muc_index ) ) . ': ' . $muc;
	}
}
$output .= '
' . __( 'Date:', MODAL_SURVEY_TEXT_DOMAIN ) . ' ' . $survey_exp[ 'user_details' ]->created . '
' . __( 'Total Score:', MODAL_SURVEY_TEXT_DOMAIN ) . ' ' . $survey_exp[ 'user_details' ]->allscore . '
';
if ( $survey_exp[ 'user_details' ]->alltimer > 0 ) {
$output .= __( 'Required Time:', MODAL_SURVEY_TEXT_DOMAIN ) . ' ' . $survey_exp[ 'user_details' ]->alltimer . __( 'sec', MODAL_SURVEY_TEXT_DOMAIN ) . '
';
}
$output .= __( 'Participant answers marked with stars: *', MODAL_SURVEY_TEXT_DOMAIN );
}
	foreach( $survey_exp[ 'questions' ] as $qkey=>$questions ) {
	$output .= '


' . $questions['name'].'

';
			foreach ( $questions as $key=>$answer ) {
				if ( is_numeric( $key ) ) {
				$thaoptions = unserialize( $answer[ "aoptions" ] );
				$marker = "";
				$admin_comment = "";
				if ( $personal ) {
					if ( in_array( $key, $user_votes[ $qkey ] ) ) {
						$marker = "* ";
						$admin_comment = '
' . $thaoptions[ 16 ];
					}
					if ( $thaoptions[ 0 ] == "open" ) {
						$opan = explode( "|", $user_votes[ $qkey ][ 0 ] );
						if ( isset( $opan[ 1 ] ) ) {
							$answer[ 'answer' ] = __( 'Other', MODAL_SURVEY_TEXT_DOMAIN ) . ": " . $opan[ 1 ];
						}
					}					
				}
	$output .= '' . $marker . __( 'Answer:', MODAL_SURVEY_TEXT_DOMAIN ) . ' ' . $answer[ 'answer' ] . '
' . __( 'Count:', MODAL_SURVEY_TEXT_DOMAIN ) . ' ' . $answer[ 'count' ] . '
' . __( 'Percentage:', MODAL_SURVEY_TEXT_DOMAIN ) . ' ' . $answer['percentage'] . '% ' . $admin_comment . '

';
if ( $thaoptions[ 0 ] == 'open' ) {
	$answers_text = $wpdb->get_results( "SELECT answertext, count FROM " . $this->wpdb->base_prefix . "modal_survey_answers_text WHERE `survey_id` = '" . $survey_exp[ 'id' ] . "' AND `id` = '" . $answer[ 'uniqueid' ] . "' ORDER BY count DESC" );
if ( ! empty( $answers_text ) ) {
$output .= 'Open Text Answers:';
	foreach( $answers_text as $at ) {
$output .= '
' . $at->answertext . ' ' . $at->count . '';
	}
	$output .= '
';
}
}
				}
			}
	$output .= '
' . __( 'Total Votes:', MODAL_SURVEY_TEXT_DOMAIN ) . ' ' . $questions[ 'count' ];
	}
	if ( file_put_contents( $path, $output ) ) {
		$result = "success";
	}
	else {
		$result = __( 'Write error', MODAL_SURVEY_TEXT_DOMAIN );
	}
}
?>