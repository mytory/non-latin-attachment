<?php
if( ! isset($_GET['id']) or empty($_GET['id'])){
    echo "error! enter attachment ID!";
    exit;
}

include '../../../wp-blog-header.php';

$filename_for_download = nlf_get_filename_for_download($_GET['id']);

$attachment = get_post($_GET['id']);
$attachment_meta = get_post_meta($_GET['id']);
$upload_dir = wp_upload_dir();

$original_path = $upload_dir['basedir'] . '/' . $attachment_meta['_wp_attached_file'][0];
$filename_encoded = rawurlencode($filename_for_download);

status_header(200);
header('cache-control: no-cache');
header('Content-type: ' . $attachment->post_mime_type);
header("Content-Disposition: attachment; filename*=UTF-8''{$filename_encoded}");

// orifinal file
readfile($original_path);