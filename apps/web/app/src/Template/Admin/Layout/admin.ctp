<!DOCTYPE html>
<html lang="ja">
<head>
<?php echo $this->Html->charset(); ?>
<meta name="viewport" content="<?php include WWW_ROOT."admin/common/include/viewport.inc" ?>">
<title>HOMEPAGE MANAGER</title>

<link rel="stylesheet" href="/admin/common/css/normalize.css">
<!-- <link rel="stylesheet" href="/admin/common/css/jquery.fancybox.css"> -->
<link rel="stylesheet" href="/admin/common/css/font.css">
<link rel="stylesheet" href="/admin/common/css/common.css">
<link rel="stylesheet" href="/admin/common/css/jquery.mCustomScrollbar.min.css">
<link rel="stylesheet" href="/admin/common/css/jquery-ui-1.9.2.custom/css/smoothness/jquery-ui-1.9.2.custom.min.css">
<link rel="stylesheet" href="/admin/common/css/colorbox.css">
<style type="text/css">
    .scrollbar{
    height:500px;
    overflow:hidden;
    padding:10px;
}
</style>
<script src="/admin/common/js/jquery.js"></script>
<script src="/admin/common/js/jquery-ui-1.9.2.custom.min.js"></script>
<!-- <script src="/admin/common/js/jquery.fancybox.pack.js"></script> -->
<script src="/admin/common/js/base.js"></script>
<!-- <script src="/admin/common/js/fancy.js"></script> -->
<script src="/admin/common/js/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="/admin/common/js/jquery.colorbox-min.js"></script>
<script src="/admin/common/js/colorbox.js"></script>

<!--[if lt IE 9]>
<script src='/common/js/html5shiv.js'></script>
<![endif]-->

<?php echo $this->fetch('beforeHeaderClose');?>
</head>

<body>
<?php echo $this->fetch('afterBodyStart');?>

<div id="container">
<?php echo $this->element('header');?>
<?php echo $this->element('side');?>

<?php echo $this->fetch('beforeContentStart');?>

  <div id="content">

<?php echo $this->fetch('content'); ?>

<?php include WWW_ROOT."admin/common/include/footer.inc" ?>
  </div>
<?php echo $this->fetch('afterContentClose');?>
</div>




<?php echo $this->fetch('beforeBodyClose');?>
</body>
<script type="text/javascript">
$(function(){
    $(".scrollbar").mCustomScrollbar({
        scrollInertia: 0,
        mouseWheelPixels: 50
    });
    var re = document.getElementById('clock');
    var item = function() {
        var items = new Date();
        h = ('0' + items.getHours()).slice(-2);
        m = ('0' + items.getMinutes()).slice(-2);
        s = ('0' + items.getSeconds()).slice(-2);
        re.innerHTML = h+':'+m;
        setTimeout(item,100);
    }
    setTimeout(item,100);
});
</script>
</html>
