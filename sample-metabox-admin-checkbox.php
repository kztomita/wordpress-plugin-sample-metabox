<?php
/*
 * Sample Metabox
 * checkbox追加のサンプル
 */

/*
 * フォーム表示処理
 */
function sample_metabox_add_checkbox()
{
	wp_nonce_field('sample_metabox', 'sample_metabox_checkbox_nonce');

	$id = get_the_ID();

	/* 登録データ取得 */
	$post_meta = get_post_meta($id, SAMPLE_METABOX_METAKEY_CHECKBOX, true);
	$post_meta = $post_meta ? $post_meta : array();
 
	$items = array(array('text' => 'テキスト1', 'value' => 1),
		       array('text' => 'テキスト2', 'value' => 2),
		       );
 
	$name = SAMPLE_METABOX_NAME_CHECKBOX;
	foreach($items as $item){
		$checked = array_search($item['value'], $post_meta) !== false ?
			'checked="checked"' : '';

		echo <<<END_OF_TEXT
<label><input type="checkbox" name="{$name}[]" value="{$item['value']}" $checked>{$item['text']}</label> 
END_OF_TEXT;
	}
}

function sample_metabox_add_meta_boxes_checkbox()
{
	add_meta_box('id_sample_metabox_checkbox', 'チェックボックス', 'sample_metabox_add_checkbox', 'post', 'normal', 'high');
}
add_action('add_meta_boxes', 'sample_metabox_add_meta_boxes_checkbox');

/*
 * 保存処理
 */
function sample_metabox_save_checkbox($post_id)
{
	/*
	 * nonceのチェックには、check_admin_referer()ではなく、wp_verify_nonce()を使用する。
	 * check_admin_referer()だと記事の新規作成時や、ゴミ箱に入れる時に
	 * nonceがなくdieしてしまうので、wp_verify_nonce()で返り値をチェックして、
	 * 保存処理をスキップするようにする。
	 */
	if (!isset($_POST['sample_metabox_checkbox_nonce'])) {
		/*
		 * 不正なリクエストでnonceがない。
		 * 新規作成/ごみ箱に入れた時。
		 */
		return;
	}
	if (!wp_verify_nonce($_POST['sample_metabox_checkbox_nonce'], 'sample_metabox')) {
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

	$value = isset($_POST[SAMPLE_METABOX_NAME_CHECKBOX]) && is_array($_POST[SAMPLE_METABOX_NAME_CHECKBOX]) ?
		$_POST[SAMPLE_METABOX_NAME_CHECKBOX] : array();

	if($value){
		update_post_meta($post_id, SAMPLE_METABOX_METAKEY_CHECKBOX, $value);
	} else {
		delete_post_meta($post_id, SAMPLE_METABOX_METAKEY_CHECKBOX);
	}
}
/*
 * save_post_{$post_type}のactionを使用
 * 全post_typeを対象にするなら、save_post actionを使用
 */
add_action('save_post_post', 'sample_metabox_save_checkbox');

