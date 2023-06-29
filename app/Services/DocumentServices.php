<?php

namespace Services;

use TCPDF;

class DocumentServices
{
    public function getListDocs(): array
    {
        $docs = include_once storageDir() . '/docs.php';
        $names = [];

        foreach ($docs as $doc) {
            $names[] = $doc['name'];
        }

        return $names;
    }

    public function getDocument($name)
    {
        $docs = include_once storageDir() . '/docs.php';
        $info = null;
        foreach ($docs as $item) {
            if ($item['name'] == trim($name)) {
                $info = $item;
                break;
            }
        }

        if (is_null($info))
            return 'null';

        $info['down'] = str_replace('#fio#', $_GET['fio'], $info['down']);

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', true);

        $pdf->AddPage();

// Первый текст
        $pdf->SetY(20); // Сдвигаем X на 30% от ширины страницы
        $pdf->SetX($pdf->getPageWidth() * 0.6); // Сдвигаем X на 30% от ширины страницы
        $pdf->SetFont('dejavuserif', '', 13, '', false);
        $pdf->MultiCell(0, 0, $info['up'], 0, 'L', false, 1, '', '', true);

// Заголовок
        $pdf->Ln(); // Новая строка
        $pdf->SetFont('dejavuserif', 'B', 16); // Жирный шрифт и увеличиваем размер для заголовка
        $pdf->Cell(0, 10, $info['title'], 0, 1, 'C'); // Выводим текст по центру

// Второй обычный текст
        $pdf->SetFont('dejavuserif', '', 13, '', false);
        $pdf->SetX($pdf->getPageWidth() * 0.1); // Возвращаем отступ в начальное положение
        $pdf->Write(0, $info['down'], '', 0, 'L', true, 0, false, false, 0);

// Дата
        $pdf->Ln(10); // Добавляем 2 строки
        $pdf->SetFont('dejavuserif', '', 13, '', false);
        $date = date('d.m.Y') . 'г.'; // Получаем текущую дату в формате 01.01.2023г.
        $pdf->Write(0, $date, '', 0, 'L', true, 0, false, false, 0);

        $pdf->Output('my_pdf.pdf', 'I');
        die();
    }
}