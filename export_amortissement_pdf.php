<?php
require_once 'fpdf186/fpdf.php';

// Vérifier que les données ont été envoyées
if (!isset($_POST['simulationData'])) {
    die('Aucune donnée de simulation reçue.');
}

$simulationData = json_decode($_POST['simulationData'], true);
if (!$simulationData) {
    die('Données de simulation invalides.');
}

// Classe PDF personnalisée
class AmortissementPDF extends FPDF
{
    private $simulationData;
    
    public function __construct($simulationData) {
        parent::__construct();
        $this->simulationData = $simulationData;
    }
    
    // En-tête
    function Header()
    {
        // Logo ou titre de l'entreprise
        $this->SetFont('Arial', 'B', 20);
        $this->SetTextColor(51, 102, 153);
        $this->Cell(0, 15, 'FINANCE PRO', 0, 1, 'C');
        
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 10, 'Tableau d\'Amortissement', 0, 1, 'C');
        
        $this->Ln(5);
        
        // Informations du prêt
        $this->SetFont('Arial', '', 10);
        $this->SetFillColor(245, 245, 245);
        
        // Première ligne d'informations
        $this->Cell(50, 8, 'Client: ' . $this->simulationData['client'], 1, 0, 'L', true);
        $this->Cell(50, 8, 'Type: ' . $this->simulationData['typePret'], 1, 0, 'L', true);
        $this->Cell(50, 8, 'Montant: ' . $this->formatCurrency($this->simulationData['montantAccorde']), 1, 0, 'L', true);
        $this->Cell(40, 8, 'Durée: ' . $this->simulationData['dureeMois'] . ' mois', 1, 1, 'L', true);
        
        // Deuxième ligne d'informations
        $this->Cell(50, 8, 'Délai: ' . $this->simulationData['delai'] . ' mois', 1, 0, 'L', true);
        $this->Cell(50, 8, 'Mode: ' . $this->simulationData['modePaiement'], 1, 0, 'L', true);
        $this->Cell(50, 8, 'Début: ' . date('d/m/Y', strtotime($this->simulationData['dateDebut'])), 1, 0, 'L', true);
        $this->Cell(40, 8, 'Taux: ' . $this->simulationData['tauxAnnuel'] . '%', 1, 1, 'L', true);
        
