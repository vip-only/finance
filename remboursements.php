<?php 
$page_title = "Gestion des Remboursements - Finance Pro";
$current_page = "remboursements";
$custom_styles = '<link rel="stylesheet" href="statics/css/styleclient.css">';
include 'includes/header.php';
?>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
        <div class="header-section">
            <div class="page-info">
                <h2>Gestion des Remboursements</h2>
                <p class="subtitle">Suivez et gérez les remboursements de vos prêts</p>
            </div>
            <div class="total-fond">
                <div class="total-amount" id="totalRemboursements">0,00 €</div>
                <div class="total-label">Total des Remboursements</div>
            </div>
        </div>
        
        <div class="view-buttons">
            <button class="view-btn active" onclick="afficherOnglet('echeances')" id="btn-echeances">
                📅 Échéances en attente
            </button>
            <button class="view-btn" onclick="afficherOnglet('remboursements')" id="btn-remboursements">
                💳 Historique des remboursements
            </button>
            <button class="view-btn" onclick="afficherOnglet('statuts')" id="btn-statuts">
                📊 Statut des clients
            </button>
            <button class="view-btn" onclick="afficherOnglet('premierRetard')" id="btn-premierRetard">
                ⏰ Prêts en retard 1er remboursement
            </button>
        </div>
        
        <div id="echeances" class="tab-content active">
            <div class="section">
                <h2>Échéances en attente de remboursement</h2>
                
                <div class="filter-section">
                    <div class="search-container">
                        <input type="text" id="searchEcheances" placeholder="Rechercher par client ou type de prêt..." 
                            onkeyup="filtrerEcheances()" class="search-input">
                        <button onclick="resetFiltreEcheances()" class="btn-secondary">Effacer</button>
                        <button onclick="chargerEcheances()" class="btn-success">Actualiser</button>
                    </div>
                    <div class="filter-stats">
                        <span id="filterStatsEcheances">Affichage de toutes les échéances</span>
                    </div>
                </div>
                
                <table id="echeancesTable">
                    <thead>
                        <tr id="tableHeader">
                            <th>Client</th>
                            <th>Type de prêt</th>
                            <th>N° Échéance</th>
                            <th>Date d'échéance</th>
                            <th>Montant</th>
                            <th>Capital</th>
                            <th>Intérêt</th>
                            <th>Assurance</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="echeancesTableBody">
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Onglet Historique des remboursements -->
        <div id="remboursements" class="tab-content">
            <div class="section">
                <h2>Historique des remboursements</h2>
                <div class="filter-section">
                    <div class="search-container">
                        <input type="text" id="searchRemboursements" placeholder="Rechercher par client ou référence..." 
                            onkeyup="filtrerRemboursements()" class="search-input">
                        <button onclick="resetFiltreRemboursements()" class="btn-secondary">Effacer</button>
                        <button onclick="chargerRemboursements()" class="btn-success">Actualiser</button>
                    </div>
                    <div class="filter-stats">
                        <span id="filterStatsRemboursements">Affichage de tous les remboursements</span>
                    </div>
                </div>
                <table id="remboursementsTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Montant payé</th>
                            <th>Mode de paiement</th>
                            <th>Référence</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="remboursementsTableBody">
                    </tbody>
                </table>
                <div class="total-fond" style="margin-top:24px; float:right;">
                    <div class="total-amount" id="totalRemboursements">0,00 €</div>
                    <div class="total-label">Total des Remboursements</div>
                </div>
            </div>
        </div>
        
        <div id="statuts" class="tab-content">
            <div class="section">
                <h2>Statut des clients</h2>
                
                <div class="filter-section">
                    <div class="search-container">
                        <input type="text" id="searchStatuts" placeholder="Rechercher par nom du client..." 
                            onkeyup="filtrerStatuts()" class="search-input">
                        <select id="filtreStatut" onchange="filtrerStatuts()" class="filter-select">
                            <option value="">Tous les statuts</option>
                            <option value="En cours">En cours</option>
                            <option value="En retard">En retard</option>
                            <option value="Soldé">Soldé</option>
                        </select>
                        <button onclick="resetFiltreStatuts()" class="btn-secondary">Effacer</button>
                        <button onclick="chargerStatuts()" class="btn-success">Actualiser</button>
                    </div>
                    <div class="filter-stats">
                        <span id="filterStatsStatuts">Affichage de tous les clients</span>
                    </div>
                </div>
                
                <table id="statutsTable">
                    <thead>
                        <tr id="tableHeader">
                            <th>Client</th>
                            <th>Type de prêt</th>
                            <th>Montant accordé</th>
                            <th>Montant payé</th>
                            <th>Reste à payer</th>
                            <th>Échéances payées</th>
                            <th>Dernier paiement</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody id="statutsTableBody">
                    </tbody>
                </table>
            </div>
        </div>
        
        <div id="premierRetard" class="tab-content">
            <div class="section">
                <h2>Prêts en retard pour le 1er remboursement</h2>
                <div class="filter-section">
                    <button onclick="chargerPretsPremierRetard()" class="btn-success">Actualiser</button>
                </div>
                <table id="premierRetardTable">
                    <thead>
                        <tr>
                            <th>ID Prêt</th>
                            <th>Client</th>
                            <th>Type de prêt</th>
                            <th>Montant accordé</th>
                            <th>Date début remboursement</th>
                            <th>Délai (mois)</th>
                        </tr>
                    </thead>
                    <tbody id="premierRetardTableBody">
                    </tbody>
                </table>
            </div>
        </div>

