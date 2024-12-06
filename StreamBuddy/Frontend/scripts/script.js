function createNewList() {
    alert("Creating a new list feature is not yet implemented.");
}

function editList(listID) {
    console.log("Edit list called for ID:", listID);
    const listName = prompt("Enter new name for the list:");
    if (listName) {
        console.log("New list name:", listName);
        const formData = new FormData();
        formData.append('action', 'edit_list');
        formData.append('listID', listID);
        formData.append('listName', listName);

        fetch('../../Backend/list_operations.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('List updated successfully!');
                loadUserLists(); // Refresh the list display
            } else {
                alert(data.message || 'Error updating list');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the list.');
        });
    }
}

function deleteList() {
    alert("Deleting a list is not yet implemented.");
}

function toggleEditForm() {
    alert("Edit profile form is not yet implemented.");
}

function openCreateListModal() {
    document.getElementById('createListModal').style.display = 'block';
    loadMediaItems();
}

function closeCreateListModal() {
    document.getElementById('createListModal').style.display = 'none';
}

let currentPage = 1;
const itemsPerPage = 20;

function loadMediaItems(genre = '', page = 1) {
    const formData = new FormData();
    formData.append('action', 'get_media');
    formData.append('page', page);
    if (genre) {
        formData.append('genre', genre);
    }

    fetch('../../Backend/list_operations.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayMediaItems(data.data);
            updatePagination(data.totalPages);
        }
    })
    .catch(error => console.error('Error:', error));
}

function displayMediaItems(mediaItems) {
    const mediaList = document.getElementById('mediaList');
    mediaList.innerHTML = '';

    mediaItems.forEach(item => {
        const mediaElement = document.createElement('div');
        mediaElement.className = 'media-item';
        mediaElement.innerHTML = `
            <input type="checkbox" name="mediaItems" value="${item.mediaID}">
            <img src="${item.pictures}" alt="${item.title}">
            <p>${item.title}</p>
        `;
        mediaList.appendChild(mediaElement);
    });
}

function filterMediaByGenre() {
    const genre = document.getElementById('genreFilter').value;
    loadMediaItems(genre);
}

function loadUserLists() {
    fetch('../../Backend/list_operations.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayUserLists(data.data);
            }
        })
        .catch(error => console.error('Error:', error));
}

function displayUserLists(lists) {
    const listsContainer = document.getElementById('userLists');
    listsContainer.innerHTML = '';

    lists.forEach(list => {
        const listElement = document.createElement('div');
        listElement.className = 'list';
        listElement.innerHTML = `
            <h3>${list.name}</h3>
            <p>Items: ${list.item_count}</p>
            <div class="list-actions">
                <button onclick="editList(${list.listID})">Edit</button>
                <button onclick="deleteList(${list.listID})">Delete</button>
            </div>
        `;
        listsContainer.appendChild(listElement);
    });
}

// Add event listener for form submission
document.getElementById('createListForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const listName = document.getElementById('listName').value;
    const selectedMedia = Array.from(document.querySelectorAll('input[name="mediaItems"]:checked'))
        .map(checkbox => checkbox.value);

    const formData = new FormData();
    formData.append('action', 'create_list');
    formData.append('listName', listName);
    formData.append('mediaItems', JSON.stringify(selectedMedia));

    fetch('../../Backend/list_operations.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('List created successfully!');
            closeCreateListModal();
            loadUserLists();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while creating the list.');
    });
});

// Load user lists when the page loads
document.addEventListener('DOMContentLoaded', () => {
    loadUserLists();
});

let userData = null;

function toggleEditMode() {
    const profileView = document.getElementById('profileView');
    const profileForm = document.getElementById('profileForm');
    
    // Show edit form and hide view mode
    profileView.style.display = 'none';
    profileForm.style.display = 'block';
    
    // Populate form with current values
    document.getElementById('username').value = userData.username;
    document.getElementById('email').value = userData.email;
}

function cancelEdit() {
    const profileView = document.getElementById('profileView');
    const profileForm = document.getElementById('profileForm');
    
    // Show view mode and hide edit form
    profileView.style.display = 'block';
    profileForm.style.display = 'none';
    
    // Clear password fields
    document.getElementById('currentPassword').value = '';
    document.getElementById('newPassword').value = '';
}

// Update the DOMContentLoaded event listener for profile functionality
document.addEventListener('DOMContentLoaded', () => {
    // Load user profile data and reviews
    fetch('../../Backend/Profile.php')
        .then(response => response.json())
        .then(data => {
            userData = data.userData;
            
            // Update display values
            document.getElementById('displayUsername').textContent = userData.username;
            document.getElementById('displayEmail').textContent = userData.email;
            document.getElementById('memberSince').textContent = 
                new Date(userData.createdate).toLocaleDateString();
            
            // Load reviews
            const reviewsContainer = document.getElementById('userReviews');
            if (reviewsContainer) {
                reviewsContainer.innerHTML = '';
                
                data.reviews.forEach(review => {
                    const reviewElement = document.createElement('div');
                    reviewElement.className = 'review-item';
                    reviewElement.innerHTML = `
                        <h3>${review.title}</h3>
                        <p>Rating: ${review.rating}/10</p>
                        <p>"${review.text}"</p>
                    `;
                    reviewsContainer.appendChild(reviewElement);
                });
            }
        })
        .catch(error => {
            console.error('Error loading profile data:', error);
        });

    // Handle profile form submission
    const form = document.getElementById('profileForm');
    if (form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            const formData = new FormData(form);
            
            fetch('../../Backend/Profile_update.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Profile updated successfully!');
                    window.location.reload();
                } else {
                    alert(data.message || 'Error updating profile');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the profile.');
            });
        });
    }
});

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}