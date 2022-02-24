<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>サイネージ</title>


<link rel="stylesheet" media="all" href="/content/css/reset.css">
<link rel="stylesheet" media="all" href="/content/css/master.css">
<link rel="stylesheet" media="all" href="/content/css/style.css">
<script src="/user/common/js/hls.js"></script>

<script src="/user/common/js/jquery-3.5.1.min.js"></script>
<script src="/user/common/js/cms-slim.js"></script>
<!-- 字幕 -->
<style>
:root {
  --initial-left-pos: 0;
  --initial-font-size: 0;
  --width: 0;
  --height: 0;
}
body::-webkit-scrollbar {
  background:#000000;
  display: none;
  width: 0;
}
</style>
<script>
$(function () {
  var height_new = <?= $height ?>/parseFloat(window.devicePixelRatio);
  var width_new = <?= $width ?>/parseFloat(window.devicePixelRatio);
  var bottom_pos = <?= $height ?> - height_new;
  var font_size = Math.max(1, Math.min(height_new*0.05 - 2, width_new*0.05 - 2));

  $('.rolling_caption_wrapper').css('bottom', bottom_pos+'px');
  $(':root').css({
    '--initial-left-pos': width_new + 'px', 
    '--initial-font-size': font_size + 'px', 
    '--height': height_new, 
    '--width': width_new
  });
  // $('.rolling_caption_text').css('animation-name', 'marquee');
  $('.displayed_area').css('width', width_new + 'px');
});
</script>
<!-- 字幕 -->
<style>
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
  height: 1.6em;
  font-size: var(--initial-font-size);
}

