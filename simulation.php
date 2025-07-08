<?php 
$page_title = "Simulation de prêt - Finance Pro";
$current_page = "simulation";
$custom_styles = '<link rel="stylesheet" href="statics/css/styleclient.css">';
include 'includes/header.php';
?>

<?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <h2>Simulation de prêt</h2>
        <p class="subtitle">Simulez vos prêts et générez des tableaux d'amortissement</p>
        
        <div class="section">
            <h2>Paramètres du prêt</h2>
            <form id="simuForm" onsubmit="event.preventDefault();simulerPret();">
                <div class="form-row">
                    <div class="form-group">
                        <label for="idClient">Client</label>
                        <select id="idClient" name="idClient" required>
                            <option value="">Sélectionnez un client</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="idTypePret">Type de prêt</label>
                        <select id="idTypePret" name="idTypePret" required>
                            <option value="">Sélectionnez un type de prêt</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="montantAccorde">Montant accordé</label>
                        <input type="number" id="montantAccorde" name="montantAccorde" required min="1">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="dureeMois">Durée (mois)</label>
                        <input type="number" id="dureeMois" name="dureeMois" required min="1">
                    </div>
                    <div class="form-group">
                        <label for="DELAI">Délai (mois)</label>
                        <input type="number" id="DELAI" name="DELAI" required min="0" value="0">
                    </div>
                    <div class="form-group">
                        <label for="modePaiement">Mode de paiement</label>
                        <select id="modePaiement" name="modePaiement" required>
                            <option value="">Sélectionnez un mode de paiement</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn-success">Simuler</button>
            </form>
        </div>

        <div id="pretSimuleSection" class="section" style="display:none;">
            <h2>Prêt simulé</h2>
            <table id="pretSimuleTable">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Type de prêt</th>
                        <th>Montant</th>
                        <th>Durée (mois)</th>
                        <th>Délai (mois)</th>
                        <th>Mode de paiement</th>
                        <th>Date début remboursement</th>
                        <th>Date fin remboursement</th>
                        <th class="actions">Actions</th>
                    </tr>
                </thead>
                <tbody id="pretSimuleBody"></tbody>
            </table>
        </div>

        <div id="amortissementSection" class="section" style="display:none;">
            <h2>Tableau d'amortissement (annuité constante)</h2>
            <table id="amortissementTable">
                <thead>
                    <tr>
                        <th>Mois</th>
                        <th>Date paiement</th>
                        <th>Annuité</th>
                        <th>Total intérêt</th>
                        <th>Assurance</th>
                        <th>Capital remboursé</th>
                        <th>Capital restant</th>
                        <th>Total à payer</th>
                    </tr>
                </thead>
                <tbody id="amortissementBody"></tbody>
            </table>
        </div>
        
    </div>
</div>