        $this->Ln(10);
    }
    
    // Pied de page
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . ' - Généré le ' . date('d/m/Y à H:i'), 0, 0, 'C');
    }
    
    // Formatage monétaire
    function formatCurrency($amount)
    {
        return number_format($amount, 2, ',', ' ') . ' €';
    }
    
    // Calcul et génération du tableau
    function genererTableau()
    {
        $montant = $this->simulationData['montantAccorde'];
        $duree = $this->simulationData['dureeMois'];
        $tauxAnnuel = $this->simulationData['tauxAnnuel'];
        $assuranceAnnuel = $this->simulationData['assuranceAnnuel'];
        $dateDebut = $this->simulationData['dateDebut'];
        
        // Calculs
        $tauxMensuel = pow(1 + $tauxAnnuel / 100, 1/12) - 1;
        $assuranceMensuelle = $montant * $assuranceAnnuel / 100 / $duree;
        $annuite = $montant * ($tauxMensuel / (1 - pow(1 + $tauxMensuel, -$duree)));
        
        // En-tête du tableau
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(51, 102, 153);
        $this->SetTextColor(255, 255, 255);
        
        $this->Cell(15, 8, 'Mois', 1, 0, 'C', true);
        $this->Cell(25, 8, 'Date', 1, 0, 'C', true);
        $this->Cell(25, 8, 'Annuité', 1, 0, 'C', true);
        $this->Cell(25, 8, 'Intérêts', 1, 0, 'C', true);
        $this->Cell(25, 8, 'Assurance', 1, 0, 'C', true);
        $this->Cell(25, 8, 'Capital', 1, 0, 'C', true);
        $this->Cell(25, 8, 'Restant', 1, 0, 'C', true);
        $this->Cell(25, 8, 'Total', 1, 1, 'C', true);
        
        // Données du tableau
        $this->SetFont('Arial', '', 7);
        $this->SetTextColor(0, 0, 0);
        
        $capitalRestant = $montant;
        $totalInterets = 0;
        $totalAssurance = 0;
        $totalCapital = 0;
        
        for ($mois = 1; $mois <= $duree; $mois++) {
            // Vérifier si on doit créer une nouvelle page
            if ($this->GetY() > 250) {
                $this->AddPage();
                // Réafficher l'en-tête du tableau
                $this->SetFont('Arial', 'B', 8);
                $this->SetFillColor(51, 102, 153);
                $this->SetTextColor(255, 255, 255);
                
                $this->Cell(15, 8, 'Mois', 1, 0, 'C', true);
                $this->Cell(25, 8, 'Date', 1, 0, 'C', true);
                $this->Cell(25, 8, 'Annuité', 1, 0, 'C', true);
                $this->Cell(25, 8, 'Intérêts', 1, 0, 'C', true);
                $this->Cell(25, 8, 'Assurance', 1, 0, 'C', true);
                $this->Cell(25, 8, 'Capital', 1, 0, 'C', true);
                $this->Cell(25, 8, 'Restant', 1, 0, 'C', true);
                $this->Cell(25, 8, 'Total', 1, 1, 'C', true);
                
                $this->SetFont('Arial', '', 7);
                $this->SetTextColor(0, 0, 0);
            }
            
            // Calculs pour le mois
            $datePaiement = date('d/m/Y', strtotime($dateDebut . ' +' . ($mois - 1) . ' months'));
            $interets = $capitalRestant * $tauxMensuel;
            $capitalRembourse = $annuite - $interets;
            $capitalRestantApres = $capitalRestant - $capitalRembourse;
            $totalMensuel = $annuite + $assuranceMensuelle;
            
            $totalInterets += $interets;
            $totalAssurance += $assuranceMensuelle;
            $totalCapital += $capitalRembourse;
            
            // Couleur alternée pour les lignes
            $fill = ($mois % 2 == 0);
            if ($fill) {
                $this->SetFillColor(248, 248, 248);
            }
            
            $this->Cell(15, 6, $mois, 1, 0, 'C', $fill);
            $this->Cell(25, 6, $datePaiement, 1, 0, 'C', $fill);
            $this->Cell(25, 6, $this->formatCurrency($annuite), 1, 0, 'R', $fill);
            $this->Cell(25, 6, $this->formatCurrency($interets), 1, 0, 'R', $fill);
            $this->Cell(25, 6, $this->formatCurrency($assuranceMensuelle), 1, 0, 'R', $fill);
            $this->Cell(25, 6, $this->formatCurrency($capitalRembourse), 1, 0, 'R', $fill);
            $this->Cell(25, 6, $this->formatCurrency($capitalRestantApres > 0 ? $capitalRestantApres : 0), 1, 0, 'R', $fill);
            $this->Cell(25, 6, $this->formatCurrency($totalMensuel), 1, 1, 'R', $fill);
            
            $capitalRestant = $capitalRestantApres;
        }
        
        // Ligne de total
        $this->Ln(2);
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(51, 102, 153);
        $this->SetTextColor(255, 255, 255);
        
        $this->Cell(65, 8, 'TOTAUX', 1, 0, 'C', true);
        $this->Cell(25, 8, $this->formatCurrency($totalInterets), 1, 0, 'R', true);
        $this->Cell(25, 8, $this->formatCurrency($totalAssurance), 1, 0, 'R', true);
        $this->Cell(25, 8, $this->formatCurrency($totalCapital), 1, 0, 'R', true);
        $this->Cell(25, 8, '', 1, 0, 'R', true);
        $this->Cell(25, 8, $this->formatCurrency(($annuite * $duree) + ($assuranceMensuelle * $duree)), 1, 1, 'R', true);
        
        // Résumé
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 8, 'RÉSUMÉ DU PRÊT', 0, 1, 'C');
        
        $this->SetFont('Arial', '', 9);
        $this->SetFillColor(245, 245, 245);
        
        $this->Cell(95, 6, 'Montant emprunté:', 1, 0, 'L', true);
        $this->Cell(95, 6, $this->formatCurrency($montant), 1, 1, 'R', true);
        
        $this->Cell(95, 6, 'Total des intérêts:', 1, 0, 'L', true);
        $this->Cell(95, 6, $this->formatCurrency($totalInterets), 1, 1, 'R', true);
        
        $this->Cell(95, 6, 'Total des assurances:', 1, 0, 'L', true);
        $this->Cell(95, 6, $this->formatCurrency($totalAssurance), 1, 1, 'R', true);
        
        $this->Cell(95, 6, 'Coût total du crédit:', 1, 0, 'L', true);
        $this->Cell(95, 6, $this->formatCurrency($totalInterets + $totalAssurance), 1, 1, 'R', true);
        
        $this->Cell(95, 6, 'Montant total à rembourser:', 1, 0, 'L', true);
        $this->Cell(95, 6, $this->formatCurrency($montant + $totalInterets + $totalAssurance), 1, 1, 'R', true);
    }
}

// Créer le PDF
$pdf = new AmortissementPDF($simulationData);
$pdf->AddPage();
$pdf->genererTableau();

// Nom du fichier
$filename = 'Amortissement_' . str_replace(' ', '_', $simulationData['client']) . '_' . date('Y-m-d') . '.pdf';

// Envoyer le PDF au navigateur
$pdf->Output('D', $filename);
?>