<?php
include('connect.php');

// Example: fetch total counts
$totalVoters = $conn->query("SELECT COUNT(*) AS cnt FROM users WHERE role = 'student'")
                    ->fetch_assoc()['cnt'];
$totalCandidates = $conn->query("SELECT COUNT(*) AS cnt FROM candidates")
                       ->fetch_assoc()['cnt'];
$totalMessages = $conn->query("SELECT COUNT(*) AS cnt FROM contact_messages")
                      ->fetch_assoc()['cnt'];
$totalVotes = $conn->query("SELECT COUNT(*) AS cnt FROM votes")
                      ->fetch_assoc()['cnt'];
?>
<div class="row g-4">
  <div class="col-md-4">
    <div class="card text-white bg-primary">
      <div class="card-body">
        <h5 class="card-title">Total Voters</h5>
        <p class="card-text display-6"><?= $totalVoters ?></p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-white bg-success">
      <div class="card-body">
        <h5 class="card-title">Total Candidates</h5>
        <p class="card-text display-6"><?= $totalCandidates ?></p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-white bg-info">
      <div class="card-body">
        <h5 class="card-title">New Messages</h5>
        <p class="card-text display-6"><?= $totalMessages ?></p>
      </div>
    </div>
  </div>
</div>

<hr class="my-4">

<h4>Quick Actions</h4>
<div class="d-flex gap-3">
  <a href="?page=add_voters" class="btn btn-outline-light">Add Voter</a>
  <a href="?page=add_candidate" class="btn btn-outline-light">Add Candidate</a>
  <a href="?page=view_messages" class="btn btn-outline-light">View Messages</a>
</div>
