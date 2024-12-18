document.addEventListener('DOMContentLoaded', function() {
    // Initialize date picker
    flatpickr("#datePicker", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        minDate: "today",
        time_24hr: false
    });

    // Create Town Hall
    const createTownHallBtn = document.getElementById('createTownHallBtn');
    if (createTownHallBtn) {
        createTownHallBtn.addEventListener('click', function() {
            const form = document.getElementById('createTownHallForm');
            const formData = new FormData(form);

            fetch('api/create_townhall.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', 'Town Hall created successfully!');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showToast('error', data.message || 'Error creating town hall');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'An error occurred while creating the town hall');
            });
        });
    }

    // Register for Town Hall
    document.querySelectorAll('.register-btn').forEach(button => {
        button.addEventListener('click', function() {
            const townhallId = this.dataset.id;
            
            fetch('api/register_townhall.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    townhall_id: townhallId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', 'Successfully registered for the town hall!');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showToast('error', data.message || 'Error registering for town hall');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'An error occurred while registering');
            });
        });
    });

    // Unregister from Town Hall
    document.querySelectorAll('.unregister-btn').forEach(button => {
        button.addEventListener('click', function() {
            if (!confirm('Are you sure you want to unregister from this town hall?')) {
                return;
            }

            const townhallId = this.dataset.id;
            
            fetch('api/unregister_townhall.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    townhall_id: townhallId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', 'Successfully unregistered from the town hall');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showToast('error', data.message || 'Error unregistering from town hall');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'An error occurred while unregistering');
            });
        });
    });

    // View Town Hall Details
    document.querySelectorAll('.view-townhall').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const townhallId = this.dataset.id;
            
            fetch(`api/get_townhall.php?id=${townhallId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const townhall = data.townhall;
                    const modal = document.getElementById('viewTownHallModal');
                    const detailsDiv = document.getElementById('townhallDetails');

                    // Format the details HTML
                    let html = `
                        <h3>${townhall.title}</h3>
                        <div class="mb-4">
                            <span class="badge bg-${getStatusBadgeClass(townhall.status)}">${townhall.status}</span>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Details</h5>
                                <p><strong>Date:</strong> ${formatDate(townhall.date)}</p>
                                <p><strong>Location:</strong> ${townhall.location}</p>
                                <p><strong>Capacity:</strong> ${townhall.capacity}</p>
                                <p><strong>Registered:</strong> ${townhall.registered_count}</p>
                                ${townhall.meeting_link ? `<p><strong>Meeting Link:</strong> <a href="${townhall.meeting_link}" target="_blank">${townhall.meeting_link}</a></p>` : ''}
                            </div>
                            <div class="col-md-6">
                                <h5>Description</h5>
                                <p>${townhall.description}</p>
                            </div>
                        </div>`;

                    if (townhall.agenda) {
                        html += `
                        <div class="mb-4">
                            <h5>Agenda</h5>
                            <p>${townhall.agenda}</p>
                        </div>`;
                    }

                    if (townhall.documents && townhall.documents.length > 0) {
                        html += `
                        <div class="mb-4">
                            <h5>Documents</h5>
                            <ul class="list-unstyled">
                                ${townhall.documents.map(doc => `
                                    <li class="mb-2">
                                        <i class="bi bi-file-earmark-text me-2"></i>
                                        <a href="${doc.file_path}" target="_blank">${doc.title}</a>
                                        <small class="text-muted ms-2">Uploaded by ${doc.uploader_name}</small>
                                    </li>
                                `).join('')}
                            </ul>
                        </div>`;
                    }

                    if (townhall.updates && townhall.updates.length > 0) {
                        html += `
                        <div>
                            <h5>Updates</h5>
                            <div class="list-group">
                                ${townhall.updates.map(update => `
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <small class="text-muted">${formatDate(update.created_at)}</small>
                                            <small class="text-muted">By ${update.creator_name}</small>
                                        </div>
                                        <p class="mb-1">${update.update_text}</p>
                                    </div>
                                `).join('')}
                            </div>
                        </div>`;
                    }

                    detailsDiv.innerHTML = html;
                    new bootstrap.Modal(modal).show();
                } else {
                    showToast('error', data.message || 'Error loading town hall details');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'An error occurred while loading town hall details');
            });
        });
    });
});

// Helper Functions
function formatDate(dateString) {
    return new Date(dateString).toLocaleString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit'
    });
}

function getStatusBadgeClass(status) {
    switch (status) {
        case 'upcoming':
            return 'primary';
        case 'ongoing':
            return 'success';
        case 'completed':
            return 'secondary';
        case 'cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
}

function showToast(type, message) {
    const toast = new bootstrap.Toast(document.createElement('div'));
    toast._element.classList.add('toast', 'position-fixed', 'bottom-0', 'end-0', 'm-3');
    toast._element.setAttribute('role', 'alert');
    toast._element.setAttribute('aria-live', 'assertive');
    toast._element.setAttribute('aria-atomic', 'true');
    
    toast._element.innerHTML = `
        <div class="toast-header bg-${type === 'success' ? 'success' : 'danger'} text-white">
            <strong class="me-auto">${type === 'success' ? 'Success' : 'Error'}</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            ${message}
        </div>
    `;
    
    document.body.appendChild(toast._element);
    toast.show();
    
    setTimeout(() => {
        toast._element.remove();
    }, 5000);
}
