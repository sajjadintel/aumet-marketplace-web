<div class="footer bg-white py-4 d-flex flex-lg-column" id="kt_footer">
    <!--begin::Container-->
    <div class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between">
        <!--begin::Copyright-->
        <div class="text-dark order-2 order-md-1">
            <span class="text-muted font-weight-bold mr-2"><?php echo str_replace("{Y}", date('Y'), $vFooterText); ?></span>
        </div>
        <!--end::Copyright-->
        <!--begin::Nav-->
        <div class="nav nav-dark">
            <?php if (getenv('ENV') == Constants::ENV_LOC) { ?>
                <a href="https://business.aumet.me/about-us" target="_blank" class="nav-link pl-0 pr-5"><?php echo $vFooterLinks_about ?></a>
                <a href="https://blog.aumet.me/" target="_blank" class="nav-link pl-0 pr-5"><?php echo $vFooterLinks_blog ?></a>
                <a href="https://business.aumet.me/terms/conditions" target="_blank" class="nav-link pl-0 pr-0"><?php echo $vFooterLinks_terms ?></a>
            <?php } ?>
        </div>
        <!--end::Nav-->
    </div>
    <!--end::Container-->
</div>