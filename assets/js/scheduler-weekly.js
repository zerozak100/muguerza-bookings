jQuery(function($) {
    $('#dates-and-times').on('click', '.calendar-next, .calendar-previous', function() {
        var date = $(this).data('date');
        var timezone = $('#timezone').val();
        var apexCalendarId = $('#apex_calendar_id').val();

        mgbShowCalendar(date, timezone, apexCalendarId);
    });

    function mgbShowCalendar( date, timezone, apexCalendarId ) {
        $.ajax({
            method: "POST",
            url: AJAX.adminUrl,
            data: {
                action: "mgb_show_calendar",
                startDate: date,
                timezone,
                apexCalendarId,
            },
            dataType: 'json',
            success: function( response ) {
                $('#dates-and-times').html(response.data.payload);
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                // console.log('error', e);
            }
            // contentType: 'application/json',
            // contentType: 'application/x-www-form-urlencoded; charset=UTF-8', // default
        })
    }

});