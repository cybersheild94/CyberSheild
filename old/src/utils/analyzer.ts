import { ScanResult } from '../types';

export function analyzeURL(rawUrl: string, userId: number | null = null, userName: string = 'Guest'): ScanResult {
  let cleanUrl = rawUrl.trim();
  if (!/^https?:\/\//i.test(cleanUrl)) {
    cleanUrl = 'http://' + cleanUrl;
  }

  let parsed: URL;
  try {
    parsed = new URL(cleanUrl);
  } catch {
    // Basic fallback parsing if URL constructor throws on malformed syntax
    const match = cleanUrl.match(/^(https?:\/\/)?([^/?#]+)(.*)?$/i);
    const host = match ? match[2] : 'unknown';
    return {
      id: Math.floor(Date.now() / 1000),
      user_id: userId,
      user_name: userName,
      url: cleanUrl,
      domain: host,
      protocol: cleanUrl.startsWith('https') ? 'https' : 'http',
      risk_score: 50,
      result: 'SUSPICIOUS',
      url_length: cleanUrl.length,
      has_https: cleanUrl.startsWith('https'),
      has_ip: false,
      suspicious_keywords: '',
      subdomain_count: 0,
      special_character_count: 0,
      reasons: ['Malformed or unconventional URL structure detected.'],
      ip_address: '127.0.0.1',
      created_at: new Date().toISOString()
    };
  }

  const scheme = parsed.protocol.replace(':', '').toLowerCase() as 'http' | 'https';
  const host = parsed.hostname.toLowerCase();
  let riskScore = 0;
  const reasons: string[] = [];

  // 1. HTTPS Inspection
  const hasHttps = scheme === 'https';
  if (!hasHttps) {
    riskScore += 10;
    reasons.push('Missing HTTPS SSL/TLS encryption (+10 risk). Unencrypted plain-text transport.');
  }

  // 2. Raw IP address check
  const hasIp = /^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$/.test(host);
  if (hasIp) {
    riskScore += 25;
    reasons.push('Uses raw numeric IP address instead of registered domain (+25 risk). Classic phishing hallmark.');
  }

  // 3. Length metrics
  const urlLength = rawUrl.length;
  if (urlLength > 75) {
    riskScore += 15;
    reasons.push(`Abnormally long URL (${urlLength} characters, > 75 threshold) used to obscure destination (+15 risk).`);
  } else if (urlLength > 54) {
    riskScore += 8;
    reasons.push(`Moderate URL length (${urlLength} characters) (+8 risk).`);
  }

  // 4. Suspicious Keywords
  const phishKeywords: Record<string, number> = {
    login: 12,
    verify: 14,
    verification: 14,
    account: 10,
    password: 15,
    credential: 15,
    secure: 8,
    banking: 15,
    bank: 12,
    update: 8,
    confirm: 10,
    confirmation: 10,
    wallet: 14,
    crypto: 12,
    authentication: 12,
    auth: 8,
    signin: 10,
    'sign-in': 10,
    billing: 10,
    'support-team': 10,
    security: 8,
    session: 8,
    recover: 12,
    suspended: 12,
    urgent: 10,
    validation: 10
  };

  const matchedKeywords: string[] = [];
  let keywordScore = 0;
  for (const [kw, weight] of Object.entries(phishKeywords)) {
    if (cleanUrl.toLowerCase().includes(kw)) {
      matchedKeywords.push(kw);
      keywordScore += weight;
    }
  }
  keywordScore = Math.min(35, keywordScore);
  if (keywordScore > 0) {
    riskScore += keywordScore;
    reasons.push(`Contains credential-harvesting keywords: "${matchedKeywords.slice(0, 5).join('", "')}" (+${keywordScore} risk).`);
  }

  // 5. Brand Impersonation & Typosquatting
  const popularBrands = [
    'paypal', 'apple', 'google', 'microsoft', 'netflix', 'amazon', 'facebook',
    'instagram', 'chase', 'wellsfargo', 'binance', 'coinbase', 'metamask', 'whatsapp'
  ];
  for (const brand of popularBrands) {
    if (cleanUrl.toLowerCase().includes(brand)) {
      const isOfficial = new RegExp(`(^|\\.)${brand}\\.(com|org|net|co\\.uk)$`, 'i').test(host);
      if (!isOfficial) {
        riskScore += 25;
        reasons.push(`High-confidence brand impersonation targeting "${brand}" on illegitimate host (${host}) (+25 risk).`);
        break;
      }
    }
  }

  // 6. Subdomains
  let subdomainCount = 0;
  if (!hasIp && host) {
    const parts = host.split('.');
    if (parts.length > 2) {
      subdomainCount = parts.length - 2;
    }
  }
  if (subdomainCount >= 3) {
    riskScore += 20;
    reasons.push(`Excessive subdomain hierarchy (${subdomainCount} subdomains) designed to deceive mobile address bars (+20 risk).`);
  } else if (subdomainCount === 2) {
    riskScore += 10;
    reasons.push(`Elevated subdomain depth (${subdomainCount} subdomains) (+10 risk).`);
  }

  // 7. Special Characters
  const atCount = (cleanUrl.match(/@/g) || []).length;
  const hyphenCount = (host.match(/-/g) || []).length;
  const doubleSlashPath = (cleanUrl.slice(8).match(/\/\//g) || []).length;
  const specialChars = (cleanUrl.match(/[@\-_%&=?#]/g) || []).length;

  if (atCount > 0) {
    riskScore += 25;
    reasons.push('URL contains "@" sign (+25 risk). Typical browser credential-masking evasion tactic.');
  }
  if (hyphenCount >= 3) {
    riskScore += 15;
    reasons.push(`Unusually high hyphen count in domain name (${hyphenCount} hyphens) (+15 risk).`);
  }
  if (doubleSlashPath > 0) {
    riskScore += 20;
    reasons.push('URL path contains secondary redirect double slashes "//" (+20 risk).');
  }

  // 8. URL Shorteners
  const shorteners = ['bit.ly', 'tinyurl.com', 'goo.gl', 't.co', 'is.gd', 'buff.ly', 'ow.ly', 'cutt.ly', 'rb.gy', 'rebrand.ly', 'tiny.cc'];
  if (shorteners.includes(host)) {
    riskScore += 20;
    reasons.push(`URL shortener detected (${host}) masking destination server (+20 risk).`);
  }

  // 9. Suspicious TLDs
  const suspiciousTLDs = ['.xyz', '.top', '.click', '.work', '.gq', '.cf', '.tk', '.ml', '.ga', '.buzz', '.vip', '.rest', '.sbs', '.cam', '.cfd', '.quest'];
  for (const tld of suspiciousTLDs) {
    if (host.endsWith(tld)) {
      riskScore += 15;
      reasons.push(`Uses low-reputation / high-abuse TLD (${tld}) commonly deployed in phishing campaigns (+15 risk).`);
      break;
    }
  }

  // 10. Whitelist mitigation
  const trusted = ['google.com', 'microsoft.com', 'apple.com', 'github.com', 'amazon.com', 'wikipedia.org', 'youtube.com', 'linkedin.com', 'gov.in'];
  for (const td of trusted) {
    if (host === td || host.endsWith('.' + td)) {
      riskScore = Math.max(0, riskScore - 30);
      break;
    }
  }

  riskScore = Math.min(100, Math.max(0, riskScore));

  let result: 'SAFE' | 'SUSPICIOUS' | 'PHISHING' = 'SAFE';
  if (riskScore >= 60) {
    result = 'PHISHING';
    reasons.push('Critical phishing indicators triggered. High likelihood of credential interception.');
  } else if (riskScore >= 30) {
    result = 'SUSPICIOUS';
    reasons.push('Heuristics triggered multiple warning signals. Proceed with heightened caution.');
  } else {
    result = 'SAFE';
    if (reasons.length === 0) {
      reasons.push('Valid HTTPS encryption established with normal domain lexical characteristics.');
      reasons.push('No credential-harvesting triggers or suspicious subdomain hierarchies found.');
    }
  }

  return {
    id: Math.floor(1000 + Math.random() * 9000),
    user_id: userId,
    user_name: userName,
    url: cleanUrl,
    domain: host,
    protocol: scheme,
    risk_score: riskScore,
    result,
    url_length: urlLength,
    has_https: hasHttps,
    has_ip: hasIp,
    suspicious_keywords: matchedKeywords.join(', '),
    subdomain_count: subdomainCount,
    special_character_count: specialChars,
    reasons,
    ip_address: '192.168.1.101',
    created_at: new Date().toISOString().replace('T', ' ').substring(0, 19)
  };
}
