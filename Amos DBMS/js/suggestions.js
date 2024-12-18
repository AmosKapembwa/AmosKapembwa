document.addEventListener('DOMContentLoaded', function() {
    // Initial load
    loadSuggestions();
    
    // Event listeners for filters
    document.getElementById('categoryFilter').addEventListener('change', loadSuggestions);
    document.getElementById('statusFilter').addEventListener('change', loadSuggestions);
    document.getElementById('sortBy').addEventListener('change', loadSuggestions);
    
    // Debounce search input
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(loadSuggestions, 300);
    });

    // New suggestion form submission
    document.getElementById('submitSuggestion').addEventListener('click', submitNewSuggestion);

    // Comment form submission
    document.getElementById('commentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitComment();
    });
});

function loadSuggestions() {
    const category = document.getElementById('categoryFilter').value;
    const status = document.getElementById('statusFilter').value;
    const search = document.getElementById('searchInput').value;
    const sortBy = document.getElementById('sortBy').value;

    let url = 'api/get_suggestions.php?';
    if (category) url += `category=${encodeURIComponent(category)}&`;
    if (status) url += `status=${encodeURIComponent(status)}&`;
    if (search) url += `search=${encodeURIComponent(search)}&`;
    url += `sort_by=${sortBy}&sort_direction=DESC`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            updateStats(data.stats);
            displaySuggestions(data.suggestions);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load suggestions. Please try again later.');
        });
}

function updateStats(stats) {
    const statsHtml = `
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Suggestions</h5>
                    <h2>${stats.total_suggestions}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Implemented</h5>
                    <h2>${stats.implemented_count}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">In Progress</h5>
                    <h2>${stats.approved_count}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h5 class="card-title">Contributors</h5>
                    <h2>${stats.unique_contributors}</h2>
                </div>
            </div>
        </div>
    `;
    document.getElementById('suggestionStats').innerHTML = statsHtml;
}

