<div class="clearfix"></div>
<div class="tab-pane {if $post_result['tab'] == 'admin'} active {/if} " id="mocean_user">
{if $post_result['tab'] == 'admin' && isset($post_result['error'])} 
	<div class="bootstrap">
		<div class="alert alert-danger">
		  {$post_result['error']}
		</div>
	</div>
{/if}
{if $post_result['tab'] == 'admin' && isset($post_result['success'])} 
	<div class="bootstrap">
		<div class="alert alert-success">
		 {$post_result['success']}
		</div>
	</div>
{/if}
{$moceanAdmin}
</div>
