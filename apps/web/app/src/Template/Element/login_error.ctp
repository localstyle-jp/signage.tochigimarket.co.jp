<?php if ($this->Common->session_check('Flash.flash.0.message')): ?>

    <?= $this->Flash->render('flash', ['element' => 'Flash/login_error']); ?>

<?php endif; ?>