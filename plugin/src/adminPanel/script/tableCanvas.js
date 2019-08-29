var canvas = document.getElementById("canvas");
var ctx = canvas.getContext("2d");

/**
 * Ist true, wenn der Positionierungs-Modus aktiv ist, d.h. wenn mit der Maus das grüne 
 * Rechteck gezeichnet werden kann.
 */
var isPositioningActive = false;

/**
 * Ist entweder undefined oder enthält ein Objekt mit den Keys x und y. Die zugehörigen Values
 * sind die Koordinaten in Pixel, an denen mit dem Drücken der Maus begonnen wurde.
 */
var mouseDown = undefined;

/**
 * Ist entweder undefined oder enthält ein Objekt mit den Keys x und y. Die zugehörigen Values sind 
 * die Koordinaten in Pixel, an denen die Maus released wurde.
 */
var mouseUp = undefined;

/**
 * true, wenn die Maus aktuell gedürkct ist.
 */
var isMouseDown = false;

/**
 * table-Objekt, welches gerade bearbeitet wird. undefined, falls gerade kein Objekt bearbeitet wird.
 */
var tableToEdit = undefined;

$(document).ready(function() {
    setInterval(function() {
        redrawCanvas();
    }, 10);
    redrawCanvas();

    canvas.addEventListener("click", onClick);
})

/**
 * Gibt true zurück, wenn im Dropdown-Menü "Außen" ausgewählt wurde.
 */
function isOutside() {
    var insideOutside = document.getElementById("insideOutside").value;
    return (insideOutside == "outside");
}

/**
 * Zeichnet das Canvas neu. Wird automatisch aufgerufen.
 */
function redrawCanvas() {
    var io = isOutside();

    if(!io){
        document.getElementById('backgroundImg').style.backgroundImage = "./../../assets/maxresdefault.jpg";
    } else {
        document.getElementById('backgroundImg').style.backgroundImage = "./../../assets/outside.jpg";
    }

    canvas.width = canvas.parentElement.offsetWidth;
    canvas.height = canvas.parentElement.offsetHeight;

    ctx.clearRect(0, 0, canvas.width, canvas.height);

    for(var table of allTables) {
        if(io != table.isOutside || (tableToEdit !== undefined && tableToEdit.id == table.id)) continue;

        var posX = (table.position["posX"]) * canvas.width;
        var posY = (table.position["posY"]) * canvas.height;
        var width = (table.position["width"]) * canvas.width;
        var height = (table.position["height"]) * canvas.height;

        const innerDistance = 5;
        var strokeX = posX + innerDistance;
        var strokeY = posY + innerDistance;
        var strokeWidth = width - (2 * innerDistance);
        var strokeHeight = height - (2 * innerDistance);

        ctx.fillStyle = '#f5f7f5';
        ctx.fillRect(posX, posY, width, height);
        ctx.lineWidth = 0.5;
        ctx.strokeStyle = '#606361';
        ctx.strokeRect(posX, posY, width, height);

        ctx.strokeStyle = '#ff7e05'; // orange
        ctx.fillStyle = '#ff7e05';
        ctx.lineWidth = 2;
        ctx.strokeRect(strokeX, strokeY, strokeWidth, strokeHeight);

        ctx.font = '25px sans-serif';

        var fontSize = 25;
        while (ctx.measureText(table.title + " (" + table.seats + ")").width > strokeWidth && fontSize > 15) {

          fontSize -= 2.5;
          ctx.font = fontSize + 'px sans-serif';
        }

        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';

        ctx.fillStyle = "#000000";
        ctx.fillText(table.title + " (" + table.seats + ")", posX + width / 2, posY + height / 2);
    }

    if(mouseDown !== undefined && mouseUp !== undefined) {
        ctx.fillStyle = "#00ff00";
        var width = mouseUp.x - mouseDown.x;
        var height = mouseUp.y - mouseDown.y;

        ctx.fillRect(mouseDown.x, mouseDown.y, width, height);
    }

}


/**
 * Wird aufgerufen, wenn der Benutzer auf den Button zum Hinzufügen eines neuen Tisches klickt.
 */
function addTable() {
    $("#addTable").removeClass("hidden");
    $("#addTable form").trigger("reset");
    $("#addTable_publish").removeClass("hidden");
}

/**
 * Wird aufgerufen, sobald der Benutzer den Button "Positionieren" im Widget zum Erstellen eines 
 * neuen Tisches drückt.
 */
