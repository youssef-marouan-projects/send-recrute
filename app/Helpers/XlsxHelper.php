<?php

// Minimal .xlsx reader: no Composer dependency, just ZipArchive + SimpleXML
// (an .xlsx file is a zip archive of XML files). Reads the first sheet only,
// treats row 1 as headers, and returns an array of associative rows.
class XlsxHelper
{
    // Returns ['headers' => [...], 'rows' => [ ['email' => ..., 'post' => ...], ... ]]
    // or false on failure.
    public static function readFirstSheet($filePath)
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return false;
        }

        // 1. Shared strings (string cells reference an index into this table)
        $sharedStrings = [];
        $ssXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($ssXml !== false) {
            $ss = simplexml_load_string($ssXml);
            if ($ss !== false) {
                foreach ($ss->si as $si) {
                    // <si> can contain plain <t> or multiple <r><t> runs
                    if (isset($si->t)) {
                        $sharedStrings[] = (string) $si->t;
                    } else {
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string) $r->t;
                        }
                        $sharedStrings[] = $text;
                    }
                }
            }
        }

        // 2. Find the first worksheet (usually sheet1.xml)
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXml === false) {
            $zip->close();
            return false;
        }

        $sheet = simplexml_load_string($sheetXml);
        $zip->close();
        if ($sheet === false) {
            return false;
        }

        $grid = []; // rowIndex => [colLetter => value]
        foreach ($sheet->sheetData->row as $row) {
            $rowIndex = (int) $row['r'];
            foreach ($row->c as $c) {
                $ref  = (string) $c['r'];       // e.g. "B3"
                $type = (string) $c['t'];       // 's' = shared string, else numeric/inline
                $col  = preg_replace('/[0-9]/', '', $ref);

                $value = '';
                if (isset($c->v)) {
                    $v = (string) $c->v;
                    if ($type === 's') {
                        $value = $sharedStrings[(int) $v] ?? '';
                    } else {
                        $value = $v;
                    }
                } elseif (isset($c->is->t)) {
                    $value = (string) $c->is->t; // inline string
                }

                $grid[$rowIndex][$col] = $value;
            }
        }

        if (empty($grid)) {
            return false;
        }

        ksort($grid);
        $rowNumbers = array_keys($grid);
        $headerRowNum = array_shift($rowNumbers);
        $headerRow = $grid[$headerRowNum];

        // Map column letters to lower-cased header names
        $columnMap = [];
        foreach ($headerRow as $col => $label) {
            $columnMap[$col] = strtolower(trim($label));
        }

        $rows = [];
        foreach ($rowNumbers as $rn) {
            $rawRow = $grid[$rn];
            $assoc = [];
            foreach ($columnMap as $col => $label) {
                if ($label === '') continue;
                $assoc[$label] = isset($rawRow[$col]) ? trim($rawRow[$col]) : '';
            }
            // Skip fully empty rows
            if (implode('', $assoc) === '') continue;
            $rows[] = $assoc;
        }

        return [
            'headers' => array_values($columnMap),
            'rows'    => $rows,
        ];
    }
}
