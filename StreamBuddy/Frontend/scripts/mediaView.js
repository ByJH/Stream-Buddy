document.addEventListener("DOMContentLoaded", () => {
    const movieGrid = document.querySelector(".movie-grid");
    const categoryButtons = document.querySelectorAll(".tag");
    const searchButton = document.getElementById("searchButton");
    const searchInput = document.getElementById("searchInput");
    const streamingCheckboxes = document.querySelectorAll(".filter-group input[type='checkbox']");
    
    let allMovies = []; // Store all movies fetched from the backend
    let activePlatforms = []; // Store selected platforms
    let activeCategory = "All"; // Store the selected category (default: All)

    // Function to fetch movies from the backend
    const fetchMovies = () => {
        const hostname = window.location.hostname;
        const url = new URL(`https://${hostname}/team_2/StreamBuddy/Backend/viewMedia.php`);

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    allMovies = data.data; // Store all fetched movies
                    applyFilters(); // Apply filters on the fetched movies
                } else {
                    movieGrid.innerHTML = `<p>${data.message}</p>`;
                }
            })
            .catch(error => {
                console.error("Fetch error:", error);
                movieGrid.innerHTML = `<p>Error loading movies: ${error.message}</p>`;
            });
    };

    // Function to display movies in the grid
    const displayMovies = (movies) => {
        movieGrid.innerHTML = "";

        if (!movies || movies.length === 0) {
            movieGrid.innerHTML = "<p>No movies found.</p>";
            return;
        }

        movies.forEach(movie => {
            const imagePath = movie.pictures.startsWith('/') ? movie.pictures : '/' + movie.pictures;
            
            const movieCard = document.createElement("div");
            movieCard.classList.add("movie-card");
            movieCard.innerHTML = `
                <div class="thumbnail">
                    <img src="${imagePath}" alt="${movie.title}" 
                         onerror="this.src='/StreamBuddy/Frontend/images/default-pfp.png'"
                         loading="lazy">
                </div>
                <div class="details">
                    <p class="movie-name">${movie.title}</p>
                    <p class="genre">${movie.genre}</p>
                    <p class="platform">${movie.streaming_platform}</p>
                </div>
            `;

            movieCard.addEventListener('click', () => {
                const modal = document.getElementById('movieDetailsModal');
                const closeBtn = document.getElementById('closeModal');
                
                document.getElementById('movieImage').src = imagePath;
                document.getElementById('movieTitle').textContent = movie.title;
                document.getElementById('movieReleaseDate').textContent = movie.releasedate;
                document.getElementById('movieGenre').textContent = movie.genre;
                document.getElementById('movieDescription').textContent = movie.description;
                document.getElementById('moviePlatform').textContent = movie.streaming_platform;
                
                modal.style.display = 'block';

                closeBtn.onclick = () => {
                    modal.style.display = 'none';
                };

                window.onclick = (event) => {
                    if (event.target === modal) {
                        modal.style.display = 'none';
                    }
                };
            });

            movieGrid.appendChild(movieCard);
        });
    };

    // Function to apply all filters (category, platform, search)
    const applyFilters = () => {
        let filteredMovies = allMovies;

        // Filter by category
        if (activeCategory !== "All") {
            filteredMovies = filteredMovies.filter(movie => movie.category === activeCategory);
        }

        // Filter by streaming platforms
        if (activePlatforms.length > 0) {
            filteredMovies = filteredMovies.filter(movie => activePlatforms.includes(movie.streaming_platform));
        }

        // Filter by search query
        const query = searchInput.value.trim().toLowerCase();
        if (query) {
            filteredMovies = filteredMovies.filter(movie =>
                movie.title.toLowerCase().includes(query) || movie.genre.toLowerCase().includes(query)
            );
        }

        displayMovies(filteredMovies); // Display the filtered movies
    };

    // Event listener for category buttons
    categoryButtons.forEach(button => {
        button.addEventListener("click", () => {
            categoryButtons.forEach(btn => btn.classList.remove("selected"));
            button.classList.add("selected");
            activeCategory = button.textContent.trim();
            applyFilters(); // Apply filters when category changes
        });
    });

    // Event listener for streaming platform checkboxes
    streamingCheckboxes.forEach(checkbox => {
        checkbox.addEventListener("change", () => {
            activePlatforms = Array.from(streamingCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
            applyFilters(); // Apply filters when platform changes
        });
    });

    // Event listener for search button
    searchButton.addEventListener("click", applyFilters);

    // Event listener for Enter key in search input
    searchInput.addEventListener("keydown", (event) => {
        if (event.key === "Enter") {
            applyFilters(); // Apply filters when search input changes
        }
    });

    // Initial fetch to load movies
    fetchMovies();
});