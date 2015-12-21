jQuery(document).ready(function () {
  //
  // media modalの処理
  //
  // wp.mediaを使って実装する。
  // 参照: https://codex.wordpress.org/Javascript_Reference/wp.media
  //
  // 'media-upload.php?type=image&amp;TB_iframe=true'を開く手もあるが、
  // ファイル選択時の処理として、send_to_editor()をカスタマイズする必要がある。
  // send_to_editor()をこじらせるより安全なのと、
  // modalのカスタマイズが行えるので、wp.mediaを使う。
  //

  // カスタムしたmedia modalの作成
  function createCustomMedia()
  {
    var media = wp.media({
      title: 'ファイルアップロード',
      library: {type: ''},
      frame: 'select',
      button: {text: '選択'},
      multiple: false
    });

    return media;
  }

  var customMedia;
  jQuery('#sample_metabox_add_media').click(function (event) {
    event.preventDefault();

    if (customMedia) {
      customMedia.open();
      return;
    }

    customMedia = createCustomMedia();
    customMedia.on('select', function() {
      var attachment = customMedia.state().get('selection').first().toJSON();
      jQuery('#sample_metabox_media_url').val(attachment.url);
    });

    customMedia.open();
  });

});
