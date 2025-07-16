let ProductMoreInfoLoader =
{
    productId: 0,
    activeReviewRequest: false,
    activeDescriptionRequest: false,
    activeCharacteristicsRequest: false,
    page: 1,

    init : function(data) {
        $this = this;

        this.productId = data.product_id;

        $(document).ready(function() {
            //$this.showMoreReviews();
        });
        $(window).scroll(function() {
            //$this.showMoreReviews(); //бесконечная подгрузка
            $this.showReviews(); //подгрузка с кнопкой
            $this.showDescription();
            $this.showCharacteristics();
        });
        $(document).on('click', '#moreReviewsButton', function() {
            $(this).after('<div class="loading" id="reviewLoading"></div>').remove();
            $this.showReviews();
        })
    },

    showDescription: function() {
        $this = this;

        if ($("#descriptionLoading").length > 0 && $(window).scrollTop() + $(window).height() > $('#descriptionLoading').offset().top) {
            if (!$this.activeDescriptionRequest) {
                $this.activeDescriptionRequest = true;
                $.ajax({
                    url: '/product_more',
                    data: {page: $this.page, product_id: $this.productId, item: 'description'},
                    dataType: 'html',
                    success: function(data){
                        $('#descriptionLoading').after(data).remove();
                    },
                    error: function() {
                    },
                    complete: function() {
                        $this.activeDescriptionRequest = false;
                        $this.requestCompleted();
                    }
                });
            }
        }
    },

    showCharacteristics: function() {
        $this = this;

        if ($("#characteristicsLoading").length > 0 && $(window).scrollTop() + $(window).height() > $('#characteristicsLoading').offset().top) {
            if (!$this.activeCharacteristicsRequest) {
                $this.activeCharacteristicsRequest = true;
                $.ajax({
                    url: '/product_more',
                    data: {page: $this.page, product_id: $this.productId, item: 'characteristics'},
                    dataType: 'html',
                    success: function(data){
                        $('#characteristicsLoading').after(data).remove();
                        $this.activeCharacteristicsRequest = false;
                    },
                    error: function() {

                    },
                    complete: function() {
                        $this.activeCharacteristicsRequest = false;
                        $this.requestCompleted();
                    }
                });
            }
        }
    },

    //пока не используется т.к. не позволяет читать описание
    showMoreReviews: function() {
        $this = this;

        if ($("#reviewLoading").length > 0 && $(window).scrollTop() + $(window).height() > $('#reviewLoading').offset().top) {
            $('#reviewLoading').css('visibility', 'visible');
            if (!$this.activeReviewRequest) {
                $this.activeReviewRequest = true;
                $this.page++;
                $.ajax({
                    url: '/product_more',
                    data: {page: $this.page, product_id: $this.productId, item: 'reviews'},
                    dataType: 'html',
                    success: function(data){
                        if (data.length === 0) {
                            $('#reviewLoading').remove();
                        }
                        $('#reviewList').append(data);

                        // if ($(window).scrollTop() + $(window).height() > $('#reviewLoading').offset().top) {
                        //     $('html, body').animate({
                        //         scrollTop: $("#reviewLoading").offset().top-$(window).height()-10
                        //     }, 1000);
                        // }
                        $this.activeReviewRequest = false;
                        $('#reviewLoading').css('visibilite','hidden');
                        $('a.reviewImg').colorbox({rel:'review-img', maxWidth:'100%', maxHeight: '100%'});
                    },
                    error: function() {
                        $('#reviewLoading').css('visibility', 'visible');
                    },
                    complete: function() {
                        $this.activeReviewRequest = false;
                        $this.requestCompleted();
                    }
                });
            }
        }
    },

    showReviews: function() {
        $this = this;

        if ($("#reviewLoading").length > 0 && $(window).scrollTop() + $(window).height() > $('#reviewLoading').offset().top) {
            if (!$this.activeReviewRequest) {
                $this.activeReviewRequest = true;
                $.ajax({
                    url: '/product_more',
                    data: {page: $this.page, product_id: $this.productId, item: 'reviews'},
                    dataType: 'html',
                    success: function(data){
                        $('#reviewList').append(data);
                        $('#reviewList').after('<button id="moreReviewsButton" class="more-reviews">Показать еще отзывы</button>');
                        $('#reviewLoading').remove();
                        $this.activeReviewRequest = false;
                        $this.page++;
                        $('a.reviewImg').colorbox({rel:'review-img', maxWidth:'100%', maxHeight: '100%'});
                    },
                    error: function() {

                    },
                    complete: function() {
                        $this.activeReviewRequest = false;
                        $this.requestCompleted();
                    }
                });
            }
        }
    },

    requestCompleted: function() {
        if (!this.activeReviewRequest && !$this.activeDescriptionRequest && !$this.activeCharacteristicsRequest) {
            let hash = window.location.hash;
            if (hash) {
                if (hash === '#reviews' && this.page > 2) {
                    let marker = $("#reviewLoading").length > 0 ? $('#reviewLoading') : $('#moreReviewsButton');
                    if ($(window).scrollTop() + $(window).height() > marker.offset().top) {
                        $('html, body').animate({
                            scrollTop: marker.offset().top-$(window).height()-10
                        }, 1000);
                    }
                } else {
                    $('html, body').animate({
                        scrollTop: $(hash).offset().top
                    }, 1000);
                }

                //удаляем якорь чтобы при подгрузке отзывов не было прокрутки к якорю (либо можно поставить проверку на this.page)
                window.history.replaceState(null, null, window.location.pathname + window.location.search);
            }

        }
    }
}
