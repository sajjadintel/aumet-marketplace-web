<?php
ob_start("compress_htmlcode");
function compress_htmlcode($codedata)
{
    $searchdata = array(
        '/\>[^\S ]+/s', // remove whitespaces after tags
        '/[^\S ]+\</s', // remove whitespaces before tags
        '/(\s)+/s' // remove multiple whitespace sequences
    );
    $replacedata = array('>', '<', '\\1');
    $codedata = preg_replace($searchdata, $replacedata, $codedata);
    return $codedata;
}

?>
<!--begin::Container-->
<div class="container-fluid">
    <div class="card card-custom gutter-b mt-20">
        <div class="card-body">
        <?php foreach ($notifications['subset'] as $notification): ?>
            <div class="card mb-2" onclick="markAsReadAndRedirect(<?php echo $notification->id; ?>, '/web/distributor/order/pending')" style="cursor: pointer">
                <div class="card-body <?php echo !$notification->read ?: 'bg-secondary'; ?>">
                    <h3 class="d-flex align-items-center">
                        <?php if(!$notification->read) : ?>
                            <span class="label label-danger mx-2" id="unread" style="height: 0.5rem; width: 0.5rem"></span>
                        <?php endif; ?>
                        <?php echo $notification->title; ?>
                    </h3>
                    <div><?php echo $notification->body; ?></div>
                    <small><?php echo (new DateTime($notification->created_at))->format('d/m/Y H:i:s') ?></small>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
</div>
<?php ob_end_flush(); ?>

<script>
    function markAsReadAndRedirect(notificationId, redirectUrl) {
        $.ajax({
            url: `/web/distributor/notification/${notificationId}/read`,
            method: 'POST',

        });
        WebApp.loadPage(redirectUrl);
    }
</script>
