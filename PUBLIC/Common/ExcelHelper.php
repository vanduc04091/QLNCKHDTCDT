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

        // styles.xml: s=0 mặc định, s=1 header (đậm + nền xám)
        $zip->addFromString('xl/styles.xml',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font>'
            . '<font><b/><sz val="11"/><name val="Calibri"/></font></fonts>'
            . '<fills count="3"><fill><patternFill patternType="none"/></fill>'
            . '<fill><patternFill patternType="gray125"/></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFDDEBF7"/><bgColor indexed="64"/></patternFill></fill></fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="2">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1"/>'
            . '</cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>');

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

    /** Sinh XML cho 1 worksheet. */
    private static function sheetXml(array $sheet): string
    {
        $headers = $sheet['headers'] ?? [];
        $rows = $sheet['rows'] ?? [];
        $title = $sheet['title'] ?? '';   // tiêu đề lớn (tùy chọn) trên cùng

        $rowsXml = '';
        $rowNum = 1;

        // Tiêu đề (tùy chọn) — 1 ô đầu, in đậm
        if ($title !== '') {
            $rowsXml .= '<row r="' . $rowNum . '">' . self::cell('A' . $rowNum, $title, 1) . '</row>';
            $rowNum++;
            $rowsXml .= '<row r="' . $rowNum . '"></row>'; // dòng trống
            $rowNum++;
        }

        // Header
        if ($headers) {
            $rowsXml .= '<row r="' . $rowNum . '">';
            $col = 0;
            foreach ($headers as $h) {
                $rowsXml .= self::cell(self::colLetter($col) . $rowNum, $h, 1);
                $col++;
            }
            $rowsXml .= '</row>';
            $rowNum++;
        }

        // Data
        foreach ($rows as $r) {
            $rowsXml .= '<row r="' . $rowNum . '">';
            $col = 0;
            foreach ($r as $v) {
                $rowsXml .= self::cell(self::colLetter($col) . $rowNum, $v, 0);
                $col++;
            }
            $rowsXml .= '</row>';
            $rowNum++;
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetData>' . $rowsXml . '</sheetData></worksheet>';
    }

    /** 1 ô: số -> kiểu number, còn lại -> inline string. $style: 0 thường, 1 header. */
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
