<div class="main-layout">
    <div class="sidebar">
        <div class="sidebar-content">
            <div class="logo">
                <h1>Finance</h1>
                <p>Gestion de Prêts</p>
            </div>
            
            <nav class="nav-menu">
                <a href="template.html#simulation" class="nav-item <?php echo ($current_page === 'simulation') ? 'active' : ''; ?>">
                    <div class="nav-icon">📊</div>
                    <div class="nav-text">Simulation de Prêt</div>
                </a>
                <a href="fond.php" class="nav-item <?php echo ($current_page === 'fond') ? 'active' : ''; ?>">
                    <div class="nav-icon">💰</div>
                    <div class="nav-text">Fonds dans l'établissement financier</div>
                </a>
                <a href="clients.php" class="nav-item <?php echo ($current_page === 'clients') ? 'active' : ''; ?>">
                    <div class="nav-icon">👥</div>
                    <div class="nav-text">Clients</div>
                </a>
                <a href="ajoutPret.php" class="nav-item <?php echo ($current_page === 'pret') ? 'active' : ''; ?>">
                    <div class="nav-icon">💰</div>
                    <div class="nav-text">Prêts</div>
                </a>
                <a href="template.html#statistiques" class="nav-item <?php echo ($current_page === 'statistiques') ? 'active' : ''; ?>">
                    <div class="nav-icon">📈</div>
                    <div class="nav-text">Statistiques des Intérêts</div>
                </a>
                <a href="template.html#remboursement" class="nav-item <?php echo ($current_page === 'remboursement') ? 'active' : ''; ?>">
                    <div class="nav-icon">💳</div>
                    <div class="nav-text">Remboursements</div>
                </a>
                <a href="typepret.php" class="nav-item <?php echo ($current_page === 'typepret') ? 'active' : ''; ?>">
                    <div class="nav-icon">📋</div>
                    <div class="nav-text">Types de Prêts</div>
                </a>
            </nav>
            
            <div class="logout-section logout">
                <a href="template.html#deconnexion" class="nav-item">
                    <div class="nav-icon">🚪</div>
                    <div class="nav-text">Déconnexion</div>
                </a>
            </div>
        </div>
    </div>