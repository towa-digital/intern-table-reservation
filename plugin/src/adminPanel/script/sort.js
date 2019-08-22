
function sort(sortBtn, tableObj, sortAscending) {
    var id = sortBtn.parentNode.id;
    
    Array.prototype.slice.call(tableObj.getElementsByClassName("toSort"))
        .map(function (x) { return tableObj.removeChild(x); })
        .sort(function (xRow, yRow) { 
            var x = xRow.getElementsByClassName(id)[0];
            var y = yRow.getElementsByClassName(id)[0];


            if(x === undefined || y === undefined || x.innerHTML === y.innerHTML) return 0;
            if(x.innerHTML > y.innerHTML) return sortAscending ? -1 : 1;
            else return sortAscending ? 1 : -1;
        })
        .forEach(function (x) { tableObj.appendChild(x); });
}