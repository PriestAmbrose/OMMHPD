jQuery(document).ready(function($) {
  $('#bible-calendar').on('click', '.calendar-day', function() {
    let date = $(this).data('date');

    $.post(bible_plugin_ajax.ajax_url, {
      action: 'bible_plugin_get_day',
      date: date
    }, function(response) {
      $('#bible-day-content').html(response);
    });
  });
});
