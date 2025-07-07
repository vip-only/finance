<?php 
$page_title = "Gestion des Types de Prêts - Finance Pro";
$current_page = "typepret";
$custom_styles = '<link rel="stylesheet" href="statics/css/styleclient.css">';
include 'includes/header.php';
?>

<?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <h2>Gestion des Types de Prêts</h2>
        <p class="subtitle">Gérez les différents types de prêts proposés par votre établissement</p>
        
        <div class="section">
            <h2>Créer un nouveau type de prêt</h2>
            <form id="typePretForm">
                <input type="hidden" id="idTypePret">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="libelle">Libellé du prêt</label>
                        <input type="text" id="libelle" name="libelle" required placeholder="Ex: Prêt Personnel">
                    </div>
                    <div class="form-group">
                        <label for="taux">Taux d'intérêt (%)</label>
                        <input type="number" id="taux" name="taux" step="0.01" required min="0" max="100">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="assurance">Assurance (%)</label>
                        <input type="number" id="assurance" name="assurance" step="0.01" min="0" max="100" value="0">
                    </div>
                    <div class="form-group">
                        <label for="dateCreation">Date de création</label>
                        <input type="date" id="dateCreation" name="dateCreation" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="pretmin">Montant minimum</label>
                        <input type="number" id="pretmin" name="pretmin" required min="0" placeholder="Ex: 100000">
                    </div>
                    <div class="form-group">
                        <label for="pretmax">Montant maximum</label>
                        <input type="number" id="pretmax" name="pretmax" required min="0" placeholder="Ex: 5000000">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="dureeMoisMax">Durée maximale (en mois)</label>
                        <input type="number" id="dureeMoisMax" name="dureeMoisMax" required min="1" placeholder="Ex: 24">
                    </div>
                    <div class="form-group">
                        <label for="dateAbolition">Date d'abolition (optionnelle)</label>
                        <input type="date" id="dateAbolition" name="dateAbolition">
                    </div>
                </div>
                
                <button type="button" onclick="ajouterOuModifier()" class="btn-success">Ajouter / Modifier</button>
                <button type="button" onclick="resetForm()">Réinitialiser</button>
            </form>
        </div>
        
        <div class="section">
            <h2>Liste des types de prêts</h2>
            <table id="typePretsTable">
                <thead>
                    <tr>
                        <th>Libellé</th>
                        <th>Taux (%)</th>
                        <th>Assurance (%)</th>
                        <th>Montant Min</th>
                        <th>Montant Max</th>
                        <th>Durée Max (mois)</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="typePretsTableBody">
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="detailsModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Détails du type de prêt</h2>
        <div id="typePretDetails"></div>
    </div>
</div>

