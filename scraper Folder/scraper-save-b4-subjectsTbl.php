<?php
// ======================================================
// Termux CLI Scraper - FULL COMPLETE EDITION (2026)
// ======================================================

if (php_sapi_name() !== 'cli') die("Run from CLI only.\n");

set_time_limit(0);
ini_set('memory_limit', '512M');

require 'config4scraper.php'; // Expects $pdo

// ----------------- CLI ARGUMENTS -----------------
$subject_input = $argv[1] ?? null;
$startPage     = isset($argv[2]) ? (int)$argv[2] : 1;
$stopPage      = isset($argv[3]) ? (int)$argv[3] : 1025;

if (!$subject_input) {
    die("Usage: php scraper.php <subject> <start_page> <end_page>\nExample: php scraper.php physics 1 1025\n");
}

// ----------------- CONFIG & PATHS -----------------
$base_url = "https://myschool.ng/classroom/" . strtolower(trim($subject_input));
$logFile = "scrape.log";
$imgFolder = "images";

if (!is_dir($imgFolder)) mkdir($imgFolder, 0777, true);

// ----------------- SYLLABUS MAPPING -----------------
function detectTopic($subject, $questionText) {
    $questionText = strtolower($questionText);
    
    $syllabus = [
        'physics' => [
            'Interaction of Matter' => ['matter', 'quantity', 'unit', 'density', 'pressure', 'archimedes'],
            'Motion' => ['velocity', 'acceleration', 'force', 'friction', 'viscosity', 'circular motion'],
            'Heat Energy' => ['temperature', 'expansion', 'gas law', 'boyle', 'charles', 'latent heat'],
            'Waves' => ['wave', 'frequency', 'wavelength', 'reflection', 'refraction', 'diffraction', 'light', 'sound'],
            'Electricity' => ['current', 'voltage', 'resistance', 'circuit', 'ohm', 'capacitor', 'inductor'],
            // ... Add your other topics here
        ],
        'biology' => [
            'Cell Biology' => ['cell','nucleus','cytoplasm','organelle'],
            'Genetics' => ['gene','allele','dna','chromosome','inheritance']
        ],
        // ... Add your other subjects here
    ];

    if (!isset($syllabus[$subject])) return "General";

    foreach ($syllabus[$subject] as $topic => $keywords) {
        foreach ($keywords as $word) {
            if (strpos($questionText, $word) !== false) return $topic;
        }
    }
    return "General";
}

// ----------------- HELPERS -----------------

function formatSubject($sub) {
    return strtolower(substr(str_replace([' ', '-'], '', $sub), 0, 6));
}


// ----------------- HELPERS -----------------

function logMessage($msg) {
    global $logFile; // Added this line to fix the error
    $content = "[".date("H:i:s")."] $msg\n";
    file_put_contents($logFile, $content, FILE_APPEND);
    echo $content;
}

function saveImage($url, $newName) {
    global $imgFolder; // Ensure this is also global
    $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION)) ?: 'png';
    $filename = $newName . "." . $extension;
    $fullPath = $imgFolder . "/" . $filename;
    
    if (file_exists($fullPath)) return $filename;

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT => "Mozilla/5.0",
        CURLOPT_TIMEOUT => 10
    ]);
    $content = curl_exec($ch);
    curl_close($ch);

    if ($content) {
        file_put_contents($fullPath, $content);
        return $filename;
    }
    return null;
}



function fetchPage($url, $maxRetries = 2) {
       $userAgents = [
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36",
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
        "Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Mobile Safari/537.36",
        "Mozilla/5.0 (iPhone; CPU iPhone OS 17_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Mobile/15E148 Safari/604.1",
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:122.0) Gecko/20100101 Firefox/122.0",
        "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36"
    ];

    
    for ($i = 1; $i <= $maxRetries; $i++) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_USERAGENT => $userAgents[array_rand($userAgents)],
            // ADD THESE TWO LINES FOR BETTER STEALTH:
            CURLOPT_HTTPHEADER => [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                'Accept-Language: en-US,en;q=0.5',
                'Referer: https://myschool.ng/classroom'
            ],
        ]);
        
        $html = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch); // Catch the specific error
        curl_close($ch);

        if ($http_code === 200 && $html) return $html;
        
        // This will tell us if it's a "Connection Refused", "Timeout", or "403 Forbidden"
        logMessage("Attempt $i: HTTP $http_code | Error: $error");
        
        sleep(5); 
    }
    return false;
}




// ----------------- MAIN LOOP -----------------
logMessage(">>> STARTING DEEP SCRAPE: $subject_input (Pages $startPage to $stopPage)");

