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

    <div id="filtreSection" class="section">
        <h2>Filtrer les prêts simulés par client</h2>
        <div class="form-row">
            <div class="form-group">
                <label for="filtreClient">Client</label>
                <select id="filtreClient" onchange="chargerPretsSimules();">
                    <option value="">Sélectionnez un client</option>
                </select>
            </div>
            <div class="form-group">
                <button class="btn-compare" onclick="comparerAmortissements();">Comparer</button>
            </div>
        </div>
        <table id="pretsSimulesTable" style="display:none;">
            <thead>
                <tr>
                    <th>Sélectionner</th>
                    <th>ID Prêt</th>
                    <th>Client</th>
                    <th>Type de prêt</th>
                    <th>Montant</th>
                    <th>Durée (mois)</th>
                    <th>Délai (mois)</th>
                    <th>Mode de paiement</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                </tr>
            </thead>
            <tbody id="pretsSimulesBody"></tbody>
        </table>
    </div>

    <div id="comparaisonSection" class="section" style="display:none;">
        <h2>Comparaison des amortissements</h2>
        <div id="amortissementsContainer"></div>
    </div>

    <div id="amortissementSection" class="section" style="display:none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h2>Tableau d'amortissement (annuité constante)</h2>
            <button id="exportPdfBtn" class="btn-success" onclick="exporterPDF()" style="display: none;">
                📄 Exporter en PDF
            </button>
        </div>
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

