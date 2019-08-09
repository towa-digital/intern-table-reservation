initQuickEdit(function(rowElement) {
    var title = rowElement.getElementsByClassName("m_title")[0].innerHTML;
    var isOutside = (rowElement.getElementsByClassName("m_isOutside")[0].innerHTML == "ja") ? true : false;
    var numberOfSeats = rowElement.getElementsByClassName("m_numberOfSeats")[0].innerHTML;

    rowElement.removeChild(rowElement.getElementsByClassName("m_title")[0]);
    rowElement.removeChild(rowElement.getElementsByClassName("m_isOutside")[0]);
    rowElement.removeChild(rowElement.getElementsByClassName("m_numberOfSeats")[0]);

    var content = '<td><input type="text" name="title" value="'+title+'"/></td>';
    content += '<td><input type="checkbox" name="isOutside" '+(isOutside ? 'checked' : '')+'/></td>';
    content += '<td><input type="number"  name="numberOfSeats" value="'+numberOfSeats+'" /></td>';

    rowElement.innerHTML = content + rowElement.innerHTML;
});