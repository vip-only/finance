<?php 
$page_title = "Gestion des Fonds - Finance Pro";
$current_page = "fond";
$custom_styles = '<link rel="stylesheet" href="statics/css/styleclient.css">';
include 'includes/header.php';
?>

<?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="header-section">
            <div class="page-info">
                <h2>Gestion des Fonds</h2>
                <p class="subtitle">Gérez les fonds entrants de votre établissement</p>
            </div>
            <div class="total-fond">
                <div class="total-amount" id="totalFond">0,00 MGA</div>
                <div class="total-label">Total des Fonds</div>
            </div>
        </div>
        
        <div class="section">
            <h2>Créer un nouveau fond</h2>
            <form id="clientForm">
                <input type="hidden" id="idClient">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="montant">Montant</label>
                        <input type="number" id="montant" name="montant" step="0.01" required min="0">
                    </div>
                    <div class="form-group">
                        <label for="datefond">Date du fond</label>
                        <input type="date" id="datefond" name="datefond" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="descri">Description</label>
                    <textarea id="descri" name="descri" placeholder="Description du fond entrant..."></textarea>
                </div>
                
                <button type="button" onclick="ajoutFond()" class="btn-success">Ajouter</button>
                <button type="button" onclick="resetForm()">Réinitialiser</button>
            </form>
        </div>
        
        <div class="section">
            <h2>Liste des fonds</h2>
            
            <table id="clientsTable">
                <thead>
                    <tr id="tableHeader">
                        <th>Description</th>
                        <th>Date</th>
                        <th>Montant</th>
                    </tr>
                </thead>
                <tbody id="clientsTableBody">
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const apiBase = "http://localhost:80/finance/ws";

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
        document.getElementById('datefond').value = today;
        chargerClients();
    });
    
    function chargerClients() {
        ajax("GET", "/fonds", null, (data) => {
            const tbody = document.getElementById('clientsTableBody');
            tbody.innerHTML = '';
            let totalFonds = 0;
            
            data.forEach(fond => {
                totalFonds += parseFloat(fond.montant || 0);
                
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${fond.descri || 'Aucune description'}</td>
                    <td>${new Date(fond.datefond).toLocaleDateString('fr-FR')}</td>
                    <td style="font-weight: 600; color: var(--color-700);">${parseFloat(fond.montant).toLocaleString('fr-FR', {
                        style: 'currency',
                        currency: 'MGA'
                    })}</td>
                `;
                tbody.appendChild(tr);
            });
            
            // Mettre à jour le total affiché
            document.getElementById('totalFond').textContent = totalFonds.toLocaleString('fr-FR', {
                style: 'currency',
                currency: 'MGA'
            });
        });
    }
    
    function ajoutFond() {
        const id = document.getElementById('idClient').value;
        const montant = document.getElementById('montant').value;
        const descri = document.getElementById('descri').value;
        const datefond = document.getElementById('datefond').value;
        
        if (!montant || !datefond) {
            showAlert('Veuillez remplir tous les champs obligatoires.', 'error');
            return;
        }
        
        const data = `montant=${encodeURIComponent(montant)}&descri=${encodeURIComponent(descri)}&datefond=${encodeURIComponent(datefond)}`;
        
        ajax("POST", "/fonds", data, () => {
            showAlert('Fond créé avec succès!', 'success');
            resetForm();
            chargerClients(); // Recharger pour mettre à jour le total
        });
    }
    
    function viewClient(id) {
        ajax("GET", `/fonds/${id}`, null, (fond) => {
            const detailsDiv = document.getElementById('clientDetails');
            detailsDiv.innerHTML = `
                <p><strong>ID:</strong> ${fond.idfond}</p>
                <p><strong>Montant:</strong> ${parseFloat(fond.montant).toLocaleString('fr-FR', {
                    style: 'currency',
                    currency: 'MGA'
                })}</p>
                <p><strong>Description:</strong> ${fond.descri || 'Aucune description'}</p>
                <p><strong>Date:</strong> ${new Date(fond.datefond).toLocaleDateString('fr-FR')}</p>
            `;
            document.getElementById('detailsModal').style.display = 'block';
        });
    }
      
    function resetForm() {
        document.getElementById('idClient').value = '';
        document.getElementById('montant').value = '';
        document.getElementById('descri').value = '';
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('datefond').value = today;
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