</div>

<div id="remboursementModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Enregistrer un remboursement</h2>
        <form id="remboursementForm">
            <input type="hidden" id="idAmortissement">
            <input type="hidden" id="idPret">
            <input type="hidden" id="numMois">
            <input type="hidden" id="montantMensuel">
            <input type="hidden" id="capitalRembourse">
            <input type="hidden" id="interet">
            <input type="hidden" id="assurance">
            <input type="hidden" id="capitalRestant">
            
            <div class="form-group">
                <label>Client</label>
                <input type="text" id="clientInfo" readonly>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="montantPaye">Montant payé</label>
                    <input type="number" id="montantPaye" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="modePaiement">Mode de paiement</label>
                    <select id="modePaiement" required>
                        <option value="">Sélectionner...</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="reference">Référence</label>
                <input type="text" id="reference" placeholder="Numéro de chèque, référence virement, etc.">
            </div>
            
            <div class="form-group">
                <label>Détails de l'échéance</label>
                <div class="echeance-details">
                    <div>Capital à rembourser : <span id="capitalDetail"></span></div>
                    <div>Intérêts : <span id="interetDetail"></span></div>
                    <div>Assurance : <span id="assuranceDetail"></span></div>
                    <div><strong>Total attendu : <span id="totalAttendu"></span></strong></div>
                </div>
            </div>
            
            <button type="button" onclick="enregistrerRemboursement()" class="btn-success">Enregistrer</button>
            <button type="button" onclick="closeModal()">Annuler</button>
        </form>
    </div>
</div>

<div id="detailsModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeDetailsModal()">&times;</span>
        <h2>Détails du remboursement</h2>
        <div id="remboursementDetails"></div>
    </div>
</div>

