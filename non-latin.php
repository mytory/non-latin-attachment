<?php
/*
Plugin Name: uploading downloading non-latin filename
Description: You can upload files has non-latin filename and download original filename. 이 플러그인을 사용하면 파일명이 한글인 파일을 올릴 수 있고, 다운받을 때는 원래 파일명으로 다운받을 수 있다.
Author: Ahn, Hyoung-woo
Author URI: http://mytory.co.kr
Version: 1.1.4
License: GPL2 (http://www.gnu.org/licenses/gpl-2.0.html)
*/

/**
 * Rename filename by datetime.
 * 파일명을 날짜시간에 기반한 것으로 바꿔 준다. 즉, 비 알파벳 문자여도 서버에서 문제가 없도록 하기 위한 조치다.
 * 대신 원본 파일명을 세션에 담아서 업로드할 때 세션에 있는 파일명으로 업로트 파일 포스트의 타이틀을 설정해 준다.
 * $file 은 $_FILE['filedname']이다.
 * @param array $file
 */
function nlf_prefilter($file) {
	global $current_user;
	$unique_id = "{$_SERVER['REQUEST_TIME']}-$current_user->ID";

	// 파일명에 비알파벳이 없다면 파일명 변경은 필요없다.
	if( $file['name'] == nlf_remove_nonlatin_char($file['name']) ){
		update_user_meta( $current_user->ID, "nlf-need-change-{$unique_id}", "no");
		return $file;
	}

	update_user_meta( $current_user->ID, "nlf-need-change-{$unique_id}", "yes");

	$unique_id = "{$_SERVER['REQUEST_TIME']}-$current_user->ID";
	$path_info = pathinfo($file['name']);
	update_user_meta($current_user->ID, "nlf-original-filename-{$unique_id}", $file['name']);
	$new_filename = date('Ymd_His');
	update_user_meta($current_user->ID, "nlf-new-filename-{$unique_id}", $new_filename);
	$file['name'] = $new_filename . '.' . $path_info['extension']; 
	
	return $file;
}
add_filter('wp_handle_upload_prefilter', 'nlf_prefilter');

/**
 * set post_title by original filename.
 * 파일을 첨부할 때 원본 파일명으로 첨부 파일의 post_title을 넣어 준다.
 * $attachment 는 post_id 다.
 * @param int $attachment
 */
function nlf_add_attachment($attachment){
	global $current_user;

	$unique_id = "{$_SERVER['REQUEST_TIME']}-$current_user->ID";
	$post = get_post($attachment);

	$nlf_need_change = get_user_meta($current_user->ID, "nlf-need-change-{$unique_id}", true);

	// 파일명 변경이 필요한 경우
	if($nlf_need_change == 'yes'){
		$new_filename = get_user_meta($current_user->ID, "nlf-new-filename-{$unique_id}", true);
		$orginal_filename = get_user_meta($current_user->ID, "nlf-original-filename-{$unique_id}", true);

		//포스트 타이틀을 사용자가 따로 넣어 주지 않았다면 원 파일명으로 첨부파일의 타이틀을 넣어 준다.
		if( $new_filename == $post->post_title ){
			$post->post_title = $orginal_filename;

			//첨부파일 타이틀 업데이트에 실패하면 에러 메시지를 출력하고 죽는다.
			if( wp_update_post((array)$post) === 0 ){
				echo 'error occured!';
				if(current_user_can('edit_files')){
					echo '- ' . __FILE__ . ' LINE  ' . __LINE__;
					echo '(파일명과 줄 번호는 파일 편집 권한이 있는 사람에게만 보입니다. 따로 권한을 변경하지 않았다면 파일 편집 권한은 관리자에게만 있습니다.)';
				}
				exit;
			}
		}
		
		// 다 사용한 메타 정보는 삭제한다.
		delete_user_meta($current_user->ID, "nlf-new-filename-{$unique_id}");
		delete_user_meta($current_user->ID, "nlf-original-filename-{$unique_id}");
		delete_user_meta($current_user->ID, "nlf-need-change-{$unique_id}");
	}
}
add_action('add_attachment', 'nlf_add_attachment');

// TODO 첨부파일의 ID도 집어 넣지 않는다.
// 기존에 올린 첨부파일의 경로를 수정하는 기능은 차차 만든다.

/**
 * ========================= for GD bbPress Attachment =================================
 */

function nlf_enqueue_script() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('nlf-common', plugin_dir_url(__FILE__) . 'non-latin.js', array('jquery'), '1.1.4', TRUE);

	$plugin_url = plugin_dir_url(__FILE__);
	$wp_upload_dir = wp_upload_dir();
	$upload_baseurl = $wp_upload_dir['baseurl'];

	$nlf_arr = array( 
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'plugin_url' => $plugin_url,
		'upload_baseurl' => $upload_baseurl . '/',
	);

	wp_localize_script( 'nlf-common', 'nlf', $nlf_arr);
}
add_action('wp_enqueue_scripts', 'nlf_enqueue_script');

