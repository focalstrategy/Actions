<div class="form-group {{ $errors->has($field->getFieldName()) ? 'has-error' : '' }}">
	{{ Form::label($field->getFieldName(), $field->getLabel()) }}
	{{ Form::select($field->getFieldName(),$field->getOptions(), null, ['class' => 'form-control enable-select2-multi','multiple' => 'multiple']) }}

	@include('_partials.form_error',['field' => $field->getFieldName()])
</div>
<script nonce="{{ Csp::getNonce() }}">
	$('select.enable-select2-multi').each(function() {
		var placeholder = $(this).attr('placeholder') || $(this).data('placeholder');

		$(this).select2({width: '100%', placeholder:placeholder, multiple: true});
	});
</script>
