function loadAvailableTables(from, to, useDefaultEndTime, reservationId, callback) {
    var data = {
        'action': 'my_action',
        'from': from,
        'to': to,
        'useDefaultEndTime': useDefaultEndTime ? 1 : 0,
        'reservationId': reservationId
    };

    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
    $.post(ajaxurl, data, function(response) {
        callback(JSON.parse(response));
        
    });
}