const AccCSV = document.getElementById("AccCSV");
$("#csvFileValidate").css("visibility", "hidden");

$("#labelAccCSV").click(function () {
    $("#csvFileValidate").css("visibility", "hidden");
});
AccCSV.addEventListener("change", () => {
    const file = AccCSV.files[0];
    if (file) {
        var fileNameParts = file.name.split(".");
        var fileExtension = fileNameParts[fileNameParts.length - 1];

        if (fileExtension === "csv") {
            $("#js-modal-csv-upload").show();
            $(".js-modal-csv-upload-overlay").show();
            $("#myFormCsv").submit(); // IDでフォームを送信
        } else {
            $("#csvFileValidate").css("visibility", "visible");
        }
    }
});