function startPositioningOfNewTable() {
    $(".jsError").removeClass("hidden");


    if(document.getElementById("title").value == "") {
        $(".jsError").text("Bitte gib einen Tischnamen ein!");
        return;
    }

    for(table of allTables) {
        if(table.title == document.getElementById("title").value) {
            $(".jsError").text("Dieser Tischname ist bereits vorhanden!");
            return;
        }
    }

    var numberOfSeats = document.getElementById("numberOfSeats").value;
    if(parseInt(numberOfSeats) != numberOfSeats) {
        $(".jsError").text("Die Anzahl Plätze muss eine gültige Ganzzahl sein!");
        return;
    }

    if(numberOfSeats <= 0) {
        $(".jsError").text("Die Anzahl Plätze muss größer gleich 1 sein.");
        return;
    }

    $(".jsError").addClass("hidden");


    closeWidgets();
    $("#mainBar").addClass("hidden"); 
    $("#positioningBarOfNewTable").removeClass("hidden");

    isPositioningActive = true;
    canvas.addEventListener("mousedown", onMouseDown);
    canvas.addEventListener("mousemove", onMouseMove);
    canvas.addEventListener("mouseup", onMouseUp);
}

/**
 * Wird aufgerufen, wenn der Benutzer beim Bearbeiten eines Tisches den Button zum
 * Neu-Positionieren des Tisches drückt.
 */
function startEditPositioning() {
    $("#mainBar").addClass("hidden"); 
    $("#positioningBarForEdit").removeClass("hidden");

    hideWidgets();

    isPositioningActive = true;
    canvas.addEventListener("mousedown", onMouseDown);
    canvas.addEventListener("mousemove", onMouseMove);
    canvas.addEventListener("mouseup", onMouseUp);
}

/**
 * Wird aufgerufen, um alle Widgets zu schließen. Ferner wird der bearbeitete Tisch
 * auf undefined gesetzt.
 */
function closeWidgets() {
    $(".jsError").addClass("hidden");

    $(".widget").addClass("hidden");
    
    $(".multiplePublish").addClass("hidden");

    tableToEdit = undefined;
    
}

/**
 * 
 * Versteckt alle Widgets.
 */
function hideWidgets() {
    $(".widget").addClass("hidden");

}

/**
 * Wird aufgerufen, wenn der Benutzer beim Bearbeiten des Tisches beim Neupositionieren die Änderungen verwerfen möchte.
 * Diese Funktion macht dann die Änderungen rückgängig und bringt den Benutzer ins Edit-Widget zurück.
 */
function discardNewPositioning_backToWdiget() {
    saveNewPositioning_backToWidget();
    mouseDown = {
        "x": tableToEdit.position.posX * canvas.width,
        "y": tableToEdit.position.posY * canvas.height
    }
    
    mouseUp = {
        "x": (parseFloat(tableToEdit.position.posX) + parseFloat(tableToEdit.position.width)) * canvas.width,
        "y": (parseFloat(tableToEdit.position.posY) + parseFloat(tableToEdit.position.height)) * canvas.height
    }

}

/**
 * Wird aufgerufen, wenn der Benutzer beim Bearbeiten des Tisches beim Neupositionieren seine Änderungen übernehmen möchte.
 * Diese Funktion übernimmt die Änderungen und bringt den Benutzer ins Edit-Widget zurück.
 */
function saveNewPositioning_backToWidget() {
    $("#addTable").removeClass("hidden");
    $("#mainBar").removeClass("hidden");
    $("#positioningBarForEdit").addClass("hidden");

    isPositioningActive = false;
    canvas.removeEventListener("mousedown", onMouseDown);
    canvas.removeEventListener("mousemove", onMouseMove);
    canvas.removeEventListener("mouseup", onMouseUp);
}

/**
 * Wird aufgerufen, wenn der Benutzer das Positionieren (eines neu zu erstellenden Tisches) abbrechen möchte.
 */
function cancelPositioning() {
    $("#mainBar").removeClass("hidden");
    $("#positioningBarOfNewTable").addClass("hidden");

    isPositioningActive = false;
    canvas.removeEventListener("mousedown", onMouseDown);
    canvas.removeEventListener("mousemove", onMouseMove);
    canvas.removeEventListener("mouseup", onMouseUp);

    mouseUp = undefined;
    mouseDown = undefined;
}

