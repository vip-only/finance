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
        
        <div class="section">
            <h2>Liste des clients</h2>
            
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
    </div>
</div>

<div id="detailsModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Détails du client</h2>
        <div id="clientDetails"></div>
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
    
    function chargerClients() {
        ajax("GET", "/clients", null, (data) => {
            const tbody = document.getElementById('clientsTableBody');
            tbody.innerHTML = '';
            
            data.forEach(client => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${client.nom}</td>
                    <td>${client.prenom}</td>
                    <td>${client.email}</td>
                    <td>${client.telephone || 'N/A'}</td>
                    <td>${client.profession || 'N/A'}</td>
                    <td class="actions">
                        <button onclick='remplirFormulaire(${JSON.stringify(client).replace(/'/g, "&#39;")})'>✏️</button>
                        <button onclick='viewClient(${client.idClient})' class="btn-success">👁️</button>
                        <button onclick='supprimerClient(${client.idClient})' class="btn-danger">🗑️</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        });
    }
    
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
                <p><strong>ID:</strong> ${client.idClient}</p>
                <p><strong>Nom:</strong> ${client.nom}</p>
                <p><strong>Prénom:</strong> ${client.prenom}</p>
                <p><strong>Email:</strong> ${client.email}</p>
                <p><strong>Téléphone:</strong> ${client.telephone || 'N/A'}</p>
                <p><strong>Adresse:</strong> ${client.adresse || 'N/A'}</p>
                <p><strong>Date de naissance:</strong> ${client.dateNaissance || 'N/A'}</p>
                <p><strong>Profession:</strong> ${client.profession || 'N/A'}</p>
                <p><strong>Revenu mensuel:</strong> ${client.revenuMensuel ? parseFloat(client.revenuMensuel).toFixed(2) + ' €' : 'N/A'}</p>
                <p><strong>Date d'inscription:</strong> ${client.dateInscription ? new Date(client.dateInscription).toLocaleDateString() : 'N/A'}</p>
            `;
            document.getElementById('detailsModal').style.display = 'block';
        });
    }
    
    function supprimerClient(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce client?')) {
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