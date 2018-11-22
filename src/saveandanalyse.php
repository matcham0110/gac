<?php
/**
 * Classe SaveAndAnalyse pour l'exercice
 * @Author Mathieu CHAMMAH
 * @For GAC TECHNOLOGY
 */
class SaveAndAnalyse
{
  private $csv_file;
  private $db;

  /**
   * Constructeur
   * Charge le fichier CSV
   */
  function __construct($csv_file_input = null)
  {
    $this->csv_file = $csv_file_input;
    if ($this->csv_file === null || strpos($this->csv_file, '.csv') === false)
      throw new Exception("Vous devez préciser un fichier, et un fichier (.csv) !");
    if (!$this->dbConnexion())
      throw new Exception("Erreur dans la connexion à la base de donnée");
    return true;
  }
  /**
   * VERIFICATION DES DONNEES A INSERT DANS LA BDD
   */
  private function securityCheck($tab) {
    if (!is_numeric($tab[0]) || !is_numeric($tab[1]) || !is_numeric($tab[2]))
      return false;
    else if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", DateTime::createFromFormat("d/m/Y", $tab[3])->format("Y-m-d")))
      return false;
    else if (!preg_match('#^([01][0-9])|(2[0-4])(:[0-5][0-9]){1,2}$#', $tab[4]))
      return false;
    else if (empty($tab[7]))
      return false;
    else
      return true;
  }
  /**
   * SCRIPT D'IMPORT DANS LA BASE DE DONNEE
   */
  public function importIntoDb() {
      if(($handle = fopen($this->csv_file, "r")) !== FALSE)
        {
          $count = 1;
          while(($col = fgetcsv($handle, 1000, ';', '"')) !== FALSE)
          {
              if ($count >= 4) { // Data a partir de la ligne 4
                if ($this->securityCheck($col)) {
                  $compte_facture = $col[0];
                  $id_facture = $col[1];
                  $id_abonne = $col[2];
                  $cdate = DateTime::createFromFormat("d/m/Y", $col[3])->format("Y-m-d"); // Format DATE de Mysql
                  $ctime = $col[4];
                  $volume_reel = $col[5];
                  $volume_facture = $col[6];
                  $type = $col[7];
                  $sql 	= "INSERT INTO `calls_details` VALUES ($compte_facture, $id_facture, $id_abonne, '$cdate', '$ctime', '$volume_reel', '$volume_facture', \"$type\")";
                  if (!mysqli_query($this->db, $sql)) {
                        printf("Message d'erreur : %s\n", mysqli_error($this->db));
                    }
                }
              }
            $count++;
          }
          return true;
        }
      else
        return false;
    }
    /**
     * REQUETE SQL POUR LA DUREE TOTALE DES APPELS EMIS DEPUIS $date_input (format SQL)
     */
    public function req1($date_input) {
      $sql = "SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( `ctime` ) ) ) AS SUMOFTIME FROM `calls_details` WHERE `cdate` > '$date_input' AND `type`LIKE '%appel%'";
      $query = mysqli_query($this->db, $sql);
      echo "La durée totale réelle des appels effectués après le  $date_input est : <strong>". mysqli_fetch_array($query)['SUMOFTIME']."</strong><br/>";
      return true;
    }
    /**
     * REQUETE SQL POUR LES VOLULES DATA FACTURES EN DEHORS DE LA TRANCHE HORAIRE 8h00-18h00, PAR ABONNE
     */
    public function req2() {
      $sql = "SELECT SUM(`volume_facture`) AS `TOP10`, `id_abonne` FROM `calls_details` WHERE `type` LIKE '%connexion%' AND (`ctime` > '18:00:00' OR `ctime` < '08:00:00')  GROUP BY `id_abonne` ORDER BY `TOP10` DESC LIMIT 10";
      $query = mysqli_query($this->db, $sql);
      echo "Voici le TOP 10 des volumes data facturés en dehors de la tranche horaire 8h00-18h00, par abonné :<br/><ul><strong>";
      while ($row = mysqli_fetch_array($query))
        {
          echo "<li>".$row['TOP10']." de l'abonné n°".$row['id_abonne']."</li>";
        }
      echo "</strong></ul>";
      return true;
    }
    /**
     * REQUETE SQL POUR LA QUANTITE TOTALE DE SMS ENVOYES PAR L'ENSEMBLE DES ABONNES
     */
    public function req3() {
      $sql = "SELECT COUNT(*) AS `TOTAL` FROM `calls_details` WHERE `type` LIKE '%sms%'";
      $query = mysqli_query($this->db, $sql);
      echo "La quantité totale de SMS envoyés par l'ensemble des abonnés est :<strong>".mysqli_fetch_array($query)['TOTAL']."</strong>";
      return true;
    }
    /**
     * CONNEXION A LA BASE DE DONNEE
     */
    private function dbConnexion() {
        $this->db = mysqli_connect('localhost', 'root', '', 'gac');
        if (!$this->db) {
            echo "Error: " . mysqli_connect_error();
        	return false;
        }
        return true;
    }
}

// Object $saa (SaveAndAnalyse)
try {
    $saa = new SaveAndAnalyse("../config/csv/tickets_appels_201202.csv");
} catch (Exception $e) {
    echo $e->getMessage();
    die();
}
if ($saa->importIntoDb())
  {
    $saa->req1("2012-02-15");
    $saa->req2();
    $saa->req3();
  }
else
  echo "Vous devez charger un fichier valide !";
?>
