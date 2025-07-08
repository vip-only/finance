<?php 
$page_title = "Gestion des Clients - Finance Pro";
$current_page = "clients";
$custom_styles = '<link rel="stylesheet" href="statics/css/styleclient.css">';
include 'includes/header.php';
?>

<?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <h2>Gestion des Clients</h2>
        <p class="subtitle">Gérez votre portefeuille clients et leurs informations</p>
        
        <div class="section">
            <h2>Liste des clients</h2>
            
            <div class="filter-section">
                <div class="search-container">
                    <input type="text" id="searchInput" placeholder="Rechercher par nom, prénom, email, téléphone ou profession..." 
                           onkeyup="filtrerClients()" class="search-input">
                    <button onclick="resetFiltre()" class="btn-secondary">Effacer</button>
                    <button onclick="chargerClients()" class="btn-success">Actualiser</button>
                </div>
                <div class="filter-stats">
                    <span id="filterStats">Affichage de tous les clients</span>
                </div>
            </div>
            
            <table id="clientsTable">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Profession</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="clientsTableBody">
                </tbody>
            </table>
        </div>
        
        <div class="section">
            <h2>Créer un nouveau client</h2>
            <form id="clientForm">
                <input type="hidden" id="idClient">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom">Nom</label>
                        <input type="text" id="nom" name="nom" required>
                    </div>
                    <div class="form-group">
                        <label for="prenom">Prénom</label>
                        <input type="text" id="prenom" name="prenom" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input type="tel" id="telephone" name="telephone">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="adresse">Adresse</label>
                    <textarea id="adresse" name="adresse"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="dateNaissance">Date de naissance</label>
                        <input type="date" id="dateNaissance" name="dateNaissance">
                    </div>
                    <div class="form-group">
                        <label for="profession">Profession</label>
                        <input type="text" id="profession" name="profession">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="revenuMensuel">Revenu mensuel</label>
                        <input type="number" id="revenuMensuel" name="revenuMensuel" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="motdepasse">Mot de passe</label>
                        <input type="password" id="motdepasse" name="motdepasse" required>
                    </div>
                </div>
                
                <button type="button" onclick="ajouterOuModifier()" class="btn-success">Ajouter / Modifier</button>
                <button type="button" onclick="resetForm()">Réinitialiser</button>
            </form>
        </div>
        
    </div>
</div>

<div id="detailsModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Détails du client</h2>
        <div id="clientDetails"></div>
    </div>
</div>

