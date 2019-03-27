<div class="form-group {{ $errors->has($field->getFieldName()) ? 'has-error' : '' }}">
	@if(!empty($field->getLabel()))
		{!! Form::label($field->getFieldName(), $field->getLabel()) !!}
	@endif
	{!! Form::textArea($field->getFieldName(),null, $field->getAttributes()) !!}

	@include('actions::fields.form_error',['field' => $field->getFieldName()])
</div>
