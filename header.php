<!-- header.php -->
<style>
  .navbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    min-height: 78px;
    padding: 12px 30px;
    border-bottom: 1px solid #e2e8ef;
    background: #fff;
    color: #071c35;
    font-family: 'DM Sans', sans-serif;
  }

  .branding {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .branding img {
    width: 42px;
    height: 42px;
    object-fit: contain;
  }

  .branding-copy h1 {
    margin: 0;
    color: #071c35;
    font-family: 'Space Grotesk', sans-serif;
    font-size: 1.15rem;
    font-weight: 700;
  }

  .branding-copy span {
    display: block;
    margin-top: 2px;
    color: #6c7b8c;
    font-size: .72rem;
  }

  .navbar .hamburger {
    display: grid;
    width: 40px;
    height: 40px;
    place-items: center;
    border: 1px solid #e2e8ef;
    border-radius: 7px;
    color: #0d4677;
    background: #fff;
    cursor: pointer;
    font-size: 1.2rem;
  }

  @media (max-width: 768px) {
    .navbar { padding: 12px 18px; }
  }
</style>

<header class="navbar">
  <div class="branding">
    <img src="images/IAA.png" alt="IAA Logo">
    <div class="branding-copy">
      <h1>Online Voting</h1>
      <span>IAA digital election portal</span>
    </div>
  </div>
  <button type="button" class="hamburger" onclick="toggleSidebar()" aria-label="Toggle navigation menu">☰</button>
</header>
</div>
