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
    print_r($data, true)
);
        
$card_preferences = $data ["card_preferences"];
$period_settings = $data ["period_settings"];
$selected_students = $data ["students"];

$primary_color = ( $card_preferences ['primary_color_accent'] ?? '#808080' ) ;

$pri_color_preference_style = " 
background: {$primary_color}38;
      font-weight: bold;
      font-size : 13px "; //background
      
      

$secondary_color = ( $card_preferences ['secondary_color_accent'] ?? '#D9534F' ) ;
      
$sec_color_preference_style = 
" 2px solid {$secondary_color}CC"; //border
  

$container_border_stylexxx = "
border: 5px solid;
border-image: linear-gradient(
135deg, 
$primary_color,
$secondary_color) 1 ;
padding : 10px;
";


$container_border_style = "
border: 8px solid $primary_color ;
padding : 5px;
";


$logoPath_diamond = __DIR__ . '/../../../public/reportcard/assets/logo/logo_diamond.jpeg' ; //for watermark when school logo is unavailable

$logoUrl_from_db = $card_preferences ['logo_url'] ?? null; //later : inform user that no logo is in db if result = null

$logoPath = $logoUrl_from_db 
? 
__DIR__ . '/../../../public/reportcard/assets/logo/' . $logoUrl_from_db
:
$logoPath_diamond;

if (!file_exists($logoPath)) {
   $logoPath = $logoPath_diamond;
}


$logoExtension = pathinfo($logoPath, PATHINFO_EXTENSION);

$logoData = base64_encode(file_get_contents($logoPath));

$logoSrc = 'data:image/' . $logoExtension . ';base64,' . $logoData;



/*
        ob_start();
        echo "<pre>";
        
        var_dump (">extracted data into view", $data );
        echo "</pre>";
        
        include $path;

        return ob_get_clean();
  */
        
        ob_start();

include __DIR__ . '/../Views/reportcard/header.php';

foreach ($selected_students as $student) {

$passport_default = __DIR__ . '/../../../public/reportcard/assets/passport/passport_avatar.png' ; 

$passportUrl_from_db = $student ['passport_url'] ?? null; //later : inform user that no PASPORT FOR STUDENT in db if result = null


$passportPath = $passportUrl_from_db 
? 
__DIR__ . '/../../../public/reportcard/assets/passport/' . $passportUrl_from_db
:
$passport_default;

if (!file_exists($passportPath)) {
   $passportPath = $passport_default;
}

//for base64 conversion

$passportExtension = pathinfo($passportPath, PATHINFO_EXTENSION);

$passportData = base64_encode(file_get_contents($passportPath));

$passportSrc = 'data:image/' . $passportExtension . ';base64,' . $passportData;


echo "<div class=\"report_container\" style= \" $container_border_style \" > " ;


    include __DIR__ . '/../Views/reportcard/student_section.php';
}

include __DIR__ . '/../Views/reportcard/footer.php';

echo "</div>" ;

return ob_get_clean();

        
    }
}


























