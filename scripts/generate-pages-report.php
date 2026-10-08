<?php

if ($argc < 4) {
    fwrite(STDERR, "Usage: php generate-pages-report.php <junit.xml> <tests.html> <output-dir>\n");
    exit(1);
}

[$script, $junitPath, $testsHtmlPath, $outputDir] = $argv;

if (!file_exists($junitPath)) {
    fwrite(STDERR, "JUnit file not found: {$junitPath}\n");
    exit(1);
}

if (!file_exists($testsHtmlPath)) {
    fwrite(STDERR, "Test report file not found: {$testsHtmlPath}\n");
    exit(1);
}

if (!is_dir($outputDir) && !mkdir($outputDir, 0777, true) && !is_dir($outputDir)) {
    fwrite(STDERR, "Unable to create output directory: {$outputDir}\n");
    exit(1);
}

$xml = simplexml_load_file($junitPath);
if ($xml === false) {
    fwrite(STDERR, "Unable to parse JUnit XML.\n");
    exit(1);
}

$stats = [
    'tests' => 0,
    'failures' => 0,
    'errors' => 0,
    'skipped' => 0,
    'time' => 0.0,
];

$collect = function (SimpleXMLElement $suite) use (&$collect, &$stats): void {
    $attributes = $suite->attributes();
    $stats['tests'] += (int)($attributes['tests'] ?? 0);
    $stats['failures'] += (int)($attributes['failures'] ?? 0);
    $stats['errors'] += (int)($attributes['errors'] ?? 0);
    $stats['skipped'] += (int)($attributes['skipped'] ?? 0);
    $stats['time'] += (float)($attributes['time'] ?? 0);

    if (isset($suite->testsuite)) {
        foreach ($suite->testsuite as $childSuite) {
            $collect($childSuite);
        }
    }
};

if (isset($xml->testsuite)) {
    foreach ($xml->testsuite as $suite) {
        $collect($suite);
    }
}

$passed = max(0, $stats['tests'] - $stats['failures'] - $stats['errors'] - $stats['skipped']);
$successRate = $stats['tests'] > 0 ? (int) round(($passed / $stats['tests']) * 100) : 100;

$testsHtmlName = basename($testsHtmlPath);

$html = <<<HTML
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>collectorUD Report</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 0; background: #f8fff8; color: #1f2937; }
    .wrap { max-width: 900px; margin: 0 auto; padding: 24px 16px; }
    .card { background: #fff; border: 1px solid #d9edd9; border-radius: 14px; padding: 18px; }
    .grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; margin-top: 14px; }
    .metric { background: #fff; border: 1px solid #d9edd9; border-radius: 12px; padding: 10px; }
    .metric b { font-size: 1.15rem; }
    .btn { display: inline-block; margin-top: 12px; text-decoration: none; color: #fff; background: #2e7d32; padding: 8px 12px; border-radius: 8px; }
    @media (max-width: 700px) { .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
  </style>
</head>
<body>
  <div class="wrap">
    <section class="card">
      <h1>collectorUD - Test Report</h1>
      <p>Generated at: %s</p>
      <div class="grid">
        <div class="metric"><small>Total tests</small><br><b>%d</b></div>
        <div class="metric"><small>Passed</small><br><b>%d</b></div>
        <div class="metric"><small>Failed + errors</small><br><b>%d</b></div>
        <div class="metric"><small>Pass rate</small><br><b>%d%%</b></div>
      </div>
      <a class="btn" href="%s">Open Testdox Report</a>
    </section>
  </div>
</body>
</html>
HTML;

$finalHtml = sprintf(
    $html,
    gmdate('c'),
    $stats['tests'],
    $passed,
    $stats['failures'] + $stats['errors'],
    $successRate,
    htmlspecialchars($testsHtmlName, ENT_QUOTES, 'UTF-8')
);

copy($testsHtmlPath, rtrim($outputDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $testsHtmlName);
file_put_contents(rtrim($outputDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'index.html', $finalHtml);

echo "Report generated in {$outputDir}\n";
