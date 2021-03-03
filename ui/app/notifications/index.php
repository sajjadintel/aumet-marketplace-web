<?php foreach ($f3->get('notifications') as $notification): ?>
    <div class="<?php echo $notification->read ?: 'bg-gray'; ?>">
        <h3><?php echo $notification->title; ?></h3>
        <div><?php echo $notification->body; ?></div>
    </div>
<?php endforeach; ?>