/**
 * Wird aufgerufen, wenn der Benutzer den "Tisch speichern"-Button klickt.
 */
function submitNewTable() {
    var click1 = {
        "x": Math.min(mouseDown.x, mouseUp.x) / canvas.width,
        "y": Math.min(mouseDown.y, mouseUp.y) / canvas.height
    };
    var click2 = {
        "x": Math.max(mouseDown.x, mouseUp.x) / canvas.width,
        "y": Math.max(mouseDown.y, mouseUp.y) / canvas.height
    };
    var dimensions = {
        "width": click2.x - click1.x,
        "height": click2.y - click1.y
    }

    $("body").css("overflowY", "hidden");
    var elemsToAppend = "<input type='number' name='posX' step='0.01' class='hidden' value='"+click1.x+"' />";
    elemsToAppend += "<input type='number' name='posY' step='0.01' class='hidden' value='"+click1.y+"' />";
    elemsToAppend += "<input type='number' name='width' step='0.01' class='hidden' value='"+dimensions.width+"' />";
    elemsToAppend += "<input type='number' name='height' step='0.01' class='hidden' value='"+dimensions.height+"' />";
    elemsToAppend += "<input type='checkbox' name='isOutside' class='hidden' "+(isOutside() ? "checked" : "") +" />";


    $("#main form").append(elemsToAppend);
    
}


/**
 * Wird aufgerufen, wenn der Positionier-Modus aktiv ist und der Benutzer im Canvas 
 * einen Mausklick startet.
 */
function onMouseDown(event) {
    if(! isPositioningActive) return;

    isMouseDown = true;
    mouseDown = {
        "x": event.offsetX,
        "y": event.offsetY
    }
    mouseUp = undefined;
    console.log("DISABLED PROPERTY SET TO TRUE");
    $("#submitTableButton").prop("disabled", true);
}

function onMouseMove(event) {
    // falls die Maus nicht gedrückt ist, nichts tun
    if(! isMouseDown) return;

    mouseUp = {
        "x": event.offsetX,
        "y": event.offsetY
    }
}

/**
 * Wird aufgerufen, wenn der Positionier-Modus aktiv ist und der Benutzer im Canvas
 * einen Mausklick beendet. 
 */
function onMouseUp(event) {
    // soll nicht ausgeführt werden, wenn der Mausklick außerhalb des Canvas begonnen hat.
    if(! isPositioningActive) return;

    isMouseDown = false;


    if(Math.abs((mouseUp.x - mouseDown.x) / canvas.width) + Math.abs((mouseUp.y - mouseDown.y) / canvas.height) > 0.01) {
        $("#submitTableButton").prop("disabled", false);

    } else {
        mouseDown = undefined;
        mouseUp = undefined;
    }
}

function onClick(evt) {
    if(isPositioningActive) return;

    
    var x = evt.offsetX;
    var y = evt.offsetY;

    for(var table of allTables) {
        if(table.isOutside != isOutside()) continue;

        var posX = table.position.posX * canvas.width;
        var posY = table.position.posY * canvas.height;
        var width = table.position.width * canvas.width;
        var height = table.position.height * canvas.height;

        

        if(x >= posX && x <= posX + width && y >= posY && y <= posY + height) {
           startEdit(table);
        }
    }
}

function startEdit(tableObject) {
    $("#addTable").removeClass("hidden");
    $("#editTable_publish").removeClass("hidden");

    $("#title").val(tableObject.title);
    $("#numberOfSeats").val(tableObject.seats);
    $("#isDisabled").val(tableObject.isDisabled);

    $("#editTable_saveBtn").val(tableObject.id);
    $("#editTable_deleteBtn").val(tableObject.id);

    mouseDown = {
        "x": tableObject.position.posX * canvas.width,
        "y": tableObject.position.posY * canvas.height
    }
    
    mouseUp = {
        "x": (parseFloat(tableObject.position.posX) + parseFloat(tableObject.position.width)) * canvas.width,
        "y": (parseFloat(tableObject.position.posY) + parseFloat(tableObject.position.height)) * canvas.height
    }

    tableToEdit = tableObject;

}

function changeRoomDimensions() {
    $("#changeRoomDimensions").removeClass("hidden");
    $("#saveRoomDimensionsBtn").val($("#insideOutside").val());


    $("#roomWidth").val(isOutside() ? width_outside : width_inside);
    $("#roomDepth").val(isOutside() ? depth_outside : depth_inside);
}