jQuery(function ($) {

    const messages = document.getElementById('ssr_container');

    function scrollToBottom() {
        messages.scrollTop = messages.scrollHeight;
    }
    $('.replace').click(
        function () {
            if ($('#ssr_replace').val() == '' || $('#ssr_search').val() == '') {
                return;
            }
            confirm("Please make sure enter value, Once start can't be modify or stop there are chances of site malfunction/damage! ");
            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: {
                    action: 'ssr_handel',
                    replace: $('#ssr_replace').val(),
                    search: $('#ssr_search').val(),
                    perfom: 'ssr_start_replace'
                }
            }).done(function (msg) {
                clearInterval(timeValue);
            });

            $flg = 0;
            var timeValue = setInterval(
                function () {
                    $.ajax({
                        type: "POST",
                        url: ajaxurl,
                        data: {
                            action: 'ssr_handel',
                            perfom: 'ssr_status'

                        }
                    }).done(function (msg) {
                        if (msg == 'completed!') {
                            clearInterval(timeValue);
                            $('.ssr_status').html($('.ssr_status').html() + '<br># completed!');
                            return;
                        }
                        $.each(msg, function () {
                            $('.ssr_status').html($('.ssr_status').html() + '<br>#' + this);
                            scrollToBottom();
                        });
                    });
                }, 1000
            );
        }
    );
});