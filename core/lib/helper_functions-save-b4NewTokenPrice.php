<?php

function authorizeCompilation($compilation) {
    return $compilation['school_id'] == $_SESSION['school_id'];
}

function report_error ($showErr) {
// 1️⃣ Error reporting (dev only)
if($showErr)
{
error_reporting(E_ALL);
ini_set('display_errors', 1);
}
}


function setFlash(string $type, string $text): void {
    $_SESSION['flash'][] = [
        'type' => $type,
        'text' => $text
    ];
}

function getFlash(): array {
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}



/* ==========================================================
   Simple Smart Logger
   - Creates a new log file every hour/minute
   - Stores logs under /logs folder
========================================================== */

if (!function_exists('log_debug')) {
    function log_debug($data, $prefix = 'debug') {
        /* ensure logs directory exists */
        $logDir = __DIR__ . '/../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0775, true);
        }

        /* create log filename like: debug_2025-10-13_14-32.log */
        $timestamp = date('Y-m-d_H-i');
        $filename = "{$logDir}/{$prefix}_{$timestamp}.log";

        /* convert any variable to readable format */
        $output = print_r($data, true);

        /* build log entry with timestamp inside file */
        $logEntry = "[" . date('Y-m-d H:i:s') . "] " . $output . "\n";

        /* append to the right file */
        file_put_contents($filename, $logEntry, FILE_APPEND);
    }
}


/****** echo token info ******/
function echoTokenPaymentForm ()
    {
    
$appName = $_SESSION ['appName'];
/*********  buy tokens ********/
echo <<< _HTML

<div class="payment_form" style="display:none;">
<form method="POST" action="/{$appName}/paystack/initialize" class="card p-4 shadow-sm">

    <h4 class="mb-3">Buy Tokens</h4>

    <div class="form-check mb-2">
        <input class="form-check-input" type="radio" name="tier" value="starter" id="tierStarter" checked>
        <label class="form-check-label" for="tierStarter">
            <strong>Starter</strong> — 10 tokens
            <span class="text-muted">(₦1,200)</span>
        </label>
    </div>

    <div class="form-check mb-2">
        <input class="form-check-input" type="radio" name="tier" value="basic" id="tierBasic">
        <label class="form-check-label" for="tierBasic">
            <strong>Basic</strong> — 25 tokens
            <span class="text-muted">(₦2,500)</span>
        </label>
    </div>

    <div class="form-check mb-2">
        <input class="form-check-input" type="radio" name="tier" value="pro" id="tierPro">
        <label class="form-check-label" for="tierPro">
            <strong>Pro</strong> — 60 tokens
            <span class="text-muted">(₦5,400)</span>
        </label>
    </div>

    <div class="form-check mb-4">
        <input class="form-check-input" type="radio" name="tier" value="school" id="tierSchool">
        <label class="form-check-label" for="tierSchool">
            <strong>School</strong> — 150 tokens
            <span class="text-muted">(₦12,000)</span>
        </label>
    </div>

    <div class="alert alert-light border mb-4">
        <div class="d-flex justify-content-between">
            <span>Tokens</span>
            <strong><span id="tokens">10</span></strong>
        </div>
        <div class="d-flex justify-content-between">
            <span>Total</span>
            <strong>₦<span id="amount">1,200</span></strong>
        </div>
    </div>

    <button type="submit" class="btn btn-success w-100">
        Proceed to Payment
    </button>

</form>
</div>

<hr>
           
_HTML;

    }

/*********/

function echoTokenSummary($dailyFreeQuota ,  $freeUsedToday,$tokenBalance)
    {
   
/***  token balance  ****/
 $remainingFreeQuota = max(0, $dailyFreeQuota - $freeUsedToday) ;

  
echo <<< _HTML

<div class="container mt-4 mb-2">

    <div class="row g-3">

        <!-- Daily Free Prints -->
        <div class="col-md-6">
            <div class="card border-success shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Daily Free Prints</h6>sss

                    <h1 class="fw-bold text-success">
                     $remainingFreeQuota
                    </h1>

                    <small class="text-muted">
                   $freeUsedToday of $dailyFreeQuota  used today
                    </small>

                    <hr>

                    <span class="badge bg-success">
                        Resets daily
                    </span>
                </div>
            </div>
        </div>

        <!-- Paid Token Balance -->
        <div class="col-md-6">
            <div class="card border-primary shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Paid Token Balance</h6>

                    <h1 class="fw-bold text-primary">
                         $tokenBalance 
                    </h1>

                    <small class="text-muted">
                        Used for extra prints
                    </small>

                    <hr>
                    
                    <button class="btn btn-primary btn-sm"  onclick="$('.payment_form').fadeToggle();">
  Buy Tokens (Toggle)
</button>

                </div>
            </div>
        </div>

    </div>

</div>
_HTML;

        
     }

/********
***
****
*********/
/**
 * Generates a standardized filename, removing spaces/dashes and limiting subject length.
 */
function generate_exam_filename($exam_body, $year, $subject, $q_label, $filetype = 'png') {
    // 1. Helper to remove spaces and dashes
    $cleaner = function($str) {
        return str_replace([' ', '-'], '', strtolower(trim($str)));
    };

    // 2. Clean basic inputs
    $exam_body = $cleaner($exam_body);
    $q_label   = $cleaner($q_label);
    $filetype  = $cleaner($filetype);

    // 3. Clean subject and take first 6 characters
    $short_subject = substr($cleaner($subject), 0, 6);

    // 4. Construct the filename
    return "{$exam_body}_{$year}_{$short_subject}_q{$q_label}.{$filetype}";
}



?>




























