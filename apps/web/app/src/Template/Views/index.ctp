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
</style>
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script>
$(function () {
  var zoom = <?= $width; ?> / $('iframe').width();
  var scale = 'scale(' + zoom + ')';
  $('iframe').css('transform', scale).css('-webkit-transform', scale).css('-ms-transform', scale).css('-o-transform', scale).css('-moz-transform', scale);
});
</script>
</head>
<body style="margin: 0; height:<?= $height; ?>px;">
<iframe src="<?= $this->Url->build(['controller' => 'content', 'action' => 'machine', $machine->machine_content_id, '?' => $query]); ?>" width="<?= $width; ?>" height="<?= $height; ?>"></iframe>
<!-- marquee -->
<div class="marquee_wrapper">
  <p class="marquee">testtesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttest</p>
</div>
  
<style>
  .marquee_wrapper {
    width: <?= $width; ?>px; height: 100px; 
    position: absolute; top: 980px;
    background-color: white;
  }
  .marquee_wrapper > .marquee {
    font-size: 100px;
    margin:0; display:inline-block; white-space:nowrap;
    animation-name:marquee_test; animation-timing-function:linear;
    animation-duration:60s; animation-iteration-count:infinite;
	}
  @keyframes marquee_test {
    from   { transform: translate(<?= $width; ?>px);} 
    99%,to { transform: translate(-100%);}
  }
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
