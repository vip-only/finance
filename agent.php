<?php 
$page_title = "Ajout d'agent";
$current_page = "agent";
$custom_styles = '<link rel="stylesheet" href="statics/css/styleclient.css">';
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
  <h2>Gestion des Agents</h2>
  <p class="subtitle">Ajoutez un agent à votre équipe</p>

  <div class="section">
    <h2>Créer un nouvel agent</h2>
    <form id="agentForm">
      <input type="hidden" id="idAgent">

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
          <label for="motdepasse">Mot de passe</label>
          <input type="password" id="motdepasse" name="motdepasse" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="role">Rôle</label>
          <input type="text" id="role" name="role">
        </div>
        <div class="form-group">
          <label for="etatActif">État Actif</label>
          <input type="number" id="etatActif" name="etatActif" placeholder="1=Actif, 0=Inactif" value="1">
        </div>
      </div>

      <button type="button" onclick="ajouterAgent()" class="btn-success">Ajouter Agent</button>
      <button type="button" onclick="resetForm()" class="btn-secondary">Réinitialiser</button>
    </form>
  </div>

  <div class="section">
    <h2>Liste des agents</h2>
    <table id="table-agents">
      <thead>
        <tr>
          <th>ID</th><th>Nom</th><th>Prénom</th><th>Email</th><th>Rôle</th><th>État</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>
  <script>
    const apiBase = "http://localhost:80/www/Ocy/finance/ws";

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

    function chargerAgents() {
      ajax("GET", "/agents", null, (data) => {
        const tbody = document.querySelector("#table-agents tbody");
        tbody.innerHTML = "";
        data.forEach(a => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td>${a.idAgent}</td>
            <td>${a.nom}</td>
            <td>${a.prenom}</td>
            <td>${a.email}</td>
            <td>${a.role}</td>
            <td>${a.etatActif == 1 ? 'Actif' : 'Inactif'}</td>
          `;
          tbody.appendChild(tr);
        });
      });
    }

    function ajouterAgent() {
      const nom = document.getElementById("nom").value;
      const prenom = document.getElementById("prenom").value;
      const email = document.getElementById("email").value;
      const motdepasse = document.getElementById("motdepasse").value;
      const role = document.getElementById("role").value;
      const etatActif = document.getElementById("etatActif").value;

      const data = `nom=${encodeURIComponent(nom)}&prenom=${encodeURIComponent(prenom)}&email=${encodeURIComponent(email)}&motdepasse=${encodeURIComponent(motdepasse)}&role=${encodeURIComponent(role)}&etatActif=${encodeURIComponent(etatActif)}`;

      ajax("POST", "/agents", data, () => {
        chargerAgents();
        resetForm();
      });
    }

    function resetForm() {
      document.getElementById("nom").value = '';
      document.getElementById("prenom").value = '';
      document.getElementById("email").value = '';
      document.getElementById("motdepasse").value = '';
      document.getElementById("role").value = '';
      document.getElementById("etatActif").value = '1';
    }

    chargerAgents();
  </script>
