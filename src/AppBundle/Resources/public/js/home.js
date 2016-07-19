$(document).ready(function() {
    clearSearchInput();
    loadEntryForm();
    refreshOneFeed();
    refreshAllComponent();
});

function clearSearchInput()
{
    $('#search_on_google_search').on('click', function() {
        $(this).val('');
    })
}

function initColorPicker()
{
    $('.colorpicker-component').colorpicker();
}

function loadEntryForm()
{
    $("#entryForm").on("show.bs.modal", function(e) {
        var link = $(e.relatedTarget);
        $(this).find(".modal-content").load(link.attr("href"), function() {
            initColorPicker();
            saveFormAjax();
        });
    });
}