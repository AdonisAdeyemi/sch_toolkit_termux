<?php

namespace ReportCard\Controllers;

use ReportCard\Models\CardPreferencesModel;
use Core\Controllers\BaseController;
use PDO;

class CardPreferencesController extends BaseController
{
    private CardPreferencesModel $model;

    public function __construct(PDO $pdo)
    {
        $this->model = new CardPreferencesModel($pdo);
    }

    public function index(): void
    {
        $schoolId = $_SESSION['school_id'];

        $prefs = $this->model->getCardPreferences($schoolId);
        
  $folderName = 'logo';
  $fileName =     $prefs['logo_url'] ?? null ;
  
$prefs['logo_url'] = getAssetUrl( $folderName , $fileName );     
$default = [
'primary_color_accent' => "#ff1122" ,
'secondary_color_accent' => "#1122ff"
] ;

        $this->render('card_preferences/index', [
        'title' => "Card Design Preferences",
        'appName' => $this->appName(),
            'prefs' => $prefs,
          'default' => $default
        ]);
    }

    
public function save(): void
{
    header('Content-Type: application/json');

    try {
    
        $schoolId = (int) $_SESSION['school_id'];

            
        //xxxxxxxxxxxxxxxxxxxxxxx

        $data = $_POST;
        
       if (empty($data)) {
         throw new \Exception(
         'Empty data. Nothing to save.'
              );
            }

        /*
        |--------------------------------------------------------------------------
        | Logo Upload
        |--------------------------------------------------------------------------
        */
        if (
            isset($_FILES['logo']) &&
            $_FILES['logo']['error'] === UPLOAD_ERR_OK
        ) {



$data['logo_url'] = $this->uploadImage(
    $_FILES['logo'],
    'logo',
    'school_' . $schoolId
);
        }

        $ok = $this->model->updateCardPreferences(
            $schoolId,
            $data
        );

        echo json_encode([
            'status' => $ok
                ? 'success'
                : 'error'
        ]);

    } catch (\Throwable $e) {

        http_response_code(500);

        echo json_encode([
            'status'  => 'error',
            'message' => $e->getMessage()
        ]);
    }
}




}









