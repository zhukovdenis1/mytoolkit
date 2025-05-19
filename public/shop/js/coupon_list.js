let CouponList =
{
    init: function (data) {
        $(document).ready(function () {
            $('a.short-coupon').click(function(e) {
                e.preventDefault(); // Предотвращаем стандартное действие ссылки

                // Находим следующий за ссылкой div.details
                var detailsContent = $(this).next('div.details').html();

                // Открываем colorbox с содержимым div.details
                $.colorbox({
                    html: detailsContent,
                    // width: "80%",
                    // height: "80%",
                    // maxWidth: "800px",
                    // maxHeight: "600px"
                });
            });
        });
    },
}
