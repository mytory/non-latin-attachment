uploading downloading non-latin filename
========================================

Contributors: mytory
Donate link: http://mytory.co.kr/paypal-donation
Tags: uploading downloading non-latin filename
Requires at least: 2.9
Tested up to: 3.5
Stable tag: 1.0.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to upload/download non-latin filename.

Description
-----------

WordPress cannot attach files with non-latin file name (e.g., Korean). This is the one major drawback to popularization of WordPress among non-english users.

This plugin will rename the file (with latin or non-latin names) to numbers, stores the original file name as a title of media post, and upload the file to the server. When a user attempts to download the file, the file will be returned with corresponding media post's title. But image files will not be processed as such: image files will be returned with numbered name. Because, src value of shoud be real filename on server.

This plugin supports GD bbPress Attachments of bbPress.

CAUTION: The files uploaded with this plugin will be downloaded via 'download.php'. Therefore, these file links in the post will be broken when the plugin is removed. But in case of images files these links, of course, will be fine without this plugin.

워드프레스는 기본적으로 파일명이 알파벳으로 된 것들만 첨부를 할 수 있다. 그래서 파일명이 한글로 돼 있거나 하면 업로드가 되지 않는다. 비 영미권 사용자들에게 가장 골때리는 문제다.

이 플러그인을 설치하면 업로드할 때 파일명을 숫자로 교체한다. (파일명이 영문이든 영문이 아니든 무조건 교체한다.) 대신 원래 파일명은 미디어 포스트의 제목으로 넣어 준다. 사용자가 파일을 다운받을 때는 파일명을 미디어 포스트의 제목으로 바꿔서 다운받게 된다. 물론 이미지파일의 경우 서버의 실제 파일명을 <img>의 src에 넣어야 하기 때문에 서버에 있는 숫자 파일명을 사용해서 본문에 넣게 된다.

이 플러그인은 bbPress의 GD bbPress Attachments도 지원한다. (파일명이 숫자로 나오게 되는데 이걸 미디어 포스트의 제목으로 변경해 준다. js를 이용한다.)

주의 : 파일을 다운로드할 때 플러그인의 download.php 를 거치게 돼 있다. 만약 이 플러그인을 삭제하면 다운로드 링크들은 깨지게 될 거다. 물론 이미지 파일들은 멀쩡할 거다.

Installation
------------

1. Upload the 'uploadingdownloading-non-latin-filename' folder to the '/wp-content/plugins/' directory. 
1. Activate the plugin through the 'Plugins' menu in WordPress.

Screenshots
-----------

1. Non-latin(Korean) filename downloaing on Firefox.
1. Non-latin(Korean) filename downloaing on IE.
1. Non-latin(Korean) filename downloaing on Chrome.

Changelog
---------

###1.0.9
Support iPad.

###1.0.8
Using WP ajax method.

###1.0.7
correct filename on iPhone.

###1.0.6
fixed incorrect wp_enqueue_script calling.

###1.0.5
Changed the file name. bbPress bug fixed.

###1.0.4
Finally fixed the errors caused by the foldername.

###1.0.3
Fixed download error. Because plusgin foldername. 

###1.0.2
fixed for firefox download

###1.0.1
fixed download error