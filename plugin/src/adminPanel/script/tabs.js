
function openTab(element, id) {
    var allTabButtons = document.getElementsByClassName(".tabListBtn");
    for(var c = 0; c < allTabButtons.length; c++) {
        allTabButtons[c].classList.remove("activeTabBtn");
    }
    element.classList.add("activeTabBtn");

    hideAll();
    document.getElementById(id).classList.remove("hidden");
}

function hideAll() {
    var allTabs = document.getElementsByClassName("tabElement");
    for(var c = 0; c < allTabs.length; c++) {
        allTabs[c].classList.add("hidden");
    }
}