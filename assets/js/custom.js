function isFileUploaded(id) {
    var inputElement = document.getElementById(id);
    var files = inputElement.files;
    if (files.length == 0) {
        alert("Please select a file.");
        return false;
    }
    else {
        return true;
    }
}