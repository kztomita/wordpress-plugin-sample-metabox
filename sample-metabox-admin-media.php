<?php
/*
 * Sample Metabox
 * メディアライブラリを使ったファイルアップロードおよび選択のサンプル
 */

define('SAMPLE_METABOX_ID_ADD_MEDIA', 'sample_metabox_add_media');

/*
 * フォーム表示処理
 */
function sample_metabox_add_media()
{
	wp_nonce_field('sample_metabox', 'sample_metabox_media_nonce');

	$id = get_the_ID();

	$post_meta = get_post_meta($id, SAMPLE_METABOX_METAKEY_MEDIA, true);

	$value = $post_meta ? $post_meta['url'] : '';
	$value_esc = esc_url($value);

	$name = SAMPLE_METABOX_NAME_MEDIA;
	$link_id = SAMPLE_METABOX_ID_ADD_MEDIA;

	echo <<<END_OF_TEXT
<p><input type="text" name="{$name}" id="{$name}" value="{$value_esc}" style="width:90%;" /></p>
<p><a href="javascript:void(0);" id="{$link_id}" class="button">ファイル選択</a></p>
END_OF_TEXT;


}

function sample_metabox_add_meta_boxes_media()
{
	add_meta_box('id_sample_metabox_media', 'メディアライブラリを使ったファイルのアップロードと選択', 'sample_metabox_add_media', 'post', 'normal', 'high');

}
add_action('add_meta_boxes', 'sample_metabox_add_meta_boxes_media');


function sample_metabox_enqueue_scripts($hook)
{
	if ($hook == 'post.php' || $hook == 'post-new.php') {
		$url = plugins_url('js/media_upload.js', __FILE__);
		wp_enqueue_script('media_upload', $url);
	}
}
add_action('admin_enqueue_scripts', 'sample_metabox_enqueue_scripts');

/*
 * 保存処理
 */
function sample_metabox_save_media($post_id)
{
	if (!isset($_POST['sample_metabox_media_nonce'])) {
		return;
	}
	if (!wp_verify_nonce($_POST['sample_metabox_media_nonce'], 'sample_metabox')) {
		return;
	}
       
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	/* 権限チェック */
	if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {
		if (!current_user_can('edit_page', $post_id)) {
			return;
		}
	} else {
		if (!current_user_can('edit_post', $post_id)) {
			return;
		}
	}

	$url = isset($_POST[SAMPLE_METABOX_NAME_MEDIA]) && is_string($_POST[SAMPLE_METABOX_NAME_MEDIA]) ?
		esc_url_raw($_POST[SAMPLE_METABOX_NAME_MEDIA]) : '';

	if($url !== ''){
		$post_meta = array('url' => $url);
		update_post_meta($post_id, SAMPLE_METABOX_METAKEY_MEDIA, $post_meta);
	} else {
		delete_post_meta($post_id, SAMPLE_METABOX_METAKEY_MEDIA);
	}
}
/*
 * save_post_{$post_type}のactionを使用
 * 全post_typeを対象にするなら、save_post actionを使用
 */
add_action('save_post_post', 'sample_metabox_save_media');

