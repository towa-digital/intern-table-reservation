var allFreeTables = [];
var freeTables = [];

function showError(text) {
    $("#jsError").html(text);
    $("#jsError").removeClass("hidden");
}


function onDateChange(fromElement, toElement, reservationId) {

    // leere das Feld, das im Fehlerfalle angezeigt wird
    $("#jsError").html("");
    $("#jsError").addClass("hidden");

    // verstecke das Dropdown-Menü für die Tischauswahl und zeige stattdessen Fehlermeldung
    $(".selectTable").addClass("hidden");
    $(".selectTableError").removeClass("hidden");
    $(".selectTableError").html("Bitte erst Datum wählen!");
    $(".add_selectTable").addClass("hidden");

    // lese eingegebene Datumswerte aus
    var from = fromElement.value;
    var to = toElement.value;

    // falls ein Datum noch nicht eingegeben wurde, mache nichts
    if(from == "" || to == "") return;

    // Validierung
    var fromDate = new Date(from);
    var toDate = new Date(to);
    if(fromDate > toDate) {
        showError("Das Beginndatum darf nicht nach dem Enddatum liegen.");
        return;
    }
    if(fromDate < new Date()) {
        showError("Das Beginndatum darf nicht in der Vergangenheit liegen");
        return;
    }

    // zeige das Dropdown-Menü für die Tischauswahl und verstecke die Fehlermeldung
    $(".selectTable").removeClass("hidden");
    $(".selectTableError").addClass("hidden");
    $(".selectTableError").html("");
    $(".add_selectTable").removeClass("hidden");

        // verfügbare Tische per AJAX laden
        loadAvailableTables(from, to, reservationId, function(allTables) {
            freeTables = allTables.slice();
            allFreeTables = allTables.slice();
    
            updateDropdownMenus();
            
        });
    
}


function addElement() {
    $(".selectTableParent").append("<select name='table[]' class='selectTable' onchange='updateDropdownMenus()'></select>");
    updateDropdownMenus();
}

function updateDropdownMenus() {
    freeTables = allFreeTables.slice();


    // lade alle Dropdown-Menüs in ein Array
    var allSelectElems = document.getElementsByClassName("selectTable");
    
    /*
    * In diesem Array sind die values der ausgewählten option-Tags enthalten. Wenn ein
    * Dropdown-Menü keinen ausgewählten Wert hat, entspricht der Wert -1. Dieses Array
    * ist gleich lang wie allSelectElems.
    */
    var selectedIds = [];

    // iteriere über alle Dropdown-Menüs und speichere den value des option-Tags in selectedIds
    for(var c = 0; c < allSelectElems.length; c++) {
        var selectElem = allSelectElems[c];
        var selectedOption = selectElem.options[selectElem.selectedIndex];
        
        selectedIds.push({
            "id": selectedOption === undefined ? -1 : selectedOption.value,
            "title": selectedOption === undefined ? "" : selectedOption.text
        });

        if(selectedOption !== undefined) {
            for( var i = 0; i < freeTables.length; i++){ 
                if (freeTables[i]["id"] == selectedOption.value) {
                    freeTables.splice(i, 1); 
                }
            }
        }
    }

    console.log(freeTables);

    // entferne alle option-Tags aus den Dropdown-Menüs
    $(".selectTable").empty();

    // iteriere über alle Dropdown-Menüs
    for(var i = 0; i < allSelectElems.length; i++) {
        allSelectElems[i].innerHTML += "<option disabled selected value>Bitte wählen!</option>";

        // füge zuerst, wenn vorhanden, das ausgewählte Element als option-Tag ein
        // wir wissen, dass dies nicht in freeTables enthalten sein kann, da es aus diesem Array gelöscht worden ist
        if(selectedIds[i].id != -1) {
            allSelectElems[i].innerHTML += '<option value="'+selectedIds[i].id+'" selected>'+selectedIds[i].title+'</option>';        
        }        

        // iteriere über alle freien Tische
        for(var c = 0; c < freeTables.length; c++) {
        /*    // wenn die ID des Tisches dieselbe ist wie die, die vorher im entsprechenden Dropdown-Menü ausgewählt war
            // soll dieses Element wieder ausgewählt werden
            var shouldSelect = selectedIds[i].value == freeTables[c]["id"];

            


            // option-Tag ans Dropdown-Menü anhängen
            allSelectElems[i].innerHTML += '<option value="'+freeTables[c]["id"]+'" '+(shouldSelect ? 'selected' : '')+'>'+freeTables[c]["title"]+'</option>';    */
            allSelectElems[i].innerHTML += '<option value="'+freeTables[c]["id"]+'">'+freeTables[c]["title"]+'</option>';        
    
        }
    
    } 
}


/*function updateDropdownMenus() {
    freeTables = allFreeTables.slice();

    
}*/
