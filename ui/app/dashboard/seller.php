<!--begin::Entry-->
<div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Dashboard-->
        <div class="row">

            <div class="col-md-3">
                <div class="card card-custom bg-primary card-stretch gutter-b">
                    <div class="card-body my-4">
                        <a href="#" class="card-title font-weight-bolder text-white font-size-h6 mb-4 text-hover-state-dark d-block">Orders</a>
                        <div class="font-weight-bold text-white font-size-sm">
                            <span class="font-size-h2 mr-2">
                                <?php if ($dashboard_order > $dashboard_orderYesterday) {
                                    echo '<i class="la la-rocket la-lg text-white"></i> ';
                                } elseif ($dashboard_order < $dashboard_orderYesterday) {
                                    echo '<i class="la la-arrow-down la-lg text-white"></i> ';
                                } else {
                                    echo '<i class="la la-equals la-lg text-white"></i> ';
                                }
                                echo $dashboard_order; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-3">
                <div class="card card-custom bg-primary card-stretch gutter-b">
                    <div class="card-body my-4">
                        <a href="#" class="card-title font-weight-bolder text-white font-size-h6 mb-4 text-hover-state-dark d-block">Revenue</a>
                        <div class="font-weight-bold text-white font-size-sm">
                            <span class="font-size-h2 mr-2">
                                <?php if ($dashboard_revenue > $dashboard_revenueYesterday) {
                                    echo '<i class="la la-rocket la-lg text-white"></i> ';
                                } elseif ($dashboard_revenue < $dashboard_revenueYesterday) {
                                    echo '<i class="la la-arrow-down la-lg text-white"></i> ';
                                } else {
                                    echo '<i class="la la-equals la-lg text-white"></i> ';
                                }
                                echo 'AED ' . $dashboard_revenue; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-custom bg-primary card-stretch gutter-b">
                    <div class="card-body my-4">
                        <a href="#" class="card-title font-weight-bolder text-white font-size-h6 mb-4 text-hover-state-dark d-block">Customers</a>
                        <div class="font-weight-bold text-white font-size-sm">
                            <span class="font-size-h2 mr-2">
                                <?php if ($dashboard_customer > $dashboard_customerYesterday) {
                                    echo '<i class="la la-rocket la-lg text-white"></i> ';
                                } elseif ($dashboard_customer < $dashboard_customerYesterday) {
                                    echo '<i class="la la-arrow-down la-lg text-white"></i> ';
                                } else {
                                    echo '<i class="la la-equals la-lg text-white"></i> ';
                                }
                                echo $dashboard_customer; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-custom bg-primary card-stretch gutter-b">
                    <div class="card-body my-4">
                        <a href="#" class="card-title font-weight-bolder text-white font-size-h6 mb-4 text-hover-state-dark d-block">New Customers</a>
                        <div class="font-weight-bold text-white font-size-sm">
                            <span class="font-size-h2 mr-2">
                                <?php if ($dashboard_new_customer > $dashboard_new_customerYesterday) {
                                    echo '<i class="la la-rocket la-lg text-white"></i> ';
                                } elseif ($dashboard_new_customer < $dashboard_new_customerYesterday) {
                                    echo '<i class="la la-arrow-down la-lg text-white"></i> ';
                                } else {
                                    echo '<i class="la la-equals la-lg text-white"></i> ';
                                }
                                echo $dashboard_new_customer; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!--begin::Row-->
        <div class="row">

            <div class="col-6">
                <div class="card card-custom card-stretch gutter-b">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">Recent Orders</span>
                            <span class="text-muted mt-3 font-weight-bold font-size-sm">New Unprocessed Orders</span>
                        </h3>
                    </div>
                    <div class="card-body pt-2 pb-2 mt-n3">
                        <div class="datatable datatable-bordered datatable-head-custom" id="kt_datatable_orders"></div>
                    </div>

                </div>
            </div>

            <div class="col-6">
                <div class="card card-custom card-stretch gutter-b">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">Best Selling Products</span>
                            <span class="text-muted mt-3 font-weight-bold font-size-sm">Top 5 most selling products</span>
                        </h3>
                    </div>
                    <div class="card-body pt-2 pb-2 mt-n3">
                        <div class="datatable datatable-bordered datatable-head-custom" id="kt_datatable_products"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    DistributorDashboardDataTable.init();
</script>