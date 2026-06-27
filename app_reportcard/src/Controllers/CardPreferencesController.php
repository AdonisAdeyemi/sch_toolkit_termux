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
        
   $prefs['logo_url'] = $this->getAssetUrl(
    'logo',
    $prefs['logo_url'] ?? null
);     
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


/*
            $allowed = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp'
            ];

            $mime = mime_content_type(
                $_FILES['logo']['tmp_name']
            );

            if (!isset($allowed[$mime])) {
                throw new \Exception(
                    'Only JPG, PNG and WEBP images are allowed.'
                );
            }

            $extension = $allowed[$mime];

            $filename =
                'school_' .
                $schoolId .
                '_' .
                time() .
                '.' .
                $extension;

            $uploadDir =
                PROJECT_ROOT .
                '/public/reportcard/assets/logo/';

            if (!is_dir($uploadDir)) {
                mkdir(
                    $uploadDir,
                    0755,
                    true
                );
            }

            $destination =
                $uploadDir .
                $filename;

            if (
                !move_uploaded_file(
                    $_FILES['logo']['tmp_name'],
                    $destination
                )
            ) {
                throw new \Exception(
                    'Failed to upload logo.'
                );
            }


            $data['logo_url'] = $filename;
*/

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