<style>
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
    
    .btn-secondary {
        background-color: #6c757d;
        color: white;
        padding: 10px 16px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.3s ease;
    }
    
    .btn-secondary:hover {
        background-color: #5a6268;
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
    
    .highlight {
        background-color: #fff3cd;
        padding: 2px 4px;
        border-radius: 3px;
        font-weight: 600;
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
    const apiBase = "http://localhost:80/finance/ws";
    let allClients = []; 
    function ajax(method, url, data, callback) {
        const xhr = new XMLHttpRequest();
        xhr.open(method, apiBase + url, true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        callback(JSON.parse(xhr.responseText));
                    } catch (e) {
                        showAlert("Erreur de format de réponse du serveur.", "error");
                    }
                } else {
                    showAlert("Erreur serveur : " + xhr.status, "error");
                }
            }
        };
        xhr.send(data);
    }
    
    function chargerClients() {
        ajax("GET", "/clients", null, (data) => {
            allClients = data; 
            afficherClients(data);
            mettreAJourStats(data.length, data.length);
        });
    }
    
    function afficherClients(clients) {
        const tbody = document.getElementById('clientsTableBody');
        tbody.innerHTML = '';
        
        if (clients.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="no-results">Aucun client trouvé</td></tr>';
            return;
        }
        
        clients.forEach(client => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${client.nom}</td>
                <td>${client.prenom}</td>
                <td>${client.email}</td>
                <td>${client.telephone || 'N/A'}</td>
                <td>${client.profession || 'N/A'}</td>
                <td class="actions">
                    <button onclick='remplirFormulaire(${JSON.stringify(client).replace(/'/g, "&#39;")})' title="Modifier">✏️</button>
                    <button onclick='viewClient(${client.idClient})' class="btn-success" title="Voir détails">👁️</button>
                    <button onclick='supprimerClient(${client.idClient})' class="btn-danger" title="Supprimer">🗑️</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }
    
    function filtrerClients() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
        
        if (searchTerm === '') {
            afficherClients(allClients);
            mettreAJourStats(allClients.length, allClients.length);
            return;
        }
        
        const clientsFiltres = allClients.filter(client => {
            return (
                client.nom.toLowerCase().includes(searchTerm) ||
                client.prenom.toLowerCase().includes(searchTerm) ||
                client.email.toLowerCase().includes(searchTerm) ||
                (client.telephone && client.telephone.toLowerCase().includes(searchTerm)) ||
                (client.profession && client.profession.toLowerCase().includes(searchTerm)) ||
                (client.adresse && client.adresse.toLowerCase().includes(searchTerm))
            );
        });
        
        afficherClients(clientsFiltres);
        mettreAJourStats(clientsFiltres.length, allClients.length);
    }
    
    function mettreAJourStats(affichage, total) {
        const statsElement = document.getElementById('filterStats');
        if (affichage === total) {
            statsElement.textContent = `Affichage de tous les clients (${total})`;
        } else {
            statsElement.textContent = `Affichage de ${affichage} client(s) sur ${total}`;
        }
    }
    
    function resetFiltre() {
        document.getElementById('searchInput').value = '';
        afficherClients(allClients);
        mettreAJourStats(allClients.length, allClients.length);
    }
    
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    const debouncedFilter = debounce(filtrerClients, 300);
    
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', debouncedFilter);
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    filtrerClients();
                }
            });
        }
    });
    
    function ajouterOuModifier() {
        const id = document.getElementById('idClient').value;
        const nom = document.getElementById('nom').value;
        const prenom = document.getElementById('prenom').value;
        const email = document.getElementById('email').value;
        const telephone = document.getElementById('telephone').value;
        const adresse = document.getElementById('adresse').value;
        const dateNaissance = document.getElementById('dateNaissance').value;
        const profession = document.getElementById('profession').value;
        const revenuMensuel = document.getElementById('revenuMensuel').value;
        const motdepasse = document.getElementById('motdepasse').value;
        
        if (!nom || !prenom || !email || !motdepasse) {
            showAlert('Veuillez remplir tous les champs obligatoires.', 'error');
            return;
        }
        
        const data = `nom=${encodeURIComponent(nom)}&prenom=${encodeURIComponent(prenom)}&email=${encodeURIComponent(email)}&telephone=${encodeURIComponent(telephone)}&adresse=${encodeURIComponent(adresse)}&dateNaissance=${encodeURIComponent(dateNaissance)}&profession=${encodeURIComponent(profession)}&revenuMensuel=${encodeURIComponent(revenuMensuel)}&motdepasse=${encodeURIComponent(motdepasse)}`;
        
        if (id) {
            ajax("PUT", `/clients/${id}`, data, () => {
                showAlert('Client modifié avec succès!', 'success');
                resetForm();
                chargerClients();
            });
        } else {
            ajax("POST", "/clients", data, () => {
                showAlert('Client créé avec succès!', 'success');
                resetForm();
                chargerClients();
            });
        }
    }
    
    function remplirFormulaire(client) {
        document.getElementById('idClient').value = client.idClient;
        document.getElementById('nom').value = client.nom;
        document.getElementById('prenom').value = client.prenom;
        document.getElementById('email').value = client.email;
        document.getElementById('telephone').value = client.telephone || '';
        document.getElementById('adresse').value = client.adresse || '';
        document.getElementById('dateNaissance').value = client.dateNaissance || '';
        document.getElementById('profession').value = client.profession || '';
        document.getElementById('revenuMensuel').value = client.revenuMensuel || '';
        document.getElementById('motdepasse').value = '';
    }
    
    function viewClient(id) {
        ajax("GET", `/clients/${id}`, null, (client) => {
            const detailsDiv = document.getElementById('clientDetails');
            detailsDiv.innerHTML = `
                <div style="display: grid; gap: 15px;">
                    <div><strong>ID:</strong> #${client.idClient}</div>
                    <div><strong>Nom:</strong> ${client.nom}</div>
                    <div><strong>Prénom:</strong> ${client.prenom}</div>
                    <div><strong>Email:</strong> ${client.email}</div>
                    <div><strong>Téléphone:</strong> ${client.telephone || 'N/A'}</div>
                    <div><strong>Adresse:</strong> ${client.adresse || 'N/A'}</div>
                    <div><strong>Date de naissance:</strong> ${client.dateNaissance ? new Date(client.dateNaissance).toLocaleDateString('fr-FR') : 'N/A'}</div>
                    <div><strong>Profession:</strong> ${client.profession || 'N/A'}</div>
                    <div><strong>Revenu mensuel:</strong> ${client.revenuMensuel ? parseFloat(client.revenuMensuel).toLocaleString('fr-FR', {style: 'currency', currency: 'EUR'}) : 'N/A'}</div>
                    <div><strong>Date d'inscription:</strong> ${client.dateInscription ? new Date(client.dateInscription).toLocaleDateString('fr-FR') : 'N/A'}</div>
                </div>
            `;
            document.getElementById('detailsModal').style.display = 'block';
        });
    }
    
    function supprimerClient(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce client? Cette action est irréversible.')) {
            ajax("DELETE", `/clients/${id}`, null, () => {
                showAlert('Client supprimé avec succès!', 'success');
                chargerClients();
            });
        }
    }
    
    function resetForm() {
        document.getElementById('idClient').value = '';
        document.getElementById('nom').value = '';
        document.getElementById('prenom').value = '';
        document.getElementById('email').value = '';
        document.getElementById('telephone').value = '';
        document.getElementById('adresse').value = '';
        document.getElementById('dateNaissance').value = '';
        document.getElementById('profession').value = '';
        document.getElementById('revenuMensuel').value = '';
        document.getElementById('motdepasse').value = '';
    }
    
    function closeModal() {
        document.getElementById('detailsModal').style.display = 'none';
    }
    
    window.onclick = function(event) {
        const modal = document.getElementById('detailsModal');
        if (event.target === modal) {
            modal.style.display = 'none';
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
    
    chargerClients();
</script>

<?php include 'includes/footer.php'; ?>