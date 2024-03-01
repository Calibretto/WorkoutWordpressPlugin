function addWarmup() {
    hideAllNotices();

    var name = elementValue("warmup_name");
    if (name != null) {
        if (name.length <= 0) {
            displayNotice('warmup-name-error');
        } else {
            submitForm('add-warmup', 'warmup-general-error');
        }
    } else {
        // Display general error.
        displayNotice('warmup-general-error');
    }
}

function hideAllNotices() {
    hideNotice('warmup-general-error');
    hideNotice('warmup-name-error');
}