<script>
    // Note : Cette simulation enregistre les prêts simulés dans la base avec etat=0 (simulé).
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
                    showAlert("Erreur serveur : " + xhr.status + " - " + xhr.responseText, "error");
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
        chargerOptions('filtreClient', 'client', 'idClient', "CONCAT(nom,' ',prenom)");
    });

    function simulerPret() {
        const idClient = document.getElementById('idClient').value;
        const idTypePret = document.getElementById('idTypePret').value;
        const montantAccorde = parseFloat(document.getElementById('montantAccorde').value);
        const dureeMois = parseInt(document.getElementById('dureeMois').value);
        const DELAI = parseInt(document.getElementById('DELAI').value);
        const modePaiement = document.getElementById('modePaiement').value;

        // Sauvegarder le prêt simulé dans la base
        const data = `idClient=${encodeURIComponent(idClient)}&idTypePret=${encodeURIComponent(idTypePret)}&montantAccorde=${encodeURIComponent(montantAccorde)}&dureeMois=${encodeURIComponent(dureeMois)}&DELAI=${encodeURIComponent(DELAI)}&modePaiement=${encodeURIComponent(modePaiement)}&isSimulation=true`;

        ajax("POST", "/prets", data, (response) => {
            if (response.error) {
                showAlert("Erreur lors de la sauvegarde du prêt : " + response.error, "error");
                return;
            }

            // Récupérer le taux et l'assurance pour affichage
            ajax("GET", `/type_pret/${idTypePret}/taux`, null, (tauxResponse) => {
                if (tauxResponse.error) {
                    showAlert("Erreur lors de la récupération du taux : " + tauxResponse.error, "error");
                    return;
                }

                const tauxAnnuel = tauxResponse.tauxAnnuel || 5;
                const assuranceAnnuel = tauxResponse.assurance || 0;
                const today = new Date().toISOString().split('T')[0];
                const dateDebut = addMonthsToDate(today, DELAI);
                const dateFin = addMonthsToDate(dateDebut, dureeMois);

                const clientLabel = document.getElementById('idClient').selectedOptions[0].text;
                const typePretLabel = document.getElementById('idTypePret').selectedOptions[0].text;
                const modePaiementLabel = document.getElementById('modePaiement').selectedOptions[0].text;

                const tbody = document.getElementById('pretSimuleBody');
                tbody.innerHTML = `
                    <tr>
                        <td>${clientLabel}</td>
                        <td>${typePretLabel}</td>
                        <td>${formatNumber(montantAccorde)}</td>
                        <td>${dureeMois}</td>
                        <td>${DELAI}</td>
                        <td>${modePaiementLabel}</td>
                        <td>${dateDebut}</td>
                        <td>${dateFin}</td>
                        <td class="actions">
                            <button class="btn-success" onclick="genererAmortissementSimule(${response.id})">Accepter</button>
                            <button class="btn-danger" onclick="rejeterSimulation(${response.id})">Rejeter</button>
                        </td>
                    </tr>
                `;
                document.getElementById('pretSimuleSection').style.display = '';
                document.getElementById('amortissementSection').style.display = 'none';
                document.getElementById('exportPdfBtn').style.display = 'none';

                window.simulationPret = {
                    idPret: response.id,
                    idClient, idTypePret, montantAccorde, dureeMois, DELAI, modePaiement, dateDebut, tauxAnnuel, assuranceAnnuel,
                    clientLabel, typePretLabel, modePaiementLabel
                };

                // Rafraîchir la liste des prêts simulés
                chargerPretsSimules();
                showAlert('Prêt simulé enregistré avec succès!', 'success');
            });
        });
    }

    function chargerPretsSimules() {
        const idClient = document.getElementById('filtreClient').value;
        if (!idClient) {
            document.getElementById('pretsSimulesTable').style.display = 'none';
            return;
        }

        ajax("GET", "/prets", null, (prets) => {
            ajax("GET", "/etat_prets", null, (etats) => {
                ajax("GET", "/pret/options/type_pret/idTypePret/libelle", null, (typesPret) => {
                    ajax("GET", "/pret/options/modePaiement/idModePaiement/libelle", null, (modesPaiement) => {
                        ajax("GET", "/pret/options/client/idClient/CONCAT(nom,' ',prenom)", null, (clients) => {
                            const tbody = document.getElementById('pretsSimulesBody');
                            tbody.innerHTML = '';
                            prets.forEach(p => {
                                const etatPret = etats.find(e => e.idPret == p.idPret);
                                if (etatPret && etatPret.etat == 4 && p.idClient == idClient) {
                                    const clientLabel = clients.find(c => c.id == p.idClient)?.label || p.idClient;
                                    const typePretLabel = typesPret.find(t => t.id == p.idTypePret)?.label || p.idTypePret;
                                    const modePaiementLabel = modesPaiement.find(m => m.id == p.modePaiement)?.label || p.modePaiement;

                                    tbody.innerHTML += `
                                        <tr>
                                            <td><input type="checkbox" class="pret-checkbox" value="${p.idPret}"></td>
                                            <td>${p.idPret}</td>
                                            <td>${clientLabel}</td>
                                            <td>${typePretLabel}</td>
                                            <td>${formatNumber(p.montantAccorde)}</td>
                                            <td>${p.dureeMois}</td>
                                            <td>${p.DELAI}</td>
                                            <td>${modePaiementLabel}</td>
                                            <td>${p.dateDebutRemboursement}</td>
                                            <td>${p.dateFinRemboursement}</td>
                                        </tr>
                                    `;
                                }
                            });
                            document.getElementById('pretsSimulesTable').style.display = tbody.innerHTML ? '' : 'none';
                        });
                    });
                });
            });
        });
    }

    function genererAmortissementSimule(idPret) {
        ajax("GET", `/prets/${idPret}`, null, (pret) => {
            if (pret.error) {
                showAlert("Erreur lors de la récupération du prêt : " + pret.error, "error");
                return;
            }
            ajax("GET", `/type_pret/${pret.idTypePret}/taux`, null, (tauxResponse) => {
                if (tauxResponse.error) {
                    showAlert("Erreur lors de la récupération du taux : " + tauxResponse.error, "error");
                    return;
                }

                ajax("GET", "/pret/options/client/idClient/CONCAT(nom,' ',prenom)", null, (clients) => {
                    ajax("GET", "/pret/options/type_pret/idTypePret/libelle", null, (typesPret) => {
                        ajax("GET", "/pret/options/modePaiement/idModePaiement/libelle", null, (modesPaiement) => {
                            const clientLabel = clients.find(c => c.id == pret.idClient)?.label || pret.idClient;
                            const typePretLabel = typesPret.find(t => t.id == pret.idTypePret)?.label || pret.idTypePret;
                            const modePaiementLabel = modesPaiement.find(m => m.id == pret.modePaiement)?.label || pret.modePaiement;

                            const i = Math.pow(1 + tauxResponse.tauxAnnuel / 100, 1/12) - 1;
                            const n = pret.dureeMois;
                            const C = pret.montantAccorde;
                            const taux_assurance = tauxResponse.assurance / 100;
                            const assurance_mensuelle = C * taux_assurance / n;
                            const A = C * (i / (1 - Math.pow(1 + i, -n)));

                            let capital_restant = C;
                            let amortissementRows = '';
                            let totalInteret = 0;
                            for (let mois = 1; mois <= n; mois++) {
                                const datePaiement = addMonthsToDate(pret.dateDebutRemboursement, mois - 1);
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
                            document.getElementById('pretSimuleSection').style.display = 'none';
                            document.getElementById('exportPdfBtn').style.display = 'inline-block';

                            window.simulationPret = {
                                idPret: idPret,
                                idClient: pret.idClient,
                                idTypePret: pret.idTypePret,
                                montantAccorde: pret.montantAccorde,
                                dureeMois: pret.dureeMois,
                                DELAI: pret.DELAI,
                                modePaiement: pret.modePaiement,
                                dateDebut: pret.dateDebutRemboursement,
                                tauxAnnuel: tauxResponse.tauxAnnuel,
                                assuranceAnnuel: tauxResponse.assurance,
                                clientLabel, typePretLabel, modePaiementLabel
                            };

                            showAlert('Tableau d\'amortissement généré avec succès!', 'success');
                        });
                    });
                });
            });
        });
    }

    function comparerAmortissements() {
        const checkboxes = document.querySelectorAll('.pret-checkbox:checked');
        if (checkboxes.length < 2) {
            showAlert("Veuillez sélectionner au moins deux prêts pour comparer.", "error");
            return;
        }

        const container = document.getElementById('amortissementsContainer');
        container.innerHTML = '';
        document.getElementById('comparaisonSection').style.display = '';

        checkboxes.forEach(checkbox => {
            const idPret = checkbox.value;
            ajax("GET", `/prets/${idPret}`, null, (pret) => {
                if (pret.error) {
                    showAlert("Erreur lors de la récupération du prêt : " + pret.error, "error");
                    return;
                }
                ajax("GET", `/type_pret/${pret.idTypePret}/taux`, null, (tauxResponse) => {
                    if (tauxResponse.error) {
                        showAlert("Erreur lors de la récupération du taux : " + tauxResponse.error, "error");
                        return;
                    }

                    ajax("GET", "/pret/options/client/idClient/CONCAT(nom,' ',prenom)", null, (clients) => {
                        ajax("GET", "/pret/options/type_pret/idTypePret/libelle", null, (typesPret) => {
                            ajax("GET", "/pret/options/modePaiement/idModePaiement/libelle", null, (modesPaiement) => {
                                const clientLabel = clients.find(c => c.id == pret.idClient)?.label || pret.idClient;
                                const typePretLabel = typesPret.find(t => t.id == pret.idTypePret)?.label || pret.idTypePret;
                                const modePaiementLabel = modesPaiement.find(m => m.id == pret.modePaiement)?.label || pret.modePaiement;

                                const i = Math.pow(1 + tauxResponse.tauxAnnuel / 100, 1/12) - 1;
                                const n = pret.dureeMois;
                                const C = pret.montantAccorde;
                                const taux_assurance = tauxResponse.assurance / 100;
                                const assurance_mensuelle = C * taux_assurance / n;
                                const A = C * (i / (1 - Math.pow(1 + i, -n)));

                                let capital_restant = C;
                                let amortissementRows = '';
                                let totalInteret = 0;
                                for (let mois = 1; mois <= n; mois++) {
                                    const datePaiement = addMonthsToDate(pret.dateDebutRemboursement, mois - 1);
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

                                const div = document.createElement('div');
                                div.className = 'amortissement-container';
                                div.innerHTML = `
                                    <h3>Amortissement du prêt ${idPret} (${typePretLabel}, ${formatNumber(C)})</h3>
                                    <table>
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
                                        <tbody>${amortissementRows}</tbody>
                                    </table>
                                `;
                                container.appendChild(div);
                            });
                        });
                    });
                });
            });
        });

        showAlert('Comparaison des amortissements générée avec succès!', 'success');
    }

    // function comparerAmortissements() {
    //     const checkboxes = document.querySelectorAll('.pret-checkbox:checked');
    //     if (checkboxes.length < 2) {
    //         showAlert("Veuillez sélectionner au moins deux prêts pour comparer.", "error");
    //         return;
    //     }

    //     const container = document.getElementById('amortissementsContainer');
    //     container.innerHTML = '';
    //     document.getElementById('comparaisonSection').style.display = '';

    //     checkboxes.forEach(checkbox => {
    //         const idPret = checkbox.value;
    //         ajax("GET", `/prets/${idPret}`, null, (pret) => {
    //             if (pret.error) {
    //                 showAlert("Erreur lors de la récupération du prêt : " + pret.error, "error");
    //                 return;
    //             }
    //             ajax("GET", `/type_pret/${pret.idTypePret}/taux`, null, (tauxResponse) => {
    //                 if (tauxResponse.error) {
    //                     showAlert("Erreur lors de la récupération du taux : " + tauxResponse.error, "error");
    //                     return;
    //                 }

    //                 ajax("GET", "/pret/options/type_pret/idTypePret/libelle", null, (typesPret) => {
    //                     const typePretLabel = typesPret.find(t => t.id == pret.idTypePret)?.label || pret.idTypePret;

    //                     const i = Math.pow(1 + tauxResponse.tauxAnnuel / 100, 1/12) - 1;
    //                     const n = pret.dureeMois;
    //                     const C = pret.montantAccorde;
    //                     const taux_assurance = tauxResponse.assurance / 100;
    //                     const assurance_mensuelle = C * taux_assurance / 12; // Assurance mensuelle basée sur le taux annuel
    //                     const A = C * (i / (1 - Math.pow(1 + i, -n)));
    //                     const total_a_payer_mensuel = A + assurance_mensuelle;

    //                     // Titre formaté
    //                     const titre = `${typePretLabel}, ${formatNumber(C)}, ${n} mois`;

    //                     // Création de l'élément HTML
    //                     const div = document.createElement('div');
    //                     div.className = 'amortissement-summary';
    //                     div.innerHTML = `
    //                         <h3>${titre}</h3>
    //                         <p>Annuité : ${formatNumber(A)}</p>
    //                         <p>Assurance mensuelle : ${formatNumber(assurance_mensuelle)}</p>
    //                         <p>Total à payer mensuel : ${formatNumber(total_a_payer_mensuel)}</p>
    //                     `;
    //                     container.appendChild(div);
    //                 });
    //             });
    //         });
    //     });

    //     showAlert('Comparaison des amortissements générée avec succès!', 'success');
    // }

    function rejeterSimulation(idPret) {
        ajax("DELETE", `/prets/${idPret}`, null, () => {
            document.getElementById('pretSimuleSection').style.display = 'none';
            document.getElementById('amortissementSection').style.display = 'none';
            document.getElementById('exportPdfBtn').style.display = 'none';
            window.simulationPret = null;
            chargerPretsSimules();
            showAlert('Prêt simulé supprimé.', 'info');
        });
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

    function exporterPDF() {
        const p = window.simulationPret;
        if (!p) {
            showAlert('Aucune simulation disponible pour l\'export.', 'error');
            return;
        }

        // Préparer les données pour l'export
        const exportData = {
            client: p.clientLabel,
            typePret: p.typePretLabel,
            montantAccorde: p.montantAccorde,
            dureeMois: p.dureeMois,
            delai: p.DELAI,
            modePaiement: p.modePaiementLabel,
            dateDebut: p.dateDebut,
            tauxAnnuel: p.tauxAnnuel,
            assuranceAnnuel: p.assuranceAnnuel
        };

        // Créer un formulaire pour envoyer les données
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'export_amortissement_pdf.php';
        form.target = '_blank';

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'simulationData';
        input.value = JSON.stringify(exportData);
        form.appendChild(input);

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);

        showAlert('Export PDF en cours...', 'info');
    }
</script>

<?php //include 'includes/footer.php'; ?>