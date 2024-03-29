var activeEdit = null;
var formContentSetter;

function initQuickEdit(setFormContent) {
    formContentSetter = setFormContent;
}

function edit(id) {
    if(activeEdit !== null) {
        cancelEdit();
    }
    if(formContentSetter === undefined) {
        console.error("initQuickEdit wurde nie aufgerufen!");
    }

    activeEdit = {
        id: id,
        innerHTML: document.getElementById("row_"+id).innerHTML
    };

    $(".sortButton").addClass("hidden");
    document.getElementById("editBtn_"+id).classList.add("hidden");
    document.getElementById("deleteBtn_"+id).classList.add("hidden");
	document.getElementById("saveBtn_"+id).classList.remove("hidden");
    document.getElementById("cancelBtn_"+id).classList.remove("hidden");
    

    formContentSetter(document.getElementById("row_"+id), id);
    
}


function cancelEdit() {
    if(activeEdit === null) return;

    var id = activeEdit["id"];

    $(".sortButton").removeClass("hidden");
    document.getElementById("editBtn_"+id).classList.remove("hidden");
    document.getElementById("deleteBtn_"+id).classList.remove("hidden");
	document.getElementById("saveBtn_"+id).classList.add("hidden");
    document.getElementById("cancelBtn_"+id).classList.add("hidden");

    document.getElementById("row_"+id).innerHTML = activeEdit["innerHTML"];

    activeEdit = null;
}

$(document).ready(function() {
    $(document).keydown(function(event) {
        if(event.keyCode == 27) {
            cancelEdit();
        }
        if(event.keyCode == 13) {
            event.preventDefault();
        }
    });
});