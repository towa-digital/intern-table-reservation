/**
 * Funktion, welche von einem Button aufgerufen wird, um die entsprechende Spalte in 
 * einer Liste zu sortieren.
 * sortBtn ist hierbei einfach "this" im onclick-Eventhandler, diese Variable zeigt
 * also auf den Button, welcher diese Funktion aufgerufen hat
 * tableObj ist eine Referenz auf das <table>-Objekt, welches sortiert werden soll
 * sortAscending ist ein boolescher Wahrheitswert, welcher true ist, wenn aufsteigend
 * sortiert werden soll
 * sortType ist entweder "string" (Standardwert), "number" oder "date", je nachdem, 
 * von welchem Typ die Daten sind, welche sortiert werden sollen
 */
function sort(sortBtn, tableObj, sortAscending, sortType) {
    if(sortType === undefined) sortType = "string";

    var id = sortBtn.parentNode.id;
    
    Array.prototype.slice.call(tableObj.getElementsByClassName("toSort"))
        .map(function (x) { return tableObj.removeChild(x); })
        .sort(function (xRow, yRow) { 
            var x = xRow.getElementsByClassName(id)[0];
            var y = yRow.getElementsByClassName(id)[0];

            var xVal, yVal;
            if(sortType === "string") {
                xVal = x.innerHTML;
                yVal = y.innerHTML;
            } else if (sortType === "number") {
                xVal = parseFloat(x.innerHTML);
                yVal = parseFloat(y.innerHTML);

                if(isNaN(xVal)) xVal = -1;
                if(isNaN(yVal)) yVal = -1;
            } else if(sortType === "date") {
                xVal = getDateFromAustrianString(x.innerHTML).getTime();
                yVal = getDateFromAustrianString(y.innerHTML).getTime();
            } else {
                console.error("unknown sortType");
            }


            if(x === undefined || y === undefined || xVal === yVal) return 0;
            if(xVal > yVal) return sortAscending ? 1 : -1;
            else return sortAscending ? -1 : 1;
        })
        .forEach(function (x) { tableObj.appendChild(x); });
}

function getDateFromAustrianString(string) {
    var stringParts = string.split(/[\s.:]+/);

    var date = new Date(parseInt(stringParts[2]), parseInt(stringParts[1]), parseInt(stringParts[0]), parseInt(stringParts[3]), parseInt(stringParts[4]));

    return date;
}