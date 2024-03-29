function convertDateTime(string) {
    var stringParts = string.split(/[\s.:]+/);
    return stringParts[2] + "-" + stringParts[1] + "-" + stringParts[0] + "T" + stringParts[3] + ":" + stringParts[4];
}

initQuickEdit(function(rowElement, id) {
    var tables = rowElement.getElementsByClassName("m_tables")[0].innerHTML;
    var from = rowElement.getElementsByClassName("m_from")[0].innerHTML;
    var to = rowElement.getElementsByClassName("m_to")[0].innerHTML;
    var numberOfSeats = rowElement.getElementsByClassName("m_numberOfSeats")[0].innerHTML;
    var firstname = rowElement.getElementsByClassName("m_firstname")[0].innerHTML;
    var lastname = rowElement.getElementsByClassName("m_lastname")[0].innerHTML;
    var mail = rowElement.getElementsByClassName("m_mail")[0].innerHTML;
    var phonenumber = rowElement.getElementsByClassName("m_phonenumber")[0].innerHTML;
    var remarks = rowElement.getElementsByClassName("m_remarks")[0].innerHTML;

    rowElement.removeChild(rowElement.getElementsByClassName("m_tables")[0]);
    rowElement.removeChild(rowElement.getElementsByClassName("m_from")[0]);
    rowElement.removeChild(rowElement.getElementsByClassName("m_to")[0]);
    rowElement.removeChild(rowElement.getElementsByClassName("m_numberOfSeats")[0]);
    rowElement.removeChild(rowElement.getElementsByClassName("m_firstname")[0]);
    rowElement.removeChild(rowElement.getElementsByClassName("m_lastname")[0]);
    rowElement.removeChild(rowElement.getElementsByClassName("m_mail")[0]);
    rowElement.removeChild(rowElement.getElementsByClassName("m_phonenumber")[0]);
    rowElement.removeChild(rowElement.getElementsByClassName("m_remarks")[0]);


    var tableArray = tables.split(",");

    var content = "";
    content += "<td><div class='selectTableParent inline'>";
    for(var i = 0; i < tableArray.length; i++) {
        var titleToSelect = tableArray[i];

        content += "<select class='selectTable' name='table[]' onchange='updateDropdownMenus()'>";
        for(var counter = 0; counter < allTables.length; counter++) {
            content += "<option value='"+allTables[counter]["id"]+"' "+(titleToSelect == allTables[counter]["title"] ? "selected" : "") +">"+allTables[counter]["title"]+"</option>";
        }
        content += "</select>";
    }
    content += "</div><p class='selectTableError hidden'>Bitte erst Datum wählen!</p><button type='button' class='edit' onclick='addElement("+id+")'><i class='fa fa-plus'></i></button></td>";

    content += '<td><input type="datetime-local" name="from" class="from" value="'+convertDateTime(from)+'" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}" placeholder="Beginn der Reservierung" oninput="onDateChange(document.getElementsByClassName(\'from\')[0], document.getElementsByClassName(\'to\')[0], '+id+')" /></td>';
    content += '<td><input type="datetime-local" name="to" class="to" value="'+convertDateTime(to)+'" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}" placeholder="Ende der Reservierung" oninput="onDateChange(document.getElementsByClassName(\'from\')[0], document.getElementsByClassName(\'to\')[0], '+id+')"/></td>';
    content += '<td><input type="number" name="numberOfSeats" value="'+numberOfSeats+'" /></td>';
    content += '<td><input type="text" name="firstname" value="'+firstname+'" /></td>';
    content += '<td><input type="text" name="lastname" value="'+lastname+'" /></td>';
    content += '<td><input type="text" name="mail" value="'+mail+'" /></td>';
    content += '<td><input type="text" name="phonenumber" value="'+phonenumber+'" /></td>';
    content += '<td><input type="text" name="remarks" value="'+remarks+'" /></td>';



    rowElement.innerHTML = content + rowElement.innerHTML;

    onDateChange(document.getElementsByClassName("from")[0], document.getElementsByClassName("to")[0], id);
});