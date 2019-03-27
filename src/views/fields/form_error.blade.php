@if($errors->has($field))
	<span class="text-danger">{{ $errors->first($field) }}</span>
@else
	<span class="text-danger"></span>
@endif
