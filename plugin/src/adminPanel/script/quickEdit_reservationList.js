function convertDateTime(string) {
    var stringParts = string.split(/[\s.:]+/);
    return stringParts[2] + "-" + stringParts[1] + "-" + stringParts[0] + "T" + stringParts[3] + ":" + stringParts[4];
}

initQuickEdit(function(rowElement) {
    var from = rowElement.getElementsByClassName("m_from")[0].innerHTML;
    var to = rowElement.getElementsByClassName("m_to")[0].innerHTML;
    var firstname = rowElement.getElementsByClassName("m_firstname")[0].innerHTML;
    var lastname = rowElement.getElementsByClassName("m_lastname")[0].innerHTML;
    var mail = rowElement.getElementsByClassName("m_mail")[0].innerHTML;
    var phonenumber = rowElement.getElementsByClassName("m_phonenumber")[0].innerHTML;

    rowElement.removeChild(rowElement.getElementsByClassName("m_tables")[0]);
    rowElement.removeChild(rowElement.getElementsByClassName("m_from")[0]);
    rowElement.removeChild(rowElement.getElementsByClassName("m_to")[0]);
    rowElement.removeChild(rowElement.getElementsByClassName("m_firstname")[0]);
    rowElement.removeChild(rowElement.getElementsByClassName("m_lastname")[0]);
    rowElement.removeChild(rowElement.getElementsByClassName("m_mail")[0]);
    rowElement.removeChild(rowElement.getElementsByClassName("m_phonenumber")[0]);

    var content = "";
    content += "<td><select name='table'>";
    for(var counter = 0; counter < allTables.length; counter++) {
        content += "<option value='"+allTables[counter]["id"]+"'>"+allTables[counter]["title"]+"</option>";
    }
    content += "</select></td>";

    content += '<td><input type="datetime-local" name="from" value="'+convertDateTime(from)+'" /></td>';
    content += '<td><input type="datetime-local" name="to" value="'+convertDateTime(to)+'" /></td>';
    content += '<td><input type="text" name="firstname" value="'+firstname+'" /></td>';
    content += '<td><input type="text" name="lastname" value="'+lastname+'" /></td>';
    content += '<td><input type="text" name="mail" value="'+mail+'" /></td>';
    content += '<td><input type="text" name="phonenumber" value="'+phonenumber+'" /></td>';


    rowElement.innerHTML = content + rowElement.innerHTML;
});