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
	               		toastr.success(data.notify);
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
	               		toastr.error(data.notify);
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
;(function ( $ ) {

	$.fn.bigbox = function( command, args ) {
		var self = this;

		$('.bigbox-modal-bg').one('click', function(){
			self.close({currentTarget: $('.close-dlg')});
		});

		self.show = function() {
			var content_route = self.data('content-route');
			if(content_route != null) {
				self.find('.bigbox-body').html("").load(content_route,function() {
					Init.inits();
					self.find('.bigbox-hide-button').hide();

					var found_adjust = self.find('div.bigbox-sm');
					if(found_adjust.length > 0) {
						self.addClass('bigbox-sm');
						self.addClass('in');
						var form_height = self.find('.bigbox-header').height();
						form_height += self.find('.bigbox-body').height();
						form_height += self.find('.bigbox-footer').height();

						var window_height = $(window).height();

						if(form_height < window_height) {
							self.css('height', 'fit-content');
						}
					} else {
						self.addClass('in');
					}
					$('body').addClass('bigbox-open');
				});
			} else {
				self.addClass('in');
				$('body').addClass('bigbox-open');
			}

			$('[data-dismiss="modal"]',self).click(self.close);
			self.trigger('show.bs.modal',self);
		};

		self.close = function(e) {
			self.trigger('hide.bs.modal',{is_cancel: $(e.currentTarget).is('.close-dlg')});
			self.removeClass('in');
			$('body').removeClass('bigbox-open');
			self.removeClass('bigbox-sm');
			self.css('height', 'auto');

			$('[data-dismiss="modal"]',self).unbind('click');
		};

		var fn = self[command];
		if(typeof fn === 'function') {
			fn(args);
		}

		return self;
	}

	$.fn.bigboxBtn = function() {
		var self = this;

		self.on('click','.bigbox-btn',function(e) {
			e.preventDefault();

			var $btn = $(this);

			var $bigbox = $('.bigbox.ajax');
			$bigbox.find('.bigbox-btn-submit').show();

			if($bigbox.length <= 0)  {
				Logger.error("View: bigbox-ajax is missing");
			}

			$bigbox.data('calling_button',$btn);
			$bigbox.data('content-route',$btn.data('content-route'));
			$bigbox.find('.bigbox-title > span').text($btn.data('title'));

			if($btn.data('save-button-text')) {
				var $bigbox = $('.bigbox.ajax');

				$bigbox.find('.bigbox-btn-submit').html($btn.data('save-button-text'));
			}

			if($btn.data('hide-save-button')) {
				var $bigbox = $('.bigbox.ajax');

				$bigbox.find('.bigbox-btn-submit').hide();
			}

			$('.bigbox.ajax').bigbox('show');
		});

		self.on('keypress','.bigbox input',function(e) {
			if(e.keyCode == 13) {
				$('.bigbox-btn-submit').click();
				return false;
			}
		});

		self.on('click','.bigbox .bigbox-btn-submit',function(e) {
			e.preventDefault();

			var $form = $(this).parents('.bigbox').find('form');
			$('.bigbox.ajax').find('.status').text('');
			$form.find('.text-danger').text('');

			var valid = true;
			$.each($form.find('input,select'),function(i) {
				if(!$(this)[0].validity.valid) {
					valid = false;

					$(this)
					.next('.text-danger')
					.text($(this)[0].validationMessage);
				}
			});

			if(!valid) {
				$('.bigbox.ajax').find('.status').text('There are invalid fields on the form.');
				return;
			}

			if($form.find('.bigbox-submittable').length > 0) {
				$form.find('.bigbox-submittable').click();
				return;
			}

			$.ajax({
				type: "POST",
				url: $form.attr('action'),
	           data: $form.serialize(), // serializes the form's elements.
	           success: function(data) {
	           	if(data.success) {
	           		$('.bigbox.ajax').bigbox('close',e);
	           		if($form.data('on-success-complete')) {
	           			var fn = window[$form.data('on-success-complete')];
	           			if(typeof fn === 'function') {
	           				fn(data, $form);
	           			}
	           		}

	           		if($form.data('refresh-on-success')) {
	           			window.location.reload();
	           		}

	           		if(data.notify) {
	           			toastr.success(data.notify);
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

	           		if(data.message) {
	           			$('.bigbox.ajax').find('.status').text(data.message);
	           		}

	           		if(data.notify) {
	           			toastr.error(data.notify);
	           		}
	           	}
	           	else {
	           		if(data.message) {
	           			$('.bigbox.ajax').find('.status').text(data.message);
	           		}

	           		if(data.notify) {
	           			toastr.error(data.notify);
	           		}
	           	}

	           	if($form.data('on-action-response')) {
	           		var fn = window[$form.data('on-action-response')];
	           		if(typeof fn === 'function') {
	           			fn(data, $form.parents('.bigbox').data('calling_button'));
	           		}
	           	}
	           }
	       });
		});

		return self;
	}
}( jQuery ));



$(function() {
	$('body').bigboxBtn();
})
