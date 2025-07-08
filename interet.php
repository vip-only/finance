<?php 
$page_title = "Intérêts gagnés par mois - Finance Pro";
$current_page = "interet";
$custom_styles = '<link rel="stylesheet" href="statics/css/styleclient.css">';
include 'includes/header.php';
?>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="header-section">
        <div class="page-info">
            <h2>Intérêts gagnés par mois</h2>
            <p class="subtitle">Visualisez les intérêts encaissés par l'établissement entre deux dates</p>
        </div>
        <div class="total-fond">
            <div class="total-amount" id="totalInterets">0,00 €</div>
            <div class="total-label">Total des Intérêts</div>
        </div>
    </div>
    
    <div class="section">
        <h3>Sélection de la période</h3>
        
        <div class="filter-section">
            <div class="search-container">
                <div class="date-group">
                    <label>Du :</label>
                    <select id="moisDebut" class="search-input" required>
                        <option value="">Mois</option>
                        <option value="01">Janvier</option>
                        <option value="02">Février</option>
                        <option value="03">Mars</option>
                        <option value="04">Avril</option>
                        <option value="05">Mai</option>
                        <option value="06">Juin</option>
                        <option value="07">Juillet</option>
                        <option value="08">Août</option>
                        <option value="09">Septembre</option>
                        <option value="10">Octobre</option>
                        <option value="11">Novembre</option>
                        <option value="12">Décembre</option>
                    </select>
                    <select id="anneeDebut" class="search-input" required>
                        <option value="">Année</option>
                    </select>
                </div>
                
                <div class="date-group">
                    <label>Au :</label>
                    <select id="moisFin" class="search-input" required>
                        <option value="">Mois</option>
                        <option value="01">Janvier</option>
                        <option value="02">Février</option>
                        <option value="03">Mars</option>
                        <option value="04">Avril</option>
                        <option value="05">Mai</option>
                        <option value="06">Juin</option>
                        <option value="07">Juillet</option>
                        <option value="08">Août</option>
                        <option value="09">Septembre</option>
                        <option value="10">Octobre</option>
                        <option value="11">Novembre</option>
                        <option value="12">Décembre</option>
                    </select>
                    <select id="anneeFin" class="search-input" required>
                        <option value="">Année</option>
                    </select>
                </div>
                
                <button onclick="chargerInteretsParMois()" class="btn-success">Afficher les intérêts</button>
                <button onclick="resetPeriode()" class="btn-secondary">Réinitialiser</button>
            </div>
            <div class="filter-stats">
                <span id="periodeAffichee">Sélectionnez une période pour voir les intérêts</span>
            </div>
        </div>
        
        <div id="resultatSection" style="display: none;">
            <!-- Section Graphique -->
            <div class="chart-section">
                <h3>Graphique des intérêts par mois</h3>
                <div class="chart-container">
                    <canvas id="interetsChart"></canvas>
                </div>
                <div class="chart-controls">
                    <button onclick="toggleChartType('bar')" class="chart-btn active" id="barBtn">📊 Barres</button>
                    <button onclick="toggleChartType('line')" class="chart-btn" id="lineBtn">📈 Courbe</button>
                    <button onclick="toggleChartType('pie')" class="chart-btn" id="pieBtn">🥧 Camembert</button>
                </div>
            </div>
            
            <!-- Section Tableau -->
            <div class="table-section">
                <h3>Détails par mois</h3>
                <table id="tableInteretsParMois">
                    <thead>
                        <tr>
                            <th>Mois</th>
                            <th>Année</th>
                            <th>Intérêts gagnés</th>
                            <th>% du total</th>
                        </tr>
                    </thead>
                    <tbody id="interetsTableBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Inclusion de Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const apiBase = "http://localhost/finance/ws";
    let allInterets = [];
    let interetsChart = null;
    let currentChartType = 'bar';

    // Couleurs basées sur le style.css
    const chartColors = {
        primary: '#a83232',      // var(--color-500)
        secondary: '#8b2323',    // var(--color-600)
        accent: '#c75c5c',       // var(--color-400)
        light: '#e9bdbd',        // var(--color-200)
        lighter: '#f5e6e6',      // var(--color-100)
        dark: '#6b1a1a',         // var(--color-700)
        darker: '#4a1010'        // var(--color-800)
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
                        showAlert("Erreur de format de réponse du serveur.", "error");
                    }
                } else {
                    showAlert("Erreur serveur : " + xhr.status, "error");
                }
            }
        };
        xhr.send(data);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date();
        const currentYear = today.getFullYear();
        const currentMonth = String(today.getMonth() + 1).padStart(2, '0');
        
        const anneeDebut = document.getElementById('anneeDebut');
        const anneeFin = document.getElementById('anneeFin');
        
        for (let year = 2020; year <= currentYear + 2; year++) {
            const option1 = new Option(year, year);
            const option2 = new Option(year, year);
            anneeDebut.appendChild(option1);
            anneeFin.appendChild(option2);
        }
        
        // Valeurs par défaut : janvier de l'année courante au mois courant
        document.getElementById('moisDebut').value = '01';
        document.getElementById('anneeDebut').value = currentYear;
        document.getElementById('moisFin').value = currentMonth;
        document.getElementById('anneeFin').value = currentYear;
    });

    function chargerInteretsParMois() {
        const moisDebut = document.getElementById('moisDebut').value;
        const anneeDebut = document.getElementById('anneeDebut').value;
        const moisFin = document.getElementById('moisFin').value;
        const anneeFin = document.getElementById('anneeFin').value;
        
        if (!moisDebut || !anneeDebut || !moisFin || !anneeFin) {
            showAlert('Veuillez sélectionner tous les champs.', 'error');
            return;
        }
        
        // Créer les dates au format YYYY-MM-DD
        const dateDebut = `${anneeDebut}-${moisDebut}-01`;
        
        // Dernier jour du mois de fin
        const dernierJour = new Date(parseInt(anneeFin), parseInt(moisFin), 0).getDate();
        const dateFin = `${anneeFin}-${moisFin}-${String(dernierJour).padStart(2, '0')}`;
        
        // Vérifier que la période est valide
        if (new Date(dateDebut) > new Date(dateFin)) {
            showAlert('La date de début doit être antérieure à la date de fin.', 'error');
            return;
        }

        ajax("GET", `/remboursements/interets-par-mois?dateDebut=${dateDebut}&dateFin=${dateFin}`, null, (data) => {
            allInterets = data;
            afficherInterets(data);
            creerGraphique(data);
            
            // Afficher la section résultat
            document.getElementById('resultatSection').style.display = 'block';
            
            // Afficher la période en format lisible
            const moisNoms = [
                'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
            ];
            
            const nomMoisDebut = moisNoms[parseInt(moisDebut) - 1];
            const nomMoisFin = moisNoms[parseInt(moisFin) - 1];
            
            let textePeriode;
            if (moisDebut === moisFin && anneeDebut === anneeFin) {
                textePeriode = `${nomMoisDebut} ${anneeDebut}`;
            } else if (anneeDebut === anneeFin) {
                textePeriode = `${nomMoisDebut} - ${nomMoisFin} ${anneeDebut}`;
            } else {
                textePeriode = `${nomMoisDebut} ${anneeDebut} - ${nomMoisFin} ${anneeFin}`;
            }
            
            document.getElementById('periodeAffichee').textContent = `Période : ${textePeriode}`;
        });
    }

    function afficherInterets(interets) {
        const tbody = document.getElementById('interetsTableBody');
        tbody.innerHTML = '';
        
        if (interets.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="no-results">Aucun intérêt trouvé pour cette période</td></tr>';
            document.getElementById('totalInterets').textContent = '0,00 €';
            return;
        }
        
        // Calculer le total
        let totalInterets = 0;
        interets.forEach(row => {
            totalInterets += parseFloat(row.totalInteret);
        });
        
        // Afficher les lignes
        interets.forEach(row => {
            const tr = document.createElement('tr');
            const montant = parseFloat(row.totalInteret);
            const pourcentage = totalInterets > 0 ? (montant / totalInterets * 100) : 0;
            
            // Convertir le mois numérique en nom de mois français
            const moisNoms = [
                'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
            ];
            const nomMois = moisNoms[parseInt(row.moisNum) - 1];
            
            tr.innerHTML = `
                <td><strong>${nomMois}</strong></td>
                <td>${row.annee}</td>
                <td style="font-weight:600; color:var(--color-700);">${montant.toLocaleString('fr-FR', {
                    style:'currency',
                    currency:'EUR'
                })}</td>
                <td style="color:var(--color-600); font-weight:600;">${pourcentage.toFixed(1)}%</td>
            `;
            tbody.appendChild(tr);
        });
        
        // Mettre à jour le total
        document.getElementById('totalInterets').textContent = totalInterets.toLocaleString('fr-FR', {
            style: 'currency',
            currency: 'EUR'
        });
    }

    function creerGraphique(data) {
        const ctx = document.getElementById('interetsChart').getContext('2d');
        
        // Détruire le graphique existant s'il y en a un
        if (interetsChart) {
            interetsChart.destroy();
        }
        
        if (data.length === 0) return;
        
        // Préparer les données
        const moisNoms = [
            'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
            'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
        ];
        
        const labels = data.map(item => {
            const nomMois = moisNoms[parseInt(item.moisNum) - 1];
            return `${nomMois} ${item.annee}`;
        });
        
        const values = data.map(item => parseFloat(item.totalInteret));
        
        // Générer des couleurs dégradées
        const backgroundColors = data.map((_, index) => {
            const colors = [
                chartColors.primary,
                chartColors.accent,
                chartColors.secondary,
                chartColors.light,
                chartColors.dark
            ];
            return colors[index % colors.length];
        });
        
        const borderColors = backgroundColors.map(color => color);
        
        const config = {
            type: currentChartType,
            data: {
                labels: labels,
                datasets: [{
                    label: 'Intérêts gagnés (€)',
                    data: values,
                    backgroundColor: currentChartType === 'pie' ? backgroundColors : chartColors.primary + '80',
                    borderColor: currentChartType === 'pie' ? borderColors : chartColors.primary,
                    borderWidth: 2,
                    fill: currentChartType === 'line' ? false : true,
                    tension: currentChartType === 'line' ? 0.4 : 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Évolution des intérêts gagnés',
                        color: chartColors.dark,
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    },
                    legend: {
                        display: currentChartType === 'pie',
                        position: 'right',
                        labels: {
                            color: chartColors.dark,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: chartColors.dark,
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: chartColors.primary,
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed.y || context.parsed;
                                return `Intérêts: ${value.toLocaleString('fr-FR', {
                                    style: 'currency',
                                    currency: 'EUR'
                                })}`;
                            }
                        }
                    }
                },
                scales: currentChartType !== 'pie' ? {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: chartColors.dark,
                            callback: function(value) {
                                return value.toLocaleString('fr-FR', {
                                    style: 'currency',
                                    currency: 'EUR'
                                });
                            }
                        },
                        grid: {
                            color: chartColors.light
                        }
                    },
                    x: {
                        ticks: {
                            color: chartColors.dark
                        },
                        grid: {
                            color: chartColors.light
                        }
                    }
                } : {}
            }
        };
        
        interetsChart = new Chart(ctx, config);
    }

    function toggleChartType(type) {
        currentChartType = type;
        
        document.querySelectorAll('.chart-btn').forEach(btn => btn.classList.remove('active'));
        document.getElementById(type + 'Btn').classList.add('active');
        
        if (allInterets.length > 0) {
            creerGraphique(allInterets);
        }
    }

    function resetPeriode() {
        const today = new Date();
        const currentYear = today.getFullYear();
        const currentMonth = String(today.getMonth() + 1).padStart(2, '0');
        
        document.getElementById('moisDebut').value = '01';
        document.getElementById('anneeDebut').value = currentYear;
        document.getElementById('moisFin').value = currentMonth;
        document.getElementById('anneeFin').value = currentYear;
        document.getElementById('resultatSection').style.display = 'none';
        document.getElementById('periodeAffichee').textContent = 'Sélectionnez une période pour voir les intérêts';
        document.getElementById('totalInterets').textContent = '0,00 €';
        
        if (interetsChart) {
            interetsChart.destroy();
            interetsChart = null;
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

<style>
    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 30px;
        padding: 20px 0;
        border-bottom: 2px solid var(--color-200);
    }
    
    .date-group {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
    .date-group label {
        font-weight: 600;
        color: var(--color-700);
        min-width: 30px;
    }
    
    .search-container {
        display: flex;
        gap: 15px;
        align-items: center;
        flex-wrap: wrap;
    }
    .chart-section {
        background: var(--color-50);
        border: 2px solid var(--color-200);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 30px;
    }
    
    .chart-section h3 {
        color: var(--color-800);
        margin-bottom: 20px;
        font-size: 18px;
        font-weight: 600;
    }
    
    .chart-container {
        position: relative;
        height: 400px;
        width: 100%;
        margin-bottom: 20px;
    }
    
    .chart-controls {
        display: flex;
        gap: 10px;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .chart-btn {
        background: var(--color-100);
        color: var(--color-700);
        border: 2px solid var(--color-300);
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .chart-btn:hover {
        background: var(--color-200);
        border-color: var(--color-400);
        transform: translateY(-1px);
    }
    
    .chart-btn.active {
        background: var(--color-600);
        color: var(--color-50);
        border-color: var(--color-700);
        box-shadow: 0 4px 8px rgba(168, 50, 50, 0.2);
    }
    .table-section {
        background: var(--color-50);
        border: 2px solid var(--color-200);
        border-radius: 12px;
        padding: 24px;
    }
    
    .table-section h3 {
        color: var(--color-800);
        margin-bottom: 20px;
        font-size: 18px;
        font-weight: 600;
    }
    #tableInteretsParMois {
        width: 100%;
        margin-top: 0;
    }
    .section {
        width: 100%;
        max-width: none;
    }
    
    .filter-section {
        width: 100%;
        box-sizing: border-box;
    }
    @media (max-width: 768px) {
        .chart-container {
            height: 300px;
        }
        
        .chart-controls {
            flex-direction: column;
            align-items: center;
        }
        
        .chart-btn {
            width: 100%;
            justify-content: center;
            max-width: 200px;
        }
        
        .chart-section, .table-section {
            padding: 16px;
        }
    }
</style>

<?php include 'includes/footer.php'; ?>