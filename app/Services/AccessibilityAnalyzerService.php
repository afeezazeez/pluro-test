<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;

class AccessibilityAnalyzerService
{
    /**
     * Analyze the HTML content for accessibility issues.
     * @param string $htmlContent
     * @return array
     */
    public function analyze(string $htmlContent): array
    {
        $dom = new  DOMDocument();

        // Suppress warnings for invalid HTML and load content
        libxml_use_internal_errors(true);
        $dom->loadHTML($htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        $issues = [
            'text_alternatives' => $this->checkMissingAlt($xpath),
        ];

        $complianceScore = $this->calculateComplianceScore($issues);

        return [
            'compliance_score' => $complianceScore,
            'issues' => $issues
        ];
    }

    /**
     * Check for images missing alt attributes.
     * @param DOMXPath $xpath
     * @return array
     */
    private function checkMissingAlt(DOMXPath $xpath): array
    {
        $issues = [];
        $images = $xpath->query('//img[not(@alt) or @alt=""]');

        foreach ($images as $img) {
            $issues[] = [
                'message' => 'img element missing alt attribute.',
                'element' => $img->ownerDocument->saveHTML($img),
                'suggested_fix' => 'Add an alt attribute to your img element.',
            ];
        }

        return $issues;
    }



    /**
     * Calculate total compliance/accessibility score
     * @param array $issues
     * @return int
     */
    public function calculateComplianceScore(array $issues):int
    {
        $rulesWithIssues = 0;
        $totalRules = count($issues);

        foreach ($issues as $ruleIssues) {
            if (!empty($ruleIssues)) {
                $rulesWithIssues++;
            }
        }

        $score = (($totalRules - $rulesWithIssues) / $totalRules) * 100;

        return (int) $score;
    }


}
