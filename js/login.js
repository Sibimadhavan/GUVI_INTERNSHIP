$("#loginBtn").click(function () {

    let email = $("#email").val().trim();
    let password = $("#password").val().trim();

    if (!email || !password) {
        $("#errorMsg").text("All fields required");
        return;
    }

    $.ajax({
        url: "/INTERNSHIP/PHP/login.php",
        type: "POST",
        contentType: "application/json",
        dataType: "json",
        data: JSON.stringify({
            email: email,
            password: password
        }),
        success: function (response) {

            if (response.status === "success") {
                localStorage.setItem("session_token", response.token);
                localStorage.setItem("user_id", response.user_id);
                window.location.href = "profile.html";
            } else {
                $("#errorMsg").text("Invalid login");
            }
        }
    });
});
