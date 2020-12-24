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
    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>
<!--begin::Container-->
<div class="container">
    <div class="d-flex flex-row">
        <div class="flex-row-fluid">

            <div class="card card-custom" style="margin-bottom: 20px;">
            <?php if(count($allCartItems) > 0): ?>
            <?php foreach ($allSellers as $seller) : ?>

                <div class="card-header flex-wrap border-0 pt-6 pb-0">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label font-weight-bolder font-size-h3 text-primary"><?php echo $seller->name ?></span>
                    </h3>
                </div>

                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table">

                            <thead>
                                <tr>
                                    <th width="50%"><?php echo $vModule_product_name ?></th>
                                    <th class="text-center" width="20%"><?php echo $vModule_cart_quantity ?></th>
                                    <th class="text-center" width="20%"><?php echo $vModule_cart_note ?></th>
                                    <th class="text-right" width="10%"><?php echo $vModule_cart_unitPrice ?></th>
                                    <th class="text-right" width="10%"><?php echo $vModule_cart_tax ?></th>
                                    <th class="text-right" width="10%"><?php echo $vModule_cart_productOrderPrice ?></th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                    $subTotalPrice = 0;
                                    $currency = $mapSellerIdCurrency[$seller->sellerId];
                                    $currencySymbol = $currency->symbol;
                                    $currencyId = $currency->id;
                                ?>
                                <?php foreach ($allCartItems[$seller->sellerId] as $item) : ?>
                                    <tr>
                                        <td class="d-flex align-items-center font-weight-bolder font-size-h5">
                                            <div class="symbol symbol-60 flex-shrink-0 mr-4 bg-light">
                                                <div class="symbol-label" style="background-image: url('<?php echo $item->image ?>')"></div>
                                            </div>
                                            <div>
                                                <a href="javascript:;" onclick="WebApp.loadSubPage('/web/entity/<?php echo $item->entityId ?>/product/<?php echo $item->productId ?>')" class="text-dark text-hover-primary"><?php echo $item->name ?></a>
                                                <?php if($item->quantityFree > 0) : ?>
                                                    <p id="quantityFreeHolder-<?php echo $item->productId ?>" class="text-danger">Free <?php echo $item->name ?> x<span id="quantityFree-<?php echo $item->productId ?>"><?php echo $item->quantityFree ?></span></p>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <a onclick="CartCheckout.updateQuantity(<?php echo $item->productId ?>, -1, <?php echo $item->stock?>, <?php echo $item->id ?>, <?php echo $seller->sellerId ?>, updateTotalPrice)" class="btn btn-xs btn-light-success btn-icon mr-2">
                                                <i class="ki ki-minus icon-xs"></i>
                                            </a>
                                            <input style="width: 40%;" type="number" id="quantity-<?php echo $item->productId ?>" onfocusout="CartCheckout.updateQuantity(<?php echo $item->productId ?>, 0, <?php echo $item->stock?>, <?php echo $item->id ?>, <?php echo $seller->sellerId ?>, updateTotalPrice)" class="mr-2 font-weight-bolder quantity" min="0" max="<?php echo $item->stock ?>" value="<?php echo $item->quantity ?>" name="quantity">
                                            <a onclick="CartCheckout.updateQuantity(<?php echo $item->productId ?>, 1, <?php echo $item->stock ?>, <?php echo $item->id ?>, <?php echo $seller->sellerId ?>, updateTotalPrice)" class="btn btn-xs btn-light-success btn-icon">
                                                <i class="ki ki-plus icon-xs"></i>
                                            </a>
                                        </td>

                                        <td class="text-center align-middle">
                                                <input style="width: 100%;" type="text" id="note-<?php echo $item->productId ?>" onfocusout="CartCheckout.updateNote(<?php echo $item->productId ?>, <?php echo $item->id ?>, <?php echo $seller->sellerId ?>)" class="mr-2 font-weight-bolder quantity" value="<?php echo $item->note ?>" name="note">
                                        </td>

                                        <td class="text-right align-middle font-weight-bolder font-size-h5"><?php echo $item->unitPrice . " " . $currencySymbol  ?></td>
                                        <td class="text-right align-middle font-weight-bolder font-size-h5 productVat-<?php echo $seller->sellerId ?>" data-currency="<?php echo $currencySymbol ?>" id="productVat-<?php echo $item->productId ?>"><?php echo $item->vat . "%" ?></td>
                                        <td class="text-right align-middle font-weight-bolder font-size-h5 productPrice-<?php echo $seller->sellerId ?>" data-currency="<?php echo $currencySymbol ?>" data-vat="<?php echo $item->vat ?>" data-unitPrice="<?php echo $item->unitPrice ?>" data-productPrice="<?php echo $item->quantity * $item->unitPrice ?>" id="productPrice-<?php echo $item->productId ?>"><?php echo $item->quantity * $item->unitPrice . " " . $currencySymbol ?></td>
                                        <td class="text-right align-middle">
                                            <a href="javascript:;" onclick="CartCheckout.removeItemModal(<?php echo $item->id ?>)" class="btn btn-sm btn-light btn-text-danger btn-hover-primary btn-icon  mr-2" title="">
                                                <span class="svg-icon svg-icon-md">
                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24" />
                                                            <path d="M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z" fill="#000000" fill-rule="nonzero" />
                                                            <path d="M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3" />
                                                        </g>
                                                    </svg>
                                                </span>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                        $productPrice = $item->quantity  * $item->unitPrice;
                                        $productTax = ($item->vat / 100)  * $productPrice;

                                        $subTotalPrice += $productPrice;
                                        $tax += $productTax;
                                        $totalPrice += ($productTax  + $productPrice);
                                    ?>
                                <?php endforeach; ?>
                                <tr>
                                    <td colspan="5"></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="text-right pr-10">
                            <p class="font-weight-bolder font-size-h4">
                                <span class="text-primary"><?php echo $vModule_cart_subTotal ?></span>
                                <span class="subTotalPrice" data-subTotalPrice="<?php echo $subTotalPrice ?>" data-currencyId="<?php echo $currencyId ?>" id="subTotalPrice-<?php echo $seller->sellerId ?>"><?php echo $subTotalPrice . " " . $currencySymbol ?></span>
                            </p>
                            <p class="font-weight-bolder font-size-h4">
                                <span class="text-primary"><?php echo $vModule_cart_tax ?></span>
                                <span class="tax" data-vat="<?php echo $tax ?>" data-currencyId="<?php echo $currencyId ?>" id="tax-<?php echo $seller->sellerId ?>"><?php echo $tax . " " . $currencySymbol ?></span>
                            </p>
                            <p class="font-weight-bolder font-size-h4">
                                <span class="text-primary"><?php echo $vModule_cart_total ?></span>
                                <span class="totalPrice" data-totalPrice="<?php echo $totalPrice ?>" data-currencyId="<?php echo $currencyId ?>" id="totalPrice-<?php echo $seller->sellerId ?>"><?php echo $totalPrice . " " . $currencySymbol ?></span>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
                

                <div class="text-right p-10">
                    <p class="font-weight-bolder font-size-h4"> <span class="text-primary"><?php echo $vModule_cart_formula ?> </span> <span id="formula"></span></p>
                    <p class="font-weight-bolder font-size-h4"> <span class="text-primary"><?php echo $vModule_cart_grandSubTotal ?> </span> <span id="grandSubTotal"></span></p>
                    <p class="font-weight-bolder font-size-h4"> <span class="text-primary"><?php echo $vModule_cart_tax ?> </span> <span id="tax"></span></p>
                    <p class="font-weight-bolder font-size-h4"> <span class="text-primary"><?php echo $vModule_cart_grandTotal ?> </span> <span id="grandTotal"></span></p>
                    <div class="radio-inline py-10" style="justify-content: flex-end;">
                        <label class="mr-5 radio radio-success"><?php echo $vModule_cartCheckout_paymentMethodTitle ?></label>
                        <?php foreach($allPaymentMethods as $paymentMethod) : ?>
                            <label class="radio radio-success">
                                <?php if($paymentMethod->id == 1) : ?>
                                    <input type="radio" name="paymentMethod" id="paymentMethod-<?php echo $paymentMethod->id; ?>" checked/>
                                <?php else: ?>
                                    <input type="radio" name="paymentMethod" id="paymentMethod-<?php echo $paymentMethod->id; ?>"/>
                                <?php endif; ?>
                                <span></span>
                                <?php echo $paymentMethod->name; ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <a class="btn btn-success font-weight-bolder px-8" onclick="CartCheckout.submitOrderModal()">
                        <?php echo $vModule_cartCheckout_submitOrder ?>
                    </a>
                    <p class="border-0 text-muted text-right pt-10"><?php echo $vModule_cart_term ?></td>
                </div>
            <?php else: ?>
                <div class="text-center p-10">
                    <p class="font-weight-bolder font-size-h3"><?php echo $vModule_cartCheckout_empty ?></p>

                    <a href="javascript:;" onclick="WebApp.loadPage('/web/product/search')" class="btn btn-sm font-weight-bolder btn-primary">
                        Browse Products
                    </a>

                </div>
            <?php endif; ?>
            </div>
        </div>

    </div>
