<script type="text/javascript">
    item = {};
    item ["item_name"] = "<?php echo $item->name; ?>";
    item ["item_id"] = "<?php echo $item->entityProductId; ?>";
    item ["price"] = "<?php echo $item->unitPrice; ?>";
    item ["item_brand"] = "<?php echo $seller->name ?>";
    item ["index"] = Object.keys(productItemListGTM).length+1;
    item ["quantity"] = "<?php echo $item->quantity ?>";
    item ["currency"] = "<?php echo $currencySymbol; ?>";
    item ["availability"] = "<?php echo $item->stockStatusName_en; ?>";
    item ["made_in"] = "<?php echo $item->madeInCountryName_en ?>";
    item ["seller_id"] = "<?php echo $seller->sellerId ?>";
    productItemListGTM.push(item);
</script>

    <input type="hidden" class="hidden_item_name" value="<?php echo $item->name; ?>">
    <input type="hidden" class="hidden_item_id" value="<?php echo $item->entityProductId; ?>">
    <input type="hidden" class="hidden_price" value="<?php echo $item->unitPrice; ?>">
    <input type="hidden" class="hidden_brand_name" value="<?php echo $seller->name; ?>">
    <input type="hidden" class="hidden_quantity" value="<?php echo $item->quantity; ?>">
    <input type="hidden" class="hidden_made_in" value="<?php echo $item->madeInCountryName_en; ?>">
    <input type="hidden" class="hidden_availability" value="<?php echo $item->stockStatusName_en; ?>">
    <input type="hidden" class="hidden_currency" value="<?php echo $currencySymbol; ?>">
    <input type="hidden" class="hidden_seller_id" value="<?php echo $seller->sellerId; ?>">