@if(Config::get('app.debug') && !$action->hasAllDefaults())
	<div class="alert alert-warning">
		<p>Some default values are missing for this action:</p>
		<ul>
			@foreach($action->getRequiredDefaultFields() as $d)
				<li>{{ $d }}: {!! isset($action->getDefaultData()[$d]) ? $action->getDefaultData()[$d] : '<span class="text-danger">NOT FOUND!</span>' !!}</li>
			@endforeach
		</ul>
	</div>
@endif
