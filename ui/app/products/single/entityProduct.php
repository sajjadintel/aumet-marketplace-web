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
<style>
    .slick-prev:before,
    .slick-next:before {
        color: #13B9A9;
    }
</style>
<!--begin::Container-->
<div class="container">
    <div class="d-flex flex-row">

        <div class="flex-row-fluid">


            <div class="card card-custom gutter-b">
                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label font-weight-bolder text-primary"></span>
                        <span class="text-muted mt-3 font-weight-bold font-size-sm"></span>
                    </h3>
                </div>

                <div class="card-body d-flex rounded flex-column flex-md-row flex-lg-column flex-xxl-row">
                    <div class="row w-100 product-detail">

                        <div class="col-xxl-6 col-xl-6 col-lg-5 col-md-5 col-sm-12 col-xs-12 col-12">
                            <img src="<?php echo $objEntityProduct->image ?>">
                            <?php if (count($arrSubimage) > 0) : ?>
                                <div class="p-5">
                                    <div id="autoplayContainer" class="autoplay gallery" style="height: 100px;">
                                        <?php foreach ($arrSubimage as $subimageObj) : ?>
                                            <div class="px-5 col-4 image-input image-input-empty image-input-outline">
                                                <a href="/<?php echo $subimageObj->subimage; ?>" class="image-input-wrapper" style="display:block; width: 100%; height: 100px; background-size: 100% 100%; background-image: url('/<?php echo $subimageObj->subimage; ?>'); box-shadow: 0 0.25rem 0.75rem 0.25rem rgb(0 0 0 / 8%); cursor: pointer;">
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-xxl-6 col-xl-6 col-lg-7 col-md-7 col-sm-12 col-xs-12 col-12">
                            <div>
                                <h3 class=" font-size-h2 mb-1">
                                    <span class="font-weight-boldest"><?php echo $objEntityProduct->scientificName ?></span>
                                    <span class="font-weight-normal"><?php echo $objEntityProduct->productName ?></span>
                                </h3>
                            </div>


                            <div class="product-price-wrapper">

                                <div>
                                    <span class="distributor-name"><?php echo $objEntityProduct->entityName ?></span>
                                </div>

                                <hr>

                                <div>
                                    <div>

                                    </div>
                                    <div>

                                    </div>
                                    <div id="product-actions" class="cart-buttons-product-detail">

                                        <div class="price">
                                            <div class="title"><?php echo $vModule_product_pricePerUnit ?></div>
                                            <div class="value">
                                                <?php echo $objEntityProduct->unitPrice . ' ' . $objEntityProduct->currency ?>
                                            </div>
                                        </div>

                                        <?php if ($objEntityProduct->bonusTypeId == 2 && $objEntityProduct->bonusOptions != null) { ?>
                                            <div class="bonus">
                                                <div class="title"><?php echo $vModule_cart_bonus ?></div>
                                                <div class="value"><?php echo $objEntityProduct->bonusOptions[0]->name ?></div>
                                            </div>
                                        <?php } ?>


                                        <?php if (Helper::isPharmacy($_SESSION['objUser']->roleId) && $objEntityProduct->stockStatusId == 1) { ?>
                                            <div class="add-to-cart-wrapper">
                                                <input id="quantity-<?php echo $objEntityProduct->id ?>" type="number" min="1" value="1" />
                                                <div id="addToCart" onclick="addToCart()"><?php echo $vModule_product_addToCart ?></div>
                                            </div>
                                        <?php } ?>

                                    </div>
                                </div>

                            </div>

                            <div class="product-item-details">

                                <h3>Details</h3>

                                <div class="d-flex mb-3">
                                    <span class="title"><?php echo $vModule_product_madeIn ?></span>
                                    <div class="country">
                                        <img src="/assets/img/countries/<?php echo strtolower(Helper::getCountryIso($objEntityProduct->madeInCountryName_en)) ?>.svg">
                                        <span class="value"><?php echo $objEntityProduct->madeInCountryName ?></span>
                                    </div>
                                </div>
                                <div class="d-flex mb-3">
                                    <span class="title"><?php echo $vModule_product_scientificName ?></span>
                                    <span class="value"><?php echo $objEntityProduct->scientificName ?></span>
                                </div>
                                <div class="d-flex mb-3">
                                    <span class="title"><?php echo $vModule_product_stockStatus ?></span>
                                    <span class="value"><?php echo $objEntityProduct->{"stockStatusName_" . $_SESSION['userLang']} ?></span>
                                </div>
                                <div class="d-flex mb-3">
                                    <span class="title"><?php echo $vModule_product_stockStatusDate ?></span>
                                    <span class="value"><?php echo (new DateTime($objEntityProduct->stockUpdateDateTime))->format('d/m/Y'); ?></span>
                                </div>

                            </div>

                        </div>

                        <div class="product-description">
                            <?php echo $objEntityProduct->description ?>
                        </div>

                    </div>

                </div>
            </div>


            <?php if ($arrProductOtherOffers != null && sizeof($arrProductOtherOffers) != 0) { ?>
                <div class="card card-custom product-item">
                    <div class="card-header border-0 py-5 product-item-similar-header" style="margin: 10px 0 0 0;">
                        <h3>
                            <?php echo $vModule_product_allOffers ?>
                            <?php echo $objEntityProduct->productName ?>
                        </h3>
                    </div>

                    <div class="card-body">

                        <table id="datatableLocal" class="compact hover order-column row-border table datatable datatable-bordered datatable-head-custom text-left">
                        </table>

                    </div>

                </div>
            <?php } ?>

            <?php if ($arrProductFromThisDistributor != null && sizeof($arrProductFromThisDistributor) != 0) { ?>
                <div>
                    <div class="product-item-similar-header">
                        <h3><?php echo $vModule_product_otherFromSameDistributor ?></h3>

                        <a href="javascript:;" onclick="viewAllSameDistributor()">View All</a>
                    </div>

                    <div class="row">
                        <?php foreach ($arrProductFromThisDistributor as $objItem) : ?>
                            <div class="col-xxl-2 col-xl-2 col-lg-3 col-md-4 col-sm-6 col-xs-6 col-6" style="overflow: hidden">
                                <a href="javascript:;" onclick="WebApp.loadSubPage('/web/entity/<?php echo $objItem->entityId ?>/product/<?php echo $objItem->id ?>')" class="text-dark text-hover-primary">
                                    <div class="card card-custom product-item">

                                        <div class="image-wrapper">
                                            <div class="image">
                                                <img src="<?php echo $objItem->image ?>" style="">
                                            </div>
                                            <div class="image-hover-wrapper">
                                                <div class="view-button">View</div>
                                            </div>
                                        </div>

                                        <div class="product-item-content">
                                            <span class="title"><?php echo Helper::truncateText($objItem->productName, 45) ?></span>
                                            <span class="price"><?php echo $objItem->unitPrice . ' ' . $objEntityProduct->currency ?></span>
                                        </div>

                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php } ?>

            <?php if ($arrRelatedEntityProduct != null && sizeof($arrRelatedEntityProduct) != 0) { ?>
                <div>
                    <div class="product-item-similar-header">
                        <h3 class="products-item-title">
                            <?php echo $vModule_product_other ?>
                            <?php echo $objEntityProduct->scientificName ?>
                        </h3>

                        <a href="javascript:;" onclick="viewAllSameScientificName()">View All</a>
                    </div>


                    <div class="row">
                        <?php foreach ($arrRelatedEntityProduct as $objItem) : ?>
                            <div class="col-xxl-2 col-xl-2 col-lg-3 col-md-4 col-sm-6 col-xs-6 col-6" style="overflow: hidden">
                                <a href="javascript:;" onclick="WebApp.loadSubPage('/web/entity/<?php echo $objItem->entityId ?>/product/<?php echo $objItem->id ?>')" class="text-dark text-hover-primary">
                                    <div class="card card-custom product-item">

                                        <div class="image-wrapper">
                                            <div class="image">
                                                <img src="<?php echo $objItem->image ?>" style="">
                                            </div>
                                            <div class="image-hover-wrapper">
                                                <div class="view-button">View</div>
                                            </div>
                                        </div>

                                        <div class="product-item-content">
                                            <span class="title"><?php echo Helper::truncateText($objItem->productName, 45) ?></span>
                                            <span class="price"><?php echo $objItem->unitPrice . ' ' . $objEntityProduct->currency ?></span>
                                        </div>

                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php } ?>


        </div>

    </div>
