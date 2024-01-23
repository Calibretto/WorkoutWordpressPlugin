function equipmentValue(id) {
    var e = document.getElementById(id);
    if (e != null) {
        return e.value;
    }

    return null;
}

function equipmentCheckValue() {
    var min = equipmentValue("equipment_value_min");
    var max = equipmentValue("equipment_value_max");
    var step = equipmentValue("equipment_value_step");
    var unit = equipmentValue("equipment_units");

    var null_count = 0;
    if ((min == null) || (min.length == 0)) null_count++;
    if ((max == null) || (max.length == 0)) null_count++;
    if ((step == null) || (step.length == 0)) null_count++;
    if (unit == "empty") null_count++;
    
    return (null_count == 0) || (null_count == 4);
}

function submitEquipmentForm(id) {
    var form = document.getElementById(id);
    if (form != null) {
        form.submit();
    } else {
        // Display general error.
        displayNotice('equipment-general-error');        
    }
}

function addEquipment() {
    hideAllNotices();

    var name = equipmentValue("equipment_name");
    if (name != null) {
        if (name.length > 0) {
            if (equipmentCheckValue() == false) {
                if (confirm("You haven't filled out the value section completely.\n\nPress OK to create a piece of equipment with no value.")) {
                    submitEquipmentForm('add-equipment');
                }
            } else {
                submitEquipmentForm('add-equipment');
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
        submitEquipmentForm('delete-equipment');
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