$(document).ready(function () {
    $("#signupForm").submit(function (event) {
        event.preventDefault(); // Prevent the default form submission behavior

        // Get the selected user type
        var userType = $("#userType").val();

        // Redirect to the appropriate page based on the user type
        if (userType === "company") {
            window.location.href = "company_info.html";
        } else if (userType === "passenger") {
            window.location.href = "passenger_info.html";
        }
    });
});