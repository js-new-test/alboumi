<?php
require('autoload.php');
global $lumise, $lumise_helper;

$data = $lumise->connector->get_session('lumise_cart');
$items = isset($data['items']) ? $data['items'] : array();
$fields = array(
    array('email', 'Billing E-Mail'),
    array('address', 'Street Address'),
    array('zip', 'Zip Code'),
    array('city', 'City'),
    array('country', 'Country')
);

$lumise_helper->process_cart();
$page_title = 'Shopping Cart';


include(theme('header.php'));
?>
        <div class="lumise-bread">
            <div class="container">
                <h1><?php echo $lumise->lang('Shopping Cart'); ?></h1>
            </div>
        </div>
        <div class="container">

            <div id="confirm">
                <?php
                $lumise_helper->show_sys_message();
                ?>
                <?php if(count($items) > 0):?>
                    <div class="span12">
                        <div class="wrap-table">
                            <table class="lumise-table sty2">
                                <thead>
                                    <tr>
                                        <th><?php echo $lumise->lang('Thumbnails'); ?></th>
                                        <th><?php echo $lumise->lang('Product Name'); ?></th>
                                        <th width="20%"><?php echo $lumise->lang('Attributes'); ?></th>
                                        <th width="10%"><?php echo $lumise->lang('Qty'); ?></th>
                                        <th width="10%"><?php echo $lumise->lang('Action'); ?></th>
                                        <th class="text-right" width="10%"><?php echo $lumise->lang('Subtotal'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $total = 0;
                                    $index = 0;
                                    foreach($items as $item):

                                        $cart_data = $lumise->lib->get_cart_item_file($item['file']);
                                        $meta = $lumise->lib->cart_meta($cart_data);
                                        $item = array_merge($item, $cart_data);

                                    ?>
                                    <tr>
                                        <td>
                                            <?php
                                            if(count($item['screenshots'])> 0):
                                                foreach($item['screenshots'] as $image):?>
                                                    <img width="150" src="<?php echo $lumise->cfg->upload_url.$image; ?>" />
                                                <?php endforeach;
                                            endif;
                                            ?>
                                        </td>
                                        <td><?php echo $item['product_name'];?></td>
                                        <td>
                                            <?php foreach($meta as $me): ?>
                                                <p>
                                                    <strong><?php echo $me['name']; ?></strong> : <?php echo $me['value']; ?>
                                                </p>
                                            <?php endforeach;?>
                                        </td>
                                        <td><?php echo $item['qty'];?></td>
                                        <td class="action">
                                            <?php if(false === $item['template']):?>
                                                <a href="<?php echo $lumise->cfg->tool_url;?>?product_base=<?php echo $item['product_id'];?>&cart=<?php echo $item['cart_id'];?>" class="edit"><?php echo $lumise->lang('Edit'); ?></a>
                                            <?php endif;?>
                                                <a data-cartid="<?php echo $item['cart_id'];?>" data-action="remove" href="<?php echo $lumise->cfg->url;?>cart.php?action=remove&item=<?php echo $item['cart_id'];?>" class="remove"><?php echo $lumise->lang('Remove'); ?></a>
                                        </td>
                                        <td class="text-right"><?php echo $lumise->lib->price($item['price']['total']);?>
                                            <?php $total += $item['price']['total'];?>
                                        </td>
                                    </tr>
                                    <?php
                                    $index++;
                                    endforeach;
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-right">
                                            <strong><?php echo $lumise->lang('Total'); ?></strong>
                                        </td>
                                        <td class="text-right"><?php echo $lumise->lib->price($total);?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="span12 last">

                        <div class="form-actions align-right">
                            <a href="<?php echo $lumise->cfg->url;?>checkout.php" class="btn btn-large btn-primary">
                                <?php echo $lumise->lang('Proceed To Checkout'); ?>
                            </a>
                        </div>
                    </div> <!-- .span8 -->
                <?php else:?>
                    <div class="span12">
                        <p><?php echo $lumise->lang('Your cart is currently empty.'); ?></p>
                    </div>
                    <div class="form-actions">
                        <a href="<?php echo $lumise->cfg->url;?>" class="btn btn-large btn-primary">
                            <?php echo $lumise->lang('Continue Shopping'); ?>
                        </a>
                    </div>
                <?php endif;?>
            </div>
        </div>
<script type="text/javascript">
(function($) {
    $('[data-action="remove"]').click(function(){
        var data = $(this).attr('data-cartid');

        var items = JSON.parse(localStorage.getItem('LUMISE-CART-DATA'));
        delete items[data];
        localStorage.setItem('LUMISE-CART-DATA', JSON.stringify(items));

    });
})(jQuery);
</script>
<?php
include(theme('footer.php'));
$lumise->connector->set_session('lumise_cart', $data);
