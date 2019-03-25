<div class="form-group {{ $errors->has($field->getFieldName()) ? 'has-error' : '' }}">
	@if(!empty($field->getLabel()))
		{{ Form::label($field->getFieldName(), $field->getLabel()) }}
	@endif
	<input type="date" name="{{ $field->getFieldName() }}" class="form-control"  value="">

	@include('_partials.form_error',['field' => $field->getFieldName()])
</div>
