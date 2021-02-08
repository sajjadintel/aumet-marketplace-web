<?php

\Middleware::instance()->before('GET|POST /web/distributor/*', function(\Base $f3, $params, $alias) {
    // if not distributor
    if (!Helper::isDistributor($f3->get('SESSION.objUser')->roleId)) {
        $f3->set('SESSION.notAuthorized',1,20);
        $f3->reroute('/web');
    }
});

\Middleware::instance()->before('GET|POST /web/pharmacy/*', function(\Base $f3, $params, $alias) {

   // if not pharmacy
    if (!Helper::isPharmacy($f3->get('SESSION.objUser')->roleId)) {
        $f3->set('SESSION.notAuthorized',1,20);
        $f3->reroute('/web');
    }
});

\Middleware::instance()->before('GET|POST /web/cart/*', function(\Base $f3, $params, $alias) {

    // if not pharmacy
    if (!Helper::isPharmacy($f3->get('SESSION.objUser')->roleId)) {
        $f3->set('SESSION.notAuthorized',1,20);
        $f3->reroute('/web');
    }
});

\Middleware::instance()->run();
