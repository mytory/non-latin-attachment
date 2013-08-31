=== uploading downloading non-latin filename ===
Contributors: mytory
Donate link: http://mytory.co.kr/paypal-donation
Tags: uploading downloading non-latin filename
Requires at least: 2.9
Tested up to: 3.6
Stable tag: 1.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to upload/download non-latin filename.

== Description ==

WordPress cannot attach files with non-latin file name (e.g., Korean). This is the one major drawback to popularization of WordPress among non-english users.

This plugin will rename the file (if file has non-latin name) to numbers, stores the original file name as a title of media post, and upload the file to the server. When a user attempts to download the file, the file will be returned with corresponding media post's title. But image files will not be processed as such:** image files will be returned with numbered name. Because, src value of shoud be real filename on server.  

This plugin supports GD bbPress Attachments of bbPress.

From ver 1.1, not changes file URL. Ajax request changes download url. So, download url in DB points original file url.

워드프레스는 기본적으로 파일명이 알파벳으로 된 것들만 첨부를 할 수 있다. 그래서 파일명이 한글로 돼 있거나 하면 업로드가 되지 않는다. 비 영미권 사용자들에게 가장 골때리는 문제다.

이 플러그인을 설치하면, 한글로 된 파일명을 제대로 업로드한다. (파일명이 영문인 경우엔 파일명을 바꾸지 않는다.) 대신 원래 파일명은 미디어 포스트의 제목으로 넣어 준다. 사용자가 파일을 다운받을 때는 파일명을 미디어 포스트의 제목으로 바꿔서 다운받게 된다. 물론 이미지파일의 경우 서버의 실제 파일명을 `<img>`의 `src`에 넣어야 하기 때문에 서버에 있는 숫자 파일명을 사용해서 본문에 넣게 된다.

이 플러그인은 bbPress의 GD bbPress Attachments도 지원한다. (파일명이 숫자로 나오게 되는데 이걸 미디어 포스트의 제목으로 변경해 준다. js를 이용한다.)

버전 1.1부터는 DB 차원에서 URL을 교체하지 않는다. 아작스로 파일을 교체한다. 따라서 플러그인을 제거해도 파일 다운로드 URL이 깨지지 않는다.

== Installation ==

1. Upload the 'uploadingdownloading-non-latin-filename' folder to the '/wp-content/plugins/' directory. 
1. Activate the plugin through the 'Plugins' menu in WordPress.

== Screenshots ==

1. Non-latin(Korean) filename downloaing on Firefox.
1. Non-latin(Korean) filename downloaing on IE.
1. Non-latin(Korean) filename downloaing on Chrome.

== Changelog ==

= .1.1.1 =
* 파일명의 대문자를 소문자로 바꾸지 않는다. (No changes uppercase to lowercase.)
* 플러그인에서 스크린샷을 제외해서 용량을 대폭 줄임. (Remove screenshots from plugin folder. Size went down greatly.)

= 1.1 =
* 더이상 download 링크를 `download.php`로 박지 않으므로 플러그인을 제거해도 download 경로가 깨지지 않는다.
  (아직 과거 URL을 변경하는 것은 만들지 않았음. 나중에 만들 계획.)    
  Even if you remove this plugin, download url is not break. because ajax request changes download URLs.
  (Old download.php?id=000 url will ramain. I'll develope old url change script.)
* 파일명이 영문이면 파일명을 바꾸지 않는다. (Not rename if filename is latin characters.)
* 사용자 권한 검사할 때 deprecated 코드 제거. (Removed deprecated code when verify user capability.)
* `$_SESSION`을 사용하지 않게 했다. (No more use `$_SESSION`)
* Support iPod

= 1.0.9 =
Support iPad.

= 1.0.8 =
Using WP ajax method.

= 1.0.7 =
correct filename on iPhone.

= 1.0.6 =
fixed incorrect wp_enqueue_script calling.

= 1.0.5 =
Changed the file name. bbPress bug fixed.

= 1.0.4 =
Finally fixed the errors caused by the foldername.

= 1.0.3 =
Fixed download error. Because plusgin foldername. 

= 1.0.2 =
fixed for firefox download

= 1.0.1 =
fixed download error