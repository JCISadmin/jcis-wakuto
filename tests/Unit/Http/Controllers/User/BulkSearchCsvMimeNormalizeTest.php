<?php

namespace Tests\Unit\Http\Controllers\User;

use PHPUnit\Framework\TestCase;

/**
 * CSV MIME正規化。DB不要。
 */
class BulkSearchCsvMimeNormalizeTest extends TestCase
{
    public function test_csv系mimeをapplication_csvへ正規化する(): void
    {
        $csvMimeTypes = [
            'application/csv',
            'text/csv',
            'text/plain',
            'text/x-csv',
            'application/x-csv',
        ];

        foreach ($csvMimeTypes as $mime) {
            $this->assertSame('application/csv', $this->normalize($mime), $mime);
        }
    }

    public function test_csv以外のmimeは変換しない(): void
    {
        $unchanged = [
            'application/pdf',
            'application/zip',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'image/png',
            'application/octet-stream',
        ];

        foreach ($unchanged as $mime) {
            $this->assertSame($mime, $this->normalize($mime), $mime);
        }
    }

    public function test_親uploadと登記再uploadが同じ正規化を使う(): void
    {
        $parent = file_get_contents(dirname(__DIR__, 4) . '/app/Http/Controllers/User/BulkSearchController.php');
        $registry = file_get_contents(dirname(__DIR__, 4) . '/app/Http/Controllers/User/RegistryBulkSearchController.php');

        $this->assertStringContainsString('normalizeCsvMimeType(mime_content_type($this->filePath))', $parent);
        $this->assertStringContainsString('normalizeCsvMimeType(mime_content_type($this->filePath))', $registry);
        $this->assertStringNotContainsString('if ($this->fileType === \'text/plain\') {', $parent);
        $this->assertStringNotContainsString('if ($this->fileType === \'text/plain\') {', $registry);
        $this->assertStringNotContainsString('application/vnd.ms-excel', $parent);
    }

    private function normalize($fileType)
    {
        $src = str_replace("\r\n", "\n", file_get_contents(dirname(__DIR__, 4) . '/app/Http/Controllers/User/BulkSearchController.php'));
        if (!preg_match('/protected function normalizeCsvMimeType\(\$fileType\)\s*\{(.*?)\n    \}\s*\}\s*$/s', $src, $matches)) {
            $this->fail('normalizeCsvMimeType が見つかりません');
        }

        $normalize = eval('return function ($fileType) {' . $matches[1] . '};');

        return $normalize($fileType);
    }
}