<style>
    .filter-section {
        margin-bottom: 20px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .btn-info {
        background-color: #3b82f6;
        color: white;
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }
    
    .btn-info:hover {
        background-color: #2563eb;
    }
    
    .btn-secondary {
        background-color: #6b7280;
        color: white;
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }
    
    .btn-secondary:hover {
        background-color: #4b5563;
    }
    
    .status-actif {
        background-color: #10b981;
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .status-aboli {
        background-color: #ef4444;
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .montant-cell {
        font-weight: 600;
        color: #059669;
    }
    
    .taux-cell {
        font-weight: 600;
        color: #dc2626;
    }
</style>

<script>
    const apiBase = "http://localhost:80/finance/ws";
    let typePretsData = []; 
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
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('dateCreation').value = today;
        chargerTypesPrets();
    });
    
    function chargerTypesPrets() {
        ajax("GET", "/typeprets", null, (data) => {
            typePretsData = data;
            afficherTypesPrets(data);
        });
    }
    
    function afficherTypesPrets(data) {
        const tbody = document.getElementById('typePretsTableBody');
        tbody.innerHTML = '';
        
        data.forEach(typePret => {
            const tr = document.createElement('tr');
            const isActif = !typePret.dateAbolition;
            
            tr.innerHTML = `
                <td><strong>${typePret.libelle}</strong></td>
                <td class="taux-cell">${parseFloat(typePret.taux).toFixed(2)}%</td>
                <td>${parseFloat(typePret.assurance || 0).toFixed(2)}%</td>
                <td class="montant-cell">${parseInt(typePret.pretmin).toLocaleString('fr-FR')} €</td>
                <td class="montant-cell">${parseInt(typePret.pretmax).toLocaleString('fr-FR')} €</td>
                <td>${typePret.dureeMoisMax} mois</td>
                <td>
                    <span class="status-${isActif ? 'actif' : 'aboli'}">
                        ${isActif ? 'Actif' : 'Aboli'}
                    </span>
                </td>
                <td class="actions">
                    <button onclick='remplirFormulaire(${JSON.stringify(typePret).replace(/'/g, "&#39;")})' title="Modifier">✏️</button>
                    <button onclick='viewTypePret(${typePret.idTypePret})' class="btn-success" title="Voir détails">👁️</button>
                    ${isActif ? `<button onclick='abolirTypePret(${typePret.idTypePret})' class="btn-danger" title="Abolir">❌</button>` : ''}
                </td>
            `;
            tbody.appendChild(tr);
        });
    }
    
    function filtrerActifs() {
        const typePretsActifs = typePretsData.filter(tp => !tp.dateAbolition);
        afficherTypesPrets(typePretsActifs);
    }
    
    function filtrerTous() {
        afficherTypesPrets(typePretsData);
    }
    
    function ajouterOuModifier() {
        const id = document.getElementById('idTypePret').value;
        const libelle = document.getElementById('libelle').value;
        const taux = document.getElementById('taux').value;
        const assurance = document.getElementById('assurance').value;
        const dateCreation = document.getElementById('dateCreation').value;
        const pretmin = document.getElementById('pretmin').value;
        const pretmax = document.getElementById('pretmax').value;
        const dureeMoisMax = document.getElementById('dureeMoisMax').value;
        const dateAbolition = document.getElementById('dateAbolition').value;
        if (!libelle || !taux || !dateCreation || !pretmin || !pretmax || !dureeMoisMax) {
            showAlert('Veuillez remplir tous les champs obligatoires.', 'error');
            return;
        }
        
        if (parseInt(pretmin) >= parseInt(pretmax)) {
            showAlert('Le montant minimum doit être inférieur au montant maximum.', 'error');
            return;
        }
        
        const data = `libelle=${encodeURIComponent(libelle)}&taux=${encodeURIComponent(taux)}&assurance=${encodeURIComponent(assurance)}&dateCreation=${encodeURIComponent(dateCreation)}&pretmin=${encodeURIComponent(pretmin)}&pretmax=${encodeURIComponent(pretmax)}&dureeMoisMax=${encodeURIComponent(dureeMoisMax)}&dateAbolition=${encodeURIComponent(dateAbolition)}`;
        
        if (id) {
            ajax("PUT", `/typeprets/${id}`, data, (response) => {
                showAlert('Type de prêt modifié avec succès!', 'success');
                resetForm();
                chargerTypesPrets();
            });
        } else {
            ajax("POST", "/typeprets", data, (response) => {
                showAlert('Type de prêt créé avec succès!', 'success');
                resetForm();
                chargerTypesPrets();
            });
        }
    }
    
    function remplirFormulaire(typePret) {
        document.getElementById('idTypePret').value = typePret.idTypePret;
        document.getElementById('libelle').value = typePret.libelle;
        document.getElementById('taux').value = typePret.taux;
        document.getElementById('assurance').value = typePret.assurance || 0;
        document.getElementById('dateCreation').value = typePret.dateCreation;
        document.getElementById('pretmin').value = typePret.pretmin;
        document.getElementById('pretmax').value = typePret.pretmax;
        document.getElementById('dureeMoisMax').value = typePret.dureeMoisMax;
        document.getElementById('dateAbolition').value = typePret.dateAbolition || '';
    }
    
    function viewTypePret(id) {
        ajax("GET", `/typeprets/${id}`, null, (typePret) => {
            const detailsDiv = document.getElementById('typePretDetails');
            const isActif = !typePret.dateAbolition;
            
            detailsDiv.innerHTML = `
                <div style="display: grid; gap: 15px;">
                    <div><strong>Libellé:</strong> ${typePret.libelle}</div>
                    <div><strong>Taux d'intérêt:</strong> <span style="color: #dc2626; font-weight: 600;">${parseFloat(typePret.taux).toFixed(2)}%</span></div>
                    <div><strong>Assurance:</strong> ${parseFloat(typePret.assurance || 0).toFixed(2)}%</div>
                    <div><strong>Montant minimum:</strong> <span style="color: #059669; font-weight: 600;">${parseInt(typePret.pretmin).toLocaleString('fr-FR')} €</span></div>
                    <div><strong>Montant maximum:</strong> <span style="color: #059669; font-weight: 600;">${parseInt(typePret.pretmax).toLocaleString('fr-FR')} €</span></div>
                    <div><strong>Durée maximale:</strong> ${typePret.dureeMoisMax} mois</div>
                    <div><strong>Date de création:</strong> ${new Date(typePret.dateCreation).toLocaleDateString('fr-FR')}</div>
                    <div><strong>Statut:</strong> 
                        <span class="status-${isActif ? 'actif' : 'aboli'}">
                            ${isActif ? 'Actif' : 'Aboli'}
                        </span>
                    </div>
                    ${!isActif ? `<div><strong>Date d'abolition:</strong> ${new Date(typePret.dateAbolition).toLocaleDateString('fr-FR')}</div>` : ''}
                </div>
            `;
            document.getElementById('detailsModal').style.display = 'block';
        });
    }
    
    function abolirTypePret(id) {
        if (confirm('Êtes-vous sûr de vouloir abolir ce type de prêt? Il ne sera plus disponible pour de nouveaux prêts.')) {
            const today = new Date().toISOString().split('T')[0];
            const data = `dateAbolition=${today}`;
            
            ajax("PUT", `/typeprets/${id}`, data, (response) => {
                showAlert('Type de prêt aboli avec succès!', 'success');
                chargerTypesPrets();
            });
        }
    }
    
    function resetForm() {
        document.getElementById('idTypePret').value = '';
        document.getElementById('libelle').value = '';
        document.getElementById('taux').value = '';
        document.getElementById('assurance').value = '0';
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('dateCreation').value = today;
        document.getElementById('pretmin').value = '';
        document.getElementById('pretmax').value = '';
        document.getElementById('dureeMoisMax').value = '';
        document.getElementById('dateAbolition').value = '';
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
</script>

<?php include 'includes/footer.php'; ?>