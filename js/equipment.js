function equipment_value(id) {
    var e = document.getElementById(id);
    if (e != null) {
        return e.value;
    }

    return null;
}

function equipment_check_value() {
    var min = equipment_value("equipment_value_min");
    var max = equipment_value("equipment_value_max");
    var step = equipment_value("equipment_value_step");
    var unit = equipment_value("equipment_units");

    var null_count = 0;
    if ((min == null) || (min.length == 0)) null_count++;
    if ((max == null) || (max.length == 0)) null_count++;
    if ((step == null) || (step.length == 0)) null_count++;
    if (unit == "empty") null_count++;
    
    return (null_count == 0) || (null_count == 4);
}

function submit_equipment_form() {
    var form = document.getElementById('add-equipment');
    if (form != null) {
        form.submit();
    } else {
        // Display general error.
        displayNotice('equipment-general-error');        
    }
}

function addEquipment() {
    hideAllNotices();

    var name = equipment_value("equipment_name");
    if (name != null) {
        if (name.length > 0) {
            if (equipment_check_value() == false) {
                if (confirm("You haven't filled out the value section completely.\n\nPress OK to create a piece of equipment with no value.")) {
                    submit_equipment_form();
                }
            } else {
                submit_equipment_form();
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