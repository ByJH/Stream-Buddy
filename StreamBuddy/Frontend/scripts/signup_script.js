document.getElementById("signupForm").addEventListener("submit", function(event) {
    event.preventDefault();

    const email = document.getElementById("email").value;
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;

    if (email && username && password) {
        alert("Signup successful!");
        window.location.href = "Login.html";
    } else {
        document.getElementById("error-message").textContent = "Please fill out all fields.";
    }
});
