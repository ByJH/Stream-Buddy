document.getElementById("loginForm").addEventListener("submit", function(event) {
    event.preventDefault();

    const loginInput = document.getElementById("login").value;
    const password = document.getElementById("password").value;

    //demo login
    if ((loginInput === "admin" || loginInput === "admin@example.com") && password === "password") {
        alert("Login successful!");
        window.location.href = "home.html"; 
    } else {
        document.getElementById("error-message").textContent = "Invalid username or password.";
    }
});
