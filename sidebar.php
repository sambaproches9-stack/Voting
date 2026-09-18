<!-- sidebar.php -->
<style>
  .sidebar {
    width: 264px;
    background: #071c35;
    color: white;
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 10px 0 30px rgba(7, 28, 53, .08);
    font-family: 'DM Sans', sans-serif;
  }

  .sidebar.hidden { transform: translateX(-100%); }

  .sidebar-brand {
    display: flex;
    align-items: center;
    gap: 11px;
    min-height: 86px;
    padding: 14px 20px;
    border-bottom: 1px solid rgba(255, 255, 255, .1);
  }

  .sidebar-brand img {
    width: 42px;
    height: 42px;
    padding: 4px;
    object-fit: contain;
    border-radius: 7px;
    background: white;
  }

  .sidebar-brand strong {
    display: block;
    color: white;
    font-family: 'Space Grotesk', sans-serif;
    font-size: .95rem;
  }

  .sidebar-brand span {
    display: block;
    margin-top: 3px;
    color: #9db3c8;
    font-size: .68rem;
  }

  .sidebar-divider {
    margin: 22px 24px 10px;
    color: #718aa2;
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
  }

  .sidebar a {
    display: flex;
    align-items: center;
    gap: 13px;
    margin: 3px 12px;
    padding: 12px 14px;
    border-radius: 7px;
    color: #b8c9d9;
    font-size: .88rem;
    text-decoration: none;
    transition: background .2s ease, color .2s ease;
  }

  .sidebar a i {
    width: 18px;
    color: #8fa7bd;
    font-size: 1rem;
    text-align: center;
  }

  .sidebar a:hover,
  .sidebar a.active {
    background: rgba(255, 255, 255, .1);
    color: white;
  }

  .sidebar a:hover i,
  .sidebar a.active i { color: #ed7474; }
  .sidebar .logout-link { margin-top: 18px; color: #f0a4a4; }

  @media (max-width: 768px) {
    .sidebar { position: absolute; }
  }
</style>

<aside id="sidebar" class="sidebar" aria-label="Administration navigation">
  <div class="sidebar-brand">
    <img src="images/IAA.png" alt="IAA Logo">
    <div><strong>IAA Online Voting</strong><span>Administration portal</span></div>
  </div>

  <div class="sidebar-divider">Management</div>
  <a href="admin_dashboard.php?page=home" onclick="return typeof loadContent !== 'undefined' ? loadContent('home') : true;"><i class="bi bi-grid"></i>Dashboard</a>
  <a href="admin_dashboard.php?page=add_candidate" onclick="return typeof loadContent !== 'undefined' ? loadContent('add_candidate') : true;"><i class="bi bi-person-plus-fill"></i>Add candidates</a>
  <a href="admin_dashboard.php?page=view_candidates" onclick="return typeof loadContent !== 'undefined' ? loadContent('view_candidates') : true;"><i class="bi bi-people-fill"></i>View candidates</a>
  <a href="admin_dashboard.php?page=add_voters" onclick="return typeof loadContent !== 'undefined' ? loadContent('add_voters') : true;"><i class="bi bi-person-add"></i>Add voters</a>
  <a href="admin_dashboard.php?page=view_voters" onclick="return typeof loadContent !== 'undefined' ? loadContent('view_voters') : true;"><i class="bi bi-person-lines-fill"></i>View voters</a>

  <div class="sidebar-divider">Monitoring</div>
  <a href="admin_dashboard.php?page=results" onclick="return typeof loadContent !== 'undefined' ? loadContent('results') : true;"><i class="bi bi-bar-chart-fill"></i>View results</a>
  <a href="admin_dashboard.php?page=view_messages" onclick="return typeof loadContent !== 'undefined' ? loadContent('view_messages') : true;"><i class="bi bi-envelope-fill"></i>View messages</a>
  <a href="admin_dashboard.php?page=voting_period" onclick="return typeof loadContent !== 'undefined' ? loadContent('voting_period') : true;"><i class="bi bi-calendar-check-fill"></i>Voting period</a>
  <a href="logout.php" class="logout-link"><i class="bi bi-box-arrow-right"></i>Logout</a>
</aside>
</div>
