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
  var zoom = 1920 / $('iframe').width();
  var scale = 'scale(' + zoom + ')';
  $('iframe').css('transform', scale).css('-webkit-transform', scale).css('-ms-transform', scale).css('-o-transform', scale).css('-moz-transform', scale);
});
</script>
</head>
<body style="margin: 0; height:1080px;">
  <iframe src="<?= $this->Url->build(['controller' => 'content', 'action' => 'index', $machine->content_id, '?' => $query]); ?>" width="1920" height="1080"></iframe>
</body>
</html>
