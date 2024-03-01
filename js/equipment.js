function equipmentCheckValue() {
    var min = elementValue("equipment_value_min");
    var max = elementValue("equipment_value_max");
    var step = elementValue("equipment_value_step");
    var unit = elementValue("equipment_units");

    var null_count = 0;
    if ((min == null) || (min.length == 0)) null_count++;
    if ((max == null) || (max.length == 0)) null_count++;
    if ((step == null) || (step.length == 0)) null_count++;
    if (unit == "empty") null_count++;
    
    return (null_count == 0) || (null_count == 4);
}

function addEquipment() {
    hideAllNotices();

    var name = elementValue("equipment_name");
    if (name != null) {
        if (name.length > 0) {
            if (equipmentCheckValue() == false) {
                if (confirm("You haven't filled out the value section completely.\n\nPress OK to create a piece of equipment with no value.")) {
                    submitForm('add-equipment', 'equipment-general-error');
                }
            } else {
                submitForm('add-equipment', 'equipment-general-error');
            }
        } else {
            displayNotice('equipment-name-error');
        }
    } else {
        // Display general error.
        displayNotice('equipment-general-error');
    }
}

function deleteEquipment(id) {
    hideAllNotices();
    if(confirm("Delete this piece of equipment?") == false) {
        return;
    }

    var e = document.getElementById('equipment_delete');
    if (e != null) {
        e.value = id;
        submitForm('delete-equipment', 'equipment-general-error');
    } else {
        // Display general error.
        displayNotice('equipment-general-error');
    }
}

function editEquipment(id) {
    hideAllNotices();

    var e = document.getElementById('equipment_edit');
    if (e != null) {
        e.value = id;
        submitForm('edit-equipment', 'equipment-general-error');
    } else {
        // Display general error.
        displayNotice('equipment-general-error');
    }
}

function updateEquipment() {
    hideAllNotices();

    var name = elementValue("equipment_name");
    if (name != null) {
        if (name.length > 0) {
            if (equipmentCheckValue() == false) {
                if (confirm("You haven't filled out the value section completely.\n\nPress OK to create a piece of equipment with no value.")) {
                    submitForm('update-equipment', 'equipment-general-error');
                }
            } else {
                submitForm('update-equipment', 'equipment-general-error');
            }
        } else {
            displayNotice('equipment-name-error');
        }
    } else {
        // Display general error.
        displayNotice('equipment-general-error');
    }
}

function cancelEquipmentEdit() {
    if(confirm("Are you sure you want to cancel?") == false) {
        return;
    }

    window.location = document.location.href;
}

function hideAllNotices() {
    hideNotice('equipment-general-error');
    hideNotice('equipment-name-error');
}