<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>サイネージ</title>


<link rel="stylesheet" media="all" href="/content/css/reset.css">
<link rel="stylesheet" media="all" href="/content/css/master.css">
<link rel="stylesheet" media="all" href="/content/css/style.css">

<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

</head>
<body>
<div id="wrapper">
  <div id="cont_inner">

  <?php foreach ($materials as $material): ?>

    <div class="<?= h($material['class']); ?>"><?= $material['content']; ?></div>

  <?php endforeach; ?>


  </div>


</div><!-- / #wrapper -->
<script>
var reload = function () {
  var parent = null;
  var f = function (event) {
    if (event.data === 'parent') {
      parent = event.source;
      window.removeEventListener('message', f);
      f = null;
    }
  };
  window.addEventListener('message', f);

  return function () {
    if (parent) {
      parent.postMessage('reload', '*');
    } else {
      document.location.reload();
    }
  };
}();

// iframe API
var tag = document.createElement('script');
tag.src = "https://www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

var player;
<?php if(!empty($material_youtube)): ?>
player = <?= json_encode($material_youtube); ?>;
<?php endif; ?>


function onYouTubeIframeAPIReady() {
  var playerStateEnded = function () {

    $.each(player, function(i, val) {
      if (player[i].error_flg == 0) {
        player[i].obj.pauseVideo();
        player[i].obj.seekTo(0, true);
      }
    });
    scene_manager();
  };

  $.each (player, function(i, val) {
    player[i].obj = new YT.Player('player_' + val.no, {
      height: '100%',
      videoId: val.code,
      wmode: 'transparent',
      width: '100%',
      playerVars:{
        'autoplay': 0,
        'controls': 0,
        'loop': 0,
        'playlist': val.code,
        'rel': 0,
        'showinfo': 0,
        'color': 'white'
      },
      events: {
        'onReady': function (event) {
          event.target.mute();
        },
        'onStateChange': function (event) {
          if (event.data == 0) {
            playerStateEnded();
          }
        },
        'onError': function (event) {
          player[i].error_flg = 1;
          playerStateEnded();
        }
      }
    });
  });
  
}

var is_connect = 0;

var scene_manager = function () {
    var items = <?= json_encode($items); ?>;

    var scene_list = <?= json_encode($scene_list); ?>;

    var START_INDEX = 0;
    var END_INDEX = scene_list.length -1;
    var index = 0; // scene_listのindex
    var prev = 0;

    var show = function () {

        $.each(player, function(i, val) {
          if (player[i].error_flg == 1) {
            document.body.querySelector('.type_' + val.no).style.display = "none";
            return false;
          }
        });

        $.each(player, function(i, val) {
          if (player[i].error_flg == 1 && is_connect == 1) {
            reload();
            return false;
          }
        });

        var now = index; // indexのままfadeIn()で利用すると,fadeOut(1000)の間にindexが進んでしまう
        $('.type_' + scene_list[prev]).fadeOut(1000, function () {
            $('.type_' + scene_list[now]).fadeIn(1000);
        });

        var flg = 0;
        $.each(player, function(i, val) {
          if (flg == 0 && player[i].error_flg == 0 && items[scene_list[index]].action == 'play_video_' + val.no) {
            player[i].obj.playVideo();
            __next();
            flg = 1;
            return false;
          }
        });
        if (flg == 0) {
          setTimeout(function(){
              next();
          }, items[scene_list[index]].time);
        }

    };
    var __next = function () {
        // videoスキップしないとき
        $.each (player, function(i, val) {
          if (!(player[i].error_flg == 1 && items[scene_list[index]].action == 'play_video_' + val.no)) {
            prev = index;
            return false;
          }
        });

        index++;
        if(index > END_INDEX) {
          index = 0;
        }
    }
    var next = function () {
        __next();

        $.each (player, function(i, val) {
          if (player[i].error_flg == 1 && items[scene_list[index]].action == 'play_video_' + val.no) {
            __next();
            return false;
          }
        });

        show();
    };
    return function () {
        show();
    };
}();


var data = {
  shinkansen_up: null,
  shinkansen_down: null,
  tohoku_up: null,
  tohoku_down: null,
  nikko_down: null,
  karasuyama_down: null
};


var clock = function () {
  var now  = new Date();
  var hour = now.getHours();
  var min  = now.getMinutes();

  if (hour < 10) { hour = "0" + hour; }
  if (min < 10) { min = "0" + min; }

  var clock_time = hour + ':' + min;
  document.body.querySelector('.time_area').innerHTML = clock_time.toLocaleString();
};

var interval_clock = function () {
  setInterval(function () {
    // clock();
  }, 1000);
};

$(function () {
  interval_clock();
  scene_manager();
});
</script>

</body>
</html>
