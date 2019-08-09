<?php echo $header; ?>
<?php if ($error_warning): ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php endif; ?>
<div class="box">
   <div class="heading">
      <h1 style="background-image: url('view/image/payment.png');">
         <?php echo $heading_title; ?>
      </h1>
      <div class="buttons">
         <a onclick="$('#form').submit();" class="button">
         <span><?php echo $button_save; ?></span>
         </a>
         <a onclick="location = '<?php echo $cancel; ?>';" class="button">
         <span><?php echo $button_cancel; ?></span>
         </a>
      </div>
   </div>
   <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
         <table class="form">
            <tr>
               <td width="25%"><?php echo $status_label; ?></td>
               <td>
                  <select name="coincorner_status">
                     <?php if ($coincorner_status): ?>
                     <option value="1" selected="selected"><?php echo $text_test_mode_on; ?></option>
                     <option value="0"><?php echo $text_test_mode_off; ?></option>
                     <?php else: ?>
                     <option value="1"><?php echo $text_test_mode_on; ?></option>
                     <option value="0" selected="selected"><?php echo $text_test_mode_off; ?></option>
                     <?php endif; ?>
                  </select>
               </td>
            </tr>
            <tr>
               <td>
                  <span class="required">*</span> <?php echo $api_auth_token_label; ?>
               </td>
               <td>
                  <input style="width: 15%;" type="text" name="payment_coincorner_api_auth_public" value="<?php echo $payment_coincorner_api_auth_public; ?>"/>
                  <span>
                  Your <a href="https://www.coincorner.com/" target="_blank">coincorner</a> v2 API (public) key. You can copy this from the API settings page under <a href="http://www.coincorner.com" target="_blank">Merchant Services &gt; API</a>
                  </span>
               </td>
            </tr>
            <tr>
               <td>
                  <span class="required">*</span> <?php echo $api_auth_token_private_label; ?>
               </td>
               <td>
                  <input style="width: 15%;" type="text" name="payment_coincorner_api_auth_private" value="<?php echo $payment_coincorner_api_auth_private; ?>"/>
                  <span>
                  Your <a href="https://www.coincorner.com" target="_blank">coincorner.com</a> API secret key. You can copy this from the API settings page under <a href="http://www.coincorner.com" target="_blank">Merchant Services &gt; API</a> 
                  </span>                     
               </td>
            </tr>
            <tr>
               <td>
                  <span class="required">*</span> <?php echo $entry_api_user_id_label; ?>
               </td>
               <td>
                  <input type="text" name="payment_coincorner_api_user_id" value="<?php echo $payment_coincorner_api_user_id; ?>"/>
                  <span>
                  Your <a href="https://www.coincorner.com" target="_blank">coincorner.com</a> Account Id. You can copy this from the API settings page under <a href="http://www.coincorner.com" target="_blank">Merchant Services &gt; API</a>
                  </span>          
               </td>
            </tr>
            <tr>
               <td><span class="required">*</span>  <?php echo $entry_api_invoice_currency; ?></td>
               <td>
                <?php if (!$payment_coincorner_invoice_currency === NULL): ?>
                    <input type="text" name="payment_coincorner_invoice_currency" value="<?php echo $payment_coincorner_invoice_currency ?>" id="input-invoice-currency" class="form-control" />
                     <?php else: ?>
                    <input type="text" name="payment_coincorner_invoice_currency" value="GBP" id="input-invoice-currency" class="form-control" />
                     <?php endif; ?>
                  <span>
                  The currency you want your invoices to be displayed in. Example: GBP
                  </span>          
               </td>
            </tr>
            <tr>
               <td><span class="required">*</span>  <?php echo $entry_api_settlement_currency; ?></td>
               <td>
                 <?php if (!$payment_coincorner_settlement_currency === NULL): ?>
                  <input type="text" name="payment_coincorner_settlement_currency" value="<?php echo $payment_coincorner_settlement_currency ?>" id="input-invoice-currency" class="form-control" />
                     <?php else: ?>
                  <input type="text" name="payment_coincorner_settlement_currency" value="GBP" id="input-invoice-currency" class="form-control" />
                     <?php endif; ?>
                  <span>
                  The currency you want your orders to be settled in on your CoinCorner Account. Example: GBP
                  </span>    
               </td>
            </tr>
            <tr>
               <td><?php echo $entry_order_status; ?></td>
               <td>
                  <select name="coincorner_new_order_status_id">
                     <?php foreach ($order_statuses as $order_status): ?>
                     <?php if ($order_status['order_status_id'] == $coincorner_new_order_status_id): ?>
                     <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected">
                        <?php echo $order_status['name']; ?>
                     </option>
                     <?php else: ?>
                     <option value="<?php echo $order_status['order_status_id']; ?>">
                        <?php echo $order_status['name']; ?>
                     </option>
                     <?php endif; ?>
                     <?php endforeach; ?>
                  </select>
               </td>
            </tr>
            <tr>
               <td><?php echo $entry_canceled_status; ?></td>
               <td>
                  <select name="coincorner_cancelled_order_status_id">
                     <?php foreach ($order_statuses as $order_status): ?>
                     <?php if ($order_status['order_status_id'] == $coincorner_cancelled_order_status_id): ?>
                     <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected">
                        <?php echo $order_status['name']; ?>
                     </option>
                     <?php else: ?>
                     <option value="<?php echo $order_status['order_status_id']; ?>">
                        <?php echo $order_status['name']; ?>
                     </option>
                     <?php endif; ?>
                     <?php endforeach; ?>
                  </select>
               </td>
            </tr>
            <tr>
               <td><?php echo $entry_expired_status; ?></td>
               <td>
                  <select name="coincorner_expired_order_status_id">
                     <?php foreach ($order_statuses as $order_status): ?>
                     <?php if ($order_status['order_status_id'] == $coincorner_expired_order_status_id): ?>
                     <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected">
                        <?php echo $order_status['name']; ?>
                     </option>
                     <?php else: ?>
                     <option value="<?php echo $order_status['order_status_id']; ?>">
                        <?php echo $order_status['name']; ?>
                     </option>
                     <?php endif; ?>
                     <?php endforeach; ?>
                  </select>
               </td>
            </tr>
            <tr>
               <td><?php echo $entry_failed_status; ?></td>
               <td>
                  <select name="coincorner_failed_order_status_id">
                     <?php foreach ($order_statuses as $order_status): ?>
                     <?php if ($order_status['order_status_id'] == $coincorner_failed_order_status_id): ?>
                     <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected">
                        <?php echo $order_status['name']; ?>
                     </option>
                     <?php else: ?>
                     <option value="<?php echo $order_status['order_status_id']; ?>">
                        <?php echo $order_status['name']; ?>
                     </option>
                     <?php endif; ?>
                     <?php endforeach; ?>
                  </select>
               </td>
            </tr>
            <tr>
               <td><?php echo $entry_paid_status; ?></td>
               <td>
                  <select name="coincorner_completed_order_status_id">
                     <?php foreach ($order_statuses as $order_status): ?>
                     <?php if ($order_status['order_status_id'] == $coincorner_completed_order_status_id): ?>
                     <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected">
                        <?php echo $order_status['name']; ?>
                     </option>
                     <?php else: ?>
                     <option value="<?php echo $order_status['order_status_id']; ?>">
                        <?php echo $order_status['name']; ?>
                     </option>
                     <?php endif; ?>
                     <?php endforeach; ?>
                  </select>
               </td>
            </tr>
            <tr>
               <td><?php echo $entry_refunded_status; ?></td>
               <td>
                  <select name="coincorner_refunded_order_status_id">
                     <?php foreach ($order_statuses as $order_status): ?>
                     <?php if ($order_status['order_status_id'] == $coincorner_refunded_order_status_id): ?>
                     <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected">
                        <?php echo $order_status['name']; ?>
                     </option>
                     <?php else: ?>
                     <option value="<?php echo $order_status['order_status_id']; ?>">
                        <?php echo $order_status['name']; ?>
                     </option>
                     <?php endif; ?>
                     <?php endforeach; ?>
                  </select>
               </td>
            </tr>
         </table>
      </form>
   </div>
</div>
<form action="<?php echo $action; ?>" method="POST" id="refresh">
   <input type="text" name="refresh" value="1" style="display: none;"/>
</form>
<?php echo $footer; ?>