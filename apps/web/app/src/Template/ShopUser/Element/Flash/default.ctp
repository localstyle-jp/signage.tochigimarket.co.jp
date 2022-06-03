<?php
$class = 'message';
if (!empty($params['class'])) {
    $class .= ' ' . $params['class'];
}
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<div class="<?= h($class) ?>"" onclick="this.classList.add('hidden');">
    <div class="alert alert-info">
        <h5>
            <i class="fas fa-info"></i>お知らせ
        </h5>
        <div class="error">
            <?= $error_messages; ?>
            <div><?= $message ?></div>
        </div>
    </div>
</div>

