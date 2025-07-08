<?php 
$page_title = "Fonds par Mois - Finance Pro";
$current_page = "fond";
$custom_styles = '<link rel="stylesheet" href="statics/css/styleclient.css">';
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <h2>Fonds à la disposition de l'EF</h2>
    <form id="filtreForm" style="margin-bottom:20px;">
        <label>Début :
            <select id="debut_mois" name="debut_mois">
                <option value="">Mois</option>
                <?php for ($m=1; $m<=12; $m++) echo "<option value='".str_pad($m,2,'0',STR_PAD_LEFT)."'>".date('F', mktime(0,0,0,$m,1))."</option>"; ?>
            </select>
            <select id="debut_annee" name="debut_annee">
                <option value="">Année</option>
                <?php for ($y=2022; $y<=date('Y'); $y++) echo "<option value='$y'>$y</option>"; ?>
            </select>
        </label>
        <label>Fin :
            <select id="fin_mois" name="fin_mois">
                <option value="">Mois</option>
                <?php for ($m=1; $m<=12; $m++) echo "<option value='".str_pad($m,2,'0',STR_PAD_LEFT)."'>".date('F', mktime(0,0,0,$m,1))."</option>"; ?>
            </select>
            <select id="fin_annee" name="fin_annee">
                <option value="">Année</option>
                <?php for ($y=2022; $y<=date('Y'); $y++) echo "<option value='$y'>$y</option>"; ?>
            </select>
        </label>
        <button type="button" onclick="chargerFondsParMois()">Filtrer</button>
    </form>
    <div style="margin-bottom:15px;">
        <span>Total sur la période : <b id="totalGlobal" style="font-size:1.2em;">0 MGA</b></span>
    </div>
    <table id="fondsTable">
        <thead>
            <tr>
                <th>Période</th>
                <th>Entrées cumulées</th>
                <th>Sorties cumulées</th>
                <th>Solde disponible</th>
            </tr>
        </thead>
        <tbody id="fondsTableBody"></tbody>
    </table>
</div>

<script>
const apiBase = "http://localhost:80/finance/ws";

function chargerFondsParMois() {
    const debut_mois = document.getElementById('debut_mois').value;
    const debut_annee = document.getElementById('debut_annee').value;
    const fin_mois = document.getElementById('fin_mois').value;
    const fin_annee = document.getElementById('fin_annee').value;

    let debut = "", fin = "";
    if(debut_annee && debut_mois) debut = debut_annee + '-' + debut_mois;
    if(fin_annee && fin_mois) fin = fin_annee + '-' + fin_mois;

    fetch(apiBase + "/fonds/par-mois")
        .then(res => res.json())
        .then(data => {
            let filtered = data;
            if (debut) filtered = filtered.filter(row => row.periode >= debut);
            if (fin) filtered = filtered.filter(row => row.periode <= fin);
            const tbody = document.getElementById('fondsTableBody');
            tbody.innerHTML = '';
            let total = 0;
            filtered.forEach(row => {
                tbody.innerHTML += `
                    <tr>
                        <td>${row.periode}</td>
                        <td>${parseFloat(row.cum_entrees).toLocaleString('fr-FR', {style:'currency',currency:'MGA'})}</td>
                        <td>${parseFloat(row.cum_sorties).toLocaleString('fr-FR', {style:'currency',currency:'MGA'})}</td>
                        <td>${parseFloat(row.solde_disponible).toLocaleString('fr-FR', {style:'currency',currency:'MGA'})}</td>
                    </tr>
                `;
                total = parseFloat(row.solde_disponible);
            });
            document.getElementById('totalGlobal').innerHTML = total.toLocaleString('fr-FR', {style:'currency',currency:'MGA'});
        });
}

document.addEventListener('DOMContentLoaded', chargerFondsParMois);
</script>
