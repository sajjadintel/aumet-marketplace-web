<script>
    item = {}
    item ["item_name"] = "<?php echo $product->name; ?>";
    item ["item_id"] = "<?php echo $product->id; ?>";
    item ["price"] = "<?php echo $product->priceInt; ?>";
    item ["item_category"] = "<?php echo $product->category; ?>";
    item ["item_list_name"] = "Product list";
    item ["item_list_id"] = "<?php echo $productListName; ?>";
    item ["index"] = Object.keys(productItemListGTM).length+1;
    //item ["quantity"] = "<?php //echo $product->stock; ?>//";
    item ["currency"] = "<?php echo $product->currency; ?>";
    item ["availability"] = "<?php echo $product->inStock; ?>";
    item ["made_in"] = "<?php echo $product->madeInCountry; ?>";
    item ["manufacturer_id"] = "<?php echo $product->manufacturerName; ?>";
    productItemListGTM.push(item);
</script>
<input type="hidden" class="hidden_item_name" value="<?php echo $product->name; ?>">
<input type="hidden" class="hidden_item_id" value="<?php echo $product->id; ?>">
<input type="hidden" class="hidden_price" value="<?php echo $product->priceInt; ?>">
<input type="hidden" class="hidden_item_category" value="<?php echo $product->category; ?>">
<input type="hidden" class="hidden_item_list_id" value="<?php echo $productListName; ?>">
<input type="hidden" class="hidden_availability" value="<?php echo $product->inStock; ?>">
<input type="hidden" class="hidden_made_in" value="<?php echo $product->madeInCountry; ?>">
<input type="hidden" class="hidden_manufacturer_id" value="<?php echo $product->manufacturerName; ?>">