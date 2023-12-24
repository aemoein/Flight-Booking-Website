$(document).ready(function () {
    $("#passengerInfoForm").submit(function (event) {
        event.preventDefault();
        window.location.href = "passenger_dashboard.html";
    });
});

$(document).ready(function () {
    $("#companyInfoForm").submit(function (event) {
        event.preventDefault(); 
        window.location.href = "company_dashboard.html";
    });
});
