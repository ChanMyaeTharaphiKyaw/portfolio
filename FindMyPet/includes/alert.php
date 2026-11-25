<?php

if (isset($_SESSION['form_message'])) {
    $msg  = $_SESSION['form_message'];
    $type = $_SESSION['form_message_type'] ?? 'info';

    $icons = [
        'success' => 'ri-checkbox-circle-line',
        'error'   => 'ri-close-circle-line',
        'warning' => 'ri-alert-line',
        'info'    => 'ri-information-line'
    ];
    $icon = $icons[$type] ?? $icons['info'];

    
    unset($_SESSION['form_message']);
    unset($_SESSION['form_message_type']);
?>

    <div class="form-alert alert-<?= htmlspecialchars($type) ?>">
        <i class="<?= htmlspecialchars($icon) ?>"></i>
        <span><?= htmlspecialchars($msg) ?></span>
        <button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>
    </div>

<?php
}
?>