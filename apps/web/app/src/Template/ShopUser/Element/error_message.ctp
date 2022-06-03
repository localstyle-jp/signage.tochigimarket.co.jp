<?php if ($error_messages || $this->Common->session_check('Flash.flash.0.message')): ?>
    <?= $this->Flash->render(); ?>
<?php endif; ?>