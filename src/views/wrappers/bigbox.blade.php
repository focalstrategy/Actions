@if($action->authorise(Auth::user()))
<div class="action_wrap">
	@include('actions::wrappers.missing_defaults')

	<a href="#" class="btn {{ $action->getButtonClass() }} bigbox-btn btn-block"
		data-title="{{ $action->getTitle() }}"
		data-content-route="{{ $action->getFormUrl(true) }}" hide-save-button="true">
		{{ $action->getBigBoxButtonText() }}
	</a>
</div>
@endif
