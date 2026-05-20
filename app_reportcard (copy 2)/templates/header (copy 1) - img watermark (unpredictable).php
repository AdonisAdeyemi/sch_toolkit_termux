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
            font-family: "Times New Roman", serif;
          /*  font-size: 12px; */
            font-size:11px; 
            line-height:1.2"
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
<div style="
    position: fixed;
    top: 10%;
    left: 10%;
    width: 85%;
    height: 85%;
    opacity: 0.1;
    z-index: -1;
">
    <img src="<?= $logoSrc ?>" style="width:100%;">
</div>








