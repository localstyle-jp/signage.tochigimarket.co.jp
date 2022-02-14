<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1">
<title>サイネージ <?= h($machine->name); ?></title>
<style>
:root {
  --initial-left-pos: 0;
  --initial-font-size: 0;
}
iframe {
  border: 0;
  transform-origin: 0 0;
  -webkit-transform-origin: 0 0;
  -ms-transform-origin: 0 0;
  -o-transform-origin: 0 0;
  -moz-transform-origin: 0 0;
}
body::-webkit-scrollbar {
  background:#000000;
  display: none;
  width: 0;
}
</style>
<script src="/user/common/js/jquery-3.5.1.min.js"></script>

<script>
$(function () {
  var zoom = <?= $width; ?> / $('iframe').width() / parseFloat(window.devicePixelRatio);
  var scale = 'scale(' + zoom + ')';
  var height_new = <?= $height ?>/parseFloat(window.devicePixelRatio);
  var width_new = <?= $width ?>/parseFloat(window.devicePixelRatio);
  var bottom_pos = <?= $height ?> - height_new;
  var font_size = Math.max(1, Math.min(height_new*0.05 - 2, width_new*0.05 - 2));
  var caption_time = 0.003 * (<?= $width ?> + font_size*<?= strlen($machine->rolling_caption) ?>);

  $('iframe').css('transform', scale).css('-webkit-transform', scale).css('-ms-transform', scale).css('-o-transform', scale).css('-moz-transform', scale);
  $('.rolling_caption_wrapper').css('bottom', bottom_pos+'px');
  $(':root').css('--initial-left-pos', width_new + 'px').css('--initial-font-size', font_size + 'px');
  $('.rolling_caption_text').css('animation-duration', caption_time + 's').css('animation-name', 'marquee');
});
</script>
</head>
<body style="margin: 0; height:<?= $height; ?>px; overflow: hidden">
<iframe src="<?= $this->Url->build(['controller' => 'content', 'action' => 'machine', $machine->machine_content_id, '?' => $query]); ?>" width="<?= $width; ?>" height="<?= $height; ?>"></iframe>
<!-- 字幕 -->
<?php if(!empty($machine->rolling_caption)) : ?>
<div class="rolling_caption_wrapper">
  <div class="displayed_area">
    <p class="rolling_caption_text" id="rolling_caption_text"><?= h($machine->rolling_caption) ?></p>
  </div>
</div>
<?php endif; ?>

<style>
/* 字幕 */
.rolling_caption_wrapper {
  position: relative;
  bottom: 0px;
  width: 1px;
  height: 1px;
  line-height: 0;
}

.rolling_caption_wrapper .displayed_area {
  position: absolute;
  bottom: 8px;
  overflow: hidden;
  width: <?= $width ?>px;
  height: 1.6em;
  font-size: var(--initial-font-size);
}

.rolling_caption_text {
  font-size: var(--initial-font-size);
  position: absolute;
  bottom: 0px;
  margin:0; display:inline-block; white-space:nowrap;
  animation-timing-function:linear;
  animation-iteration-count:infinite;
  color: white;
  line-height: 1.6em;
  text-shadow: 3px 3px 4px black;
}
@keyframes marquee {
  from   { transform: translate(var(--initial-left-pos));}
  99%,to { transform: translate(-100%);}
}
/* /字幕 */
</style>

<script src="/user/common/js/cms-slim.js"></script>
<script>
var reload_flag = 1;

var disableReload = function(id) {
  var url = '/v1/management/disable-reload.json';
  var params = {
    id: id
  };
  ajax_get(url, 'post', params, function(a) {
    if (a.error.code == 0) {
    }
  });
};

var checkReload = function(id) {
  var url = '/v1/management/is-reload.json';
  var params = {
    id: id
  };
  ajax_get(url, 'post', params, function(a) {
    if (a.error.code == 0) {
      if (a.result.reload_flag == 1) {
        window.location.reload();
      }
    }
  });
};

setInterval(checkReload, 10000, <?= $machine->id; ?>);

$(function(){
  if (reload_flag == 1) {
    disableReload(<?= $machine->id; ?>);
    reload_flag = 0;
  }

})
</script>
</body>
</html>
