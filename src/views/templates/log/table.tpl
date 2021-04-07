<br/>
<br/>
<style type="text/css">
	.nobootstrap {
		    min-width: inherit !important;
	}
</style>
<div class="bootstrap">
	<a class="pull-right btn btn-warning" href="?controller=AdminModules&token={$token}&configure=moceanapinotify&tab_module=advertising_marketing&module_name=moceanapinotify"><i class="icon-cog"> Configuration</i></a >
</div>
<br/>
<br/>
<br/>
{if count($log_history) > 0 }
<div class="table-responsive-lg clearfix">
	<table style="width: 100%;" class="table">
	  <thead>
		<tr>
		  <th class="text-center">{l s='Sender' mod='moceanapinotify'}</th>
		  <th class="text-center">{l s='Date' mod='moceanapinotify'}</th>
		  <th class="text-center">{l s='Message' mod='moceanapinotify'}</th>
		  <th class="text-center">{l s='Recipient' mod='moceanapinotify'}</th>
		  <th class="text-center">{l s='Response' mod='moceanapinotify'}</th>
		  <th class="text-center">{l s='Status' mod='moceanapinotify'}</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$log_history item=log}
		<tr>
		  <td class="text-center">{$log.sender|escape:'htmlall':'UTF-8'}</td>
		  <td class="text-center">{$log.date|escape:'htmlall':'UTF-8'}</td>
		  <td class="text-center">{$log.message|escape:'htmlall':'UTF-8'}</td>
		  <td class="text-center">{$log.recipient|escape:'htmlall':'UTF-8'}</td>
		  <td class="text-center">
<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#log_response_{$log.id}">View Response</button>
	<!-- Modal -->
	<div id="log_response_{$log.id}" class="modal fade" role="dialog">
	  <div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">View Response</h4>
		  </div>
		  <div class="modal-body">
			<p>{$log.response}</p>
			<script>
			</script>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>

	  </div>
	</div>
</td>
		  <td class="text-center">{($log.status > 0) ? '<div style="background:#f33e3e;color:#fff">fail</div>':'<div style="background:#77d83b;color:#fff" >Success</div>'}</td>
		</tr>
		{/foreach}
	</tbody>
	</table>
	{$pagerContainer}
</div>
{else}
<div class="bootstrap">
	<div class="alert alert-warning">
	  <strong>Warning!</strong> No records available.
	</div>
</div>
{/if}
