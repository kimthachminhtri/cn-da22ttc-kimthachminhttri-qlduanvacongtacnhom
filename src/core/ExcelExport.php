<?php

declare(strict_types=1);

/**
 * Excel Export Class
 * 
 * Tạo file Excel (.xlsx) không cần thư viện bên ngoài
 * Sử dụng Office Open XML format
 * 
 * @package Core
 */

namespace Core;

class ExcelExport
{
    private string $title;
    private array $sheets = [];
    private string $creator = 'TaskFlow';
    
    public function __construct(string $title = 'Report')
    {
        $this->title = $title;
    }

    /**
     * Add a sheet to the workbook
     */
    public function addSheet(string $name, array $headers, array $data, array $options = []): self
    {
        $this->sheets[] = [
            'name' => substr(preg_replace('/[\\\\\/\?\*\[\]:]+/', '', $name), 0, 31),
            'headers' => $headers,
            'data' => $data,
            'options' => array_merge([
                'headerColor' => '3B82F6',
                'freezeHeader' => true,
                'autoFilter' => true,
                'columnWidths' => [],
            ], $options)
        ];
        return $this;
    }

    /**
     * Generate and download Excel file
     */
    public function download(string $filename = 'report'): void
    {
        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        
        echo $this->generate();
    }

    /**
     * Generate Excel file content
     */
    public function generate(): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx');
        $zip = new \ZipArchive();
        $zip->open($tempFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        // Add required files
        $zip->addFromString('[Content_Types].xml', $this->getContentTypes());
        $zip->addFromString('_rels/.rels', $this->getRels());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->getWorkbookRels());
        $zip->addFromString('xl/workbook.xml', $this->getWorkbook());
        $zip->addFromString('xl/styles.xml', $this->getStyles());
        $zip->addFromString('xl/sharedStrings.xml', $this->getSharedStrings());
        $zip->addFromString('docProps/app.xml', $this->getAppProps());
        $zip->addFromString('docProps/core.xml', $this->getCoreProps());

        // Add sheets
        foreach ($this->sheets as $index => $sheet) {
            $zip->addFromString('xl/worksheets/sheet' . ($index + 1) . '.xml', $this->getSheet($sheet));
        }

        $zip->close();
        
        $content = file_get_contents($tempFile);
        unlink($tempFile);
        
        return $content;
    }

    private function getContentTypes(): string
    {
        $sheets = '';
        foreach ($this->sheets as $index => $sheet) {
            $sheets .= '<Override PartName="/xl/worksheets/sheet' . ($index + 1) . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        }
        
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
    <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
    <Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
    <Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
    <Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
    ' . $sheets . '
</Types>';
    }

    private function getRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
    <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
    <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
</Relationships>';
    }

    private function getWorkbookRels(): string
    {
        $rels = '';
        foreach ($this->sheets as $index => $sheet) {
            $rels .= '<Relationship Id="rId' . ($index + 1) . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . ($index + 1) . '.xml"/>';
        }
        $nextId = count($this->sheets) + 1;
        
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    ' . $rels . '
    <Relationship Id="rId' . $nextId . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
    <Relationship Id="rId' . ($nextId + 1) . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
</Relationships>';
    }

    private function getWorkbook(): string
    {
        $sheets = '';
        foreach ($this->sheets as $index => $sheet) {
            $sheets .= '<sheet name="' . $this->xmlEncode($sheet['name']) . '" sheetId="' . ($index + 1) . '" r:id="rId' . ($index + 1) . '"/>';
        }
        
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <sheets>' . $sheets . '</sheets>
</workbook>';
    }

    private function getStyles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
    <fonts count="3">
        <font><sz val="11"/><name val="Calibri"/></font>
        <font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>
        <font><b/><sz val="12"/><name val="Calibri"/></font>
    </fonts>
    <fills count="4">
        <fill><patternFill patternType="none"/></fill>
        <fill><patternFill patternType="gray125"/></fill>
        <fill><patternFill patternType="solid"><fgColor rgb="FF3B82F6"/></patternFill></fill>
        <fill><patternFill patternType="solid"><fgColor rgb="FFF3F4F6"/></patternFill></fill>
    </fills>
    <borders count="2">
        <border/>
        <border>
            <left style="thin"><color rgb="FFE5E7EB"/></left>
            <right style="thin"><color rgb="FFE5E7EB"/></right>
            <top style="thin"><color rgb="FFE5E7EB"/></top>
            <bottom style="thin"><color rgb="FFE5E7EB"/></bottom>
        </border>
    </borders>
    <cellStyleXfs count="1">
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
    </cellStyleXfs>
    <cellXfs count="4">
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
        <xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1">
            <alignment horizontal="center" vertical="center" wrapText="1"/>
        </xf>
        <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"/>
        <xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFill="1" applyBorder="1"/>
    </cellXfs>