</div>

<script>
    var row = <?= json_encode(['id' => $objEntityProduct->id, 'entityId' => $objEntityProduct->entityId, 'productId' => $objEntityProduct->productId, 'quantity' => 1]); ?>;

    function addToCart() {
        SearchDataTable.onClickAddMoreToCart(row);
    }

    function viewAllSameDistributor() {
        <?php if (Helper::isPharmacy($_SESSION['objUser']->roleId)) { ?>
            WebApp.loadPage('/web/product/search?distributorId=<?php echo $objEntityProduct->entityId ?>');
        <?php } else { ?>
            WebApp.loadPage('/web/distributor/product');
        <?php } ?>
    }

    function viewAllSameScientificName() {
        <?php if (Helper::isPharmacy($_SESSION['objUser']->roleId)) { ?>
            WebApp.loadPage('/web/product/search?scientificNameId=<?php echo $objEntityProduct->scientificNameId ?>');
        <?php } else { ?>
            WebApp.loadPage('/web/distributor/product?scientificNameId=<?php echo $objEntityProduct->scientificNameId ?>');
        <?php } ?>
    }

    function closeImageModal() {
        $("#imageModal").modal('hide');
    }

    $('.gallery').each(function() {
        $(this).magnificPopup({
            delegate: 'a',
            type: 'image',
            gallery: {
                enabled: true
            }
        });
    });
