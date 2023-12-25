$(document).ready(function () {
    function updateFormAction() {
        var userType = $("#userType").val();
        var form = $("#signupForm");
        var email = $("#email").val();

        if (userType === "company") {
            console.log("Setting action for company");
            form.attr("action", "company_info.html?email=" + encodeURIComponent(email));
        } else if (userType === "passenger") {
            console.log("Setting action for passenger");
            form.attr("action", "passenger_info.html?email=" + encodeURIComponent(email));
        }
    }

    updateFormAction();
    $("#userType").on("change", updateFormAction);
});