<!DOCTYPE html>
<html>
<head>
    <style>

        /* =========================
           PAGE SETUP (A4 SAFE)
        ========================== */

        @page {
            margin: 20px;
        }

        body {
           /* font-family: "Times New Roman", serif; */
       font-family: DejaVu Sans, sans-serif; 
          /*  font-size: 12px; */
            font-size:11px; 
            line-height:1.0;
        }

        /* =========================
           PAGE BREAK CONTROL
        ========================== */

        .page-break {
            page-break-after: always;
        }

        .no-break {
            page-break-inside: avoid;
        }

        /* =========================
           TABLE SAFETY (VERY IMPORTANT FOR DOMPDF)
        ========================== */

        table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        tr, td, th {
            page-break-inside: avoid;
            vertical-align: top;
        }

    </style>
</head>
<body>

<!-- remember to inform user that : clean logo with white/no background = better watermarks  -->

<?php 

$schoolName = 
strtoupper ($card_preferences['printed_name'] ?? "");
$useLogoWatermark = $card_preferences ['logo_watermark'] ?? null;

if ( $useLogoWatermark ) : ?>

<div style="
    position: fixed;
    top: 10%;
    left: 10%;
    width: 85%;
    height: 85%;
    opacity: 0.15;
    z-index: -1;
">
    <img src="<?= $logoSrc ?>" style="width:100%;">
</div>

<?php else : ?>

<div style="
    position: fixed;
    top: -15%;
    left: -40%;
    width: 200%;
    white-space: nowrap;
    overflow: visible;
    text-align: center;
   line-height : 1.5;
    font-size: 70px;
    color: rgba(0,0,0,0.04);
    transform: rotate(-30deg);
    z-index: -1;
    font-weight: bold;
    letter-spacing: 5px;
">
      &bull; <?= $schoolName ?> &bull; <?= $schoolName ?> &bull;  <br>

          &bull; <?= $schoolName ?> &bull; <?= $schoolName ?> &bull;  <br>

              &bull; <?= $schoolName ?> &bull; <?= $schoolName ?> &bull;  <br>

                  &bull; <?= $schoolName ?> &bull; <?= $schoolName ?> &bull;  <br>

                      &bull; <?= $schoolName ?> &bull; <?= $schoolName ?> &bull;  <br>

                          &bull; <?= $schoolName ?> &bull; <?= $schoolName ?> &bull;  <br>

                              &bull; <?= $schoolName ?> &bull; <?= $schoolName ?> &bull;  <br>

          &bull; <?= $schoolName ?> &bull; <?= $schoolName ?> &bull;  <br>

              &bull; <?= $schoolName ?> &bull; <?= $schoolName ?> &bull;  <br>

               &bull; <?= $schoolName ?> &bull; <?= $schoolName ?> &bull;  <br>

                &bull; <?= $schoolName ?> &bull; <?= $schoolName ?> &bull;  <br>

          &bull; <?= $schoolName ?> &bull; <?= $schoolName ?> &bull;  <br>

           &bull; <?= $schoolName ?> &bull; <?= $schoolName ?> &bull;  <br>

</div>

<?php endif; ?>








