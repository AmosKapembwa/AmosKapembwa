<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Suggestions - Citizen Participation Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1>Community Suggestions</h1>
                <p class="lead">Share your ideas and help shape our community's future</p>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newSuggestionModal">
                    <i class="fas fa-plus"></i> New Suggestion
                </button>
            </div>
        </div>

        <!-- Statistics Row -->
        <div class="row mb-4" id="suggestionStats">
            <!-- Stats will be populated by JavaScript -->
        </div>

        <!-- Filters Row -->
        <div class="row mb-4">
            <div class="col-md-3">
                <select class="form-select" id="categoryFilter">
                    <option value="">All Categories</option>
                    <option value="infrastructure">Infrastructure</option>
                    <option value="education">Education</option>
                    <option value="healthcare">Healthcare</option>
                    <option value="environment">Environment</option>
                    <option value="social">Social</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="statusFilter">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="implemented">Implemented</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control" id="searchInput" placeholder="Search suggestions...">
            </div>
            <div class="col-md-2">
                <select class="form-select" id="sortBy">
                    <option value="votes">Most Voted</option>
                    <option value="created_at">Most Recent</option>
                    <option value="comment_count">Most Discussed</option>
                </select>
            </div>
        </div>

        <!-- Suggestions List -->
        <div class="row" id="suggestionsList">
            <!-- Suggestions will be populated by JavaScript -->
        </div>
    </div>

    <!-- New Suggestion Modal -->
    <div class="modal fade" id="newSuggestionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Suggestion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="newSuggestionForm">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="4" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category</label>
                                <select class="form-select" name="category" required>
                                    <option value="infrastructure">Infrastructure</option>
                                    <option value="education">Education</option>
                                    <option value="healthcare">Healthcare</option>
                                    <option value="environment">Environment</option>
                                    <option value="social">Social</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-control" name="location" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submitSuggestion">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Suggestion Modal -->
    <div class="modal fade" id="viewSuggestionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Suggestion Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="suggestionDetails">
                        <!-- Details will be populated by JavaScript -->
                    </div>
                    <hr>
                    <h6>Comments</h6>
                    <div id="suggestionComments">
                        <!-- Comments will be populated by JavaScript -->
                    </div>
                    <form id="commentForm" class="mt-3">
                        <div class="mb-3">
                            <textarea class="form-control" id="newComment" rows="2" placeholder="Add a comment..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Post Comment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/suggestions.js"></script>
</body>
</html>
