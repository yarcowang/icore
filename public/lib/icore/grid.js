/**
 * iCore Grid js
 *
 * this script is focus on grid edit
 */
(function($) {
	$.enableGridEdit = function(o) {
		if ($.enableGridEdit.enabled)
			return;

		// options
		o = $.extend({}, $.enableGridEdit.defaults, o);

		// add styles
		var style = '<style type="text/css">\n';
		for(k in o) {
			style += o[k] + '\n';
		}
		style += '</style>\n';
		$(style).appendTo('head');
		
		$(function(){
			$('table.grid td[name]').dblclick(function(){
				if ($(this).attr('mode') != 'edit') {
					var v = $(this).html().replace('&nbsp;', ' ').trim();
					$(this).html('<input type="text" value="' + v + '" size="' + v.length + '"/>');
					$(this).attr({mode:'edit'});
				}
			});

			$('table.grid td[name]').keypress(function(e){
				if (e.which == 13) {
					var me = $(this);
					var model_name = me.parent().attr('model_name');
					var id = me.parent().attr('tid');
					var name = me.attr('name');
					var v = me.children('input').val();

					$.ajax({
						type: 'POST',
						url: gridedit_url,
						data: 'op=edit&model_name=' + model_name + '&id=' + id + '&name=' + name + '&value=' + encodeURIComponent(v),
						dataType: 'json',
						success: function(o) {
							if (!o.result) {
								if (v)
									me.html(v);
								else
									me.html('&nbsp;');
								me.removeAttr('mode');
							} else {
								alert(o.message);
							}
						}
					});
				}
			});

			$('table.grid .del').click(function(){
				if (confirm("Are you sure?")) {
					var me = $(this);
					var model_name = me.parent().parent().attr('model_name');
					var id = me.parent().parent().attr('tid');

					$.ajax({
						type: 'POST',
						url: gridedit_url,
						data: 'op=del&model_name=' + model_name + '&id=' + id,
						dataType: 'json',
						success: function(o) {
							if (!o.result) {
								me.parent().parent().remove();
							} else {
								alert(o.message);
							}
						}
					});
				}
			});

			// other ops
		});

		$.enableGridEdit.enabled = true;
	};

	$.enableGridEdit.enabled = false;
	$.enableGridEdit.defaults = {
		editable : 'table.grid th.editable:after { content: " +"; }',
		op_before : 'table.grid a.op:before { content: "["; }',
		op_after : 'table.grid a.op:after { content: "]"; }'
	};
})(jQuery);

// enable
$.enableGridEdit();
