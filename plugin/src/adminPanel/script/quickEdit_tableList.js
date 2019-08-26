initQuickEdit(function(rowElement, id) {
    var title = rowElement.getElementsByClassName("m_title")[0].innerHTML;
    var isOutside = (rowElement.getElementsByClassName("m_isOutside")[0].innerHTML == "ja") ? true : false;
    var numberOfSeats = rowElement.getElementsByClassName("m_numberOfSeats")[0].innerHTML;
    var isDisabled = (rowElement.getElementsByClassName("m_isDisabled")[0].innerHTML == "ja") ? true : false;

    var posX = rowElement.getElementsByClassName("m_posX")[0].innerHTML;
    var posY = rowElement.getElementsByClassName("m_posY")[0].innerHTML;
    var width = rowElement.getElementsByClassName("m_width")[0].innerHTML;
    var height = rowElement.getElementsByClassName("m_height")[0].innerHTML;


    rowElement.removeChild(rowElement.getElementsByClassName("m_title")[0]);
    rowElement.removeChild(rowElement.getElementsByClassName("m_isOutside")[0]);
    rowElement.removeChild(rowElement.getElementsByClassName("m_numberOfSeats")[0]);
    rowElement.removeChild(rowElement.getElementsByClassName("m_isDisabled")[0]);

    rowElement.removeChild(rowElement.getElementsByClassName("m_posX")[0]);
    rowElement.removeChild(rowElement.getElementsByClassName("m_posY")[0]);
    rowElement.removeChild(rowElement.getElementsByClassName("m_width")[0]);
    rowElement.removeChild(rowElement.getElementsByClassName("m_height")[0]);



    var content = '<td><input type="text" name="title" value="'+title+'"/></td>';
    content += '<td><input type="checkbox" name="isOutside" '+(isOutside ? 'checked' : '')+'/></td>';
    content += '<td><input type="number"  name="numberOfSeats" value="'+numberOfSeats+'" /></td>';
    content += '<td><input type="checkbox" name="isDisabled" '+(isDisabled ? 'checked' : '')+'/></td>';

    content += '<td><input type="number" step="0.01" name="posX" value="'+posX+'" /></td>';
    content += '<td><input type="number" step="0.01" name="posY" value="'+posY+'" /></td>';
    content += '<td><input type="number" step="0.01" name="width" value="'+width+'" /></td>';
    content += '<td><input type="number" step="0.01" name="height" value="'+height+'" /></td>';


    rowElement.innerHTML = content + rowElement.innerHTML;
});