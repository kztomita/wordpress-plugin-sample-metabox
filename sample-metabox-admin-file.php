<?php
/*
 * Sample Metabox
 * ファイルアップロード追加のサンプル
 *
 * メディアライブラリには登録しない。
 * このため、記事をゴミ箱から削除する際に登録されているファイルも削除する。
 * ファイルを再アップロードした場合は、旧ファイルは削除する。
 */


/*
 * フォーム表示処理
 */
function sample_metabox_add_file()
{
	wp_nonce_field('sample_metabox', 'sample_metabox_file_nonce');

	$id = get_the_ID();
	$post_meta = get_post_meta($id, SAMPLE_METABOX_METAKEY_FILE, true);
	if ($post_meta) {
		echo '<p><a href="'.$post_meta['url'].'" target="_blank">'.$post_meta['url'].'</a></p>';
	}

	$name = SAMPLE_METABOX_NAME_FILE;

	echo <<<END_OF_TEXT
<input type="file" name="{$name}" id="{$name}" value="" />
END_OF_TEXT;
	if ($post_meta) {
		echo <<<END_OF_TEXT
<label><input type="checkbox" name="{$name}_delete" id="{$name}_delete" value="1" />ファイルを削除</label>
END_OF_TEXT;
	}
}

function sample_metabox_add_meta_boxes_file()
{
	add_meta_box('id_sample_metabox_file', 'ファイルアップロード(PDF)', 'sample_metabox_add_file', 'post', 'normal', 'high');
}
add_action('add_meta_boxes', 'sample_metabox_add_meta_boxes_file');

/* ファイルアップロードに必要 */
function sample_metabox_edit_form_tag() {
	echo ' enctype="multipart/form-data"';
}
add_action('post_edit_form_tag', 'sample_metabox_edit_form_tag');

/*
 * 保存処理
 */
function sample_metabox_save_file($post_id)
{
	if (!isset($_POST['sample_metabox_file_nonce'])) {
		return;
	}
	if (!wp_verify_nonce($_POST['sample_metabox_file_nonce'], 'sample_metabox')) {
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

	/*
	 * ファイル削除を行うかチェック
	 */
	$post_meta = get_post_meta($post_id, SAMPLE_METABOX_METAKEY_FILE, true);
	if ($post_meta && $_POST[SAMPLE_METABOX_NAME_FILE.'_delete']) {
		@unlink($post_meta['file']);
		delete_post_meta($post_id, SAMPLE_METABOX_METAKEY_FILE);
		return;		/* 終了。アップロードは行わない。 */
	}

	/*
	 * アップロードされたファイルのチェック
	 */
	if(empty($_FILES[SAMPLE_METABOX_NAME_FILE]['name'])) {
		/* ファイルなし */
		return;
	}

	$file = $_FILES[SAMPLE_METABOX_NAME_FILE];

	$file_type = wp_check_filetype($file['name']);
	if ($file_type['ext'] != 'pdf') {
		wp_die('Unsupported file type. (Supported file type: .pdf)');
	}
 
	/* ファイル保存 */

	/* 旧ファイルがあれば削除 */
	if ($post_meta) {
		@unlink($post_meta['file']);
	}

	/*
	 * アップロードフォルダにファイル保存。
	 * 同名ファイルあれば、リネームされた名前が使われるので、
	 * 上書きされる心配はない。
	 * sanitize_file_name()も行われる。
	 */
	if (0) {
		$upload = wp_upload_bits($file['name'], null, file_get_contents($file['tmp_name']));
	} else {
		$overrides = array('test_form' => false);
		$upload = wp_handle_upload($file, $overrides);
	}
     
	if(isset($upload['error']) && $upload['error']) {
		wp_die('Upload error : '.$upload['error']);
	}

	$post_meta = array('file' => $upload['file'],
			   'url'  => $upload['url'],
			   'type' => $upload['type'],
			   );
	update_post_meta($post_id, SAMPLE_METABOX_METAKEY_FILE, $post_meta);
}
/*
 * save_post_{$post_type}のactionを使用
 * 全post_typeを対象にするなら、save_post actionを使用
 */
add_action('save_post_post', 'sample_metabox_save_file');


/* 記事がゴミ箱から削除される時の処理 */
function sample_metabox_before_delete_post($post_id)
{
	global $post_type;
	if ($post_type != 'post')
		return;

	/* ファイルの削除 */
	$post_meta = get_post_meta($post_id, SAMPLE_METABOX_METAKEY_FILE, true);
	if ($post_meta) {
		@unlink($post_meta['file']);
	}

	/* post_meta情報自体はシステムの方で削除される */
}
add_action('before_delete_post', 'sample_metabox_before_delete_post');