</script>
<script>
    var PageClass = function() {
        var elementId = "#datatableLocal";

        var columnDefs = [{
                targets: 0,
                title: WebAppLocals.getMessage('sellingEntityName'),
                data: 'productName',
                render: function(data, type, row, meta) {
                    console.log(row);
                    var output = row['productName'];
                    return output;
                },
            },
            {
                targets: 1,
                title: WebAppLocals.getMessage('stockAvailability'),
                data: 'stockStatusId',
                orderable: false,
                render: function(data, type, row, meta) {
                    var status = {
                        1: {
                            title: WebAppLocals.getMessage('stockAvailability_available'),
                            class: ' label-primary',
                        },
                        2: {
                            title: WebAppLocals.getMessage('stockAvailability_notAvailable'),
                            class: ' label-danger',
                        },
                        3: {
                            title: WebAppLocals.getMessage('stockAvailability_availableSoon'),
                            class: ' label-warning',
                        },
                    };

                    var output = '';

                    output +=
                        '<div><span class="label label-lg font-weight-bold ' +
                        status[row.stockStatusId].class +
                        ' label-inline">' +
                        status[row.stockStatusId].title +
                        '</span></div>';

                    return output;
                },
            },
            {
                targets: 2,
                title: WebAppLocals.getMessage('unitPrice'),
                data: 'unitPrice',
                render: function(data, type, row, meta) {
                    var output = WebApp.formatMoney(row.unitPrice) + ' ' + row.currency;

                    return '<div style="width: max-content;">' + output + '</div>';
                },
            },
            /*  {
                  targets: 3,
                  title: WebAppLocals.getMessage('bonus'),
                  data: 'bonusTypeId',
                  render: function (data, type, row, meta) {
                      let output = "";
                      if (row.bonusTypeId === 2 && row.bonuses != null) {
                          console.log(row.bonuses );
                          let btnText = row.activeBonus ? row.activeBonus.minOrder + " / +" + row.activeBonus.bonus : "Select";
                          let allBonuses = row.bonuses.filter((bonus) => !row.activeBonus || row.activeBonus.id !== bonus.id);
                          let btnShowBonuses =
                              '<a style="width: max-content;" href="javascript:;" onclick=\'SearchDataTable.productAddBonusModal(' + row.id + ', ' + row.entityId + ', ' + JSON.stringify(allBonuses) + ')\'\
                          class="btn btn-sm btn-default btn-text-primary btn-hover-primary mr-2 mb-2" title="View Bonuses">\
                          <span>' + btnText + '</span></a>';
                          output += btnShowBonuses;
                      }

                      return output;
                  },
              },*/
            {
                targets: 3,
                title: WebAppLocals.getMessage('quantity'),
                data: 'id',
                orderable: false,
                render: function(data, type, row, meta) {
                    console.log(row);
                    var vQuantity = '';
                    var output = '';
                    var rowQuantity = (row.cart > 0) ? row.cart : 0;
                    /*if (row.quantity) {
                        rowQuantity = row.quantity
                    }*/
                    if (row.stockStatusId == 1) {

                        let vMinusBtn = '<a class="btn btn-xs btn-light-success btn-icon mr-2 subQty"> <i class="ki ki-minus icon-xs"></i></a>';

                        output += vMinusBtn;

                        let vQuantity =
                            '<input class="qtyBox" id="quantity-' + row.id + '" type="number" min="0" style="width: 65px; direction: ltr; margin-right: 5px;" ' +
                            'value="' + rowQuantity + '" onkeypress="return event.charCode >= 48 && event.charCode <= 57" ' +
                            'onchange=\'SearchDataTable.updateQty(' + JSON.stringify(row) + ' , <?PHP echo $objUser->id; ?>)\' />';

                        output += vQuantity;

                        let vPlusBtn = '<a class="btn btn-xs btn-light-success btn-icon mr-2 addQty"> <i class="ki ki-plus icon-xs"></i></a>';

                        output += vPlusBtn;

                        let vQuantityFree =
                            '<input class="quantityFreeInput" id="quantityFreeInput-' +
                            row.id +
                            '" required style="display: none;">\
                                <span id="quantityFreeHolder-' +
                            row.id +
                            '" class="quantityFreeHolder label label-lg font-weight-bold label-primary label-inline" style="margin-left: 5px;"></span>';
                        output += vQuantityFree;
                    }
                    return '<div style="display: flex;">' + output + '</div>';
                },
            },
            {
                targets: 4,
                title: '',
                data: 'id',
                orderable: false,
                render: function(data, type, row, meta) {

                    var btnAddToCart =
                        '<a style="display: flex;" href="javascript:;" ' + 'onclick="Cart.addItem(' + row.entityId + ',' + row.id + ',\'#quantity-' + row.id + '\',\'#quantityFreeInput-' + row.id + '\'' + ')"' + ' class="btn btn-sm btn-default btn-text-primary btn-hover-primary  mr-2 mb-2" title="Add to cart">\
                            <span class="svg-icon svg-icon-md">\
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">\
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">\
                                <rect x="0" y="0" width="24" height="24"/>\
                                <path d="M18.1446364,11.84388 L17.4471627,16.0287218 C17.4463569,16.0335568 17.4455155,16.0383857 17.4446387,16.0432083 C17.345843,16.5865846 16.8252597,16.9469884 16.2818833,16.8481927 L4.91303792,14.7811299 C4.53842737,14.7130189 4.23500006,14.4380834 4.13039941,14.0719812 L2.30560137,7.68518803 C2.28007524,7.59584656 2.26712532,7.50338343 2.26712532,7.4104669 C2.26712532,6.85818215 2.71484057,6.4104669 3.26712532,6.4104669 L16.9929851,6.4104669 L17.606173,3.78251876 C17.7307772,3.24850086 18.2068633,2.87071314 18.7552257,2.87071314 L20.8200821,2.87071314 C21.4717328,2.87071314 22,3.39898039 22,4.05063106 C22,4.70228173 21.4717328,5.23054898 20.8200821,5.23054898 L19.6915238,5.23054898 L18.1446364,11.84388 Z" fill="#000000" opacity="0.3"/>\
                                <path d="M6.5,21 C5.67157288,21 5,20.3284271 5,19.5 C5,18.6715729 5.67157288,18 6.5,18 C7.32842712,18 8,18.6715729 8,19.5 C8,20.3284271 7.32842712,21 6.5,21 Z M15.5,21 C14.6715729,21 14,20.3284271 14,19.5 C14,18.6715729 14.6715729,18 15.5,18 C16.3284271,18 17,18.6715729 17,19.5 C17,20.3284271 16.3284271,21 15.5,21 Z" fill="#000000"/>\
                            </g></svg></span></a>';

                    var btnViewProduct =
                        '<a href="javascript:;" onclick="WebApp.loadSubPage(\'/web/entity/' +
                        row.entityId +
                        '/product/' +
                        row.id +
                        '\')" class="btn btn-sm btn-default btn-text-primary btn-hover-primary btn-icon mr-2 mb-2" title="View">\
                            <span class="svg-icon svg-icon-md">\
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">\
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">\
                                <rect x="0" y="0" width="24" height="24"/>\
                                <path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>\
                                <path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" fill="#000000" fill-rule="nonzero"/>\
                                <path d="M10.5,10.5 L10.5,9.5 C10.5,9.22385763 10.7238576,9 11,9 C11.2761424,9 11.5,9.22385763 11.5,9.5 L11.5,10.5 L12.5,10.5 C12.7761424,10.5 13,10.7238576 13,11 C13,11.2761424 12.7761424,11.5 12.5,11.5 L11.5,11.5 L11.5,12.5 C11.5,12.7761424 11.2761424,13 11,13 C10.7238576,13 10.5,12.7761424 10.5,12.5 L10.5,11.5 L9.5,11.5 C9.22385763,11.5 9,11.2761424 9,11 C9,10.7238576 9.22385763,10.5 9.5,10.5 L10.5,10.5 Z" fill="#000000" opacity="0.3"/>\
                                </g></svg></span></a>';

                    var outActions = '';

                    switch (row.stockStatusId) {
                        case 1:
                            /*outActions += btnViewProduct;
                            outActions += btnAddToCart;*/
                            outActions += outActions;
                            SearchDataTable.changeProductQuantityCallback(row);
                            break;
                        case 2:
                            outActions += btnViewProduct;
                            break;
                        case 3:
                            outActions += btnViewProduct;
                            break;
                    }
                    return '<div style="display: flex;">' + outActions + '</div>';
                },
            }
        ];

        var dbAdditionalOptions = {
            datatableOptions: {
                order: [
                    [0, 'desc']
                ],
                buttons: [],
                bPaginate: false,
                bLengthChange: false,
                bFilter: false,
                bInfo: false,
                bAutoWidth: false,
                bServerSide: false,
                processing: false,
                stateSave: true
            }
        };

        var initiate = function() {
            WebApp.CreateDatatableLocal("Product List", elementId, <?php echo json_encode($arrProductOtherOffers) ?>, columnDefs, dbAdditionalOptions);
        };


        return {
            init: function() {
                initiate();
            },
        };
    }();

    PageClass.init();
</script>
<?php ob_end_flush(); ?>
<?php include_once 'image-modal.php'; ?>
<script>
    $(document).ready(function() {
        $(document.body).on("click", '.addQty', function() {
            $(this).prev().val(+$(this).prev().val() + 1);
            $(this).prev().trigger("change");
        });
        $(document.body).on("click", '.subQty', function() {
            if ($(this).next().val() > 0) {
                $(this).next().val(+$(this).next().val() - 1);
                $(this).next().trigger("change");
            }
        });
        initAutoplay();
    })

    function initAutoplay() {
        var slidesToShow = 3;
        if ($("#autoplayContainer").children().length > slidesToShow) {
            $('.autoplay').slick({
                arrows: true,
                infinite: true,
                slidesToShow: slidesToShow,
                slidesToScroll: 1
            });
        }
    }
</script>