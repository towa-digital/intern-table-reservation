/**
 * 
 * @param {int} id ID des Wochentags, zu dem die zusätzliche Zeitauswahl hinzugefügt
 * werden soll. 0 steht für Montag, 1 für Dienstag, ...
 */
function addTimePicker(id) {
    var elemCounter = $("#timePickerParent_"+id+" div").length;

    var toAppend = "<div>";
    toAppend += '<input type="time" name="openingHours['+id+']['+elemCounter+'][from]">';
    toAppend += '<span> - </span>';
    toAppend += '<input type="time" name="openingHours['+id+']['+elemCounter+'][to]">';
    toAppend += '<button type="button" class="edit" onclick="removeTimePicker(this)"><i class="fas fa-minus"></i></button>';
    toAppend += '</div>';
    
    $("#timePickerParent_"+id).append(toAppend);
}

function removeTimePicker(button) {
    var parent = button.parentElement;
    var timePickerParent = parent.parentElement;

    var timePickerSiblings = Array.prototype.slice.call(timePickerParent.parentElement.getElementsByTagName("div"));

    var indexIn = timePickerSiblings.indexOf(parent);
    var previousValue1 = "";
    var previousValue2 = "";
    for(var c = indexIn; c < timePickerSiblings.length; c++) {
        var toSet1 = previousValue1;
        var toSet2 = previousValue2;

        previousValue1 = timePickerSiblings[c].getElementsByTagName("input")[0].name;
        previousValue2 = timePickerSiblings[c].getElementsByTagName("input")[1].name;

        timePickerSiblings[c].getElementsByTagName("input")[0].name = toSet1;
        timePickerSiblings[c].getElementsByTagName("input")[1].name = toSet2;
    }

    timePickerParent.removeChild(parent);

}

function addHolidayPicker() {
    var elemCounter = $("#holidayParent div").length;
    var toAppend = "";
    toAppend += "<div class='timePickerParent' id='holidayPicker_"+elemCounter+"'>";
    toAppend += "<input type='date' name='holidays["+elemCounter+"][from]' />";
    toAppend += "<input type='date' name='holidays["+elemCounter+"][to]' />";
    toAppend += '<button type="button" class="edit" onclick="removeTimePicker(this)"><i class="fas fa-minus"></i></button>';
    toAppend += "</div>";

    $("#holidayParent").append(toAppend);
}
