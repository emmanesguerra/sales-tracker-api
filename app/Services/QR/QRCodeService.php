<?php

namespace App\Services\QR;

use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Writer;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use App\Repositories\Item\ItemRepositoryInterface;

class QRCodeService
{
    protected ItemRepositoryInterface $itemRepository;

    public function __construct(ItemRepositoryInterface $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    public function generateQRCodeForPdf(array $data)
    {
        $items = $this->itemRepository->findByArray('id', $data);

        $htmlQrList = $this->generateQrCodesHtml($items);
        $pdfFilePath = $this->createPdf($htmlQrList);

        return response()->download($pdfFilePath);
    }

    /**
     * Generate QR codes and return as HTML.
     */
    private function generateQrCodesHtml(Collection  $items): string
    {
        $renderer = new GDLibRenderer(400);
        $writer = new Writer($renderer);
        $htmlQrList = '';

        foreach ($items as $item) {
            $qrCode = $writer->writeString($item->id . ' - ' . $item->name);
            $base64QrCode = base64_encode($qrCode);

            $htmlQrList .= $this->generateQrCodeHtml($base64QrCode, $item->name);
        }

        return $htmlQrList;
    }

    /**
     * Generate a single QR code HTML block.
     */
    private function generateQrCodeHtml(string $base64QrCode, string $itemName): string
    {
        return <<<HTML
            <div style="text-align:center; padding: 10px; float: left; width: 180px; border: 1px solid #000; margin: 10px;">
                <img src="data:image/png;base64,{$base64QrCode}" alt="QR Code" height="170px" width="170px"/>
                <span style="padding-top: -10px; font-size: 1.4rem">{$itemName}</span>
            </div>
        HTML;
    }
    
    private function generateQrCodeTableHtml(string $base64QrCode, string $itemName): string
    {
        return <<<HTML
            <tr>
                <td width="80%">
                    <span style="padding: 15px; font-size: 1.2rem">{$itemName}</span>
                </td>
                <td style="text-align:center;">
                    <img src="data:image/png;base64,{$base64QrCode}" alt="QR Code" height="110px" width="110px"/>
                </td>
            </tr>
        HTML;
    }

    /**
     * Generate and save a PDF file with the given QR code HTML content.
     */
    private function createPdf(string $htmlQrList): string
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml("<html><body>{$htmlQrList}</body></html>");
        // $dompdf->loadHtml("<html><body><table width='100%' style='border-collapse: collapse' border='1'>{$htmlQrList}</table></body></html>");
        $dompdf->render();

        // Define the file path
        $pdfFilePath = storage_path('app/public/qr_code_with_item.pdf');

        // Ensure the directory exists
        $directory = dirname($pdfFilePath);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Overwrite or create the file
        File::put($pdfFilePath, $dompdf->output());

        return $pdfFilePath;
    }
}
