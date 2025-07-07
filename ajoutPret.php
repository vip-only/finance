<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des prêts</title>
</head>
<body>
    <h1>Gestion des prêts</h1>
    <div>
        <input type="hidden" id="idPret">
        <select id="idClient" required>
            <option value="">Sélectionnez un client</option>
        </select>
        <select id="idTypePret" required>
            <option value="">Sélectionnez un type de prêt</option>
        </select>
        <input type="number" id="montantAccorde" placeholder="Montant accordé" required>
        <input type="number" id="dureeMois" placeholder="Durée (mois)" required>
        <input type="number" id="DELAI" placeholder="Délai (mois)" required>
        <select id="modePaiement" required>
            <option value="">Sélectionnez un mode de paiement</option>
        </select>
        <button onclick="ajouterOuModifier()">Ajouter / Modifier</button>
    </div>
    <div style="margin-bottom: 15px;">
        <label>Mois début: <input type="number" id="moisDebut" min="1" max="12" style="width:60px"></label>
        <label>Année début: <input type="number" id="anneeDebut" min="2000" style="width:80px"></label>
        <label>Mois fin: <input type="number" id="moisFin" min="1" max="12" style="width:60px"></label>
        <label>Année fin: <input type="number" id="anneeFin" min="2000" style="width:80px"></label>
        <label>État:
            <select id="etatFilter">
                <option value="">Tous</option>
                <option value="1">En attente</option>
                <option value="2">Accepté</option>
                <option value="3">Rejeté</option>
            </select>
        </label>
        <button onclick="chargerPrets()">Filtrer</button>
    </div>
    <table id="table-prets">
        <thead>
            <tr>
                <th>ID</th><th>Client</th><th>Type Prêt</th><th>Montant</th><th>Durée</th><th>Délai</th><th>Début</th><th>Fin</th><th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <script>
        const apiBase = "http://localhost/finance/ws";

        function ajax(method, url, data, callback) {
            const xhr = new XMLHttpRequest();
            xhr.open(method, apiBase + url, true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = () => {
                if (xhr.readyState === 4) {
                    console.log("Réponse brute:", xhr.responseText);
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            callback(JSON.parse(xhr.responseText));
                        } catch (e) {
                            alert("Erreur serveur : " + xhr.responseText);
                        }
                    } else {
                        console.error("Erreur AJAX:", xhr.status, xhr.responseText);
                    }
                }
            };
            xhr.send(data);
        }

        function chargerOptions(selectId, table, idField, labelField) {
            console.log(`Chargement des options pour ${selectId} depuis ${table}`);
            ajax(
                "GET",
                `/pret/options/${table}/${idField}/${encodeURIComponent(labelField)}`,
                null,
                (options) => {
                    console.log(`Options reçues pour ${selectId}:`, options);
                    const select = document.getElementById(selectId);
                    // Conserver l'option par défaut
                    const defaultOption = select.querySelector('option[value=""]');
                    select.innerHTML = "";
                    if (defaultOption) {
                        select.appendChild(defaultOption);
                    }
                    // Ajouter les nouvelles options
                    if (options && options.length > 0) {
                        options.forEach(o => {
                            const option = document.createElement('option');
                            option.value = o.id;
                            option.textContent = o.label;
                            select.appendChild(option);
                        });
                        console.log(`${options.length} options ajoutées à ${selectId}`);
                    } else {
                        console.warn(`Aucune option reçue pour ${selectId}`);
                    }
                }
            );
        }

        // Charger les options après que le DOM soit prêt
        document.addEventListener('DOMContentLoaded', function() {
            chargerOptions('idClient', 'client', 'idClient', "CONCAT(nom,' ',prenom)");
            chargerOptions('idTypePret', 'type_pret', 'idTypePret', 'libelle');
            chargerOptions('modePaiement', 'modePaiement', 'idModePaiement', 'libelle');
            chargerPrets();
        });

        function chargerPrets() {
            // Récupère les filtres
            const moisDebut = parseInt(document.getElementById('moisDebut').value);
            const anneeDebut = parseInt(document.getElementById('anneeDebut').value);
            const moisFin = parseInt(document.getElementById('moisFin').value);
            const anneeFin = parseInt(document.getElementById('anneeFin').value);
            const etat = document.getElementById('etatFilter').value;

            ajax("GET", "/prets", null, (data) => {
                ajax("GET", "/etat_prets", null, (etats) => {
                    const tbody = document.querySelector("#table-prets tbody");
                    tbody.innerHTML = "";
                    data.forEach(p => {
                        // Trouver l'état courant
                        const etatPret = etats.find(e => e.idPret == p.idPret);
                        const etatVal = etatPret ? etatPret.etat : '';

                        // Filtrage sur dateAccepte
                        const dateAccepte = new Date(p.dateAccepte);
                        let show = true;
                        if (!isNaN(moisDebut) && !isNaN(anneeDebut)) {
                            const dateDebutFiltre = new Date(anneeDebut, moisDebut - 1, 1);
                            if (dateAccepte < dateDebutFiltre) show = false;
                        }
                        if (!isNaN(moisFin) && !isNaN(anneeFin)) {
                            const dateFinFiltre = new Date(anneeFin, moisFin, 0);
                            if (dateAccepte > dateFinFiltre) show = false;
                        }
                        if (etat && etatVal != etat) show = false;
                        if (!show) return;

                        let actions = `<button onclick='remplirFormulaire(${JSON.stringify(p)})'>✏️</button>
                                       <button onclick='supprimerPret(${p.idPret})'>🗑️</button>`;
                        if (etatVal == 1) {
                            actions += ` <button onclick='changerEtatPret(${p.idPret},2,${JSON.stringify(p)})'>Accepter</button>
                                         <button onclick='changerEtatPret(${p.idPret},3)'>Rejeter</button>`;
                        }
                        tbody.innerHTML += `
                            <tr>
                                <td>${p.idPret}</td>
                                <td>${p.idClient}</td>
                                <td>${p.idTypePret}</td>
                                <td>${p.montantAccorde}</td>
                                <td>${p.dureeMois}</td>
                                <td>${p.DELAI}</td>
                                <td>${p.dateDebutRemboursement}</td>
                                <td>${p.dateFinRemboursement}</td>
                                <td>${actions}</td>
                            </tr>
                        `;
                    });
                });
            });
        }

        function ajouterOuModifier() {
            const id = document.getElementById("idPret").value;
            const data = `idClient=${encodeURIComponent(document.getElementById("idClient").value)}&idTypePret=${encodeURIComponent(document.getElementById("idTypePret").value)}&montantAccorde=${encodeURIComponent(document.getElementById("montantAccorde").value)}&dureeMois=${encodeURIComponent(document.getElementById("dureeMois").value)}&DELAI=${encodeURIComponent(document.getElementById("DELAI").value)}&modePaiement=${encodeURIComponent(document.getElementById("modePaiement").value)}`;
            if (id) {
                ajax("PUT", `/prets/${id}`, data, () => {
                    resetForm();
                    chargerPrets();
                });
            } else {
                ajax("POST", "/prets", data, (res) => {
                    if (res.error) alert(res.error);
                    resetForm();
                    chargerPrets();
                });
            }
        }

        function remplirFormulaire(p) {
            document.getElementById("idPret").value = p.idPret;
            document.getElementById("idClient").value = p.idClient;
            document.getElementById("idTypePret").value = p.idTypePret;
            document.getElementById("montantAccorde").value = p.montantAccorde;
            document.getElementById("dureeMois").value = p.dureeMois;
            document.getElementById("DELAI").value = p.DELAI;
            document.getElementById("modePaiement").value = p.modePaiement;
        }

        function supprimerPret(id) {
            if (confirm("Supprimer ce prêt ?")) {
                ajax("DELETE", `/prets/${id}`, null, () => {
                    chargerPrets();
                });
            }
        }

        function resetForm() {
            document.getElementById("idPret").value = "";
            document.getElementById("idClient").value = "";
            document.getElementById("idTypePret").value = "";
            document.getElementById("montantAccorde").value = "";
            document.getElementById("dureeMois").value = "";
            document.getElementById("DELAI").value = "";
            document.getElementById("modePaiement").value = "";
        }

        function changerEtatPret(idPret, nouvelEtat, pretObj) {
            ajax("POST", `/prets/${idPret}/etat`, `etat=${nouvelEtat}`, (res) => {
                if (nouvelEtat == 2) {
                    ajax("POST", `/prets/${idPret}/amortissements`, null, (r) => {
                        if (r.taux_mensuel !== undefined) {
                            alert("Taux d'intérêt mensuel (i) : " + (r.taux_mensuel * 100).toFixed(4) + " %");
                        }
                        chargerPrets();
                    });
                } else {
                    chargerPrets();
                }
            });
        }
    </script>
</body>
</html>