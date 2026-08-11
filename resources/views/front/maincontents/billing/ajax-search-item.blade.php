<?php if($products){ foreach($products as $product){?>
	<li onclick="searchProductCartAdd(<?=$product['id']?>, 1);" style="cursor: pointer;">
        <span style="font-weight: bold;"><?=e($product['name'])?><?php if(!empty($product['brand'])){?> (<?=e($product['brand'])?>)<?php }?> | SKU: <?=e($product['sku'])?><?php if(!empty($product['barcode'])){?> | Barcode: <?=e($product['barcode'])?><?php }?><?php if(!empty($product['supplier_sku'])){?> | Supplier SKU: <?=e($product['supplier_sku'])?><?php }?> @ $<?=number_format($product['price'],2)?></span>
        <!-- <a href="javascript:void(0);" class="btn btn-info btn-sm" style="float: right;" onclick="searchProductCartAdd(<?=$product['id']?>, 1);"><i class="fa fa-shopping-cart"></i></a> -->
        <hr>
    </li>
<?php } } else {?>
    <li>
        <span style="font-weight: bold; color: red;">No products found</span>
        <hr>
    </li>
<?php }?>
