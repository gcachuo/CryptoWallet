<?php
$notificaciones = System::curl(["url" => "user/notifications"]);
$iconos = [
    "error" => [
        "icon" => "warning",
        "color" => "warn"
    ],
    "alert" => [
        "icon" => "notifications_active",
        "color" => "info"
    ],
    "activity_up" => [
        "icon" => "arrow_upward",
        'color' => 'success'
    ],
    "activity_down" => [
        "icon" => "arrow_downward",
        'color' => 'danger'
    ],
]
?>

<div class='card'>
    <div class='card-body padding'>
        <ul class='list-group'>
            <?php if (empty($notificaciones)): ?>
                <li class='list-group-item'>
                    No hay notificaciones
                </li>
            <?php endif; ?>
            <?php foreach ($notificaciones as $notificacion): ?>
                <li class='list-group-item'>
                    <span class='material-icons text-<?= $iconos[$notificacion['type']]['color'] ?>'>
                        <?= $iconos[$notificacion['type']]['icon'] ?>
                    </span>
                    <span class="font-weight-bold" style="margin-right: 10px">
                        <?= date('d/M/Y h:ia', strtotime($notificacion['timestamp'])) ?>
                    </span>
                    <span>
                        <?= $notificacion['message'] ?>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
