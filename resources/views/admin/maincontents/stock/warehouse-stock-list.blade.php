<?php
use App\Helpers\Helper;
$controllerRoute      = $module['controller_route'];
$current_url          = url()->current();
?>
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light"><a href="<?=url('admin/dashboard')?>">Dashboard</a> /</span> <?=$page_header?>
  </h4>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">
            <input type="text" class="form-control" placeholder="Search by product name, SKU, barcode, brand, supplier, size" id="myInput">
          </h5>
          <div class="dt-responsive table-responsive">
            <table class="table table-bordered nowrap">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Name</th>
                  <th scope="col">SKU</th>
                  <th scope="col">Barcode</th>
                  <th scope="col">Brand</th>
                  <th scope="col">Supplier</th>
                  <th scope="col">Size</th>
                  <th scope="col">Stock Qty</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              <tbody id="item-list">
                <?php if(count($rows)>0){ $sl=1; foreach($rows as $row){?>
                  <tr class="productList" id="product-row-<?=$row->id?>">
                    <th scope="row"><?=$sl++?></th>
                    <td><?=$row->name?></td>
                    <td><?=$row->sku?></td>
                    <td><?=$row->barcode?></td>
                    <td><?=$row->brand_name?></td>
                    <td><?=$row->supplier_name?></td>
                    <td><?=$row->size_name?> <?=$row->unit_name?></td>
                    <td><span id="stock-<?=$row->id?>"><?=$row->warehouse_stock?></span></td>
                    <td>
                      <a href="javascript:void(0);" class="btn btn-success btn-sm" onclick="openStockINModal(<?=$row->id?>, '<?=$row->name?>', '<?=$row->sku?>');"><i class="fa fa-arrow-up"></i>&nbsp;IN</a>
                      <a href="javascript:void(0);" class="btn btn-danger btn-sm" onclick="openStockOUTModal(<?=$row->id?>, '<?=$row->name?>', '<?=$row->sku?>');"><i class="fa fa-arrow-down"></i>&nbsp;OUT</a>
                      <a href="<?=url('admin/stock/warehouse-stock-history/' . Helper::encoded($row->id))?>" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-history"></i>&nbsp;HISTORY</a>
                    </td>
                  </tr>
                <?php } }?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="open-stock-in-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
  
</div>
<div class="modal fade" id="open-stock-out-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
  
