@extends('layouts.page')

@section('page_content')
<div class="row">
	<div class="col-sm-6">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">
					{{ $page_title }}
				</h3>
			</div>
			<div class="panel-body">
				{!! $action->render() !!}
			</div>
		</div>
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
@endsection
