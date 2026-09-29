/**
 * Created by SE on 0023, May 23, 2017.
 */
var CRMLIST = function() {
    var init_customer_lists = function() {
        $('#view_val_group').on('click', '.view-val', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var init_val = this_.attr('data-val');
            init_customer_table(1, init_val, 5);
            $('#btn_view_val').html(this_.text());
        });
        //init_customer_table(1, 50, 5);

        $('#btn_more_data').click(function(e){
           e.preventDefault();
           var this_ = $(this);
            this_.addClass('hidden');
            if(!$('body').find('#loading').length ) {
                $.ajax({
                    url: PECO.base_url() + 'cad/getrangecustomerlist',
                    type: 'post',
                    data: {'limit': 5},
                    dataType: 'json',
                    beforeSend: function () {
                        $('.list-container').append('<h3 id="loading"><i class="fa fa-spinner fa-spin fa-pulse text-info"></i> Fetching more records...');
                    }
                }).done(function (d) {
                    console.log(d);
                    this_.removeClass('hidden');
                    $('.list-container').find('#loading').remove();
                    $('.list-container').append(d.html);
                });
            }
        });
    };

    var init_customer_table = function(cls, limit, add) {
        // QUERY TOP 100
        $.ajax({
            url: PECO.base_url()+'cad/customerslistbasic',
            type: 'post',
            dataType: 'json',
            data: {'class': cls, 'limit': limit},
            beforeSend: function() {
                $('#btn_more_data').addClass('hidden');
                $('.list-container').html('<h3 id="loading"><i class="fa fa-spinner fa-spin fa-pulse text-info"></i> Generating customer top '+limit+' list..');
            }
        }).done(function(d){
            $('#btn_more_data').removeClass('hidden');
            $('.list-container').html(d.html);
        });

    };

    return {
        list: function() {
            init_customer_lists();
        },
    }
}();
