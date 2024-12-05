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

    public function test_detects_link_text_issues()
    {
        $htmlContent = '<a href="http://example.com"></a>';
        $result = $this->service->analyze($htmlContent);

        $issues = $result['issues']['navigable'] ?? [];
        $this->assertNotEmpty($issues, 'No issues detected for link with missing text.');

        $this->assertCount(1, $issues, 'Unexpected number of issues detected for link with missing text.');

        $this->assertStringContainsString(
            'Anchor contains no text.',
            $issues[0]['message'],
            'The reported issue message for a link with missing text is incorrect or missing.'
        );
    }

    public function test_does_not_report_issue_for_links_with_valid_text()
    {
        $htmlContent = '<a href="http://example.com">Visit Example</a>';
        $result = $this->service->analyze($htmlContent);

        $issues = $result['issues']['navigable'] ?? [];
        $this->assertEmpty($issues, 'Unexpected issues detected for a link with valid text.');
    }


    public function test_detects_color_contrast_issues()
    {
        $htmlContent = '<div style="color:#f0f0f0; background-color:#e0e0e0;">Content</div>';
        $result = $this->service->analyze($htmlContent);

        $issues = $result['issues']['distinguishable'] ?? [];
        $this->assertNotEmpty($issues, 'No issues detected for insufficient color contrast.');

        $this->assertCount(1, $issues, 'Unexpected number of issues detected for insufficient color contrast.');

        $this->assertStringContainsString(
            'The contrast between the colour of text and its background for the element is not sufficient to meet WCAG2.0 Level.',
            $issues[0]['message'],
            'The reported issue message for insufficient color contrast is incorrect or missing.'
        );
    }

    public function test_does_not_report_issues_for_sufficient_color_contrast()
    {
        $htmlContent = '<div style="color:#000000; background-color:#FFFFFF;">Content</div>';
        $result = $this->service->analyze($htmlContent);

        $issues = $result['issues']['distinguishable'] ?? [];
        $this->assertEmpty($issues, 'Unexpected issues detected for sufficient color contrast.');
    }

    public function test_calculate_compliance_score_with_no_issues()
    {
        $issues = [
            'text_alternatives' => [],
            'adaptable' => [],
            'navigable' => [],
            'distinguishable' => []
        ];

        $score = $this->service->calculateComplianceScore($issues);

        // Assert that the compliance score is 100 when no issues are found
        $this->assertEquals(100, $score, 'Compliance score should be 100 when no issues are found.');
    }

    public function test_calculate_compliance_score_with_partial_issues()
    {
        $issues = [
            'text_alternatives' => [['message' => 'img element missing alt attribute.']],
            'adaptable' => [['message' => 'Header nesting - header following <h1> is incorrect.']],
            'navigable' => [],
            'distinguishable' => []
        ];

        $score = $this->service->calculateComplianceScore($issues);

        // Assert that the compliance score is 50 when 2 out of 4 categories have issues
        $this->assertEquals(50, $score, 'Compliance score should be 50 when 2 out of 4 categories have issues.');
    }

    public function test_calculate_compliance_score_with_all_issues()
    {
        $issues = [
            'text_alternatives' => [['message' => 'img element missing alt attribute.']],
            'adaptable' => [['message' => 'Header nesting - header following <h1> is incorrect.']],
            'navigable' => [['message' => 'Anchor contains no text.']],
            'distinguishable' => [['message' => 'The contrast between the colour of text and its background for the element is not sufficient to meet WCAG2.0 Level.']]
        ];

        $score = $this->service->calculateComplianceScore($issues);

        // Assert that the compliance score is 0 when all 4 categories have issues
        $this->assertEquals(0, $score, 'Compliance score should be 0 when all 4 categories have issues.');
    }





}
