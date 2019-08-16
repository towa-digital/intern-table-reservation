/**
 * 
 * @param {int} id ID des Wochentags, zu dem die zusätzliche Zeitauswahl hinzugefügt
 * werden soll. 0 steht für Montag, 1 für Dienstag, ...
 */
function addTimePicker(id) {
    var elemCounter = $("#timePickerParent_"+id+" div").length;

    var toAppend = "<div>";
    toAppend += '<input type="time" name="openingHours['+id+']['+elemCounter+'][from]">';
    toAppend += '<span>-</span>';
    toAppend += '<input type="time" name="openingHours['+id+']['+elemCounter+'][to]">';
    toAppend += '</div>';
    
    $("#timePickerParent_"+id).append(toAppend);
}