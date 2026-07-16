<?php
/**
 * ExcelHelper - Xuất file .xlsx tối giản (OOXML) bằng ZipArchive, không cần thư viện ngoài.
 * Hỗ trợ tiếng Việt (UTF-8), header in đậm, nhiều sheet.
 *
 * Dùng:
 *   ExcelHelper::download('bao-cao.xlsx', [
 *      ['name' => 'Học viên', 'headers' => ['Mã','Họ tên'], 'rows' => [['HV01','Nguyễn A'], ...]],
 *   ]);
 * Hoặc 1 sheet nhanh:
 *   ExcelHelper::downloadOne('hoc-vien.xlsx', 'Học viên', $headers, $rows);
 */
class ExcelHelper
{
    /** Xuất nhiều sheet rồi đẩy file về trình duyệt và dừng. */
    public static function download(string $filename, array $sheets): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'xlsx');
        $zip = new ZipArchive();
        $zip->open($tmp, ZipArchive::OVERWRITE);

        // Chuẩn hóa sheet: đảm bảo có name/headers/rows
        $sheets = array_values($sheets);
        $n = count($sheets);

        // [Content_Types].xml
        $sheetOverrides = '';
        for ($i = 1; $i <= $n; $i++) {
            $sheetOverrides .= '<Override PartName="/xl/worksheets/sheet' . $i . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        }
        $zip->addFromString('[Content_Types].xml',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . $sheetOverrides
            . '</Types>');

        // _rels/.rels
        $zip->addFromString('_rels/.rels',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>');

        // xl/workbook.xml + rels
        $sheetsXml = ''; $relsXml = '';
        for ($i = 1; $i <= $n; $i++) {
            $name = self::safeSheetName($sheets[$i - 1]['name'] ?? ('Sheet' . $i), $i);
            $sheetsXml .= '<sheet name="' . self::xml($name) . '" sheetId="' . $i . '" r:id="rId' . $i . '"/>';
            $relsXml .= '<Relationship Id="rId' . $i . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . $i . '.xml"/>';
        }
        // rId cho styles = n+1
        $relsXml .= '<Relationship Id="rId' . ($n + 1) . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';

        $zip->addFromString('xl/workbook.xml',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets>' . $sheetsXml . '</sheets></workbook>');

        $zip->addFromString('xl/_rels/workbook.xml.rels',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' . $relsXml . '</Relationships>');

        $zip->addFromString('xl/styles.xml', self::stylesXml());

        // Mỗi worksheet
        for ($i = 1; $i <= $n; $i++) {
            $zip->addFromString('xl/worksheets/sheet' . $i . '.xml', self::sheetXml($sheets[$i - 1]));
        }

        $zip->close();

        $data = file_get_contents($tmp);
        @unlink($tmp);

        $safeName = preg_replace('/[^\w.\-]+/u', '_', $filename);
        if (!headers_sent()) {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $safeName . '"');
            header('Content-Length: ' . strlen($data));
            header('Cache-Control: max-age=0');
        }
        echo $data;
        exit;
    }

    public static function downloadOne(string $filename, string $sheetName, array $headers, array $rows): void
    {
        self::download($filename, [['name' => $sheetName, 'headers' => $headers, 'rows' => $rows]]);
    }

    // Chỉ số style trong cellXfs (xem stylesXml)
    const S_NORMAL   = 0;  // thường
    const S_HEADER   = 1;  // header: đậm, chữ trắng, nền xanh, viền, canh giữa, wrap
    const S_TITLE    = 2;  // tiêu đề lớn: đậm, cỡ 14, màu xanh đậm
    const S_CELL     = 3;  // ô dữ liệu: viền mảnh
    const S_CELL_ALT = 4;  // ô dữ liệu nền xen kẽ (zebra)
    const S_TOTAL    = 5;  // dòng tổng: đậm, nền vàng nhạt, viền
    const S_SUBTOTAL = 6;  // dòng tổng phụ: đậm, nền xanh nhạt

    /** styles.xml — bảng màu + viền + canh lề dùng cho mọi sheet. */
    private static function stylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            // fonts: 0 thường, 1 đậm trắng, 2 tiêu đề xanh đậm, 3 đậm thường
            . '<fonts count="4">'
            . '<font><sz val="11"/><name val="Calibri"/><color rgb="FF1F2937"/></font>'
            . '<font><b/><sz val="11"/><name val="Calibri"/><color rgb="FFFFFFFF"/></font>'
            . '<font><b/><sz val="14"/><name val="Calibri"/><color rgb="FF0F7A38"/></font>'
            . '<font><b/><sz val="11"/><name val="Calibri"/><color rgb="FF1F2937"/></font>'
            . '</fonts>'
            // fills: 0 none, 1 gray125, 2 xanh header, 3 zebra xám nhạt, 4 vàng nhạt (tổng), 5 xanh nhạt (tổng phụ)
            . '<fills count="6">'
            . '<fill><patternFill patternType="none"/></fill>'
            . '<fill><patternFill patternType="gray125"/></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FF16A34A"/><bgColor indexed="64"/></patternFill></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFF3F7F5"/><bgColor indexed="64"/></patternFill></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFFEF3C7"/><bgColor indexed="64"/></patternFill></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFE7F5EC"/><bgColor indexed="64"/></patternFill></fill>'
            . '</fills>'
            // borders: 0 none, 1 viền mảnh xám
            . '<borders count="2">'
            . '<border><left/><right/><top/><bottom/><diagonal/></border>'
            . '<border>'
            . '<left style="thin"><color rgb="FFD6DEDA"/></left><right style="thin"><color rgb="FFD6DEDA"/></right>'
            . '<top style="thin"><color rgb="FFD6DEDA"/></top><bottom style="thin"><color rgb="FFD6DEDA"/></bottom><diagonal/>'
            . '</border>'
            . '</borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="7">'
            // 0 normal
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            // 1 header
            . '<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1">'
            . '<alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
            // 2 title
            . '<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1">'
            . '<alignment horizontal="left" vertical="center"/></xf>'
            // 3 cell
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1">'
            . '<alignment vertical="center" wrapText="1"/></xf>'
            // 4 cell zebra
            . '<xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFill="1" applyBorder="1" applyAlignment="1">'
            . '<alignment vertical="center" wrapText="1"/></xf>'
            // 5 total
            . '<xf numFmtId="0" fontId="3" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1">'
            . '<alignment vertical="center"/></xf>'
            // 6 subtotal
            . '<xf numFmtId="0" fontId="3" fillId="5" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1">'
            . '<alignment vertical="center"/></xf>'
            . '</cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>';
    }

    /**
     * Tính bề rộng cột theo nội dung dài nhất (auto-resize).
     * @return array chỉ số cột => width
     */
    private static function autoWidths(array $headers, array $rows): array
    {
        $w = [];
        foreach ($headers as $c => $h) {
            $w[$c] = mb_strlen((string)$h, 'UTF-8') + 2;
        }
        foreach ($rows as $r) {
            $c = 0;
            foreach ($r as $v) {
                $len = mb_strlen((string)$v, 'UTF-8');
                if (!isset($w[$c]) || $len + 2 > $w[$c]) $w[$c] = $len + 2;
                $c++;
            }
        }
        // Kẹp trong khoảng dễ nhìn
        foreach ($w as $c => $x) $w[$c] = max(8, min(48, $x));
        return $w;
    }

    /**
     * Sinh XML cho 1 worksheet.
     * Hỗ trợ: title, headers, rows, và tùy chọn 'total_rows' = [chỉ số dòng data là dòng tổng]
     * (dùng cho báo cáo có dòng "TỔNG ...": tô màu nổi bật).
     */
    private static function sheetXml(array $sheet): string
    {
        $headers = $sheet['headers'] ?? [];
        $rows = $sheet['rows'] ?? [];
        $title = $sheet['title'] ?? '';
        $zebra = $sheet['zebra'] ?? true;

        $rowsXml = '';
        $rowNum = 1;
        $nCol = max(count($headers), 1);

        // Tiêu đề lớn (gộp ô ngang qua các cột)
        $merges = [];
        if ($title !== '') {
            $rowsXml .= '<row r="' . $rowNum . '" ht="22" customHeight="1">'
                      . self::cell('A' . $rowNum, $title, self::S_TITLE) . '</row>';
            if ($nCol > 1) $merges[] = 'A' . $rowNum . ':' . self::colLetter($nCol - 1) . $rowNum;
            $rowNum++;
            $rowsXml .= '<row r="' . $rowNum . '"></row>';
            $rowNum++;
        }

        // Header
        $headerRow = 0;
        if ($headers) {
            $headerRow = $rowNum;
            $rowsXml .= '<row r="' . $rowNum . '" ht="28" customHeight="1">';
            foreach ($headers as $c => $h) {
                $rowsXml .= self::cell(self::colLetter($c) . $rowNum, $h, self::S_HEADER);
            }
            $rowsXml .= '</row>';
            $rowNum++;
        }

        // Data
        $firstDataRow = $rowNum;
        $i = 0;
        foreach ($rows as $r) {
            // Nhận diện dòng tổng: ô nào đó bắt đầu bằng "TỔNG"
            $isTotal = false; $isGrand = false;
            foreach ($r as $v) {
                if (is_string($v) && mb_stripos($v, 'TỔNG CỘNG', 0, 'UTF-8') === 0) { $isGrand = true; break; }
                if (is_string($v) && mb_stripos($v, 'TỔNG', 0, 'UTF-8') === 0) { $isTotal = true; break; }
            }
            $st = $isGrand ? self::S_TOTAL : ($isTotal ? self::S_SUBTOTAL
                 : (($zebra && $i % 2 === 1) ? self::S_CELL_ALT : self::S_CELL));

            $rowsXml .= '<row r="' . $rowNum . '">';
            $c = 0;
            foreach ($r as $v) {
                $rowsXml .= self::cell(self::colLetter($c) . $rowNum, $v, $st);
                $c++;
            }
            $rowsXml .= '</row>';
            $rowNum++; $i++;
        }
        $lastDataRow = $rowNum - 1;

        // Bề rộng cột tự động
        $widths = self::autoWidths($headers, $rows);
        $colsXml = '';
        foreach ($widths as $c => $w) {
            $colsXml .= '<col min="' . ($c + 1) . '" max="' . ($c + 1) . '" width="' . round($w, 1) . '" customWidth="1"/>';
        }
        $colsXml = $colsXml ? '<cols>' . $colsXml . '</cols>' : '';

        // Đóng băng dòng header + autofilter
        $paneXml = '';
        $filterXml = '';
        if ($headerRow > 0) {
            $paneXml = '<sheetView workbookViewId="0"><pane ySplit="' . $headerRow . '" topLeftCell="A' . ($headerRow + 1)
                     . '" activePane="bottomLeft" state="frozen"/></sheetView>';
            if ($lastDataRow >= $firstDataRow) {
                $filterXml = '<autoFilter ref="A' . $headerRow . ':' . self::colLetter($nCol - 1) . $lastDataRow . '"/>';
            }
        }
        $viewsXml = $paneXml ? '<sheetViews>' . $paneXml . '</sheetViews>' : '';
        $mergeXml = $merges ? '<mergeCells count="' . count($merges) . '"><mergeCell ref="' . implode('"/><mergeCell ref="', $merges) . '"/></mergeCells>' : '';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . $viewsXml
            . '<sheetFormatPr defaultRowHeight="16"/>'
            . $colsXml
            . '<sheetData>' . $rowsXml . '</sheetData>'
            . $mergeXml
            . $filterXml
            . '</worksheet>';
    }

    /** 1 ô: số -> kiểu number, còn lại -> inline string. $style: chỉ số trong cellXfs. */
    private static function cell(string $ref, $val, int $style = 0): string
    {
        $s = $style ? ' s="' . $style . '"' : '';
        if ($val === null || $val === '') {
            return '<c r="' . $ref . '"' . $s . '/>';
        }
        // Số thuần (không bắt đầu bằng 0 nhiều chữ số như mã/sđt) -> number
        if (is_int($val) || is_float($val) || (is_string($val) && preg_match('/^-?\d+(\.\d+)?$/', $val) && !preg_match('/^0\d/', $val) && strlen($val) < 15)) {
            return '<c r="' . $ref . '"' . $s . '><v>' . $val . '</v></c>';
        }
        return '<c r="' . $ref . '"' . $s . ' t="inlineStr"><is><t xml:space="preserve">' . self::xml((string)$val) . '</t></is></c>';
    }

    private static function colLetter(int $i): string
    {
        $s = '';
        $i++;
        while ($i > 0) {
            $m = ($i - 1) % 26;
            $s = chr(65 + $m) . $s;
            $i = intdiv($i - 1, 26);
        }
        return $s;
    }

    private static function xml(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    /** Tên sheet hợp lệ Excel: <=31 ký tự, bỏ : \ / ? * [ ]. */
    private static function safeSheetName(string $name, int $idx): string
    {
        $name = preg_replace('/[:\\\\\/\?\*\[\]]/u', ' ', $name);
        $name = trim(mb_substr($name, 0, 31, 'UTF-8'));
        return $name !== '' ? $name : ('Sheet' . $idx);
    }

    // ================= ĐỌC .xlsx =================

    /**
     * Đọc sheet đầu tiên của file .xlsx thành mảng các dòng.
     * Mỗi dòng là mảng theo chỉ số cột (A=0, B=1, ...); ô trống = ''.
     * Ngày dạng số serial của Excel được tự chuyển sang 'd/m/Y'.
     *
     * @return array<int, array<int,string>> Danh sách dòng theo thứ tự trong file.
     * @throws RuntimeException nếu không mở được file.
     */
    public static function readRows(string $path): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new RuntimeException('Không mở được file Excel (.xlsx).');
        }

        // Shared strings
        $shared = [];
        $ss = $zip->getFromName('xl/sharedStrings.xml');
        if ($ss !== false) {
            if (preg_match_all('/<si\b[^>]*>(.*?)<\/si>/s', $ss, $m)) {
                foreach ($m[1] as $siXml) {
                    // Gộp mọi <t> trong 1 <si> (chuỗi có định dạng rich-text tách nhiều <t>)
                    $txt = '';
                    if (preg_match_all('/<t[^>]*>(.*?)<\/t>/s', $siXml, $tm)) {
                        foreach ($tm[1] as $t) $txt .= $t;
                    }
                    $shared[] = html_entity_decode($txt, ENT_QUOTES | ENT_XML1, 'UTF-8');
                }
            }
        }

        // Sheet đầu tiên (theo workbook.xml.rels — đơn giản: lấy sheet1.xml)
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXml === false) {
            // fallback: tìm file worksheet bất kỳ
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if (preg_match('#^xl/worksheets/sheet\d+\.xml$#', $name)) {
                    $sheetXml = $zip->getFromName($name);
                    break;
                }
            }
        }
        $zip->close();
        if ($sheetXml === false || $sheetXml === null) return [];

        $result = [];
        if (!preg_match_all('/<row\b[^>]*>(.*?)<\/row>|<row\b[^>]*\/>/s', $sheetXml, $rows, PREG_SET_ORDER)) {
            return [];
        }
        foreach ($rows as $r) {
            $inner = $r[1] ?? '';
            $line = [];
            // Ô tự đóng (<c .../>) phải khớp TRƯỚC ô có nội dung để không nuốt ô kế tiếp.
            if ($inner !== '' && preg_match_all('/<c\b([^>]*?)\/>|<c\b([^>]*)>(.*?)<\/c>/s', $inner, $cells, PREG_SET_ORDER)) {
                foreach ($cells as $c) {
                    $selfClose = ($c[1] !== '');
                    $attr = $selfClose ? $c[1] : ($c[2] ?? '');
                    $body = $selfClose ? '' : ($c[3] ?? '');
                    if (!preg_match('/r="([A-Z]+)\d+"/', $attr, $rm)) continue;
                    $colIdx = self::colIndex($rm[1]);
                    $type = preg_match('/t="([^"]+)"/', $attr, $tm) ? $tm[1] : '';

                    $val = '';
                    if ($type === 's') { // shared string
                        if (preg_match('/<v>(.*?)<\/v>/s', $body, $vm)) {
                            $val = $shared[(int)$vm[1]] ?? '';
                        }
                    } elseif ($type === 'inlineStr') {
                        if (preg_match_all('/<t[^>]*>(.*?)<\/t>/s', $body, $tm2)) {
                            $val = html_entity_decode(implode('', $tm2[1]), ENT_QUOTES | ENT_XML1, 'UTF-8');
                        }
                    } elseif ($type === 'str') { // công thức trả chuỗi
                        if (preg_match('/<v>(.*?)<\/v>/s', $body, $vm)) {
                            $val = html_entity_decode($vm[1], ENT_QUOTES | ENT_XML1, 'UTF-8');
                        }
                    } else { // số / ngày serial
                        if (preg_match('/<v>(.*?)<\/v>/s', $body, $vm)) {
                            $raw = $vm[1];
                            // Nếu là số nguyên trong khoảng ngày Excel hợp lý → coi là ngày serial
                            if (is_numeric($raw) && strpos($attr, 's=') !== false
                                && (float)$raw > 20000 && (float)$raw < 80000 && floor((float)$raw) == (float)$raw) {
                                $val = self::excelSerialToDate((int)$raw);
                            } else {
                                $val = (string)$raw;
                            }
                        }
                    }
                    $line[$colIdx] = trim($val);
                }
            }
            // Chuẩn hóa: fill cột trống thành '' đến cột lớn nhất
            if ($line) {
                $max = max(array_keys($line));
                for ($i = 0; $i <= $max; $i++) if (!isset($line[$i])) $line[$i] = '';
                ksort($line);
            }
            $result[] = array_values($line);
        }
        return $result;
    }

    /** A->0, B->1, ..., Z->25, AA->26 */
    private static function colIndex(string $letters): int
    {
        $n = 0;
        $len = strlen($letters);
        for ($i = 0; $i < $len; $i++) {
            $n = $n * 26 + (ord($letters[$i]) - 64);
        }
        return $n - 1;
    }

    /** Đổi số serial ngày Excel (base 1900) sang chuỗi d/m/Y. */
    private static function excelSerialToDate(int $serial): string
    {
        // Excel epoch 1899-12-30 (bù lỗi năm nhuận 1900)
        $ts = ($serial - 25569) * 86400;
        return gmdate('d/m/Y', $ts);
    }
}
