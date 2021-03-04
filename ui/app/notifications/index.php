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
                <div class="<?php echo $notification->read ?: 'bg-secondary'; ?>">
                    <h3><?php echo $notification->title; ?></h3>
                    <div><?php echo $notification->body; ?></div>
                    <a href="/web/distributors/"
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php ob_end_flush(); ?>