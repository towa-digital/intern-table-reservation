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

    // falls existent, deaktiviere Speichern-Btn
    if($("#publishBtn") !== undefined) {
        $("#publishBtn").prop("disabled", true);
    }

    // lese eingegebene Datumswerte aus
    var from = fromElement.value;
    var to = toElement.value;

    // falls die Checkbox, dass die standardmäßige Reservierungsdauer verwendet werden soll, existiert und angehakt ist, flag setzen
    var useDefaultEndTime = $("#useDefaultEndTime").length && $("#useDefaultEndTime").is(":checked");


    // falls ein Datum noch nicht eingegeben wurde, mache nichts
    if(from == "" || (to == "" && !useDefaultEndTime)) return;

    // Validierung
    var fromDate = new Date(from);
    var toDate = (useDefaultEndTime) ? new Date(fromDate.getTime() + DEFAULT_RESERVATION_DURATION * 60 * 1000) : new Date(to);
    if(fromDate.getTime() > toDate.getTime()) {
        showError("Das Beginndatum darf nicht nach dem Enddatum liegen.");
        return;
    }
    if(fromDate.getTime() < new Date().getTime()) {
        showError("Das Beginndatum darf nicht in der Vergangenheit liegen");
        return;
    }
    if(fromDate.getTime() < new Date(new Date().getTime() + CAN_RESERVATE_IN_MINUTES * 60 * 1000).getTime()) {
        showError("Das Beginndatum muss mindestens 30 Minuten in der Zukunft liegen.");
        return;
    }
    if(fromDate.getTime() > new Date(new Date().getTime() + 365 / 2 * 24 * 60 * 60 * 1000)) {
        showError("Das Beginndatum darf nicht weiter als ein halbes Jahr in der Zukunft liegen");
        return;
    }

    // zeige das Dropdown-Menü für die Tischauswahl und verstecke die Fehlermeldung
    $(".selectTable").removeClass("hidden");
    $(".selectTableError").addClass("hidden");
    $(".selectTableError").html("");
    $(".add_selectTable").removeClass("hidden");

    // falls existent, aktiviere Speichern-Btn
    if($("#publishBtn") !== undefined) {
        $("#publishBtn").prop("disabled", false);
    }

    // verfügbare Tische per AJAX laden
    loadAvailableTables(from, to, useDefaultEndTime, reservationId, function(allTables) {
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
        
        selectedIds.push(selectedOption === undefined ? -1 : selectedOption.value);

        if(selectedOption !== undefined) {
            for( var i = 0; i < freeTables.length; i++){ 
                if (freeTables[i]["id"] == selectedOption.value) {
                    freeTables.splice(i, 1); 
                }
            }
        }
    }

    // entferne alle option-Tags aus den Dropdown-Menüs
    $(".selectTable").empty();

    console.log(freeTables);

    // iteriere über alle Dropdown-Menüs
    for(var i = 0; i < allSelectElems.length; i++) {
        allSelectElems[i].innerHTML += "<option selected value>Tisch nicht ausgewählt</option>";

        // füge zuerst, wenn vorhanden, das ausgewählte Element als option-Tag ein
        // wir wissen, dass dies nicht in freeTables enthalten sein kann, da es aus diesem Array gelöscht worden ist
        if(selectedIds[i] != -1) {
            tableObj = undefined;

            for(var a = 0; a < allFreeTables.length; a++) {
                if(allFreeTables[a]["id"] == selectedIds[i]) tableObj = allFreeTables[a];
            }

            if(tableObj) allSelectElems[i].innerHTML += '<option value="'+tableObj.id+'" selected>'+tableObj.title+'</option>';        
        }        

        // iteriere über alle freien Tische
        for(var c = 0; c < freeTables.length; c++) {
            allSelectElems[i].innerHTML += '<option value="'+freeTables[c]["id"]+'">'+freeTables[c]["title"]+' ('+freeTables[c]["seats"]+' Plätze)</option>';        
    
        }
    
    } 
}

