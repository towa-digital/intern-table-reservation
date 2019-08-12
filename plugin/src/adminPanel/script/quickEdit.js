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

    document.getElementById("editBtn_"+id).classList.add("hidden");
	document.getElementById("saveBtn_"+id).classList.remove("hidden");
    document.getElementById("cancelBtn_"+id).classList.remove("hidden");
    

    formContentSetter(document.getElementById("row_"+id));
    
}


function cancelEdit() {
    var id = activeEdit["id"];

    document.getElementById("editBtn_"+id).classList.remove("hidden");
	document.getElementById("saveBtn_"+id).classList.add("hidden");
    document.getElementById("cancelBtn_"+id).classList.add("hidden");

    document.getElementById("row_"+id).innerHTML = activeEdit["innerHTML"];

    activeEdit = null;
}