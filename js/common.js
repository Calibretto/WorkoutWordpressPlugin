function elementValue(id) {
    var e = document.getElementById(id);
    if (e != null) {
        return e.value;
    }

    return null;
}

function submitForm(id, generalErrorId) {
    var form = document.getElementById(id);
    if (form != null) {
        form.submit();
    } else {
        // Display general error.
        displayNotice(generalErrorId);        
    }
}

function displayNotice(id) {
    var e = document.getElementById(id);
    if (e != null) {
        e.style.display = 'block';
    }
}

function hideNotice(id) {
    var e = document.getElementById(id);
    if (e != null) {
        e.style.display = 'none';
    }
}