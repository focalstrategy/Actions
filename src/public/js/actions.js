function onActionComplete(t,o){if(void 0!==t.redirect_to&&(""!=t.redirect_to?window.location=t.redirect_to:window.location.reload()),t.reload_datatable_page&&o.parents("table").DataTable().ajax.reload(null,!1),t.highlight&&o.parents("tr,.highlightable").addClass(t.highlight),t.remove_on_response&&o.parents(".action_wrap").remove(),t.remove_parent&&o.parents("tr,.removable").fadeOut("fast").remove(),t.replace_with&&o.parents(".action_wrap").replaceWith(t.replace_with),t.js_callback){var e=window[t.js_callback];"function"==typeof e&&e(t,o)}}$(function(){$(document).on("submit",".action_inline form",function(t){t.preventDefault();var o=$("button[clicked=true]").attr("name"),e=$("button[clicked=true]").val(),n=$(this).is("form")?$(this):$(this).find("form"),i=new FormData(n[0]);i.append(o,e),$.ajax({type:"POST",url:n.attr("action"),data:i,cache:!1,contentType:!1,processData:!1,success:function(t){if(t.success){if(t.notify){var o=window.toast;"function"==typeof o?toast("success",t.notify):console.log("Received notify but can not find the function toast(status, message)")}}else if(t.error&&($(".form-group").removeClass("has-errors"),$(".form-group .text-danger").text(""),t.errors&&$.each(t.errors,function(o){var e=n.find("#"+o),i=e.parents(".form-group"),a=i.find(".text-danger");i.addClass("has-errors"),a.text(t.errors[o].join(","))}),t.notify)){console.log(t.notify);var o=window.toast;"function"==typeof o?toast("error",t.notify):console.log("Received notify but can not find the function toast(status, message)")}if(t.message&&$(".message").text(t.message),n.data("on-action-response")){var e=window[n.data("on-action-response")];"function"==typeof e&&e(t,n)}}})}),$(".action_inline form button").click(function(){$(".action_inline form button",$(this).parents("form")).removeAttr("clicked"),$(this).attr("clicked","true")})}),function(t){t.fn.bigbox=function(o,e){var n=this;t(".bigbox-modal-bg").one("click",function(){n.close({currentTarget:t(".close-dlg")})}),n.show=function(){var o=n.data("content-route");null!=o?n.find(".bigbox-body").html("").load(o,function(){if(n.find(".bigbox-hide-button").hide(),n.find("div.bigbox-sm").length>0){n.addClass("bigbox-sm"),n.addClass("in");var o=n.find(".bigbox-header").height();o+=n.find(".bigbox-body").height(),o+=n.find(".bigbox-footer").height(),o<t(window).height()&&n.css("height","fit-content")}else n.addClass("in");t("body").addClass("bigbox-open"),$btn=t(n.data("calling_button")),$btn.html($btn.data("original_text"))}):(n.addClass("in"),t("body").addClass("bigbox-open"),$btn=t(n.data("calling_button")),$btn.html($btn.data("original_text"))),t('[data-dismiss="modal"]',n).click(n.close),n.trigger("show.bs.modal",n)},n.close=function(o){n.trigger("hide.bs.modal",{is_cancel:t(o.currentTarget).is(".close-dlg")}),n.removeClass("in"),t("body").removeClass("bigbox-open"),n.removeClass("bigbox-sm"),n.css("height","auto"),t('[data-dismiss="modal"]',n).unbind("click")};var i=n[o];return"function"==typeof i&&i(e),n},t.fn.bigboxBtn=function(){var o=this;return o.on("click",".bigbox-btn",function(o){o.preventDefault();var e=t(this),n=t(".bigbox.ajax");n.find(".bigbox-btn-submit").show(),n.length<=0&&console.error("View: bigbox-ajax is missing");var i=e.text();if(e.data("original_text",i),n.data("calling_button",e),n.data("content-route",e.data("content-route")),n.find(".bigbox-title > span").text(e.data("title")),e.data("save-button-text")){var n=t(".bigbox.ajax");n.find(".bigbox-btn-submit").html(e.data("save-button-text"))}if(e.data("hide-save-button")){var n=t(".bigbox.ajax");n.find(".bigbox-btn-submit").hide()}e.html('<span class="fa fa-loading fa-spin"></span> Loading…'),t(".bigbox.ajax").bigbox("show")}),o.on("keypress",".bigbox input",function(o){if(13==o.keyCode)return t(".bigbox-btn-submit").click(),!1}),o.on("click",".bigbox .bigbox-btn-submit",function(o){o.preventDefault();var e=t(this).parents(".bigbox").find("form");t(".bigbox.ajax").find(".status").text(""),e.find(".text-danger").text("");var n=!0;return t.each(e.find("input,select"),function(o){t(this)[0].validity.valid||(n=!1,t(this).next(".text-danger").text(t(this)[0].validationMessage))}),n?e.find(".bigbox-submittable").length>0?void e.find(".bigbox-submittable").click():void t.ajax({type:"POST",url:e.attr("action"),data:e.serialize(),success:function(n){if(n.success){if(t(".bigbox.ajax").bigbox("close",o),e.data("on-success-complete")){var i=window[e.data("on-success-complete")];"function"==typeof i&&i(n,e)}e.data("refresh-on-success")&&window.location.reload(),n.notify&&("undefined"!=typeof toastr?toastr.success(n.notify):console.log(n.notify))}else n.error?(t(".form-group").removeClass("has-errors"),t(".form-group .text-danger").text(""),n.errors&&t.each(n.errors,function(t){var o=e.find("#"+t),i=o.parents(".form-group"),a=i.find(".text-danger");i.addClass("has-errors"),a.text(n.errors[t].join(","))}),n.message&&t(".bigbox.ajax").find(".status").text(n.message),n.notify&&("undefined"!=typeof toastr?toastr.error(n.notify):console.error(n.notify))):(n.message&&t(".bigbox.ajax").find(".status").text(n.message),n.notify&&("undefined"!=typeof toastr?toastr.error(n.notify):console.error(n.notify)));if(e.data("on-action-response")){var i=window[e.data("on-action-response")];"function"==typeof i&&i(n,e.parents(".bigbox").data("calling_button"))}}}):void t(".bigbox.ajax").find(".status").text("There are invalid fields on the form.")}),o}}(jQuery),$(function(){$("body").bigboxBtn()});