<!DOCTYPE html>
<html>
<head>
<title>サイネージ <?= h($machine->name); ?></title>
<style>
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
/* 字幕 */
.rolling_caption_wrapper {
  height: 100px; 
  position: absolute;
  background-color: white;
}
.rolling_caption_wrapper > .rolling_caption_text {
  font-size: 100px;
  margin:0; display:inline-block; white-space:nowrap;
  animation-name:marquee; animation-timing-function:linear;
  animation-duration:calc(2s*<?= strlen($machine->rolling_caption) ?>); animation-iteration-count:infinite;
}
@keyframes marquee {
  from   { transform: translate(<?= $width; ?>px);} 
  99%,to { transform: translate(-100%);}
}
/* /字幕 */
</style>
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script>
$(function () {
  // console.log(window.innerHeight);
  // console.log(window.innerWidth);
  var zoom = <?= $width; ?> / $('iframe').width();
  // var zoom = window.innerWidth / $('iframe').width();
  var scale = 'scale(' + zoom + ')';
  // console.log(scale);
  // $('iframe').attr('width', window.innerWidth).attr('height', window.innerHeight);
  $('iframe').css('transform', scale).css('-webkit-transform', scale).css('-ms-transform', scale).css('-o-transform', scale).css('-moz-transform', scale);
  // 字幕
  // $('.rolling_caption_wrapper').css('top', calc(window.innerHeight-100px));
});
</script>
</head>
<body style="margin: 0; height:<?= $height; ?>px;">
<iframe src="<?= $this->Url->build(['controller' => 'content', 'action' => 'machine', $machine->machine_content_id, '?' => $query]); ?>" width="<?= $width; ?>" height="<?= $height; ?>"></iframe>
<!-- 字幕 -->
<!-- <div class="rolling_caption_wrapper">
  <p class="rolling_caption_text"><-?= h($machine->rolling_caption) ?></p>
</div> -->
  
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
