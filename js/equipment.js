function addEquipment() {
    hideAllNotices();

    var e = document.getElementById("equipment_name");
    if (e != null) {
        if (e.value.length > 0) {
            var form = document.getElementById('add-equipment');
            if (form != null) {
                form.submit();
            } else {
                // Display general error.
                displayNotice('equipment-general-error');        
            }
        } else {
            displayNotice('equipment-name-error');
        }
    } else {
        // Display general error.
        displayNotice('equipment-general-error');
    }
}

function displayNotice(id) {
    var e = document.getElementById(id);
    if (e != null) {
        e.style.display = 'block';
    }
}

function hideAllNotices() {
    hideNotice('equipment-general-error');
    hideNotice('equipment-name-error');
    hideNotice('equipment-added-success');
}

function hideNotice(id) {
    var e = document.getElementById(id);
    if (e != null) {
        e.style.display = 'none';
    }
}