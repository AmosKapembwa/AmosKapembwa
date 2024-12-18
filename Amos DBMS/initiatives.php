<?php
session_start();
require_once "config/database.php";
require_once "models/Initiative.php";

if (!isset($_SESSION['username'])) {
    header("location: index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$initiative = new Initiative($db);

// Security: Sanitize all inputs
$filters = array_map('htmlspecialchars', array_filter([
    'status' => $_GET['status'] ?? null,
    'category' => $_GET['category'] ?? null,
    'location' => $_GET['location'] ?? null,
    'priority' => $_GET['priority'] ?? null,
    'date_range' => $_GET['date_range'] ?? null,
    'search' => $_GET['search'] ?? null
]));

// Get initiatives from database
$initiatives = $initiative->read($filters);

$page_title = "Local Initiatives";
include_once "header.php";
?>

<!-- Custom CSS for initiatives page -->
<link rel="stylesheet" href="css/townhall-initiatives.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    /* Page Background and Font Changes */
    body {
        background-color: #f4f4f4; /* Light gray background */
        font-family: 'Roboto', sans-serif; /* Modern font */
        color: #333; /* Darker text color for better readability */
    }

    /* Enhanced Card Design */
    .initiative-card {
        transition: transform 0.2s;
        border: 1px solid #007bff; /* Blue border for cards */
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        background-color: #ffffff; /* White background for cards */
        margin-bottom: 20px;
    }
    .initiative-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        background-color: #e9ecef; /* Slightly darker on hover */
    }

    /* Typography Improvements */
    .card-title {
        font-size: 1.25rem;
        font-weight: bold;
        color: #007bff; /* Blue text for titles */
    }
    .initiative-meta {
        color: #495057; /* Darker text for metadata */
        font-size: 0.9rem;
    }

    /* Badge Colors */
    .badge {
        font-size: 0.85rem;
        padding: 0.3rem 0.6rem;
        border-radius: 10px;
    }
    .badge-success {
        background-color: #28a745; /* Green for completed */
        color: white;
    }
    .badge-primary {
        background-color: #007bff; /* Blue for in-progress */
        color: white;
    }
    .badge-warning {
        background-color: #ffc107; /* Yellow for proposed */
        color: black;
    }

    /* Stats Badge Colors */
    .stats-badge {
        font-size: 0.9rem;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        background-color: #f8f9fa;
        color: #495057;
    }

    /* Filter Section Improvements */
    .filter-section {
        background: #007bff; /* Blue background for filter section */
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        color: white; /* White text for better contrast */
    }
    .filter-section .form-label {
        font-weight: bold;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .initiative-card {
            margin-bottom: 15px;
        }
    }
</style>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4 page-header">
                <div>
                    <h2>Local Initiatives</h2>
                </div>
                <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'moderator'])): ?>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createInitiativeModal">
                    <i class="fas fa-plus"></i> Create New Initiative
                </button>
                <?php endif; ?>
            </div>

            <!-- Enhanced Filters -->
            <div class="card filter-section mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3" id="filterForm">
                        <!-- Search Bar -->
                        <div class="col-md-12 mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" name="search" placeholder="Search initiatives..." 
                                       value="<?php echo $filters['search'] ?? ''; ?>">
                            </div>
                        </div>

                        <!-- Filter Options -->
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" name="status" id="status">
                                <option value="">All Statuses</option>
                                <option value="proposed" <?php echo isset($filters['status']) && $filters['status'] === 'proposed' ? 'selected' : ''; ?>>Proposed</option>
                                <option value="in_progress" <?php echo isset($filters['status']) && $filters['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                <option value="completed" <?php echo isset($filters['status']) && $filters['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" name="category" id="category">
                                <option value="">All Categories</option>
                                <option value="infrastructure" <?php echo isset($filters['category']) && $filters['category'] === 'infrastructure' ? 'selected' : ''; ?>>Infrastructure</option>
                                <option value="education" <?php echo isset($filters['category']) && $filters['category'] === 'education' ? 'selected' : ''; ?>>Education</option>
                                <option value="healthcare" <?php echo isset($filters['category']) && $filters['category'] === 'healthcare' ? 'selected' : ''; ?>>Healthcare</option>
                                <option value="environment" <?php echo isset($filters['category']) && $filters['category'] === 'environment' ? 'selected' : ''; ?>>Environment</option>
                                <option value="social" <?php echo isset($filters['category']) && $filters['category'] === 'social' ? 'selected' : ''; ?>>Social Services</option>
                                <option value="technology" <?php echo isset($filters['category']) && $filters['category'] === 'technology' ? 'selected' : ''; ?>>Technology</option>
                                <option value="culture" <?php echo isset($filters['category']) && $filters['category'] === 'culture' ? 'selected' : ''; ?>>Culture & Arts</option>
                                <option value="sports" <?php echo isset($filters['category']) && $filters['category'] === 'sports' ? 'selected' : ''; ?>>Sports</option>
                                <option value="tourism" <?php echo isset($filters['category']) && $filters['category'] === 'tourism' ? 'selected' : ''; ?>>Tourism</option>
                                <option value="agriculture" <?php echo isset($filters['category']) && $filters['category'] === 'agriculture' ? 'selected' : ''; ?>>Agriculture</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="priority" class="form-label">Priority</label>
                            <select class="form-select" name="priority" id="priority">
                                <option value="">All Priorities</option>
                                <option value="high" <?php echo isset($filters['priority']) && $filters['priority'] === 'high' ? 'selected' : ''; ?>>High</option>
                                <option value="medium" <?php echo isset($filters['priority']) && $filters['priority'] === 'medium' ? 'selected' : ''; ?>>Medium</option>
                                <option value="low" <?php echo isset($filters['priority']) && $filters['priority'] === 'low' ? 'selected' : ''; ?>>Low</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" name="location" id="location" 
                                   placeholder="Enter location..."
                                   value="<?php echo $filters['location'] ?? ''; ?>">
                        </div>

                        <div class="col-md-3">
                            <label for="date_range" class="form-label">Date Range</label>
                            <select class="form-select" name="date_range" id="date_range">
                                <option value="">All Time</option>
                                <option value="today" <?php echo isset($filters['date_range']) && $filters['date_range'] === 'today' ? 'selected' : ''; ?>>Today</option>
                                <option value="week" <?php echo isset($filters['date_range']) && $filters['date_range'] === 'week' ? 'selected' : ''; ?>>This Week</option>
                                <option value="month" <?php echo isset($filters['date_range']) && $filters['date_range'] === 'month' ? 'selected' : ''; ?>>This Month</option>
                                <option value="year" <?php echo isset($filters['date_range']) && $filters['date_range'] === 'year' ? 'selected' : ''; ?>>This Year</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Apply Filters
                            </button>
                            <a href="initiatives.php" class="btn btn-outline-secondary">
                                <i class="fas fa-undo"></i> Reset Filters
                            </a>
                        </div>
                    </form>

                    <!-- Active Filters Display -->
                    <?php if (!empty(array_filter($filters))): ?>
                    <div class="filter-tags">
                        <?php foreach ($filters as $key => $value): ?>
                        <?php if ($value): ?>
                        <span class="filter-tag">
                            <?php echo ucfirst($key) ?>: <?php echo ucfirst($value) ?>
                            <a href="<?php echo remove_query_param($key); ?>" class="remove">×</a>
                        </span>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Initiatives Grid -->
            <div class="row">
                <?php if ($initiatives && $initiatives->rowCount() > 0): ?>
                    <?php while ($row = $initiatives->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card initiative-card">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                                    <div class="d-flex justify-content-between">
                                        <span class="badge bg-<?php 
                                            echo $row['status'] === 'completed' ? 'success' : 
                                                ($row['status'] === 'in_progress' ? 'primary' : 'warning'); 
                                            ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $row['status'])); ?>
                                        </span>
                                    </div>

                                    <div class="initiative-meta mb-3">
                                        <i class="fas fa-folder-open"></i> <?php echo ucfirst($row['category']); ?> |
                                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['location']); ?> |
                                        <i class="fas fa-calendar"></i> <?php echo date('M j, Y', strtotime($row['created_at'])); ?> |
                                        <i class="fas fa-flag"></i> Priority: <?php echo ucfirst($row['priority']); ?>
                                    </div>

                                    <p class="card-text"><?php echo substr(htmlspecialchars($row['description']), 0, 150) . '...'; ?></p>

                                    <?php if ($row['status'] !== 'completed'): ?>
                                        <div class="progress mb-3">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: <?php echo $row['progress_percentage']; ?>%"
                                                 aria-valuenow="<?php echo $row['progress_percentage']; ?>" 
                                                 aria-valuemin="0" aria-valuemax="100">
                                                <?php echo $row['progress_percentage']; ?>%
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="d-flex gap-2 mb-3">
                                        <span class="stats-badge bg-light">
                                            <i class="fas fa-comments"></i> <?php echo $row['feedback_count']; ?>
                                        </span>
                                        <span class="stats-badge bg-light">
                                            <i class="fas fa-users"></i> <?php echo $row['supporters']; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-md-12">
                        <div class="alert alert-warning">No initiatives found matching the selected filters.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Helper function to remove a query parameter from the current URL
