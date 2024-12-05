<?php

namespace Tests\Unit;
use PHPUnit\Framework\TestCase;
use App\Services\AccessibilityAnalyzerService;

class AccessibilityAnalyzerServiceTest extends TestCase
{
    private AccessibilityAnalyzerService $service;

    protected function setUp(): void
    {
        $this->service = new AccessibilityAnalyzerService();
    }

    public function test_that_it_detects_img_tag_missing_alt_attribute()
    {
        $htmlContent = '<img src="image.jpg">';
        $result = $this->service->analyze($htmlContent);

        // Assert that issues are detected
        $this->assertArrayHasKey('issues', $result);
        $issues = $result['issues']['text_alternatives'] ?? [];
        $this->assertNotEmpty($issues, 'No issues detected for missing alt attribute.');

        // Assert the specific issue message
        $this->assertCount(1, $issues);
        $this->assertStringContainsString('img element missing alt attribute.', $issues[0]['message']);
    }

    public function test_that_it_does_not_report_issue_when_alt_attribute_is_present()
    {
        $htmlContent = '<img src="image.jpg" alt="An image">';
        $result = $this->service->analyze($htmlContent);

        // Assert no issues are detected
        $issues = $result['issues']['text_alternatives'] ?? [];
        $this->assertEmpty($issues, 'Unexpected issues detected for img tags with alt attribute.');
    }

    public function test_detects_skipped_headings()
    {
        $htmlContent = '<h1>Heading 1</h1><h3>Heading 3</h3>';
        $result = $this->service->analyze($htmlContent);

        $issues = $result['issues']['adaptable'] ?? [];
        $this->assertNotEmpty($issues, 'No issues detected for skipped headings.');

        $this->assertCount(1, $issues, 'Unexpected number of issues detected for skipped headings.');

        $this->assertStringContainsString(
            'Header nesting - header following <h1> is incorrect.',
            $issues[0]['message'],
            'The reported issue message for skipped headings is incorrect or missing.'
        );
    }

    public function test_does_not_report_issue_for_correct_heading_nesting()
    {
        $htmlContent = '<h1>Heading 1</h1><h2>Heading 2</h2>';
        $result = $this->service->analyze($htmlContent);

        $issues = $result['issues']['adaptable'] ?? [];
        $this->assertEmpty($issues, 'Unexpected issues detected for correctly nested headings.');
    }



}