/**
 * Get filename from attachment title.
 * 첨부파일 post_title을 기반으로 다운로드용 파일명을 만든다.
 * @param int $attchment_id
 */
function nlf_get_filename_for_download($attchment_id){
	$attachment = get_post($attchment_id);
	$file = get_attached_file($attchment_id);
	$extension = pathinfo($file, PATHINFO_EXTENSION);
	$post_title_extenstion = pathinfo($attachment->post_title, PATHINFO_EXTENSION);
	if($extension != $post_title_extenstion){
		$filename_for_download = nlf_sanitize_file_name( $attachment->post_title ) . '.' . $extension;
	}else{
		$filename_for_download = nlf_sanitize_file_name( $attachment->post_title );
	}

	return $filename_for_download;
}

/**
 * Print filename for download that made from attachment title.
 * 첨부파일 post_title을 기반으로 다운로드용 파일명을 만들어 출력한다.
 */
function nlf_print_filename_for_download(){
	echo nlf_get_filename_for_download($_GET['id']);
	die();
}
add_action("wp_ajax_filename_for_download", "nlf_print_filename_for_download");
add_action("wp_ajax_nopriv_filename_for_download", "nlf_print_filename_for_download");

/**
 * ajax로 첨부파일 URL 배열을 받아서 nlf download.php?id=000 형태의 url객체 배열을 json형태로 만들어서 출력
 */
function nlf_get_download_url(){
	global $wpdb;
	$result = array();
	foreach ($_REQUEST['attachments'] as $guid) {
		$filename = pathinfo($guid, PATHINFO_BASENAME);
		$filetype = wp_check_filetype($filename);

		// If link is image, no change url.
		if( ! strstr($filetype['type'], 'image') ){
			$post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE guid = '{$guid}' AND post_type = 'attachment'");

			// If there is post.
			if($post){
				$result[] = array(
					'guid' => $guid,
					'download_url' => plugin_dir_url(__FILE__) . 'download.php?id=' . $post->ID
				);	
			}
		}
	}
	echo json_encode($result);
	die();
}
add_action("wp_ajax_nlf_get_download_url", "nlf_get_download_url");
add_action("wp_ajax_nopriv_nlf_get_download_url", "nlf_get_download_url");

/**
 * 플러그인을 적용할지 판단할 때 쓰는 파일명 검사 함수.
 * 알파벳은 일단 소문자로 변경해 버린다.
 * 그리고 나서 파일명의 알파벳, 숫자, _, -, 공백만 남기고 나머지는 모두 제거한다.
 * @param string $filename The filename to be sanitized
 * @return string The sanitized filename
 */
function nlf_remove_nonlatin_char( $filename ){
	$raw_filename = $filename;
	$filename = preg_replace( '/[^A-Za-z0-9_\-\. ]/', '', $filename );
	return trim($filename);
}

/**
 * wp의 sanitize_file_name과 두 가지만 빼고 똑같다.
 * 1. '\s' 대신 ' '를 사용해서 공백을 제거한다. '\s'가 맥에서 한글 파일명을 sanitize할 때 이상한 현상을 일으키기 때문이다.
 * 2. apply_filter를 제거했다.
 * @param  string $filename
 * @return string
 */
function nlf_sanitize_file_name( $filename ){
	$filename_raw = $filename;
	$special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}", chr(0));
	$special_chars = apply_filters('sanitize_file_name_chars', $special_chars, $filename_raw);
	$filename = str_replace($special_chars, '', $filename);
	$filename = preg_replace('/[ -]+/', '-', $filename);
	$filename = trim($filename, '.-_');
	
	// Split the filename into a base and extension[s]
	$parts = explode('.', $filename);

	// Return if only one extension
	if ( count($parts) <= 2 ){
		return $filename;
	}

	// Process multiple extensions
	$filename = array_shift($parts);
	$extension = array_pop($parts);
	$mimes = get_allowed_mime_types();

	// Loop over any intermediate extensions. Munge them with a trailing underscore if they are a 2 - 5 character
	// long alpha string not in the extension whitelist.
	foreach ( (array) $parts as $part) {
		$filename .= '.' . $part;

		if ( preg_match("/^[a-zA-Z]{2,5}\d?$/", $part) ) {
			$allowed = false;
			foreach ( $mimes as $ext_preg => $mime_match ) {
				$ext_preg = '!^(' . $ext_preg . ')$!i';
				if ( preg_match( $ext_preg, $part ) ) {
					$allowed = true;
					break;
				}
			}
			if ( !$allowed )
				$filename .= '_';
		}
	}
	$filename .= '.' . $extension;

	return $filename;
}

//end of non-latin.php