function displaySuggestions(suggestions) {
    const container = document.getElementById('suggestionsList');
    container.innerHTML = '';

    suggestions.forEach(suggestion => {
        const card = document.createElement('div');
        card.className = 'col-md-6 mb-4';
        
        const statusClass = getStatusClass(suggestion.status);
        const userVoteClass = getUserVoteClass(suggestion.user_vote);
        
        card.innerHTML = `
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <h5 class="card-title">${suggestion.title}</h5>
                        <span class="badge ${statusClass}">${suggestion.status}</span>
                    </div>
                    <p class="card-text">${suggestion.description.substring(0, 200)}${suggestion.description.length > 200 ? '...' : ''}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-secondary">${suggestion.category}</span>
                            <small class="text-muted ms-2">${suggestion.location}</small>
                        </div>
                        <div class="text-end">
                            <div class="btn-group">
                                <button class="btn btn-outline-primary btn-sm vote-btn ${userVoteClass === 'upvoted' ? 'active' : ''}"
                                        onclick="vote(${suggestion.suggestion_id}, 'upvote')">
                                    <i class="fas fa-arrow-up"></i>
                                </button>
                                <button class="btn btn-outline-primary btn-sm" disabled>
                                    ${suggestion.votes}
                                </button>
                                <button class="btn btn-outline-primary btn-sm vote-btn ${userVoteClass === 'downvoted' ? 'active' : ''}"
                                        onclick="vote(${suggestion.suggestion_id}, 'downvote')">
                                    <i class="fas fa-arrow-down"></i>
                                </button>
                            </div>
                            <button class="btn btn-link btn-sm ms-2" onclick="viewSuggestion(${suggestion.suggestion_id})">
                                <i class="fas fa-comments"></i> ${suggestion.comment_count}
                            </button>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">
                            By ${suggestion.creator_name} | ${new Date(suggestion.created_at).toLocaleDateString()}
                        </small>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(card);
    });
}

function getStatusClass(status) {
    const statusClasses = {
        'pending': 'bg-warning',
        'approved': 'bg-info',
        'implemented': 'bg-success',
        'rejected': 'bg-danger'
    };
    return statusClasses[status] || 'bg-secondary';
}

function getUserVoteClass(voteType) {
    if (voteType === 'upvote') return 'upvoted';
    if (voteType === 'downvote') return 'downvoted';
    return '';
}

function submitNewSuggestion() {
    const form = document.getElementById('newSuggestionForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    fetch('api/create_suggestion.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            throw new Error(data.error);
        }
        $('#newSuggestionModal').modal('hide');
        form.reset();
        loadSuggestions();
        alert('Suggestion submitted successfully!');
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to submit suggestion. Please try again.');
    });
}

function vote(suggestionId, voteType) {
    fetch('api/vote_suggestion.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            suggestion_id: suggestionId,
            vote_type: voteType
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            throw new Error(data.error);
        }
        loadSuggestions();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to record vote. Please try again.');
    });
}

let currentSuggestionId = null;

function viewSuggestion(suggestionId) {
    currentSuggestionId = suggestionId;
    fetch(`api/get_suggestion.php?id=${suggestionId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            displaySuggestionDetails(data);
            const modal = new bootstrap.Modal(document.getElementById('viewSuggestionModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load suggestion details. Please try again.');
        });
}

function displaySuggestionDetails(suggestion) {
    const statusClass = getStatusClass(suggestion.status);
    const userVoteClass = getUserVoteClass(suggestion.user_vote);
    
    const detailsHtml = `
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-start">
                <h4>${suggestion.title}</h4>
                <span class="badge ${statusClass}">${suggestion.status}</span>
            </div>
            <p>${suggestion.description}</p>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge bg-secondary">${suggestion.category}</span>
                    <small class="text-muted ms-2">${suggestion.location}</small>
                </div>
                <div class="btn-group">
                    <button class="btn btn-outline-primary btn-sm vote-btn ${userVoteClass === 'upvoted' ? 'active' : ''}"
                            onclick="vote(${suggestion.suggestion_id}, 'upvote')">
                        <i class="fas fa-arrow-up"></i>
                    </button>
                    <button class="btn btn-outline-primary btn-sm" disabled>
                        ${suggestion.votes}
                    </button>
                    <button class="btn btn-outline-primary btn-sm vote-btn ${userVoteClass === 'downvoted' ? 'active' : ''}"
                            onclick="vote(${suggestion.suggestion_id}, 'downvote')">
                        <i class="fas fa-arrow-down"></i>
                    </button>
                </div>
            </div>
            <small class="text-muted">
                By ${suggestion.creator_name} | ${new Date(suggestion.created_at).toLocaleDateString()}
            </small>
        </div>
    `;
    document.getElementById('suggestionDetails').innerHTML = detailsHtml;

    const commentsHtml = suggestion.comments.map(comment => `
        <div class="comment mb-3">
            <div class="d-flex justify-content-between">
                <strong>${comment.user_name}</strong>
                <small class="text-muted">${new Date(comment.created_at).toLocaleString()}</small>
            </div>
            <p class="mb-1">${comment.comment}</p>
        </div>
    `).join('');
    
    document.getElementById('suggestionComments').innerHTML = commentsHtml || '<p>No comments yet.</p>';
}

function submitComment() {
    const commentText = document.getElementById('newComment').value.trim();
    if (!commentText) return;

    fetch('api/comment_suggestion.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            suggestion_id: currentSuggestionId,
            comment: commentText
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            throw new Error(data.error);
        }
        document.getElementById('newComment').value = '';
        viewSuggestion(currentSuggestionId);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to post comment. Please try again.');
    });
}

// Add CSS styles for vote buttons
const style = document.createElement('style');
style.textContent = `
    .vote-btn.upvoted {
        background-color: #28a745;
        color: white;
        border-color: #28a745;
    }
    .vote-btn.downvoted {
        background-color: #dc3545;
        color: white;
        border-color: #dc3545;
    }
`;
document.head.appendChild(style);
