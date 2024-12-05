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
            'adaptable' => $this->checkSkippedHeadings($xpath),
            'navigable' => $this->checkLinkText($xpath),
            'distinguishable' => $this->checkColorContrast($xpath),
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
     * Check for skipped heading levels.
     * @param DOMXPath $xpath
     * @return array
     */
    private function checkSkippedHeadings(DOMXPath $xpath): array
    {
        $issues = [];
        $headings = $xpath->query('//h1|//h2|//h3|//h4|//h5|//h6');

        $previousLevel = 0;

        foreach ($headings as $heading) {
            $currentLevel = (int)substr($heading->nodeName, 1);
            if ($previousLevel && $currentLevel > $previousLevel + 1) {
                $issues[] = [
                    'message' => "Header nesting - header following <h{$previousLevel}> is incorrect.",
                    'element' =>  $heading->C14N(),
                    'suggested_fix' => "Modify the header levels. The <h{$previousLevel}> heading should be followed by <h" . ($previousLevel + 1) . ">, not <h{$currentLevel}>.",
                ];
            }

            $previousLevel = $currentLevel;
        }

        return $issues;
    }

    /**
     * Check for links missing descriptive text.
     * @param DOMXPath $xpath
     * @return array
     */
    private function checkLinkText(DOMXPath $xpath): array
    {
        $issues = [];
        $links = $xpath->query('//a');

        foreach ($links as $link) {
            $linkText = trim($link->nodeValue);
            if (empty($linkText) && !$link->getAttribute('aria-label')) {
                $issues[] = [
                    'message' => 'Anchor contains no text.',
                    'element' => $link->C14N(),
                    'suggested_fix' => 'Add text to the element or the title attribute of the a element or, if an image is used within the anchor, add Alt text to the image.',
                ];
            }
        }

        return $issues;
    }

    /**
     * Check for color contrast issues.
     * @param DOMXPath $xpath
     * @return array
     */
    private function checkColorContrast(DOMXPath $xpath): array
    {
        $issues = [];
        $elements = $xpath->query('//*[@style]'); // Target elements with inline styles

        foreach ($elements as $element) {

            $style = $element->getAttribute('style');

            preg_match_all('/color\s*:\s*([^;]+);/', $style, $colorMatches);
            preg_match_all('/background-color\s*:\s*([^;]+);/', $style, $bgColorMatches);

            // If we find both color and background-color
            if (!empty($colorMatches[1]) && !empty($bgColorMatches[1])) {

                $textColor = $colorMatches[1][0];
                $backgroundColor = $bgColorMatches[1][0];

                $contrastRatio = $this->getContrastRatio($textColor, $backgroundColor);
                if ($contrastRatio < 4.5) {
                    $issues[] = [
                        'message' => 'The contrast between the colour of text and its background for the element is not sufficient to meet WCAG2.0 Level.',
                        'element' => $element->ownerDocument->saveHTML($element),
                        'suggested_fix' => 'Use a colour contrast evaluator to determine if text and background colours provide a contrast ratio of 4.5:1 for standard text, or 3:1 for larger text. Change colour codes to produce sufficient contrast',
                    ];
                }
            }
        }

        return $issues;
    }

    /**
     * Calculate the contrast ratio between two colors.
     *
     * @param string $color1
     * @param string $color2
     * @return float
     */
    private function getContrastRatio(string $color1, string $color2): float
    {
        $color1Rgb = $this->hexToRgb($color1);
        $color2Rgb = $this->hexToRgb($color2);

        $l1 = $this->getLuminance($color1Rgb);
        $l2 = $this->getLuminance($color2Rgb);

        return ($l1 > $l2) ? ($l1 + 0.05) / ($l2 + 0.05) : ($l2 + 0.05) / ($l1 + 0.05);
    }

    /**
     * Convert hex color to RGB.
     * @param string $hex
     * @return array
     */
    private function hexToRgb(string $hex): array
    {
        if ($hex[0] == '#') {
            $hex = substr($hex, 1);
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return [$r, $g, $b];
    }

    /**
     * Calculate the luminance of a color.
     * @param array $rgb
     * @return float
     */
    private function getLuminance(array $rgb): float
    {
        $rgb = array_map(function ($value) {
            $value /= 255;
            return ($value <= 0.03928) ? $value / 12.92 : pow(($value + 0.055) / 1.055, 2.4);
        }, $rgb);

        return ($rgb[0] * 0.2126) + ($rgb[1] * 0.7152) + ($rgb[2] * 0.0722);
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