for ($currentPage = $startPage; $currentPage <= $stopPage; $currentPage++) {
    $url = $base_url . "?page=" . $currentPage;
    logMessage("--------------------------------------------------");
    logMessage("MAIN PAGE $currentPage: $url");

    $html = fetchPage($url, 3);
    if (!$html) {
        logMessage("ERROR: Skip page $currentPage (fetch failure).");
        continue;
    }

    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $questions = $xpath->query('//div[contains(@class,"question-item")]');
    
    if (!$questions || $questions->length === 0) {
        file_put_contents("debug_page_{$currentPage}.html", $html);
        logMessage("DIAGNOSTIC: No questions found. Saved debug_page_{$currentPage}.html");
        continue; 
    }

    foreach ($questions as $i => $q) {
        $q_order = (($currentPage - 1) * 20) + ($i + 1); // Assuming 20 questions/page
        $short_subject = formatSubject($subject_input);

        // 1. Meta Data
        $spanNode = $xpath->query('.//span[contains(@class,"badge")]', $q);
        $exam_body = "unknown"; $year = "0000";
        if ($spanNode->length) {
            $parts = preg_split('/\s+/', trim($spanNode->item(0)->textContent));
            $exam_body = strtolower(str_replace([' ', '-'], '', $parts[0] ?? 'unknown'));
            $year = $parts[1] ?? '0000';
        }

        // 2. Question Text
        $qTextNode = $xpath->query('.//div[contains(@class,"question-desc")]', $q);
        $questionText = $qTextNode->length ? trim(preg_replace('/\s+/', ' ', strip_tags($qTextNode->item(0)->textContent))) : "";

        // 3. Topic Detection
        $topic = detectTopic($subject_input, $questionText);

        // 4. Options
        $options = [];
        $liNodes = $xpath->query('.//ul/li | .//ol/li', $q);
        foreach ($liNodes as $li) {
            $strong = $xpath->query('.//strong', $li);
            $key = $strong->length ? rtrim(trim($strong->item(0)->textContent), '.') : null;
            $value = trim(str_replace($strong->item(0)->textContent ?? '', '', $li->textContent));
            if ($key) $options[$key] = $value;
        }
        $type = !empty($options) ? 'mcq' : 'theory';

        // 5. DEEP SCRAPE ANSWER
        $answer = null;
        $ansLinkNode = $xpath->query('.//a[contains(translate(text(), "VIEW ANSWER", "view answer"), "view answer")]', $q);
        if ($ansLinkNode->length > 0) {
            $ansUrl = $ansLinkNode->item(0)->getAttribute('href');
            $ansHtml = fetchPage($ansUrl, 1);
            if ($ansHtml) {
                $ansDom = new DOMDocument(); @$ansDom->loadHTML($ansHtml);
                $ansXpath = new DOMXPath($ansDom);
                $ansTextNode = $ansXpath->query('//h5[contains(@class,"text-success")]');
                if ($ansTextNode->length > 0) {
                    $raw = $ansTextNode->item(0)->textContent;
                    if (preg_match('/Option\s+([A-E])/i', $raw, $m)) { $answer = strtoupper($m[1]); }
                    else { $answer = trim(str_replace('Correct Answer:', '', $raw)); }
                }
            }
            usleep(300000); // Small 0.3s break
        }

        // 6. IMAGES
        $imgNodes = $xpath->query('.//img', $q);
        $local_images = [];
        foreach ($imgNodes as $idx => $img) {
            $suffix = ($imgNodes->length > 1) ? "_" . ($idx + 1) : "";
            // Name: waec_2023_physic_q45.png
            $imgName = "{$exam_body}_{$year}_{$short_subject}_q{$q_order}{$suffix}";
            $saved = saveImage($img->getAttribute('src'), $imgName);
            if ($saved) $local_images[] = $saved;
        }

        // 7. DB INSERT
        $stmt = $pdo->prepare("
            INSERT IGNORE INTO questions 
            (exam_body, year, subject, topic, type, q_order, q_label, question_text, options, answer, has_image, image_url) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $exam_body, $year, $short_subject, $topic, $type, $q_order, $q_order, 
            $questionText, !empty($options) ? json_encode($options) : null, 
            $answer, !empty($local_images) ? 1 : 0, 
            !empty($local_images) ? json_encode($local_images) : null
        ]);
    }

    logMessage("Page $currentPage done. Saved to DB.");
    sleep(rand(4, 7)); // Breach protection delay
}
logMessage(">>> ALL DONE.");
?>

