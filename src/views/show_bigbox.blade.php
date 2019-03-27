<div class="row">
	<div class="{{ $action->getBoxClasses()}}">
		{!! $action->render() !!}
	</div>
	@if (!empty($action->getAdditionalDataUrl()))
	<div class="col-sm-6 dashboardify">
		<div class="df-replace" data-url="{{ $action->getAdditionalDataUrl() }}">
			<br />
			<div class="text-center">
				<span class="fa fa-cog fa-spin fa-2x"></span> Loading
			</div>
		</div>
	</div>
	@endif
</div>
