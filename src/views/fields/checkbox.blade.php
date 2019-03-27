<div class="form-group {{ $errors->has($field->getFieldName()) ? 'has-error' : '' }}">
	@if(!empty($field->getLabel()))
		{!! Form::label($field->getFieldName(), $field->getLabel()) !!}
	@endif
	<div class="btn-group colour-toggle" data-toggle="buttons">
        <label class="btn btn-danger">
            <input type="checkbox" name="{{ $field->getFieldName() }}" value="1" autocomplete="off">
            <span>No</span>
        </label>
    </div>

	@include('actions::fields.form_error',['field' => $field->getFieldName()])
</div>
