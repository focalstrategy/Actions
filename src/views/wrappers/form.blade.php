@if($action->authorise(Auth::user()))
<div class="action_wrap">
	@include('actions::wrappers.missing_defaults')

	@if($action->isForm())
		@if(isset($action_data))
			{!! Form::model($action_data,[
				'class' => 'form',
				'url' => $action->getAction(),
				'method' => $action->getMethod(),
				'data-on-action-response' => 'onActionComplete'
			] + $action->getFormAttributes()) !!}
		@else
			{!! Form::open(['url' => $action->getAction(),'class' => 'form',
				'method' => $action->getMethod(),
				'data-on-action-response' => 'onActionComplete'
			] + $action->getFormAttributes()) !!}
		@endif
	@endif

	@if(count($action->getFields()))
		@foreach($action->getFields() as $field)
			{!! $field->render() !!}
		@endforeach
	@endif

	@if(count($action->getButtons()))
		@foreach($action->getButtons() as $button)
			{!! $button->render() !!}
		@endforeach
	@endif

	@if($action->isForm())
		{!! Form::close() !!}
	@endif
</div>
@endif