</div>
<!--end::Container-->
<script>
    SearchDataTable.init();
</script>
<?php ob_end_flush(); ?>
<script>
    $(document).ready(function() {
        updateTotalPrice();
    });

    function updateTotalPrice() {
        let mapCurrencyIdCurrencyStr = '<?php echo json_encode($mapCurrencyIdCurrency); ?>'; 
        let mapCurrencyIdCurrency = JSON.parse(mapCurrencyIdCurrencyStr);

        let buyerCurrencyStr = '<?php echo json_encode($buyerCurrency); ?>';
        let buyerCurrency = JSON.parse(buyerCurrencyStr);
        let symbol = buyerCurrency.symbol;

        let allSubPrices = {};
        $(".subTotalPrice").each(function(index, element) {
            let currencyId = parseInt($(element).attr("data-currencyId"));
            let subTotalPrice = parseFloat($(element).attr("data-subTotalPrice"));
            if(allSubPrices[currencyId]) {
                allSubPrices[currencyId] += subTotalPrice;
            } else {
                allSubPrices[currencyId] = subTotalPrice;
            }
        });
        
        let allTax = {};
        $(".tax").each(function(index, element) {
            let currencyId = parseInt($(element).attr("data-currencyId"));
            let tax = parseFloat($(element).attr("data-vat"));
            if(allTax[currencyId]) {
                allTax[currencyId] += tax;
            } else {
                allTax[currencyId] = tax;
            }
        });

        let allPrices = {};
        $(".totalPrice").each(function(index, element) {
            let currencyId = parseInt($(element).attr("data-currencyId"));
            let totalPrice = parseFloat($(element).attr("data-totalPrice"));
            if(allPrices[currencyId]) {
                allPrices[currencyId] += totalPrice;
            } else {
                allPrices[currencyId] = totalPrice;
            }
        });

        let grandSubTotalUSD = 0;
        let grandTaxUSD = 0;
        let grandTotalUSD = 0;
        let allFormula = [];
        Object.keys(mapCurrencyIdCurrency).forEach((currencyId) => {
            let currency = mapCurrencyIdCurrency[currencyId];
            
            let subTotalPrice = allSubPrices[currencyId];
            if(subTotalPrice && subTotalPrice > 0) {
                let subTotalPriceStr = subTotalPrice.toFixed(2);
                subTotalPrice = parseFloat(subTotalPriceStr);
                grandSubTotalUSD += subTotalPrice * currency.conversionToUSD;
            }

            let tax = allTax[currencyId];
            if(tax && tax > 0) {
                let taxStr = tax.toFixed(2);
                tax = parseFloat(taxStr);
                grandTaxUSD += tax * currency.conversionToUSD;
            }
            
            let price = allPrices[currencyId];
            if(price && price > 0) {
                let priceStr = price.toFixed(2);
                allFormula.push(priceStr + " " + currency.symbol);
                price = parseFloat(priceStr);
                grandTotalUSD += price * currency.conversionToUSD;
            }
        });

        let formula = allFormula.join(" + ");
        $("#formula").html(formula);
        
        let grandSubTotal = grandSubTotalUSD / buyerCurrency.conversionToUSD;
        let grandSubTotalStr = grandSubTotal.toFixed(2);
        $("#grandSubTotal").html(grandSubTotalStr + " " + buyerCurrency.symbol);
        
        let grandTax = grandTaxUSD / buyerCurrency.conversionToUSD;
        let grandTaxStr = grandTax.toFixed(2);
        $("#tax").html(grandTaxStr + " " + buyerCurrency.symbol);

        let grandTotal = grandTotalUSD / buyerCurrency.conversionToUSD;
        let grandTotalStr = grandTotal.toFixed(2);
        $("#grandTotal").html(grandTotalStr + " " + buyerCurrency.symbol);

        return grandTotalStr;
    }
</script>