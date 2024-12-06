// Toggle the visibility of the review form
function toggleForm() {
    const form = document.getElementById("reviewForm");
    form.style.display = form.style.display === "none" ? "block" : "none";
}

// Handle form submission
document.getElementById("addReviewForm").addEventListener("submit", async (event) => {
    event.preventDefault(); // Prevent default form submission

    const movieName = document.getElementById("movieName").value.trim();
    const reviewBody = document.getElementById("reviewBody").value.trim();
    const reviewRating = document.getElementById("reviewRating").value.trim();
    const reviewerName = document.getElementById("reviewerName").value.trim() || "Anonymous";

    if (!movieName || !reviewBody || !reviewRating) {
        alert("Please fill in all required fields.");
        return;
    }

    try {
        const response = await fetch("/team_2/StreamBuddy/Backend/add_review.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
                movie_name: movieName,
                review_body: reviewBody,
                rating: reviewRating,
                reviewer_name: reviewerName,
            }),
        });

        const result = await response.json();
        if (result.success) {
            alert(result.message);
            toggleForm(); // Hide form
            fetchReviews(); // Refresh reviews list
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error("Error submitting review:", error);
        alert("An error occurred while submitting the review.");
    }
});

// Fetch and display reviews
async function fetchReviews() {
    try {
        const response = await fetch("/team_2/StreamBuddy/Backend/get_review.php");
        const result = await response.json();

        const reviewsContainer = document.getElementById("reviews");
        reviewsContainer.innerHTML = ""; // Clear existing reviews

        if (result.success && result.reviews.length > 0) {
            result.reviews.forEach((review) => {
                const reviewCard = document.createElement("div");
                reviewCard.className = "review-card";
                reviewCard.innerHTML = `
                    <div class="stars">${"?".repeat(review.rating)}${"?".repeat(5 - review.rating)}</div>
                    <h2>${review.movie_name}</h2>
                    <p>${review.review_body}</p>
                    <div class="reviewer-info">
                        <span>${review.reviewer_name || "Anonymous"}</span>
                        <span>${new Date(review.review_date).toLocaleString()}</span>
                    </div>
                `;
                reviewsContainer.appendChild(reviewCard);
            });
        } else {
            reviewsContainer.innerHTML = "<p>No reviews yet. Be the first to add one!</p>";
        }
    } catch (error) {
        console.error("Error fetching reviews:", error);
        alert("An error occurred while fetching reviews.");
    }
}

// Fetch reviews on page load
fetchReviews();