<script>
    // Note : Cette simulation est purement locale et n'enregistre aucune donnée dans la base de données.
    const apiBase = "http://localhost/finance/ws";

    const formatNumber = (value) => {
        return Number(value).toLocaleString("fr-FR", {
            style: "currency",
            currency: "EUR",
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    };

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
                        showAlert("Erreur serveur : " + xhr.responseText, "error");
                    }
                } else {
                    console.error("Erreur AJAX:", xhr.status, xhr.responseText);
                    showAlert("Erreur serveur : " + xhr.status, "error");
                }
            }
        };
        xhr.send(data);
    }

    function chargerOptions(selectId, table, idField, labelField) {
        ajax(
            "GET",
            `/pret/options/${table}/${idField}/${encodeURIComponent(labelField)}`,
            null,
            (options) => {
                const select = document.getElementById(selectId);
                const defaultOption = select.querySelector('option[value=""]');
                select.innerHTML = "";
                if (defaultOption) {
                    select.appendChild(defaultOption);
                }
                if (options && options.length > 0) {
                    options.forEach(o => {
                        const option = document.createElement('option');
                        option.value = o.id;
                        option.textContent = o.label;
                        select.appendChild(option);
                    });
                }
            }
        );
    }

    document.addEventListener('DOMContentLoaded', function() {
        chargerOptions('idClient', 'client', 'idClient', "CONCAT(nom,' ',prenom)");
        chargerOptions('idTypePret', 'type_pret', 'idTypePret', 'libelle');
        chargerOptions('modePaiement', 'modePaiement', 'idModePaiement', 'libelle');
    });

    function simulerPret() {
        const idClient = document.getElementById('idClient').value;
        const idTypePret = document.getElementById('idTypePret').value;
        const montantAccorde = parseFloat(document.getElementById('montantAccorde').value);
        const dureeMois = parseInt(document.getElementById('dureeMois').value);
        const DELAI = parseInt(document.getElementById('DELAI').value);
        const modePaiement = document.getElementById('modePaiement').value;

        // Récupérer le taux d'intérêt et le taux d'assurance pour le type de prêt sélectionné
        ajax("GET", `/type_pret/${idTypePret}/taux`, null, (response) => {
            if (response.error) {
                showAlert("Erreur lors de la récupération du taux : " + response.error, "error");
                return;
            }

            const tauxAnnuel = response.tauxAnnuel; // Taux par défaut si non fourni
            const assuranceAnnuel = response.assurance || 0; // Assurance par défaut si non fourni

            const today = new Date().toISOString().split('T')[0];
            const dateDebut = addMonthsToDate(today, DELAI);
            const dateFin = addMonthsToDate(dateDebut, dureeMois);

            const tbody = document.getElementById('pretSimuleBody');
            tbody.innerHTML = `
                <tr>
                    <td>${idClient}</td>
                    <td>${idTypePret}</td>
                    <td>${formatNumber(montantAccorde)}</td>
                    <td>${dureeMois}</td>
                    <td>${DELAI}</td>
                    <td>${modePaiement}</td>
                    <td>${dateDebut}</td>
                    <td>${dateFin}</td>
                    <td class="actions">
                        <button class="btn-success" onclick="genererAmortissementSimule()">Accepter</button>
                        <button class="btn-danger" onclick="rejeterSimulation()">Rejeter</button>
                    </td>
                </tr>
            `;
            document.getElementById('pretSimuleSection').style.display = '';
            document.getElementById('amortissementSection').style.display = 'none';

            window.simulationPret = {
                idClient, idTypePret, montantAccorde, dureeMois, DELAI, modePaiement, dateDebut, tauxAnnuel, assuranceAnnuel
            };
        });
    }

    function genererAmortissementSimule() {
        const p = window.simulationPret;
        if (!p) return;

        const i = Math.pow(1 + p.tauxAnnuel / 100, 1/12) - 1;
        const n = p.dureeMois;
        const C = p.montantAccorde;
        const taux_assurance = p.assuranceAnnuel / 100;
        const assurance_mensuelle = C * taux_assurance / n;
        const A = C * (i / (1 - Math.pow(1 + i, -n)));

        let capital_restant = C;
        let amortissementRows = '';
        let totalInteret = 0;
        for (let mois = 1; mois <= n; mois++) {
            const datePaiement = addMonthsToDate(p.dateDebut, mois - 1);
            const interet = capital_restant * i;
            totalInteret += interet;
            const capital_rembourse = A - interet;
            const capital_restant_apres = capital_restant - capital_rembourse;
            const montantTotal = A + assurance_mensuelle;

            amortissementRows += `
                <tr>
                    <td>${mois}</td>
                    <td>${datePaiement}</td>
                    <td>${formatNumber(A)}</td>
                    <td>${formatNumber(totalInteret)}</td>
                    <td>${formatNumber(assurance_mensuelle)}</td>
                    <td>${formatNumber(capital_rembourse)}</td>
                    <td>${formatNumber(capital_restant_apres > 0 ? capital_restant_apres : 0)}</td>
                    <td>${formatNumber(montantTotal)}</td>
                </tr>
            `;
            capital_restant = capital_restant_apres;
        }
        document.getElementById('amortissementBody').innerHTML = amortissementRows;
        document.getElementById('amortissementSection').style.display = '';
        
        showAlert('Tableau d\'amortissement généré avec succès!', 'success');
    }

    function rejeterSimulation() {
        document.getElementById('pretSimuleSection').style.display = 'none';
        document.getElementById('amortissementSection').style.display = 'none';
        window.simulationPret = null;
        showAlert('Simulation rejetée.', 'info');
    }

    function addMonthsToDate(dateStr, months) {
        const d = new Date(dateStr);
        d.setMonth(d.getMonth() + months);
        if (d.getDate() !== new Date(dateStr).getDate()) {
            d.setDate(0);
        }
        return d.toISOString().split('T')[0];
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