function remove_query_param($param) {
    $params = $_GET;
    unset($params[$param]);
    return '?' . http_build_query($params);
}
?>

<!-- Create Initiative Modal -->
<div class="modal fade" id="createInitiativeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Initiative</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createInitiativeForm" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="infrastructure">Infrastructure</option>
                                <option value="education">Education</option>
                                <option value="healthcare">Healthcare</option>
                                <option value="environment">Environment</option>
                                <option value="social">Social Services</option>
                                <option value="technology">Technology</option>
                                <option value="culture">Culture & Arts</option>
                                <option value="sports">Sports</option>
                                <option value="tourism">Tourism</option>
                                <option value="agriculture">Agriculture</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="budget" class="form-label">Budget (ZMW)</label>
                            <input type="number" class="form-control" id="budget" name="budget" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="impact_area" class="form-label">Impact Area</label>
                        <textarea class="form-control" id="impact_area" name="impact_area" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="documents" class="form-label">Supporting Documents</label>
                        <input type="file" class="form-control" id="documents" name="documents[]" multiple>
                        <small class="text-muted">You can upload multiple files (PDF, DOC, DOCX, XLS, XLSX)</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitInitiative">Create Initiative</button>
            </div>
        </div>
    </div>
</div>

<!-- Update Progress Modal -->
<div class="modal fade" id="updateProgressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Initiative Progress</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateProgressForm">
                    <input type="hidden" id="initiative_id" name="initiative_id">
                    <div class="mb-3">
                        <label for="progress" class="form-label">Progress Percentage</label>
                        <input type="range" class="form-range" id="progress" name="progress" 
                               min="0" max="100" step="5">
                        <div class="text-center" id="progressValue">50%</div>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="proposed">Proposed</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="update_notes" class="form-label">Update Notes</label>
                        <textarea class="form-control" id="update_notes" name="update_notes" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitProgress">Update Progress</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle initiative creation
    document.getElementById('submitInitiative').addEventListener('click', function() {
        const form = document.getElementById('createInitiativeForm');
        const formData = new FormData(form);
        
        fetch('api/create_initiative.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Initiative created successfully!');
                location.reload();
            } else {
                alert(data.message || 'Failed to create initiative.');
            }
        });
    });

    // Handle progress updates
    const progressInput = document.getElementById('progress');
    const progressValue = document.getElementById('progressValue');
    
    progressInput.addEventListener('input', function() {
        progressValue.textContent = this.value + '%';
    });

    document.querySelectorAll('.update-progress').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('initiative_id').value = this.dataset.initiativeId;
        });
    });

    document.getElementById('submitProgress').addEventListener('click', function() {
        const form = document.getElementById('updateProgressForm');
        const formData = new FormData(form);
        
        fetch('api/update_initiative.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Progress updated successfully!');
                location.reload();
            } else {
                alert(data.message || 'Failed to update progress.');
            }
        });
    });
});
</script>

<?php include_once "footer.php"; ?>
