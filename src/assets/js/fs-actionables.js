;$(function() {
	$(document).on('submit','.action_inline form', function(e) {
		e.preventDefault();

		var name = $("button[clicked=true]").attr('name');
		var value = $("button[clicked=true]").val();

		var $form = $(this).is('form') ? $(this) : $(this).find('form');
		var fd = new FormData($form[0]);
		fd.append(name, value);

		$.ajax({
           type: "POST",
           url: $form.attr('action'),
           data: fd,
           cache: false,
	       contentType: false,
	       processData: false,
           success: function(data) {
           		if(data.success) {

	                if(data.notify) {
	                	if(typeof(toastr) != 'undefined') {
		               		toastr.success(data.notify);
	                	}
	                	else {
	                		console.log(data.notify);
	                	}
	                }
           		}
           		else if(data.error) {
           			$('.form-group').removeClass('has-errors');
           			$('.form-group .text-danger').text('');

           			if(data.errors) {
           				$.each(data.errors, function(key) {
           					var $input = $form.find('#'+key);
           					var $parent = $input.parents('.form-group');
           					var $errorBlock = $parent.find('.text-danger');

           					$parent.addClass('has-errors');
           					$errorBlock.text(data.errors[key].join(','));
           				});
           			}

	                if(data.notify) {
	                	if(typeof(toastr) != 'undefined') {
		               		toastr.error(data.notify);
	                	}
	                	else {
	                		console.error(data.notify);
	                	}
	                }
           		}

       			if(data.message) {
               		$('.message').text(data.message);
                }

           		if($form.data('on-action-response')) {
       		   		var fn = window[$form.data('on-action-response')];
					if(typeof fn === 'function') {
						fn(data, $form);
					}
       		   	}
           }
         });
	});

	$(".action_inline form button").click(function() {
        $(".action_inline form button", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });
});

function onActionComplete(data, $el) {
	if(typeof(data.redirect_to) != 'undefined') {
		if (data.redirect_to != '') {
			window.location = data.redirect_to;
		}
		else {
			window.location.reload();
		}
	}

	if(data.reload_datatable_page) {
		$el.parents('table').DataTable().ajax.reload(null, false);
	}

	if(data.highlight) {
		$el.parents('tr,.highlightable').addClass(data.highlight);
	}

	if(data.remove_on_response) {
		$el.parents('.action_wrap').remove();
	}

	if(data.remove_parent) {
		$el.parents('tr,.removable').fadeOut('fast').remove();
	}

	if(data.replace_with) {
		$el.parents('.action_wrap').replaceWith(data.replace_with);
	}

	if(data.js_callback) {
		var fn = window[data.js_callback];
		if(typeof fn === 'function') {
			fn(data, $el);
		}
	}
}