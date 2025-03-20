<?php if($products){ foreach($products as $product){?>
	<li>
        <span style="font-weight: bold;"><?=$product['name']?> (SKU : <?=$product['sku']?>) @ $<?=number_format($product['price'],2)?></span>
        <a href="javascript:void(0);" class="btn btn-info btn-sm" style="float: right;" onclick="searchProductCartAdd(<?=$product['id']?>, 1);"><i class="fa fa-shopping-cart"></i></a>
        <hr>
    </li>
<?php } } else {?>
    <li>
        <span style="font-weight: bold; color: red;">No products found</span>
        <hr>
    </li>
<?php }?>