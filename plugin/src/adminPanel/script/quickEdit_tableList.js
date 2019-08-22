initQuickEdit(function(rowElement, id) {
    var title = rowElement.getElementsByClassName("m_title")[0].innerHTML;
    var isOutside = (rowElement.getElementsByClassName("m_isOutside")[0].innerHTML == "ja") ? true : false;
    var numberOfSeats = rowElement.getElementsByClassName("m_numberOfSeats")[0].innerHTML;
    var isDisabled = (rowElement.getElementsByClassName("m_isDisabled")[0].innerHTML == "ja") ? true : false;


    rowElement.removeChild(rowElement.getElementsByClassName("m_title")[0]);
    rowElement.removeChild(rowElement.getElementsByClassName("m_isOutside")[0]);
    rowElement.removeChild(rowElement.getElementsByClassName("m_numberOfSeats")[0]);
    rowElement.removeChild(rowElement.getElementsByClassName("m_isDisabled")[0]);


    var content = '<td><input type="text" name="title" value="'+title+'"/></td>';
    content += '<td><input type="checkbox" name="isOutside" '+(isOutside ? 'checked' : '')+'/></td>';
    content += '<td><input type="number"  name="numberOfSeats" value="'+numberOfSeats+'" /></td>';
    content += '<td><input type="checkbox" name="isDisabled" '+(isDisabled ? 'checked' : '')+'/></td>';


    rowElement.innerHTML = content + rowElement.innerHTML;
});