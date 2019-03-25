<div class="form-group {{ $errors->has($field->getFieldName()) ? 'has-error' : '' }}">
	@if(!empty($field->getLabel()))
		{{ Form::label($field->getFieldName(), $field->getLabel()) }}
	@endif
	{{ Form::file($field->getFieldName(), $field->getAttributes()) }}

	@include('_partials.form_error',['field' => $field->getFieldName()])
</div>
