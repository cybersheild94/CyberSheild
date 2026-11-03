export type UserRole = 'guest' | 'user' | 'admin';

export interface User {
  id: number;
  name: string;
  email: string;
  phone?: string;
  role: 'user' | 'admin';
  status: 'active' | 'suspended';
  profile_image?: string;
  created_at: string;
  last_login?: string;
}

export interface ScanResult {
  id: number;
  user_id: number | null;
  user_name?: string;
  url: string;
  domain: string;
  protocol: 'http' | 'https';
  risk_score: number;
  result: 'SAFE' | 'SUSPICIOUS' | 'PHISHING';
  url_length: number;
  has_https: boolean;
  has_ip: boolean;
  suspicious_keywords: string;
  subdomain_count: number;
  special_character_count: number;
  reasons: string[];
  ip_address: string;
  created_at: string;
  analysis_data?: any;
}

export interface FeedbackMessage {
  id: number;
  name: string;
  email: string;
  subject: string;
  message: string;
  status: 'unread' | 'read';
  created_at: string;
}

export interface SystemSettings {
  site_title: string;
  site_tagline: string;
  max_scans_per_hour: number;
  enable_public_scans: boolean;
  contact_email: string;
  detection_engine_version: string;
  maintenance_mode: boolean;
}

export interface VivaQuestion {
  id: number;
  category: 'PHP & Backend' | 'MySQL & Database' | 'Security & Heuristics' | 'Frontend & JS' | 'Testing & Defense';
  question: string;
  answer: string;
  quickTip?: string;
}
