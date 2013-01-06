<?php
include '../../../wp-blog-header.php';
$filename_for_download = nlf_get_filename_for_download($_GET['id']);

$attachment = get_post($_GET['id']);
$attachment_meta = get_post_meta($_GET['id']);
$upload_dir = wp_upload_dir();

$original_filepath = $upload_dir['basedir'] . '/' . $attachment_meta['_wp_attached_file'][0];

header('Content-type: '.$attachment->post_mime_type);
if( strstr($_SERVER['HTTP_USER_AGENT'],'Firefox') OR strstr($_SERVER['HTTP_USER_AGENT'],'iPad') 
		OR strstr($_SERVER['HTTP_USER_AGENT'],'iPhone')){
	$filename_encoded = $filename_for_download;
}else{
	$filename_encoded = urlencode($filename_for_download);
}
header('Content-Disposition: attachment; filename="'.$filename_encoded.'"');
// orifinal file
readfile($original_filepath);