.rolling_caption_text {
  font-size: var(--initial-font-size);
  position: absolute;
  bottom: 0px;
  margin:0; display:inline-block; white-space:nowrap;
  animation-name: marquee;
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
</style>
<!-- 字幕 end -->
</head>
<body>
<div id="wrapper">
  <div id="cont_inner" style="width: <?= $width; ?>px; height: <?= $height; ?>px;">

<?php $mp4_play_block = 1; ?>
  <?php foreach ($materials as $material): ?>

  <?php if ($material['type'] != 'mp4'): ?>
    <div class="<?= h($material['class']); ?>" style="display: none;"><?= $material['content']; ?></div>
  <?php else: ?>
  <?php if ($mp4_play_block): ?>
    <div class="box type_mp4" id="mp4_play_block" style="display: none;"><?= $material['content']; ?></div>
  <?php $mp4_play_block = 0; endif; ?>
  <?php endif; ?>

  <?php endforeach; ?>

    <!-- 字幕 -->
    <div class="rolling_caption_wrapper">
      <div class="displayed_area">
        <p class="rolling_caption_text" id="rolling_caption_text"></p>
      </div>
    </div>


  </div>


</div><!-- / #wrapper -->



<!-- ローディングバー -->
<!-- <div class="loader_container" id="loader_container">
  <div class="bar_container">
    <div class="progress_bar" id="progress_bar">
      <div class="progress_percentage" id="progress_percentage">
        0%
      </div>
    </div>
  </div>
  
  <div class="text_container">
    Loading
    <span>Please Wait...</span>
  </div>
  
</div> -->
<!-- / ローディングバー -->

<!-- デバッグ用 -->
<!-- <div id="content_no_block">
  <div></div>
</div> -->

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
// var tag = document.createElement('script');
// tag.src = "https://www.youtube.com/iframe_api";
// var firstScriptTag = document.getElementsByTagName('script')[0];
// firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

var player;
var mp4;
var mp4_obj;
// var webm;
var webpage;
<?php if(!empty($material_youtube)): ?>
player = <?= json_encode($material_youtube); ?>;
<?php endif; ?>

// num_mp4 = 0;
<?php if (!empty($material_mp4)): ?>
mp4 = <?= json_encode(($material_mp4)); ?>;
// num_mp4 = Object.keys(mp4).length;
<?php endif; ?>

// <-?php if (!empty($material_webm)): ?>
// webm = <-?= json_encode(($material_webm)); ?>;
// <-?php endif; ?>

<?php if (!empty($material_webpage)): ?>
webpage = <?= json_encode(($material_webpage)); ?>;
<?php endif; ?>

// function onYouTubeIframeAPIReady() {
//   var playerStateEnded = function () {

//     $.each(player, function(i, val) {
//       if (player[i].error_flg == 0) {
//         player[i].obj.pauseVideo();
//         // player[i].obj.mute();
//         player[i].obj.seekTo(0, true);
//       }
//     });
//     scene_manager();
//   };

//   $.each (player, function(i, val) {
//     player[i].obj = new YT.Player('player_' + val.no, {
//       height: '100%',
//       videoId: val.code,
//       wmode: 'transparent',
//       width: '100%',
//       playerVars:{
//         'autoplay': 0,
//         'controls': 0,
//         'loop': 0,
//         'playlist': val.code,
//         'rel': 0,
//         'showinfo': 0,
//         'color': 'white'
//       },
//       events: {
//         'onReady': function (event) {
//           // event.target.mute();
//         },
//         'onStateChange': function (event) {
//           if (event.data == 0) {
//             playerStateEnded();
//           }
//         },
//         'onError': function (event) {
//           player[i].error_flg = 1;
//           playerStateEnded();
//         }
//       }
//     });
//   });
  
// }

function next_caption (caption_str) {
  // 字幕文字列切り替え
  var old_marquee = document.getElementById("rolling_caption_text");
  old_marquee.innerHTML = caption_str;
  old_marquee.parentNode.insertBefore(old_marquee.cloneNode(true), old_marquee);
  old_marquee.parentNode.removeChild(old_marquee);
  // 字幕の流れる時間を入力
  var width = $(':root').css('--width');
  var text_width = document.getElementById("rolling_caption_text").clientWidth;  
  var caption_time = 0.005 * (parseInt(width) + parseInt(text_width));
  $('.rolling_caption_text').css('animation-duration', caption_time + 's');
}

function checkOnline () {
  fetch("<?= $this->Url->build('/phpinfo.php', true); ?>")
    .then(res => {
      $('.offline_alert_container').remove();
    })
    .catch(error => {
      $('body').append('<div class="offline_alert_container" style="position: fixed; top: 0px; left: 0px;"><p style="font-size: 30px;">サーバに接続できません。</p></div>');
      var now = new Date();
      console.log(now+' network error: '+error);
    });
}

function createHls (i) {
  if (Hls.isSupported()) {
    var hls_config = {
      maxMaxBufferLength: 10,            // [s]
      // maxBufferSize: 5 * 1000 * 1000,    // [Byte]
    };
    var hls = new Hls(hls_config);
    hls.loadSource(mp4[i].source);
    hls.attachMedia(mp4[i].obj);
    // hls.once(Hls.Events.MEDIA_ATTACHED, ()=>{
    //   var now = new Date();
    //   console.log(now+'attached hls player: '+i);
    // });
    mp4[i].hls = hls;
  } else if (mp4[i].obj.canPlayType('application/vnd.apple.mpegurl')) {
    // mp4[i].obj.src = mp4[i].source;
  }
}

function destroyHls (i) {
  if (Hls.isSupported()) {
    mp4[i].hls.destroy();
  } else if (mp4[i].obj.canPlayType('application/vnd.apple.mpegurl')) {
    // mp4[i].obj.src = '';
  }
}

function playMp4(i, n) {        // n : play()を実行しようとした回数
  $("#mp4_play_block").html(mp4[i].content);

// console.log(mp4);
  if (mp4[i].obj!=null) {
    // var now = new Date();
    // console.log('\n-----------\n'+now+' bandwidth: '+mp4[i].hls.bandwidthEstimate+' '+i);
    mp4[i].obj.currentTime = 0;
    // mp4[i].obj.play();
    promise = mp4[i].obj.play();
    promise.then(
      function(resolve) {
        // var now = new Date();
        // console.log(now+' playMp4 '+i+' '+n);
      },
      function(reject){
        var now = new Date();
        console.log(now+'rejectMp4 '+i+' '+n);
      }
    );
    // console.log(promise);
    // console.log('playMp4 '+i+' '+n);
  }
  else if (n<3) {
    setTimeout(function(){
      playMp4(i, n+1);
    }, 1000);
  }
}

// function playWebm(i) {

//   webm[i].obj.currentTime = 0;
//   webm[i].obj.play();

// }

function pauseMp4(i) {
  $("#mp4_play_block").html(mp4[i].content);
  mp4[i].obj.pause();
  mp4[i].obj.currentTime = 0;
  // console.log('pauseMp4 '+i);
}

// function pauseWebm(i) {
//   webm[i].obj.pause();
//   webm[i].obj.currentTime = 0;
// }

function loadWebpage(i, n) {      // n : play()を実行しようとした回数
  if (webpage[i].obj!=null) {
    webpage[i].obj.src = webpage[i].source;
  }
  else if(n<3) {
    setTimeout(function(){
      loadWebpage(i, n+1);
    }, 1000);
  }
  // webpage[i].obj.removeAttribute('sandbox');
}

function removeWebpage(i) {
  webpage[i].obj.src = '';
  // webpage[i].obj.setAttribute('sandbox', '');
}

var scene_manager = function () {
    var items = <?= json_encode($items); ?>;

    var scene_list = <?= json_encode($scene_list); ?>;
    var scene_box_list = <?= json_encode($scene_box_list); ?>;
    var START_INDEX = 1;
    var END_INDEX = <?= $item_count; ?>;
    var index = START_INDEX; // scene_listのindex
    var prev = START_INDEX;
    var index_next = START_INDEX+1;
    var show = function () {
        $("#content_no_block div").text(index);

        // $.each(player, function(i, val) {
        //   if (player[i].error_flg == 1) {
        //     document.body.querySelector('.type_' + val.no).style.display = "none";
        //     return false;
        //   }
        // });

        // $.each(player, function(i, val) {
        //   if (player[i].error_flg == 1 ) {
        //     reload();
        //     return false;
        //   }
        // });

        var now = index; // indexのままfadeIn()で利用すると,fadeOut(1000)の間にindexが進んでしまう

        next_caption(items[now]['caption']);
        
        $('.type_' + scene_box_list[prev]).hide(0, function () {
            $('.type_' + scene_box_list[now]).show(0)
            if (index!=prev) {       // 最初にこの関数を回したくなかったので...
              removeWebpages(prev);
            }
        });
        // $('.type_' + scene_box_list[prev]).fadeOut(0, function () {
        //     if (index!=prev) {       // 最初にこの関数を回したくなかったので...
        //       removeWebpages(prev);
        //     }
        //     $('.type_' + scene_box_list[now]).fadeIn(0);
        // });

        var flg = 0;
        // $.each(player, function(i, val) {
        //   if (flg == 0 && player[i].error_flg == 0 && items[scene_list[index]].action == 'play_video_' + val.no) {
        //     if (player[i].obj === null) {
        //       return false;
        //     }
        //     // player[i].obj.mute();
        //     player[i].obj.playVideo();

        //     // 時間が来たら次に進むため下記コメントアウト
        //     // __next();
        //     // flg = 1;
        //     return false;
        //   }
        // });

        
        $.each(mp4, function(i, val) {
          if (items[scene_list[index]].action == 'play_mp4_' + val.no || items[scene_list[index]].action == 'play_page_mp4_' + val.no) {
            playMp4(i, 0);
            return false;
          }
        });

        // $.each(webm, function(i, val) {
        //   if (items[scene_list[index]].action == 'play_webm_' + val.no) {
        //     playWebm(i);
        //     return false;
        //   }
        // });

        $.each(webpage, function(i, val){
          if (items[scene_list[index]].action == 'load_webpage_' + val.no) {
            loadWebpage(i, 0);
            return false;
          }
        });

        if (flg == 0 && items[scene_list[index]].time != 0) {
          // createHlsPlayer();
          setTimeout(function(){
            createHlsPlayer();
          }, items[scene_list[index]].time-10000);

          setTimeout(function(){
            pauseVideos();
            // removeWebpages();
            next();
          }, items[scene_list[index]].time);
        }

    };

    var createHlsPlayer = function() {
        if (items[scene_list[index_next]].action.indexOf('play_mp4_') == 0 && END_INDEX>1) {
          createHls('no'+scene_list[index_next]);
        }
    }

    var pauseVideos = function() {
        // 再生中の動画の停止
        if (items[scene_list[index]].action.indexOf('play_mp4_') == 0) {
          pauseMp4('no'+scene_list[index]);
          if (END_INDEX>1) {
            destroyHls('no'+scene_list[index]);
          }
        }
        // if (items[scene_list[index]].action.indexOf('play_webm_') == 0) {
        //    pauseWebm('no'+scene_list[index]);
        // }
    }

    var removeWebpages = function(prev) {
        // WEBページのソース削除
        <?php if(!empty($material_webpage)): ?>
        $.each (webpage, function(i, val) {
          if (items[scene_list[prev]].action == 'load_webpage_' + val.no) {
            removeWebpage(i);
            return false;
          }
        });
        <?php endif; ?>
    }

    var __next = function () {
        // videoスキップしないとき
        <?php if(!empty($material_youtube)): ?>
        $.each (player, function(i, val) {
          if (!(player[i].error_flg == 1 && items[scene_list[index]].action == 'play_video_' + val.no)) {
            prev = index;
            player[i].obj.pauseVideo();
            // player[i].obj.mute();
            player[i].obj.seekTo(0, true);
            return false;
          }
        });
        
        <?php else: ?>
          prev = index;
        <?php endif; ?>

        index++;
        if(index > END_INDEX) {
          index = START_INDEX;
        }

        index_next++;
        if(index_next > END_INDEX) {
          index_next = START_INDEX;
        }

    }
    var next = function () {
        __next();

        // $.each (player, function(i, val) {
        //   if (player[i].error_flg == 1 && items[scene_list[index]].action == 'play_video_' + val.no) {
        //     __next();
        //     return false;
        //   }
        // });

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

$(function () {

  $.each(mp4, function(i, val) {
    if (val.type == 'mp4') {
      $("#mp4_play_block").html(mp4[i].content);
      // mp4_obj = document.getElementById('mp4_' + val.no);
      mp4[i].obj = document.getElementById('mp4_video');
      mp4[i].content = $("#mp4_video");
      if (val.no==1) {
        createHls(i);
      }
      // console.log(mp4[i].obj.volume);
      // if (Hls.isSupported()) {
      //   var hls_config = {
      //     maxMaxBufferLength: 20,            // [s]
      //     // maxBufferSize: 5 * 1000 * 1000,    // [Byte]
      //   };
      //   var hls = new Hls(hls_config);
      //   hls.loadSource(mp4[i].source);
      //   // hls.attachMedia(mp4[i].obj);
      //   mp4[i].hls = hls;
      // } else if (mp4[i].obj.canPlayType('application/vnd.apple.mpegurl')) {
      //   // mp4[i].obj.src = mp4[i].source;
      // }
    } else if (val.type == 'page_mp4') {
      var elem = document.getElementById('iframe_page_mp4_' + val.count);
      console.log(elem);
      mp4[i].obj = elem.contentWindow.document.getElementById('page_mp4_' + val.no);
      console.log(mp4[i].obj);
    }
  });
  // $.each(webm, function(i, val) {
  //   // if (val.type == 'webm') {
  //   webm[i].obj = document.getElementById('webm_' + val.no);
  //   webm[i].obj.src = webm[i].source;
  //   // }
  // });
  $.each(webpage, function(i, val) {
    webpage[i].obj = document.getElementById('webpage_' + val.no);
    if (val.no==1) {
      webpage[i].obj.src = webpage[i].source;
    }
  });
});


// var checkReloadContent = function(id, serial_no) {
//   var url = '/v1/management/is-reload-content.json';
//   var params = {
//     id: id,
//     serial_no: serial_no
//   };
//   ajax_get(url, 'post', params, function(a) {
//     if (a.error.code == 0) {
//       if (a.result.reload_flag == 1) {
//         window.location.reload();
//       }
//     }
//   });
// };

window.onload = function() {
  scene_manager();
  setInterval(checkOnline, 20000);
  // setInterval(function () {
  //   checkReloadContent(<-?= $content->id; ?>, <-?= $content->serial_no; ?>);
  // }, 10000);
}





// ローディングバー
// var bar=$('#progress_bar');
// var percentage=parseInt($('#progress_percentage').html());

// function stopProgress(){
//   clearInterval(progress);
//   setTimeout(function(){
//     $('#loader_container').hide();         
//   }, 5000);
// }

// var progress= setInterval(function(){
//   percentage++;
//   if (percentage<=100){
//     $('#progress_percentage').html(percentage+'%');
//     if (percentage>10) {
//       bar.css('width',percentage+'%');
//       // console.log(percentage);
//     }
//   }
//   else {
//     stopProgress()
//   }
// // },360*num_mp4);
// },3600);// 6 minutes wait
// /ローディングバー
</script>

</body>
</html>
