<?php
/*
 * Sample Metabox
 * エディタ追加のサンプル
 */


/*
 * フォーム表示処理
 */
function sample_metabox_add_editor()
{
	wp_nonce_field('sample_metabox', 'sample_metabox_editor_nonce');

	$id = get_the_ID();

        $content = '';

	$post_meta = get_post_meta($id, SAMPLE_METABOX_METAKEY_EDITOR, true);
	if ($post_meta) {
                $content = $post_meta;
	}

        $settings = array();

        wp_editor($content, SAMPLE_METABOX_NAME_EDITOR, $settings);
}

function sample_metabox_add_meta_boxes_editor()
{
	add_meta_box('id_sample_metabox_editor', 'エディタ', 'sample_metabox_add_editor', 'post', 'normal', 'high');
}
add_action('add_meta_boxes', 'sample_metabox_add_meta_boxes_editor');

/*
 * 保存処理
 */
function sample_metabox_save_editor($post_id)
{
	if (!isset($_POST['sample_metabox_editor_nonce'])) {
		return;
	}
	if (!wp_verify_nonce($_POST['sample_metabox_editor_nonce'], 'sample_metabox')) {
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


	/* 保存 */

        $content = $_POST[SAMPLE_METABOX_NAME_EDITOR];
	update_post_meta($post_id, SAMPLE_METABOX_METAKEY_EDITOR, $content);
}
/*
 * save_post_{$post_type}のactionを使用
 * 全post_typeを対象にするなら、save_post actionを使用
 */
add_action('save_post_post', 'sample_metabox_save_editor');

