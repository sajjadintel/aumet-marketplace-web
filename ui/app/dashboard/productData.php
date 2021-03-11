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