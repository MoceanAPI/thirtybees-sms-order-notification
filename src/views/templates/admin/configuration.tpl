{*
* 2007-2016- PrestaShop
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2016- PrestaShop SA
* @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
* International Registered Trademark & Property of PrestaShop SA
*}
<script type="text/javascript">
	$('document').ready(function() {
		$('.list-group a').on('click', function(){
$('.list-group a').each(function(index){
	$( this ).removeClass('active');
});
$( this ).addClass('active');
		});
	})
</script>
<!-- Module content -->
    <div id="modulecontent" class="clearfix">
        <!-- Nav tabs -->
		<p style="width: 16.66667%;text-align: center;">{if $balance->status > 0 } <font style="color:red">API is not connected</font> {else} <b>Balance: </b> {$balance->value} {/if}</p>
        <div class="col-lg-2">
            <div class="list-group">
                <a href="#mocean_config" class="list-group-item {if $post_result['tab'] == 'config'} active {/if}" data-toggle="tab"><i class="icon-cog"></i> {l s='Mocean Config' mod='moceanapinotify'}</a>
                <a href="#mocean_user" class="list-group-item {if $post_result['tab'] == 'admin'} active {/if}" data-toggle="tab"><i class="icon-user"></i> {l s='Admin Notification' mod='moceanapinotify'}</a>
                <a href="#mocean_customer" class="list-group-item {if $post_result['tab'] == 'users'} active {/if}" data-toggle="tab"><i class="icon-group"></i> {l s='Users Notification' mod='moceanapinotify'}</a>
            </div>
			<div class="list-group">
                <a style="cursor:pointer" data-toggle="modal" data-target="#special_tags" class="list-group-item"><i class="icon-info"></i> {l s='Special Chars' mod='moceanapinotify'}</a>
            </div>
        </div>
        <!-- Tab panes -->
        <div class="tab-content col-lg-10">
            {include file="./tabs/config.tpl"}
            {include file="./tabs/admin.tpl"}
            {include file="./tabs/users.tpl"}
        </div>
    </div>
	
	<!-- Modal Special Chars -->
	<div id="special_tags" class="modal fade" role="dialog">
	  <div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title"><?=MOCEANSMS_SPECIAL_TAGS?></h4>
		  </div>
		  <div class="modal-body">
<h1>Shop</h1>
<pre>
[shop_name]
[shop_email]
[shop_url]
</pre>
<h1>Orders</h1>
<pre>
[order_id]
[order_amount]
[order_status]
[order_product]
</pre>
<h1>Biling</h1>
<pre>
[payment_method]
[billing_first_name]
[billing_last_name]
[billing_phone]
[billing_email]
[billing_company]
[billing_address]
[billing_country]
[billing_city]
[billing_state]
[billing_postcode]
</pre>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>

	  </div>
	</div>