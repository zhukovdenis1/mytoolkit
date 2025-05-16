let ProductLoader =
{
    categoryId: 0,
    searchString: '',
    activeRequest: false,
    page: 1,

    init : function(data) {
        $this = this;

        this.categoryId = data.category_id;
        this.searchString = data.searchString;

        $(document).ready(function() {
            $this.showMore();
        });
        $(window).scroll(function() {
            $this.showMore();
        });
    },

    showMore: function()
    {
        $this = this;

        if ($("#loading").length > 0 && $(window).scrollTop() + $(window).height() > $('#loading').offset().top) {
            $('#loading').css('visibility', 'visible');
            if (!$this.activeRequest) {
                $this.activeRequest = true;
                $this.page++;
                $.ajax({
                    url: '/more',
                    data: {page: $this.page, category_id: $this.categoryId, search: $this.searchString},
                    dataType: 'html',
                    success: function(data){
                        if (data.length == 0) {
                            $('#loading').remove();
                        }
                        $('#prodList').append(data);
                        if ($(window).scrollTop() + $(window).height() > $('#loading').offset().top) {
                            $('html, body').animate({
                                scrollTop: $("#loading").offset().top-$(window).height()-10
                            }, 1000);
                        }
                        $this.activeRequest = false;
                        $('#loading').css('visibilite','hidden');
                    },
                    error: function() {
                        $this.activeRequest = false;
                        $('#loading').css('visibility', 'visible');
                    }
                });
            }

        }
    }
}