<style>

    .tab-content {
        display: none;
        width: 100%; 
        box-sizing: border-box; 
    }
    
    .tab-content.active {
        display: block;
        width: 100%; /* Ajouté pour forcer la largeur */
        box-sizing: border-box;
    }
    
    .filter-section {
        margin-bottom: 20px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }
    
    .search-container {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-bottom: 10px;
        flex-wrap: wrap;
    }
    
    .search-input {
        flex: 1;
        min-width: 300px;
        padding: 10px 15px;
        border: 2px solid #dee2e6;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.3s ease;
    }
    
    .search-input:focus {
        outline: none;
        border-color: #4CAF50;
        box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.2);
    }
    
    .search-input::placeholder {
        color: #6c757d;
    }
    
    .filter-select {
        padding: 10px 15px;
        border: 2px solid #dee2e6;
        border-radius: 6px;
        font-size: 14px;
        background: white;
        transition: border-color 0.3s ease;
    }
    
    .filter-select:focus {
        outline: none;
        border-color: #4CAF50;
        box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.2);
    }
    
    .filter-stats {
        font-size: 14px;
        color: #6c757d;
        font-style: italic;
    }
    
    .no-results {
        text-align: center;
        padding: 40px;
        color: #6c757d;
        font-style: italic;
    }
    
    .echeance-details {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-top: 10px;
    }
    
    .echeance-details div {
        margin-bottom: 5px;
        display: flex;
        justify-content: space-between;
    }
    
    .status-en-cours {
        background: #10b981;
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    #echeancesTable,
#remboursementsTable,
#statutsTable,
#premierRetardTable {
    width: 100%;
    min-width: 1200px;
    max-width: 100%;
    margin: 0 auto;
    table-layout: auto;
}
    .status-en-retard {
        background: #ef4444;
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .status-solde {
        background: #6b7280;
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .retard-cell {
        background-color: #fef2f2;
        color: #991b1b;
    }
    
    @media (max-width: 768px) {
        .search-container {
            flex-direction: column;
            align-items: stretch;
        }
        
        .search-input {
            min-width: auto;
            width: 100%;
        }
    }
</style>

<script>
    const apiBase = "http://localhost/finance/ws";
    let allEcheances = [];
    let allRemboursements = [];
    let allStatuts = [];
    let modePaiements = [];

    function ajax(method, url, data, callback) {
        const xhr = new XMLHttpRequest();
        xhr.open(method, apiBase + url, true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4 && xhr.status === 200) {
                callback(JSON.parse(xhr.responseText));
            }
        };
        xhr.send(data);
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        chargerEcheances();
        chargerModePaiements();
    });
    
    function afficherOnglet(onglet) {
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.getElementById(onglet).classList.add('active');
        document.getElementById(`btn-${onglet}`).classList.add('active');
        
        switch(onglet) {
            case 'echeances':
                chargerEcheances();
                break;
            case 'remboursements':
                chargerRemboursements();
                break;
            case 'statuts':
                chargerStatuts();
                break;
        }
    }
    
    function chargerEcheances() {
        ajax("GET", "/remboursements/amortissements-sans-remboursement", null, (data) => {
            allEcheances = data;
            afficherEcheances(data);
            mettreAJourStatsEcheances(data.length, data.length);
        });
    }
    
    function afficherEcheances(echeances) {
        const tbody = document.getElementById('echeancesTableBody');
        tbody.innerHTML = '';
        
        if (echeances.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" class="no-results">Aucune échéance en attente</td></tr>';
            return;
        }
        
        echeances.forEach(echeance => {
            const dateEcheance = new Date(echeance.dateEcheance);
            const today = new Date();
            const isEnRetard = dateEcheance < today;
            
            const tr = document.createElement('tr');
            if (isEnRetard) {
                tr.classList.add('retard-cell');
            }
            
            tr.innerHTML = `
                <td>${echeance.prenom} ${echeance.nom}</td>
                <td>${echeance.typePret}</td>
                <td>${echeance.numMois}</td>
                <td>${new Date(echeance.dateEcheance).toLocaleDateString('fr-FR')}</td>
                <td style="font-weight: 600; color: var(--color-700);">${parseFloat(echeance.montantMensuel).toLocaleString('fr-FR', {
                    style: 'currency',
                    currency: 'EUR'
                })}</td>
                <td style="font-weight: 600; color: var(--color-700);">${parseFloat(echeance.capital_rembourse).toLocaleString('fr-FR', {
                    style: 'currency',
                    currency: 'EUR'
                })}</td>
                <td style="font-weight: 600; color: var(--color-700);">${parseFloat(echeance.interet).toLocaleString('fr-FR', {
                    style: 'currency',
                    currency: 'EUR'
                })}</td>
                <td style="font-weight: 600; color: var(--color-700);">${parseFloat(echeance.assurance || 0).toLocaleString('fr-FR', {
                    style: 'currency',
                    currency: 'EUR'
                })}</td>
                <td class="actions">
                    <button onclick='ouvrirModalRemboursement(${JSON.stringify(echeance).replace(/'/g, "&#39;")})' 
                            class="btn-success" title="Enregistrer remboursement">💳</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }
    
    function filtrerEcheances() {
        const searchTerm = document.getElementById('searchEcheances').value.toLowerCase().trim();
        
        if (searchTerm === '') {
            afficherEcheances(allEcheances);
            mettreAJourStatsEcheances(allEcheances.length, allEcheances.length);
            return;
        }
        
        const echeancesFiltrees = allEcheances.filter(echeance => {
            return (
                echeance.nom.toLowerCase().includes(searchTerm) ||
                echeance.prenom.toLowerCase().includes(searchTerm) ||
                echeance.typePret.toLowerCase().includes(searchTerm)
            );
        });
        
        afficherEcheances(echeancesFiltrees);
        mettreAJourStatsEcheances(echeancesFiltrees.length, allEcheances.length);
    }
    
    function resetFiltreEcheances() {
        document.getElementById('searchEcheances').value = '';
        afficherEcheances(allEcheances);
        mettreAJourStatsEcheances(allEcheances.length, allEcheances.length);
    }
    
    function mettreAJourStatsEcheances(affichage, total) {
        const statsElement = document.getElementById('filterStatsEcheances');
        if (affichage === total) {
            statsElement.textContent = `Affichage de toutes les échéances (${total})`;
        } else {
            statsElement.textContent = `Affichage de ${affichage} échéance(s) sur ${total}`;
        }
    }
    
    function chargerRemboursements() {
        ajax("GET", "/remboursements", null, (data) => {
            allRemboursements = data;
            afficherRemboursements(data);
            mettreAJourStatsRemboursements(data.length, data.length);
            
            // Calculer le total des remboursements
            let totalMontant = 0;
            data.forEach(remboursement => {
                totalMontant += parseFloat(remboursement.montantPaye || 0);
            });
            
            // Mettre à jour le total affiché comme dans fond.php
            document.getElementById('totalRemboursements').textContent = totalMontant.toLocaleString('fr-FR', {
                style: 'currency',
                currency: 'EUR'
            });
        });
    }
    
    function afficherRemboursements(remboursements) {
        const tbody = document.getElementById('remboursementsTableBody');
        tbody.innerHTML = '';
        
        if (remboursements.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="no-results">Aucun remboursement trouvé</td></tr>';
            return;
        }
        
        remboursements.forEach(remboursement => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${new Date(remboursement.datePaiement).toLocaleDateString('fr-FR')}</td>
                <td>${remboursement.prenom} ${remboursement.nom}</td>
                <td style="font-weight: 600; color: var(--color-700);">${parseFloat(remboursement.montantPaye).toLocaleString('fr-FR', {
                    style: 'currency',
                    currency: 'EUR'
                })}</td>
                <td>${remboursement.modePaiement || 'N/A'}</td>
                <td>${remboursement.reference || 'N/A'}</td>
                <td class="actions">
                    <button onclick='voirDetailRemboursement(${remboursement.idPaiement})' 
                            class="btn-success" title="Voir détails">👁️</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }
    
    function filtrerRemboursements() {
        const searchTerm = document.getElementById('searchRemboursements').value.toLowerCase().trim();
        
        if (searchTerm === '') {
            afficherRemboursements(allRemboursements);
            mettreAJourStatsRemboursements(allRemboursements.length, allRemboursements.length);
            return;
        }
        
        const remboursementsFiltres = allRemboursements.filter(remboursement => {
            return (
                remboursement.nom.toLowerCase().includes(searchTerm) ||
                remboursement.prenom.toLowerCase().includes(searchTerm) ||
                (remboursement.reference && remboursement.reference.toLowerCase().includes(searchTerm)) ||
                (remboursement.modePaiement && remboursement.modePaiement.toLowerCase().includes(searchTerm))
            );
        });
        
        afficherRemboursements(remboursementsFiltres);
        mettreAJourStatsRemboursements(remboursementsFiltres.length, allRemboursements.length);
    }
    
    function resetFiltreRemboursements() {
        document.getElementById('searchRemboursements').value = '';
        afficherRemboursements(allRemboursements);
        mettreAJourStatsRemboursements(allRemboursements.length, allRemboursements.length);
    }
    
    function mettreAJourStatsRemboursements(affichage, total) {
        const statsElement = document.getElementById('filterStatsRemboursements');
        if (affichage === total) {
            statsElement.textContent = `Affichage de tous les remboursements (${total})`;
        } else {
            statsElement.textContent = `Affichage de ${affichage} remboursement(s) sur ${total}`;
        }
    }
    
    function chargerStatuts() {
        ajax("GET", "/remboursements/statut-clients", null, (data) => {
            allStatuts = data;
            afficherStatuts(data);
            mettreAJourStatsStatuts(data.length, data.length);
        });
    }
    
    function afficherStatuts(statuts) {
        const tbody = document.getElementById('statutsTableBody');
        tbody.innerHTML = '';
        
        if (statuts.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="no-results">Aucun client trouvé</td></tr>';
            return;
        }
        
        statuts.forEach(client => {
            const tr = document.createElement('tr');
            const statusClass = client.statut.toLowerCase().replace(' ', '-');
            
            tr.innerHTML = `
                <td>${client.prenom} ${client.nom}</td>
                <td>${client.typePret}</td>
                <td style="font-weight: 600; color: var(--color-700);">${parseFloat(client.montantAccorde).toLocaleString('fr-FR', {
                    style: 'currency',
                    currency: 'EUR'
                })}</td>
                <td style="font-weight: 600; color: var(--color-700);">${parseFloat(client.montantPaye).toLocaleString('fr-FR', {
                    style: 'currency',
                    currency: 'EUR'
                })}</td>
                <td style="font-weight: 600; color: var(--color-700);">${parseFloat(client.resteAPayer).toLocaleString('fr-FR', {
                    style: 'currency',
                    currency: 'EUR'
                })}</td>
                <td>${client.echancesPaye}/${client.echancesTotales}</td>
                <td>${client.dernierPaiement ? new Date(client.dernierPaiement).toLocaleDateString('fr-FR') : 'Aucun'}</td>
                <td><span class="status-${statusClass}">${client.statut}</span></td>
            `;
            tbody.appendChild(tr);
        });
    }
    
    function filtrerStatuts() {
        const searchTerm = document.getElementById('searchStatuts').value.toLowerCase().trim();
        const statutFiltre = document.getElementById('filtreStatut').value;
        
        let statutsFiltres = allStatuts;
        
        if (searchTerm !== '') {
            statutsFiltres = statutsFiltres.filter(client => {
                return (
                    client.nom.toLowerCase().includes(searchTerm) ||
                    client.prenom.toLowerCase().includes(searchTerm) ||
                    client.typePret.toLowerCase().includes(searchTerm)
                );
            });
        }
        
        if (statutFiltre !== '') {
            statutsFiltres = statutsFiltres.filter(client => client.statut === statutFiltre);
        }
        
        afficherStatuts(statutsFiltres);
        mettreAJourStatsStatuts(statutsFiltres.length, allStatuts.length);
    }
    
    function resetFiltreStatuts() {
        document.getElementById('searchStatuts').value = '';
        document.getElementById('filtreStatut').value = '';
        afficherStatuts(allStatuts);
        mettreAJourStatsStatuts(allStatuts.length, allStatuts.length);
    }
    
    function mettreAJourStatsStatuts(affichage, total) {
        const statsElement = document.getElementById('filterStatsStatuts');
        if (affichage === total) {
            statsElement.textContent = `Affichage de tous les clients (${total})`;
        } else {
            statsElement.textContent = `Affichage de ${affichage} client(s) sur ${total}`;
        }
    }
    
    function chargerModePaiements() {
        ajax("GET", "/remboursements/mode-paiements", null, (data) => {
            modePaiements = data;
            const select = document.getElementById('modePaiement');
            select.innerHTML = '<option value="">Sélectionner...</option>';
            
            data.forEach(mode => {
                const option = document.createElement('option');
                option.value = mode.idmodePaiement;
                option.textContent = mode.libelle;
                select.appendChild(option);
            });
        });
    }
    
    function ouvrirModalRemboursement(echeance) {
        document.getElementById('idAmortissement').value = echeance.idAmortissement;
        document.getElementById('idPret').value = echeance.idPret;
        document.getElementById('numMois').value = echeance.numMois;
        document.getElementById('montantMensuel').value = echeance.montantMensuel;
        document.getElementById('capitalRembourse').value = echeance.capital_rembourse;
        document.getElementById('interet').value = echeance.interet;
        document.getElementById('assurance').value = echeance.assurance || 0;
        document.getElementById('capitalRestant').value = echeance.capital_restant;
        
        document.getElementById('clientInfo').value = `${echeance.prenom} ${echeance.nom}`;
        document.getElementById('montantPaye').value = echeance.montantMensuel;
        document.getElementById('capitalDetail').textContent = parseFloat(echeance.capital_rembourse).toFixed(2) + ' €';
        document.getElementById('interetDetail').textContent = parseFloat(echeance.interet).toFixed(2) + ' €';
        document.getElementById('assuranceDetail').textContent = parseFloat(echeance.assurance || 0).toFixed(2) + ' €';
        document.getElementById('totalAttendu').textContent = parseFloat(echeance.montantMensuel).toFixed(2) + ' €';
        
        document.getElementById('remboursementModal').style.display = 'block';
    }
    
    function enregistrerRemboursement() {
        const data = {
            idPret: document.getElementById('idPret').value,
            idAmortissement: document.getElementById('idAmortissement').value,
            numMois: document.getElementById('numMois').value,
            montantPaye: document.getElementById('montantPaye').value,
            capital_restant: document.getElementById('capitalRestant').value,
            capital_rembourse: document.getElementById('capitalRembourse').value,
            interet: document.getElementById('interet').value,
            assurance: document.getElementById('assurance').value,
            modePaiement: document.getElementById('modePaiement').value,
            reference: document.getElementById('reference').value
        };
        
        if (!data.montantPaye || !data.modePaiement) {
            showAlert('Veuillez remplir tous les champs obligatoires.', 'error');
            return;
        }
        
        const formData = Object.keys(data).map(key => 
            `${encodeURIComponent(key)}=${encodeURIComponent(data[key])}`
        ).join('&');
        
        ajax("POST", "/remboursements", formData, (response) => {
            showAlert('Remboursement enregistré avec succès!', 'success');
            closeModal();
            chargerEcheances();
        });
    }
    
    function voirDetailRemboursement(id) {
        ajax("GET", `/remboursements/${id}`, null, (remboursement) => {
            const detailsDiv = document.getElementById('remboursementDetails');
            detailsDiv.innerHTML = `
                <p><strong>ID:</strong> #${remboursement.idPaiement}</p>
                <p><strong>Client:</strong> ${remboursement.prenom} ${remboursement.nom}</p>
                <p><strong>Montant payé:</strong> ${parseFloat(remboursement.montantPaye).toLocaleString('fr-FR', {
                    style: 'currency',
                    currency: 'EUR'
                })}</p>
                <p><strong>Date de paiement:</strong> ${new Date(remboursement.datePaiement).toLocaleDateString('fr-FR')}</p>
                <p><strong>Mode de paiement:</strong> ${remboursement.modePaiement || 'N/A'}</p>
                <p><strong>Référence:</strong> ${remboursement.reference || 'N/A'}</p>
                <p><strong>Capital remboursé:</strong> ${parseFloat(remboursement.capital_rembourse).toLocaleString('fr-FR', {
                    style: 'currency',
                    currency: 'EUR'
                })}</p>
                <p><strong>Intérêts:</strong> ${parseFloat(remboursement.interet).toLocaleString('fr-FR', {
                    style: 'currency',
                    currency: 'EUR'
                })}</p>
                <p><strong>Assurance:</strong> ${parseFloat(remboursement.assurance || 0).toLocaleString('fr-FR', {
                    style: 'currency',
                    currency: 'EUR'
                })}</p>
                <p><strong>Mois:</strong> ${remboursement.numMois}</p>
            `;
            document.getElementById('detailsModal').style.display = 'block';
        });
    }
    
    function closeModal() {
        document.getElementById('remboursementModal').style.display = 'none';
        document.getElementById('remboursementForm').reset();
    }
    
    function closeDetailsModal() {
        document.getElementById('detailsModal').style.display = 'none';
    }
    
    window.onclick = function(event) {
        const modal = document.getElementById('remboursementModal');
        const detailsModal = document.getElementById('detailsModal');
        if (event.target === modal) {
            closeModal();
        }
        if (event.target === detailsModal) {
            closeDetailsModal();
        }
    }
    
    function showAlert(message, type) {
        const existingAlert = document.querySelector('.alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.textContent = message;
        
        const container = document.querySelector('.main-content');
        container.insertBefore(alert, container.firstChild);
        
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }
    
    chargerEcheances();
    
    function chargerPretsPremierRetard() {
        ajax("GET", "/remboursements/prets-premier-retard", null, (data) => {
            afficherPretsPremierRetard(data);
        });
    }

    function afficherPretsPremierRetard(prets) {
        const tbody = document.getElementById('premierRetardTableBody');
        tbody.innerHTML = '';
        if (prets.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="no-results">Aucun prêt en retard pour le 1er remboursement</td></tr>';
            return;
        }
        prets.forEach(pret => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${pret.idPret}</td>
                <td>${pret.prenom} ${pret.nom}</td>
                <td>${pret.typePret}</td>
                <td style="font-weight:600; color:var(--color-700);">${parseFloat(pret.montantAccorde).toLocaleString('fr-FR', {style:'currency',currency:'EUR'})}</td>
                <td>${new Date(pret.dateDebutRemboursement).toLocaleDateString('fr-FR')}</td>
                <td>${pret.DELAI}</td>
            `;
            tbody.appendChild(tr);
        });
    }
</script>

<?php include 'includes/footer.php'; ?>