$(document).ready(function () {

    const token = localStorage.getItem("session_token");

    if (!token) {
        window.location.href = "login.html";
        return;
    }

    $.ajax({
        url: "/GUVI_INTERNSHIP/php/profile.php",
        type: "GET",
        headers: {
            "Authorization": "Bearer " + token
        },
        success: function (res) {
            $("#username").val(res.username);
            $("#email").val(res.email);
            $("#dob").val(res.dob);
            $("#age").val(res.age);
            $("#contact").val(res.contact);
        },
        error: function () {
            alert("Session expired");
            localStorage.removeItem("token");
            window.location.href = "login.html";
        }
    });

    $("#logoutBtn").click(function () {
        localStorage.removeItem("token");
        window.location.href = "login.html";
    });

});