<?php

namespace ReportCard\Core;

class View
{
    /**
     * Render a view file with data
     */
    public static function render(string $view, array $data = []): string
    {
        $path = __DIR__ . '/../Views/' . $view . '.php';

        if (!file_exists($path)) {
            throw new \Exception("View not found: " . $view);
        }

        extract($data);
        
                file_put_contents(
    'debug-render-view.log',
    "\n>=== in View::render - data in ===\n".
    print_r($data, true),
    FILE_APPEND
);
        
//$settings = $data ["card_preferences"];
$selectedStudents = $data ["students"];
 
/******************************/


$primary_color = ( $card_preferences ['primary_color_accent'] ?? '#808080' ) ;

$pri_color_preference_style = " 
background: {$primary_color}15;
      font-weight: bold;
      font-size : 13px "; //background
      
      

$secondary_color = ( $card_preferences ['secondary_color_accent'] ?? '#D9534F' ) ;
      
$sec_color_preference_style = 
" 2px solid {$secondary_color}E6"; //border
  

$container_border_stylexxx = "
border: 5px solid;
border-image: linear-gradient(
135deg, 
$primary_color,
$secondary_color) 1 ;
padding : 10px;
";


$container_border_style = "
border: 3px solid $primary_color ;
padding : 5px;
";


echo $container_border_style ;


$logoPath_diamond = __DIR__ . '/../../public/assets/logo/logo_diamond.jpeg' ; //for watermark when school logo is unavailable

$logoUrl_from_db = $card_preferences ['logo_url'] ?? null; //later : inform user that no logo is in db if result = null

$logoPath = $logoUrl_from_db 
? 
__DIR__ . '/../../public/assets/logo/' . $logoUrl_from_db
:
$logoPath_diamond;

if (!file_exists($logoPath)) {
   $logoPath = $logoPath_diamond;
}


$logoExtension = pathinfo($logoPath, PATHINFO_EXTENSION);

$logoData = base64_encode(file_get_contents($logoPath));

$logoSrc = 'data:image/' . $logoExtension . ';base64,' . $logoData;


 
/**************************/
 
        ob_start();

include __DIR__ . '/../Views/reportcard/header.php';

foreach ($selectedStudents as $student) {
    include __DIR__ . '/../Views/reportcard/student_section.php';
}

include __DIR__ . '/../Views/reportcard/footer.php';

return ob_get_clean();

        
    }
}


























