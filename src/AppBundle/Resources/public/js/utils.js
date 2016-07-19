function saveFormAjax()
{
    $('.formAjax').off().on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function (data) {
                $('.modal').modal('hide');
                $.notify({
                    message: data.message
                },{
                    type: data.type,
                    placement: {
                        from: "top",
                        align: "center"
                    }
                });
            }
        });
    });
}

function refreshOneFeed()
{
    $('.refreshFeed').on('click', function(e) {
        e.preventDefault();
        refreshFeed($(this));
    });
}

function refreshAllComponent()
{
    $('.refreshAllComponent').on('click', function(e) {
        e.preventDefault();
        var feeds = $('body').find('.refreshFeed');
        feeds.each(function(){
            refreshFeed($(this));
        });
    });
}

function refreshFeed(feed)
{
    var table = feed.closest('.panel').find('.table');
    table.addClass('refreshing');
    var spinner = feed.closest('.panel').find('.itemListSpinner');
    spinner.css('display','block');
    var rows = feed.closest('.panel').find('.table tr');
    $.ajax({
        url: Routing.generate('ajax_refresh_feed', { feed: feed.attr('data-component-id') }),
        type: 'GET',
        success: function (data) {
            rows.each(function(key) {
                $(this).find('td:eq(0)').html(data[key].date);
                $(this).find('td:eq(1) a').attr('href', data[key].link);
                $(this).find('td:eq(1) a').html(data[key].title);
            });
            spinner.css('display','none');
            table.removeClass('refreshing');
        }
    });
}
