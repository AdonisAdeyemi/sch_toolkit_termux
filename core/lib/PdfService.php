<?php
namespace Core\Lib;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    private Dompdf $dompdf;

    public function __construct()
    {
        $options = new Options();

        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Times-Roman');

        $this->dompdf = new Dompdf($options);
    }

    /**
     * Render PDF and stream to browser
     */
    public function stream(string $html, string $filename = 'document.pdf', bool $download = false): void
    {
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();

        $this->dompdf->stream($filename, [
            'Attachment' => $download
        ]);
    }

    /**
     * Optional: return raw PDF binary (useful for storage/email later)
     */
    public function output(string $html): string
    {
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();

        return $this->dompdf->output();
    }
}
