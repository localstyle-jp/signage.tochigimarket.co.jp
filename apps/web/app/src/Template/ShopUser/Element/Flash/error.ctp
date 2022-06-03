<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<div class="message error" onclick="this.classList.add('hidden');">
    <div class="alert alert-danger">
        <h5>
            <i class="fas fa-ban"></i>警告
        </h5>
        <div class="error">
            <?= $error_messages; ?>
            <div><?= $message ?></div>
        </div>
    </div>
</div>
