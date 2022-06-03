<?php
$class = 'message';
if (!empty($params['class'])) {
    $class .= ' ' . $params['class'];
}
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<p class="<?= h($class) ?> error" onclick="this.classList.add('hidden');"><?= $message ?></p>