</styleSheet>';
    }

    private function getSharedStrings(): string
    {
        $strings = [];
        foreach ($this->sheets as $sheet) {
            foreach ($sheet['headers'] as $header) {
                $strings[] = $header;
            }
            foreach ($sheet['data'] as $row) {
                foreach ($row as $value) {
                    if (!is_numeric($value) && $value !== null && $value !== '') {
                        $strings[] = $this->formatValue($value);
                    }
                }
            }
        }
        
        $uniqueStrings = array_unique($strings);
        $xml = '';
        foreach ($uniqueStrings as $string) {
            $xml .= '<si><t>' . $this->xmlEncode($string) . '</t></si>';
        }
        
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($strings) . '" uniqueCount="' . count($uniqueStrings) . '">' . $xml . '</sst>';
    }

    private function getSheet(array $sheet): string
    {
        $headers = $sheet['headers'];
        $data = $sheet['data'];
        $options = $sheet['options'];
        
        // Build shared strings index
        $stringIndex = [];
        $index = 0;
        foreach ($this->sheets as $s) {
            foreach ($s['headers'] as $header) {
                if (!isset($stringIndex[$header])) {
                    $stringIndex[$header] = $index++;
                }
            }
            foreach ($s['data'] as $row) {
                foreach ($row as $value) {
                    $formatted = $this->formatValue($value);
                    if (!is_numeric($value) && $value !== null && $value !== '' && !isset($stringIndex[$formatted])) {
                        $stringIndex[$formatted] = $index++;
                    }
                }
            }
        }
        
        $colCount = count($headers);
        $rowCount = count($data) + 1;
        $lastCol = $this->getColumnLetter($colCount);
        
        // Column widths
        $cols = '<cols>';
        for ($i = 1; $i <= $colCount; $i++) {
            $width = $options['columnWidths'][$i - 1] ?? 15;
            $cols .= '<col min="' . $i . '" max="' . $i . '" width="' . $width . '" customWidth="1"/>';
        }
        $cols .= '</cols>';
        
        // Sheet data
        $sheetData = '<sheetData>';
        
        // Header row
        $sheetData .= '<row r="1" spans="1:' . $colCount . '">';
        foreach ($headers as $colIndex => $header) {
            $col = $this->getColumnLetter($colIndex + 1);
            $sheetData .= '<c r="' . $col . '1" s="1" t="s"><v>' . $stringIndex[$header] . '</v></c>';
        }
        $sheetData .= '</row>';
        
        // Data rows
        foreach ($data as $rowIndex => $row) {
            $rowNum = $rowIndex + 2;
            $style = ($rowIndex % 2 === 1) ? '3' : '2';
            $sheetData .= '<row r="' . $rowNum . '" spans="1:' . $colCount . '">';
            
            $colIndex = 0;
            foreach ($row as $value) {
                $col = $this->getColumnLetter($colIndex + 1);
                $formatted = $this->formatValue($value);
                
                if (is_numeric($value) && $value !== '') {
                    $sheetData .= '<c r="' . $col . $rowNum . '" s="' . $style . '"><v>' . $value . '</v></c>';
                } elseif ($value !== null && $value !== '') {
                    $sheetData .= '<c r="' . $col . $rowNum . '" s="' . $style . '" t="s"><v>' . $stringIndex[$formatted] . '</v></c>';
                } else {
                    $sheetData .= '<c r="' . $col . $rowNum . '" s="' . $style . '"/>';
                }
                $colIndex++;
            }
            $sheetData .= '</row>';
        }
        $sheetData .= '</sheetData>';
        
        // Freeze pane
        $freezePane = $options['freezeHeader'] ? '<sheetViews><sheetView tabSelected="1" workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>' : '';
        
        // Auto filter
        $autoFilter = $options['autoFilter'] ? '<autoFilter ref="A1:' . $lastCol . $rowCount . '"/>' : '';
        
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    ' . $freezePane . '
    ' . $cols . '
    ' . $sheetData . '
    ' . $autoFilter . '
</worksheet>';
    }

    private function getAppProps(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties">
    <Application>TaskFlow</Application>
    <Company>TaskFlow System</Company>
</Properties>';
    }

    private function getCoreProps(): string
    {
        $date = date('Y-m-d\TH:i:s\Z');
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <dc:title>' . $this->xmlEncode($this->title) . '</dc:title>
    <dc:creator>' . $this->xmlEncode($this->creator) . '</dc:creator>
    <dcterms:created xsi:type="dcterms:W3CDTF">' . $date . '</dcterms:created>
    <dcterms:modified xsi:type="dcterms:W3CDTF">' . $date . '</dcterms:modified>
</cp:coreProperties>';
    }

    private function getColumnLetter(int $num): string
    {
        $letter = '';
        while ($num > 0) {
            $num--;
            $letter = chr(65 + ($num % 26)) . $letter;
            $num = intval($num / 26);
        }
        return $letter;
    }

    private function xmlEncode(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function formatValue($value): string
    {
        if ($value === null || $value === '') {
            return '';
        }
        
        // Status labels
        $statusLabels = [
            'done' => 'Hoàn thành',
            'completed' => 'Hoàn thành', 
            'in_progress' => 'Đang làm',
            'in_review' => 'Đang review',
            'todo' => 'Cần làm',
            'backlog' => 'Backlog',
            'active' => 'Đang hoạt động',
            'planning' => 'Lên kế hoạch',
            'on_hold' => 'Tạm dừng',
            'cancelled' => 'Đã hủy',
        ];
        
        if (isset($statusLabels[$value])) {
            return $statusLabels[$value];
        }
        
        // Priority labels
        $priorityLabels = [
            'urgent' => 'Khẩn cấp',
            'high' => 'Cao',
            'medium' => 'Trung bình',
            'low' => 'Thấp',
        ];
        
        if (isset($priorityLabels[$value])) {
            return $priorityLabels[$value];
        }
        
        // Boolean
        if ($value === '1' || $value === 1 || $value === true) {
            return 'Có';
        }
        if ($value === '0' || $value === 0 || $value === false) {
            return 'Không';
        }
        
        // Date formatting
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', (string)$value)) {
            try {
                $date = new \DateTime($value);
                return $date->format('d/m/Y');
            } catch (\Exception $e) {
                return (string)$value;
            }
        }
        
        return (string)$value;
    }
}
