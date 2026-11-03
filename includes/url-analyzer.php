<?php
/**
 * CYBER SHIELD - Intelligent Phishing Detection Engine
 * File: includes/url-analyzer.php
 * 
 * Architecture: AI-inspired intelligent rule-based heuristic classifier
 * Evaluates lexical, structural, statistical, and brand impersonation vectors.
 * BCA Final Year Project
 */

declare(strict_types=1);

/**
 * Analyzes a given URL and computes a deterministic threat score (0-100)
 *
 * @param string $rawUrl
 * @return array Multi-dimensional analysis results including risk score, classification, and heuristic explanations
 */
function analyzeURL(string $rawUrl): array {
    $cleanUrl = trim($rawUrl);
    if (!preg_match('#^https?://#i', $cleanUrl)) {
        $cleanUrl = 'http://' . $cleanUrl;
    }

    $parsed = parse_url($cleanUrl);
    $scheme = strtolower($parsed['scheme'] ?? 'http');
    $host = strtolower($parsed['host'] ?? '');
    $path = $parsed['path'] ?? '';
    $query = $parsed['query'] ?? '';
    $fullTarget = $host . $path . ($query ? '?' . $query : '');

    $riskScore = 0;
    $reasons = [];
    $flags = [];

    // 1. PROTOCOL / SSL INSPECTION
    $hasHttps = ($scheme === 'https');
    if (!$hasHttps) {
        $riskScore += 10;
        $reasons[] = 'Missing HTTPS SSL/TLS encryption (+10 risk). Unencrypted plain-text transport.';
        $flags['no_https'] = true;
    } else {
        $flags['has_https'] = true;
    }

    // 2. IP ADDRESS INSTEAD OF DOMAIN NAME
    $hasIp = (bool)preg_match('/^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$/', $host);
    if ($hasIp) {
        $riskScore += 25;
        $reasons[] = 'Uses raw numeric IP address instead of registered domain (+25 risk). Classic phishing hallmark.';
        $flags['raw_ip'] = true;
    }

    // 3. URL LENGTH METRICS
    $urlLength = strlen($rawUrl);
    if ($urlLength > 75) {
        $riskScore += 15;
        $reasons[] = "Abnormally long URL ({$urlLength} characters, > 75 threshold) used to obscure destination (+15 risk).";
        $flags['long_url'] = true;
    } elseif ($urlLength > 54) {
        $riskScore += 8;
        $reasons[] = "Moderate URL length ({$urlLength} characters) (+8 risk).";
        $flags['medium_url'] = true;
    }

    // 4. SUSPICIOUS HIGH-RISK KEYWORDS
    $phishKeywords = [
        'login'          => 12,
        'verify'         => 14,
        'verification'   => 14,
        'account'        => 10,
        'password'       => 15,
        'credential'     => 15,
        'secure'         => 8,
        'banking'        => 15,
        'bank'           => 12,
        'update'         => 8,
        'confirm'        => 10,
        'confirmation'   => 10,
        'wallet'         => 14,
        'crypto'         => 12,
        'authentication' => 12,
        'auth'           => 8,
        'signin'         => 10,
        'sign-in'        => 10,
        'billing'        => 10,
        'support-team'   => 10,
        'security'       => 8,
        'session'        => 8,
        'recover'        => 12,
        'suspended'      => 12,
        'urgent'         => 10,
        'validation'     => 10
    ];

    $matchedKeywords = [];
    $keywordScore = 0;
    foreach ($phishKeywords as $keyword => $weight) {
        if (stripos($cleanUrl, $keyword) !== false) {
            $matchedKeywords[] = $keyword;
            $keywordScore += $weight;
        }
    }
    // Cap keyword score to 30 to prevent single heuristic runaway
    $keywordScore = min(35, $keywordScore);
    if ($keywordScore > 0) {
        $riskScore += $keywordScore;
        $reasons[] = 'Contains credential-harvesting keywords: "' . implode('", "', array_slice($matchedKeywords, 0, 5)) . '" (+' . $keywordScore . ' risk).';
        $flags['suspicious_keywords'] = $matchedKeywords;
    }

    // 5. BRAND IMPERSONATION & TYPOSQUATTING VECTORS
    $popularBrands = [
        'paypal', 'apple', 'google', 'microsoft', 'netflix', 'amazon', 'facebook',
        'instagram', 'chase', 'wellsfargo', 'binance', 'coinbase', 'metamask', 'whatsapp'
    ];
    $brandDetected = null;
    foreach ($popularBrands as $brand) {
        // If brand appears in subdomains or path but domain is NOT the official brand domain
        if (stripos($cleanUrl, $brand) !== false) {
            // Check if domain root ends with legitimate brand.com
            if (!preg_match('/(^|\.)' . preg_quote($brand, '/') . '\.(com|org|net|co\.uk)$/i', $host)) {
                $brandDetected = $brand;
                $riskScore += 25;
                $reasons[] = "High-confidence brand impersonation targeting \"{$brand}\" on illegitimate host ({$host}) (+25 risk).";
                $flags['brand_impersonation'] = $brand;
                break;
            }
        }
    }

    // 6. SUBDOMAIN ACCUMULATION & COMPLEXITY
    $subdomainCount = 0;
    if (!$hasIp && !empty($host)) {
        $hostParts = explode('.', $host);
        // e.g. login.verify.paypal.fake-domain.xyz -> 5 parts -> 3 subdomains
        if (count($hostParts) > 2) {
            $subdomainCount = count($hostParts) - 2;
        }
    }

    if ($subdomainCount >= 3) {
        $riskScore += 20;
        $reasons[] = "Excessive subdomain hierarchy ({$subdomainCount} subdomains) designed to deceive mobile address bars (+20 risk).";
        $flags['excessive_subdomains'] = true;
    } elseif ($subdomainCount == 2) {
        $riskScore += 10;
        $reasons[] = "Elevated subdomain depth ({$subdomainCount} subdomains) (+10 risk).";
        $flags['elevated_subdomains'] = true;
    }

    // 7. SPECIAL CHARACTER & OBSCURATION COUNT
    // Phishing kits frequently use @, %, -, _, //
    $atCount = substr_count($cleanUrl, '@');
    $hyphenCount = substr_count($host, '-');
    $doubleSlashPath = substr_count(substr($cleanUrl, 8), '//');
    $specialCharCount = preg_match_all('/[@\-_%&=?#]/', $cleanUrl);

    if ($atCount > 0) {
        $riskScore += 25;
        $reasons[] = 'URL contains "@" sign (+25 risk). Typical browser credential-masking evasion tactic.';
        $flags['has_at_symbol'] = true;
    }
    if ($hyphenCount >= 3) {
        $riskScore += 15;
        $reasons[] = "Unusually high hyphen count in domain name ({$hyphenCount} hyphens) (+15 risk).";
        $flags['hyphenated_domain'] = true;
    }
    if ($doubleSlashPath > 0) {
        $riskScore += 20;
        $reasons[] = 'URL path contains secondary redirect double slashes "//" (+20 risk).';
        $flags['redirect_slashes'] = true;
    }

    // 8. COMMON URL SHORTENERS
    $shorteners = [
        'bit.ly', 'tinyurl.com', 'goo.gl', 't.co', 'is.gd', 'buff.ly',
        'ow.ly', 'cutt.ly', 'rb.gy', 'rebrand.ly', 'tiny.cc'
    ];
    $isShortener = in_array($host, $shorteners, true);
    if ($isShortener) {
        $riskScore += 20;
        $reasons[] = "URL shortener detected ({$host}) masking destination server (+20 risk).";
        $flags['url_shortener'] = true;
    }

    // 9. HIGH-RISK SUSPICIOUS TLD (Top Level Domains)
    $suspiciousTLDs = [
        '.xyz', '.top', '.click', '.work', '.gq', '.cf', '.tk', '.ml',
        '.ga', '.buzz', '.vip', '.rest', '.sbs', '.cam', '.cfd', '.quest'
    ];
    foreach ($suspiciousTLDs as $tld) {
        if (str_ends_with($host, $tld)) {
            $riskScore += 15;
            $reasons[] = "Uses low-reputation / high-abuse TLD ({$tld}) commonly deployed in phishing campaigns (+15 risk).";
            $flags['suspicious_tld'] = $tld;
            break;
        }
    }

    // 10. REPUTABLE DOMAIN WHITELIST HEURISTIC
    $trustedDomains = [
        'google.com', 'microsoft.com', 'apple.com', 'github.com', 'amazon.com',
        'wikipedia.org', 'youtube.com', 'linkedin.com', 'twitter.com', 'x.com',
        'cloudflare.com', 'stackoverflow.com', 'gov.in', 'edu'
    ];
    foreach ($trustedDomains as $td) {
        if ($host === $td || str_ends_with($host, '.' . $td)) {
            // Apply trust mitigation factor
            $riskScore = max(0, $riskScore - 30);
            $flags['trusted_domain'] = true;
            break;
        }
    }

    // FINAL CLASSIFICATION LOGIC
    $riskScore = min(100, max(0, $riskScore));

    if ($riskScore <= RISK_THRESHOLD_SAFE) {
        $result = 'SAFE';
        if (empty($reasons)) {
            $reasons[] = 'Valid HTTPS encryption established with normal domain lexical characteristics.';
            $reasons[] = 'No credential-harvesting triggers or suspicious subdomain hierarchies found.';
        }
    } elseif ($riskScore <= RISK_THRESHOLD_SUSPICIOUS) {
        $result = 'SUSPICIOUS';
        $reasons[] = 'Heuristics triggered multiple warning signals. Proceed with heightened caution.';
    } else {
        $result = 'PHISHING';
        $reasons[] = 'Critical phishing indicators triggered. High likelihood of credential interception or malicious payload.';
    }

    return [
        'risk_score'              => $riskScore,
        'result'                  => $result,
        'domain'                  => $host ?: 'unknown',
        'protocol'                => $scheme,
        'url_length'              => $urlLength,
        'has_https'               => $hasHttps,
        'has_ip'                  => $hasIp,
        'suspicious_keywords'     => implode(', ', $matchedKeywords),
        'subdomain_count'         => $subdomainCount,
        'special_character_count' => (int)$specialCharCount,
        'analysis_data'           => json_encode([
            'engine'       => 'CyberShield-Heuristics-v2',
            'flags'        => $flags,
            'timestamp'    => time(),
            'host'         => $host,
            'ai_readiness' => 'Ready for Random Forest / XGBoost feature vector vectorization'
        ]),
        'reasons'                 => implode(' | ', $reasons),
        'reasons_array'           => $reasons
    ];
}
