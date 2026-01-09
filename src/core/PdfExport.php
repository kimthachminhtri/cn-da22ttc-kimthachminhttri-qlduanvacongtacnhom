<?php

declare(strict_types=1);

/**
 * PDF Export Class
 * 
 * Tạo PDF reports không cần thư viện bên ngoài
 * Sử dụng HTML to PDF conversion với browser print
 * 
 * @package Core
 */

namespace Core;

class PdfExport
{
    private string $title;
    private string $subtitle;
    private array $data;
    private array $headers;
    private array $options;

    public function __construct(string $title = 'Report')
    {
        $this->title = $title;
        $this->subtitle = '';
        $this->data = [];
        $this->headers = [];
        $this->options = [
            'orientation' => 'portrait', // portrait, landscape
            'paper' => 'A4',
            'margin' => '10mm',
            'fontSize' => '12px',
            'headerColor' => '#3B82F6',
            'showDate' => true,
            'showPageNumbers' => true,
        ];
    }

    /**
     * Set report title
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Set report subtitle
     */
    public function setSubtitle(string $subtitle): self
    {
        $this->subtitle = $subtitle;
        return $this;
    }

    /**
     * Set table headers
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Set data rows
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Set options
     */
    public function setOptions(array $options): self
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    /**
     * Generate HTML for PDF
     */
    public function generateHtml(): string
    {
        $orientation = $this->options['orientation'];
        $margin = $this->options['margin'];
        $fontSize = $this->options['fontSize'];
        $headerColor = $this->options['headerColor'];
        $date = date('d/m/Y H:i');

        $html = <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>{$this->title}</title>
    <style>
        @page {
            size: A4 {$orientation};
            margin: {$margin};
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: {$fontSize};
            line-height: 1.5;
            color: #333;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid {$headerColor};
        }
        
        .header h1 {
            color: {$headerColor};
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .header .subtitle {
            color: #666;
            font-size: 14px;
        }
        
        .header .date {
            color: #999;
            font-size: 12px;
            margin-top: 10px;
        }
        
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: {$headerColor};
            margin-bottom: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th {
            background-color: {$headerColor};
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
        }
        
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 12px;
        }
        
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        tr:hover {
            background-color: #f3f4f6;
        }
        
        .status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
        }
        
        .status-done, .status-completed, .status-active { 
            background: #DEF7EC; color: #03543F; 
        }
        .status-in_progress, .status-in-progress { 
            background: #E1EFFE; color: #1E40AF; 
        }
        .status-todo, .status-planning { 
            background: #FEF3C7; color: #92400E; 
        }
        .status-backlog, .status-on_hold { 
            background: #F3F4F6; color: #374151; 
        }
        .status-in_review { 
            background: #EDE9FE; color: #5B21B6; 
        }
        
        .priority {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
        }
        
        .priority-urgent { background: #FEE2E2; color: #991B1B; }
        .priority-high { background: #FFEDD5; color: #9A3412; }
        .priority-medium { background: #E1EFFE; color: #1E40AF; }
        .priority-low { background: #F3F4F6; color: #374151; }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #999;
            font-size: 10px;
        }
        
        .summary {
            margin-top: 20px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 8px;
        }
        
        .summary-item {
            display: inline-block;
            margin-right: 30px;
        }
        
        .summary-label {
            color: #666;
            font-size: 11px;
        }
        
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: {$headerColor};
        }
        
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">📊 TaskFlow</div>
        <h1>{$this->title}</h1>
HTML;

        if (!empty($this->subtitle)) {
            $html .= "<p class=\"subtitle\">{$this->subtitle}</p>";
        }

        if ($this->options['showDate']) {
            $html .= "<p class=\"date\">Xuất ngày: {$date}</p>";
        }

        $html .= '</div>';

        // Summary section
        $html .= '<div class="summary">';
        $html .= '<span class="summary-item"><span class="summary-label">Tổng số:</span> <span class="summary-value">' . count($this->data) . '</span></span>';
        $html .= '</div>';

        // Table
        $html .= '<table>';
        $html .= '<thead><tr>';
        foreach ($this->headers as $header) {
            $html .= "<th>{$header}</th>";
        }
        $html .= '</tr></thead>';

        $html .= '<tbody>';
        foreach ($this->data as $row) {
            $html .= '<tr>';
            foreach ($row as $key => $value) {
                $formattedValue = $this->formatValue($key, $value);
                $html .= "<td>{$formattedValue}</td>";
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        // Footer
        $html .= <<<HTML
    <div class="footer">
        <p>TaskFlow - Hệ thống Quản lý Dự án</p>
        <p>Báo cáo được tạo tự động</p>
    </div>
    
    <script class="no-print">
        // Auto print when loaded
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
HTML;

        return $html;
    }

    /**
     * Format value based on key
     */
    private function formatValue(string $key, mixed $value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        // Status formatting
        if ($key === 'status' || str_contains($key, 'status')) {
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
            ];
            $label = $statusLabels[$value] ?? $value;
            $class = 'status-' . str_replace('_', '-', $value);
            return "<span class=\"status {$class}\">{$label}</span>";
        }

        // Priority formatting
        if ($key === 'priority') {
            $priorityLabels = [
                'urgent' => 'Khẩn cấp',
                'high' => 'Cao',
                'medium' => 'Trung bình',
                'low' => 'Thấp',
            ];
            $label = $priorityLabels[$value] ?? $value;
            return "<span class=\"priority priority-{$value}\">{$label}</span>";
        }

        // Boolean formatting
        if ($value === '1' || $value === 1 || $value === true) {
            return '✓ Có';
        }
        if ($value === '0' || $value === 0 || $value === false) {
            return '✗ Không';
        }

        // Progress formatting
        if ($key === 'progress') {
            return $value . '%';
        }

        // Date formatting
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', (string)$value)) {
            $date = new \DateTime($value);
            return $date->format('d/m/Y');
        }

        return htmlspecialchars((string)$value);
    }

    /**
     * Output HTML for browser printing
     */
    public function output(): void
    {
        header('Content-Type: text/html; charset=utf-8');
        echo $this->generateHtml();
    }

    /**
     * Download as HTML file (can be printed to PDF)
     */
    public function download(string $filename = 'report'): void
    {
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.html"');
        echo $this->generateHtml();
    }
}
