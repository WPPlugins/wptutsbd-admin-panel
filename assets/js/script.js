(function($) {
    $(document).ready( function() {
        $('tr.mp_order.status-order_received td.mp_orders_status a').append('<div class="progress"><div class="bar bar-danger" style="width: 33%;"></div></div>');
        $('tr.mp_order.status-order_paid td.mp_orders_status a').append('<div class="progress"><div class="bar bar-danger" style="width: 33%;"></div><div class="bar bar-warning" style="width: 34%;"></div></div>');
        $('tr.mp_order.status-order_shipped td.mp_orders_status a').append('<div class="progress"><div class="bar bar-danger" style="width: 33%;"></div><div class="bar bar-warning" style="width: 34%;"></div><div class="bar bar-success" style="width: 33%;"></div></div>');

        $("#adminmenu a.wp-has-submenu").click(function(e) {
            if ($('body').hasClass("folded")) {
							
						} else {
							
              e.preventDefault();
              var $li = $(this).parent();
              if($li.data("slide") !== true) {
                $li.data("slide", true);
                if($li.hasClass('wp-active-submenu')) {
                  $("ul.wp-submenu", $li).slideUp(function() {
                    $li.removeClass('wp-active-submenu');
                    $li.data("slide", false);
                  });
                } else {
                  $("ul.wp-submenu", $li).slideDown(function() {
                    $li.addClass('wp-active-submenu');
                    $li.data("slide", false);
                  });
                }
                return false;
              }
            }
        });
				
		$('.wp-submenu .current').parent().show().parent().addClass('wp-active-submenu');

        $("#collapse-menu").click(function(e) {
            if ($('body').hasClass("folded")) {
            } else {
                var $ul = $(this).parent();
                $("li.wp-active-submenu > ul", $ul).hide();
                $ul.find('li.wp-active-submenu').removeClass('wp-active-submenu');
            }
        });

        // Enable custom login
        $("input[name='login_screen[active]']").change(function() {
            if (!$(this).is(':checked')) {
                $('#enable_custom_login').hide();
            } else {
                $('#enable_custom_login').show();
            }
        }).trigger("change");

        // Equal height icon list
        _tableHeight = $('#wptutsbd-admin-icons-table').height();
        _iconHeight = $('#wptutsbd-admin-icons-list').height();
        if (_tableHeight > _iconHeight)
            $('#wptutsbd-admin-icons-list').height(_tableHeight);

        $("#wptutsbd-admin-icons-table a").click(function() {
            if(!$(this).parent().hasClass('active')) {
                $("#wptutsbd-admin-icons-table a.active").removeClass('active');
                $(this).addClass('active');
                $("#wptutsbd-admin-icons-list").data("target", this);
            }
            return false;
        });
        $("#wptutsbd-admin-icons-list a i").click(function() {
            //icon = $(this).find("i");
            var target = $("#wptutsbd-admin-icons-list").data("target");
            if($(target).is("a")) {
                var val = window.getComputedStyle(this,':before').content;
                val = escape(val).replace(/%22/g,"").replace("%u","").toLowerCase();
                $("input", target).val(val);
                $("i", target).attr("class", $(this).attr("class"));
            }
            return false;
        });
        $("#wptutsbd-admin-icons-list a").click(function() {
            $(this).find("i").trigger("click");
            return false;
        });
    });

})(jQuery);
