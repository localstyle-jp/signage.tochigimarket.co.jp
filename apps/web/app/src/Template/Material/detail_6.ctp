<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>サイネージ</title>
<link rel="stylesheet" media="all" href="/content/css/reset.css">
<style>
#wrapper {

}
#movie {
    position: absolute;
    top:500px;
    left: 40px;
    text-align: center;
    border: #FFF;
}

#content {

}
video {
    text-align: center;
}
</style>

<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

</head>
<body style="margin: 0;">
<div id="wrapper" style="width:1080px; height:1920px;background-image: url('<?= $material->attaches['image']['0'];?>'); background-size: contain;">
  <div id="movie">
    <div id="content">
        <?= $video; ?>
    </div>
  </div>


</div><!-- / #wrapper -->


</body>
</html>
