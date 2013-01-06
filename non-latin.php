<?php
/*
Plugin Name: uploading downloading non-latin filename
Description: WordPress cannot attach files with non-latin file name (e.g., Korean). This is the one major drawback to popularization of WordPress among non-english users. This plugin will rename the file (with latin or non-latin names) to numbers, stores the original file name as a title of media post, and upload the file to the server. When a user attempts to download the file, the file will be returned with corresponding media post's title. But image files will not be processed as such: image files will be returned with numbered name. Because, src value of shoud be real filename on server. 
Author: Ahn, Hyoung-woo
Author URI: http://mytory.co.kr
Version: 1.0.9
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
	$path_info = pathinfo($file['name']);
	
	$_SESSION['original_filename'] = $file['name'];
	$new_filename = date('Ymd_His');
	$_SESSION['new_filename'] = $new_filename;
	$file['name'] = $new_filename . '.' . $path_info['extension']; 
	
	return $file;
}

/**
 * set post_title by original filename.
 * 파일을 첨부할 때 원본 파일명으로 첨부 파일의 post_title을 넣어 준다.
 * $attachment 는 post_id 다.
 * @param int $attachment
 */
function nlf_add_attachment($attachment){
	$post = get_post($attachment);
	//포스트 타이틀이 없다면 원 파일명으로 첨부파일의 타이틀을 넣어 준다.
	if( $_SESSION['new_filename'] == $post->post_title ){
		$post->post_title = $_SESSION['original_filename'];
		//첨부파일 타이틀 업데이트에 실패하면 에러 메시지를 출력하고 죽는다.
		if( wp_update_post((array)$post) === 0 ){
			echo 'error occured! - ';
			if($current_user->allcaps['level_10']){
				echo __FILE__ . ' LINE  ' . __LINE__;
				echo '<br>파일명과 줄 번호는 관리자에게만 보입니다.';
			}
			exit;
		}
	}
	unset($_SESSION['original_filename']);
	unset($_SESSION['new_filename']);
}

/**
 * Set file download url from download.php script url that plugin has.
 * 파일 다운로드 url을 파일의 url을 직접 거는 대신에 플러그인이 만든 download 스크립트의 url로 바꿔 준다. 
 * 그렇게 함으로써 다운받을 때 첨부파일 포스트의 타이틀로 파일명을 대체해 준다.
 * @param string $url
 * @param int $post_id
 */
function nlf_wp_get_attachment_url($url, $post_id){
	//이미지면 실제 url을 돌려 준다.
	if( wp_attachment_is_image($post_id)){
		return $url;
	}
	
	//EG_Attchments 플러그인과 충돌하지 않도록 함.
	$backgrace = debug_backtrace();
	foreach ($backgrace as $arr) {
		if(isset($arr['class']) && $arr['class'] == 'EG_Attachments'){
			return $url;
		}
	}
	
	$url = site_url('wp-content/plugins/uploadingdownloading-non-latin-filename/download.php?id=' . $post_id);
	return $url;
}

add_filter('wp_handle_upload_prefilter', 'nlf_prefilter');
add_action('add_attachment', 'nlf_add_attachment');
add_filter('wp_get_attachment_url', 'nlf_wp_get_attachment_url', '', 2);

/**
 * ========================= for GD bbPress Attachment =================================
 */

function nlf_enqueue_script() {
	wp_enqueue_script('nlf_gd_bbpress_attachment', plugins_url('uploadingdownloading-non-latin-filename/non-latin.js'), array('jquery'), '1.0.9', 1);
	wp_localize_script( 'nlf_gd_bbpress_attachment', 'nlf', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
}

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
		$filename_for_download = sanitize_file_name($attachment->post_title) . '.' . $extension;
	}else{
		$filename_for_download = sanitize_file_name($attachment->post_title);
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
add_action('wp_enqueue_scripts', 'nlf_enqueue_script');


//end of non-latin.php