</div>
<script type="text/javascript">
  $(document).ready(function() {
    $("#myInput").on("input", function() {
      var value = $(this).val().toLowerCase();
      //alert(value);
      $("#item-list .productList").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });
  });
  function openStockINModal(productID, productName, productSKU){
    var modalHTML = '';
    modalHTML = `<div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="xCloseModalLabel">Stock IN : ${productName} (${productSKU})</h5>
                        <!-- X Close Button -->
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                      </div>
                      <div class="modal-body">
                        <form method="POST" action="" id="stockINForm">
                          @csrf
                          <input type="hidden" class="form-control" name="product_id" id="product_id" value="${productID}" required>
                          <input type="hidden" class="form-control" name="txn_type" id="txn_type" value="IN" required>
                          <input type="hidden" class="form-control" name="key" id="key" value="db9effa4e748d16e72654ef8bac2a947be6e2222" required>
                          <div class="row">
                            <div class="col-md-6 mb-3">
                              <label for="stock_date">Stock Date</label>
                              <input type="date" class="form-control" name="stock_date" id="stock_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                              <label for="txn_qty">Stock Qty</label>
                              <input type="number" class="form-control" name="txn_qty" id="txn_qty" min="1" required>
                            </div>
                            <div class="col-md-12 mb-3">
                              <label for="note">Note</label>
                              <textarea class="form-control" name="note" id="note"></textarea>
                            </div>
                            <div class="col-md-4">&nbsp;</div>
                            <div class="col-md-4">
                              <button type="submit" class="btn btn-success btn-sm">SUBMIT</button>
                            </div>
                            <div class="col-md-4">&nbsp;</div>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>`;
    $('#open-stock-in-modal').html(modalHTML).modal('show');


    // Attach AJAX submit handler
    $('#stockINForm').on('submit', function(e) {
      e.preventDefault();

      let formData = $(this).serialize();
      var url = '<?=url('/')?>';
      $.ajax({
        url: url + '/admin/stock/manage-warehouse-stock', // 👈 Change this to your actual Laravel route URL
        type: 'POST',
        data: formData,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          if(response.status){
            // Handle success (e.g. show toast, reload data, close modal)
            toastAlert("success", response.message);
            $('#open-stock-in-modal').modal('hide');
            $('#stock-' + productID).empty();
            $('#stock-' + productID).text(response.data.closing_qty);

            // Highlight the row
            let rowID = '#product-row-' + productID; // assuming product_id is returned
            $(rowID).css('background-color', '#e5d63745'); // light green

            // Optional: Remove highlight after 2 seconds
            setTimeout(function() {
              $(rowID).css('background-color', '');
            }, 3000);
          } else {
            toastAlert("error", response.message);
          }
        },
        error: function(xhr) {
          // Handle error (e.g. show validation errors)
          toastAlert("error", 'Error occurred. Please try again.');
        }
      });
    });
  }
  function openStockOUTModal(productID, productName, productSKU){
    var modalHTML = '';
    modalHTML = `<div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="xCloseModalLabel">Stock OUT : ${productName} (${productSKU})</h5>
                        <!-- X Close Button -->
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                      </div>
                      <div class="modal-body">
                        <form method="POST" action="" id="stockOUTForm">
                          @csrf
                          <input type="hidden" class="form-control" name="product_id" id="product_id" value="${productID}" required>
                          <input type="hidden" class="form-control" name="txn_type" id="txn_type" value="OUT" required>
                          <input type="hidden" class="form-control" name="key" id="key" value="db9effa4e748d16e72654ef8bac2a947be6e2222" required>
                          <div class="row">
                            <div class="col-md-6 mb-3">
                              <label for="stock_date">Stock Date</label>
                              <input type="date" class="form-control" name="stock_date" id="stock_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                              <label for="txn_qty">Stock Qty</label>
                              <input type="number" class="form-control" name="txn_qty" id="txn_qty" min="1" required>
                            </div>
                            <div class="col-md-12 mb-3">
                              <label for="note">Note</label>
                              <textarea class="form-control" name="note" id="note"></textarea>
                            </div>
                            <div class="col-md-4">&nbsp;</div>
                            <div class="col-md-4">
                              <button type="submit" class="btn btn-success btn-sm">SUBMIT</button>
                            </div>
                            <div class="col-md-4">&nbsp;</div>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>`;
    $('#open-stock-out-modal').html(modalHTML).modal('show');


    // Attach AJAX submit handler
    $('#stockOUTForm').on('submit', function(e) {
      e.preventDefault();

      let formData = $(this).serialize();
      var url = '<?=url('/')?>';
      $.ajax({
        url: url + '/admin/stock/manage-warehouse-stock', // 👈 Change this to your actual Laravel route URL
        type: 'POST',
        data: formData,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          if(response.status){
            // Handle success (e.g. show toast, reload data, close modal)
            toastAlert("success", response.message);
            $('#open-stock-out-modal').modal('hide');
            $('#stock-' + productID).empty();
            $('#stock-' + productID).text(response.data.closing_qty);

            // Highlight the row
            let rowID = '#product-row-' + productID; // assuming product_id is returned
            $(rowID).css('background-color', '#e5d63745'); // light green

            // Optional: Remove highlight after 2 seconds
            setTimeout(function() {
              $(rowID).css('background-color', '');
            }, 3000);
          } else {
            toastAlert("error", response.message);
            // Highlight the row
            let rowID = '#product-row-' + productID; // assuming product_id is returned
            $(rowID).css('background-color', '#ff000029'); // light green

            // Optional: Remove highlight after 2 seconds
            setTimeout(function() {
              $(rowID).css('background-color', '');
            }, 3000);
          }
        },
        error: function(xhr) {
          // Handle error (e.g. show validation errors)
          toastAlert("error", 'Error occurred. Please try again.');
        }
      });
    });
  }
</script>