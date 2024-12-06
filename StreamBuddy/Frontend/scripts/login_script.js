document.getElementById("loginForm").addEventListener("submit", function(event) {
    event.preventDefault();

    const loginInput = document.getElementById("login").value;
    const password = document.getElementById("password").value;

    const formData = new FormData();
    formData.append("login", loginInput);
    formData.append("password", password);

    // Send the form data to the server via AJAX
    fetch('../../Backend/login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())  // Parse the JSON response
    .then(data => {
        if (data.status === "success") {
            alert(data.message);  // Show success message
            // Check if the user is admin or regular user and redirect accordingly
            if (loginInput === 'Admin') {
                window.location.href = "../pages/Home.html"; // Redirect to admin page
            } else {
                window.location.href = "../pages/Home.html"; // Redirect to home page
            }
        } else {
            // Show error message on failure
            document.getElementById("error-message").textContent = data.message;
        }
    })
    .catch(error => {
        console.error("Error:", error);
    });
});
