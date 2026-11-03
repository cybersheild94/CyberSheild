/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import { useState } from 'react';
import { User, ScanResult, FeedbackMessage, SystemSettings } from './types';
import { Navbar } from './components/Navbar';
import { HeroScanner } from './components/HeroScanner';
import { UserDashboardView } from './components/UserDashboardView';
import { AdminDashboardView } from './components/AdminDashboardView';
import { AwarenessView } from './components/AwarenessView';
import { AboutView } from './components/AboutView';
import { ContactView } from './components/ContactView';
import { SourceCodeExplorer } from './components/SourceCodeExplorer';
import { VivaPreparationModal } from './components/VivaPreparationModal';
import { ScanDetailsModal } from './components/ScanDetailsModal';
import { AuthModal } from './components/AuthModal';
import { Shield, Sparkles, Code, BookOpen, Layers } from 'lucide-react';

export default function App() {
  // Current Session: default to demo user Alex Morgan so everything is instantly testable, but user can sign out or switch to Admin
  const [currentUser, setCurrentUser] = useState<User | null>({
    id: 2,
    name: 'Alex Morgan',
    email: 'user@cybershield.local',
    phone: '+1-555-0144',
    role: 'user',
    status: 'active',
    created_at: '2026-09-10 14:30:00',
    last_login: '2026-09-24 18:45:00'
  });

  const [activeTab, setActiveTab] = useState<string>('home');
  const [selectedScanForDetails, setSelectedScanForDetails] = useState<ScanResult | null>(null);

  // Modals
  const [showAuthModal, setShowAuthModal] = useState(false);
  const [authModalMode, setAuthModalMode] = useState<'login' | 'signup'>('login');
  const [showCodeExplorer, setShowCodeExplorer] = useState(false);
  const [showVivaModal, setShowVivaModal] = useState(false);

  // System Users
  const [allUsers, setAllUsers] = useState<User[]>([
    {
      id: 1,
      name: 'Chief Security Admin',
      email: 'admin@cybershield.local',
      phone: '+1-555-0199',
      role: 'admin',
      status: 'active',
      created_at: '2026-09-01 10:00:00',
      last_login: '2026-09-24 19:00:00'
    },
    {
      id: 2,
      name: 'Alex Morgan',
      email: 'user@cybershield.local',
      phone: '+1-555-0144',
      role: 'user',
      status: 'active',
      created_at: '2026-09-10 14:30:00',
      last_login: '2026-09-24 18:45:00'
    },
    {
      id: 3,
      name: 'Priya Sharma (BCA Student)',
      email: 'priya.sharma@university.edu',
      phone: '+91-98765-43210',
      role: 'user',
      status: 'active',
      created_at: '2026-09-15 09:12:00',
      last_login: '2026-09-23 11:20:00'
    }
  ]);

  // Initial Seed Scans (from cyber_shield.sql)
  const [allScans, setAllScans] = useState<ScanResult[]>([
    {
      id: 1001,
      user_id: 2,
      user_name: 'Alex Morgan',
      url: 'https://secure-login.paypal.account-verify-auth.xyz/webapps/login?cmd=_run',
      domain: 'secure-login.paypal.account-verify-auth.xyz',
      protocol: 'https',
      risk_score: 85,
      result: 'PHISHING',
      url_length: 76,
      has_https: true,
      has_ip: false,
      suspicious_keywords: 'login, verify, account, secure, auth',
      subdomain_count: 4,
      special_character_count: 6,
      reasons: [
        'Excessive subdomains (4 depth) designed to deceive mobile address bars (+20)',
        'Low-reputation / high-abuse TLD (.xyz) commonly deployed in phishing kits (+15)',
        'High-confidence brand impersonation targeting "paypal" on illegitimate host (+25)',
        'Multiple credential-harvesting terms ("login", "verify", "account") (+25)'
      ],
      ip_address: '192.168.1.101',
      created_at: '2026-09-23 11:15:22'
    },
    {
      id: 1002,
      user_id: 2,
      user_name: 'Alex Morgan',
      url: 'http://192.168.1.45/bank-of-america/update-credentials.html',
      domain: '192.168.1.45',
      protocol: 'http',
      risk_score: 90,
      result: 'PHISHING',
      url_length: 58,
      has_https: false,
      has_ip: true,
      suspicious_keywords: 'bank, update, credentials',
      subdomain_count: 0,
      special_character_count: 4,
      reasons: [
        'Raw numeric IP address used instead of registered domain (+25)',
        'Missing HTTPS SSL/TLS encryption (+10 risk)',
        'Credential harvesting keywords: "bank", "update", "credentials" (+30)',
        'Brand impersonation targeting "bank-of-america" (+25)'
      ],
      ip_address: '192.168.1.101',
      created_at: '2026-09-23 15:42:10'
    },
    {
      id: 1003,
      user_id: 2,
      user_name: 'Alex Morgan',
      url: 'https://github.com/torvalds/linux',
      domain: 'github.com',
      protocol: 'https',
      risk_score: 5,
      result: 'SAFE',
      url_length: 33,
      has_https: true,
      has_ip: false,
      suspicious_keywords: '',
      subdomain_count: 0,
      special_character_count: 1,
      reasons: [
        'Reputable trusted domain whitelist match (-30 mitigation)',
        'Valid HTTPS encryption established with normal lexical depth',
        'No deceptive credential triggers or excessive subdomains'
      ],
      ip_address: '192.168.1.101',
      created_at: '2026-09-24 09:20:00'
    },
    {
      id: 1004,
      user_id: 1,
      user_name: 'Chief Security Admin',
      url: 'http://update-netflix-billing-account.click/sign-in',
      domain: 'update-netflix-billing-account.click',
      protocol: 'http',
      risk_score: 75,
      result: 'PHISHING',
      url_length: 51,
      has_https: false,
      has_ip: false,
      suspicious_keywords: 'update, billing, account, signin',
      subdomain_count: 0,
      special_character_count: 4,
      reasons: [
        'Missing HTTPS transport (+10 risk)',
        'Suspicious high-abuse TLD (.click) (+15 risk)',
        'High keyword density for credential hijacking (+30)',
        'Brand impersonation targeting "netflix" (+25)'
      ],
      ip_address: '127.0.0.1',
      created_at: '2026-09-24 13:05:44'
    },
    {
      id: 1005,
      user_id: 2,
      user_name: 'Alex Morgan',
      url: 'https://login-portal.mycompany-intranet.net',
      domain: 'login-portal.mycompany-intranet.net',
      protocol: 'https',
      risk_score: 35,
      result: 'SUSPICIOUS',
      url_length: 42,
      has_https: true,
      has_ip: false,
      suspicious_keywords: 'login',
      subdomain_count: 2,
      special_character_count: 2,
      reasons: [
        'Contains credential keyword "login" (+12)',
        'Elevated subdomain hierarchy (2 depth) (+10)',
        'Hyphenated domain structure requiring caution'
      ],
      ip_address: '192.168.1.101',
      created_at: '2026-09-24 17:50:11'
    }
  ]);

  // Initial Feedback
  const [allFeedback, setAllFeedback] = useState<FeedbackMessage[]>([
    {
      id: 1,
      name: 'Alex Morgan',
      email: 'user@cybershield.local',
      subject: 'Accuracy on university credential pages',
      message: 'Great tool! Successfully identified a spoofed Microsoft login URL sent to our university inbox.',
      status: 'read',
      created_at: '2026-09-23 16:00:00'
    },
    {
      id: 2,
      name: 'Priya Sharma',
      email: 'priya.sharma@university.edu',
      subject: 'BCA Major Final Project Presentation Inquiry',
      message: 'Can we export scan history as PDF for our academic viva evaluation report?',
      status: 'unread',
      created_at: '2026-09-24 14:10:00'
    }
  ]);

  // Settings
  const [settings, setSettings] = useState<SystemSettings>({
    site_title: 'Cyber Shield Phishing Intelligence',
    site_tagline: 'AI-Inspired Rule-Based Phishing Detection & Mitigation Engine',
    max_scans_per_hour: 50,
    enable_public_scans: true,
    contact_email: 'support@cybershield.local',
    detection_engine_version: 'v2.4-hybrid-heuristics',
    maintenance_mode: false
  });

  // Handlers
  const handleScanCompleted = (newScan: ScanResult) => {
    setAllScans([newScan, ...allScans]);
  };

  const handleUpdateUserStatus = (userId: number, status: 'active' | 'suspended') => {
    setAllUsers(allUsers.map(u => (u.id === userId ? { ...u, status } : u)));
  };

  const handleDeleteUser = (userId: number) => {
    setAllUsers(allUsers.filter(u => u.id !== userId));
  };

  const handleDeleteScan = (scanId: number) => {
    setAllScans(allScans.filter(s => s.id !== scanId));
  };

  const handleUpdateFeedbackStatus = (id: number, status: 'read' | 'unread') => {
    setAllFeedback(allFeedback.map(f => (f.id === id ? { ...f, status } : f)));
  };

  const handleDeleteFeedback = (id: number) => {
    setAllFeedback(allFeedback.filter(f => f.id !== id));
  };

  // User-scoped scans
  const userScans = currentUser
    ? allScans.filter(s => s.user_id === currentUser.id)
    : [];

  return (
    <div className="min-h-screen bg-[#070b14] text-slate-100 flex flex-col selection:bg-cyan-500 selection:text-black relative">
      {/* Background Cyber Grid */}
      <div className="fixed inset-0 cyber-grid pointer-events-none z-0 opacity-80" />

      {/* Ambient Glows */}
      <div className="fixed top-0 left-1/4 w-96 h-96 bg-cyan-500/10 rounded-full blur-[120px] pointer-events-none -z-10" />
      <div className="fixed bottom-10 right-10 w-96 h-96 bg-blue-600/10 rounded-full blur-[140px] pointer-events-none -z-10" />

      {/* Global Quick Action Demo Strip */}
      <div className="bg-[#0c1427] border-b border-slate-800 text-[11px] py-1.5 px-4 text-slate-400 z-30">
        <div className="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-2">
          <div className="flex items-center gap-2">
            <span className="flex items-center gap-1 text-cyan-400 font-semibold">
              <Sparkles className="w-3.5 h-3.5" /> BCA Final Project Demo Simulator:
            </span>
            <span className="hidden sm:inline text-slate-400">
              Active Role: <strong className="text-white">{currentUser ? `${currentUser.name} (${currentUser.role.toUpperCase()})` : 'Guest Visitor'}</strong>
            </span>
          </div>

          <div className="flex items-center gap-2">
            <span className="text-slate-500">Switch Persona:</span>
            <button
              onClick={() => {
                setCurrentUser({
                  id: 2,
                  name: 'Alex Morgan',
                  email: 'user@cybershield.local',
                  phone: '+1-555-0144',
                  role: 'user',
                  status: 'active',
                  created_at: '2026-09-10 14:30:00'
                });
                setActiveTab('user-dashboard');
              }}
              className="px-2 py-0.5 rounded bg-cyan-500/10 border border-cyan-500/30 text-cyan-300 font-mono hover:bg-cyan-500/20 transition-colors"
            >
              User Mode
            </button>
            <button
              onClick={() => {
                setCurrentUser({
                  id: 1,
                  name: 'Chief Security Admin',
                  email: 'admin@cybershield.local',
                  phone: '+1-555-0199',
                  role: 'admin',
                  status: 'active',
                  created_at: '2026-09-01 10:00:00'
                });
                setActiveTab('admin-dashboard');
              }}
              className="px-2 py-0.5 rounded bg-red-500/10 border border-red-500/30 text-red-300 font-mono hover:bg-red-500/20 transition-colors"
            >
              Admin Mode
            </button>
            <button
              onClick={() => {
                setCurrentUser(null);
                setActiveTab('home');
              }}
              className="px-2 py-0.5 rounded bg-slate-800 text-slate-300 hover:text-white font-mono transition-colors"
            >
              Guest
            </button>
          </div>
        </div>
      </div>

      {/* Main Navbar */}
      <Navbar
        activeTab={activeTab}
        setActiveTab={setActiveTab}
        currentUser={currentUser}
        setCurrentUser={setCurrentUser}
        openAuthModal={(mode) => {
          setAuthModalMode(mode);
          setShowAuthModal(true);
        }}
        openVivaModal={() => setShowVivaModal(true)}
        openCodeExplorer={() => setShowCodeExplorer(true)}
      />

      {/* View Routing */}
      <main className="flex-1 z-10">
        {activeTab === 'home' && (
          <div>
            <HeroScanner
              currentUser={currentUser}
              onScanCompleted={handleScanCompleted}
              openAuthModal={(mode) => {
                setAuthModalMode(mode);
                setShowAuthModal(true);
              }}
              onViewDetails={(res) => setSelectedScanForDetails(res)}
              stats={{
                scans: allScans.length,
                threats: allScans.filter(s => s.result !== 'SAFE').length,
                users: allUsers.length,
                accuracy: '98.4%'
              }}
            />

            {/* Features Spotlight */}
            <div className="max-w-6xl mx-auto py-12 px-4 border-t border-slate-800/80">
              <div className="text-center mb-8">
                <span className="text-xs font-bold font-mono text-cyan-400 uppercase tracking-wider">Detection Architecture</span>
                <h2 className="text-2xl font-bold text-white mt-1">Multi-Stage Lexical & Statistical Inspection</h2>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div className="p-6 rounded-xl bg-[#0c1427]/80 border border-slate-800 hover:border-cyan-500/30 transition-all">
                  <div className="w-10 h-10 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center mb-4">
                    <Layers className="w-5 h-5" />
                  </div>
                  <h3 className="text-base font-bold text-white mb-2">Lexical Decomposition</h3>
                  <p className="text-xs text-slate-400 leading-relaxed">
                    Evaluates protocol encryption, host type, character lengths, subdomain levels, and symbol masking tactics in real time.
                  </p>
                </div>

                <div className="p-6 rounded-xl bg-[#0c1427]/80 border border-slate-800 hover:border-cyan-500/30 transition-all">
                  <div className="w-10 h-10 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center mb-4">
                    <Shield className="w-5 h-5" />
                  </div>
                  <h3 className="text-base font-bold text-white mb-2">Brand Spoofing Detection</h3>
                  <p className="text-xs text-slate-400 leading-relaxed">
                    Flags deceptive domain spoofing mimicking high-value institutions (PayPal, Apple, Google, Netflix, Banks).
                  </p>
                </div>

                <div className="p-6 rounded-xl bg-[#0c1427]/80 border border-slate-800 hover:border-cyan-500/30 transition-all">
                  <div className="w-10 h-10 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center mb-4">
                    <Code className="w-5 h-5" />
                  </div>
                  <h3 className="text-base font-bold text-white mb-2">Relational Persistence</h3>
                  <p className="text-xs text-slate-400 leading-relaxed">
                    Full persistent telemetry in MySQL schema with foreign keys, PDO prepared statements, and Bcrypt security.
                  </p>
                </div>
              </div>
            </div>
          </div>
        )}

        {activeTab === 'checker' && (
          <HeroScanner
            currentUser={currentUser}
            onScanCompleted={handleScanCompleted}
            openAuthModal={(mode) => {
              setAuthModalMode(mode);
              setShowAuthModal(true);
            }}
            onViewDetails={(res) => setSelectedScanForDetails(res)}
            stats={{
              scans: allScans.length,
              threats: allScans.filter(s => s.result !== 'SAFE').length,
              users: allUsers.length,
              accuracy: '98.4%'
            }}
          />
        )}

        {activeTab === 'awareness' && <AwarenessView />}
        {activeTab === 'about' && <AboutView />}
        {activeTab === 'contact' && (
          <ContactView
            currentUser={currentUser}
            onSendMessage={(msg) => setAllFeedback([msg, ...allFeedback])}
          />
        )}

        {activeTab === 'user-dashboard' && currentUser && (
          <UserDashboardView
            currentUser={currentUser}
            userScans={userScans}
            onScanNew={() => setActiveTab('checker')}
            onViewDetails={(scan) => setSelectedScanForDetails(scan)}
            onUpdateProfile={(updated) => {
              setCurrentUser({ ...currentUser, ...updated });
            }}
          />
        )}

        {activeTab === 'admin-dashboard' && (
          <AdminDashboardView
            currentAdmin={currentUser?.role === 'admin' ? currentUser : allUsers[0]}
            allUsers={allUsers}
            allScans={allScans}
            allFeedback={allFeedback}
            settings={settings}
            onUpdateUserStatus={handleUpdateUserStatus}
            onDeleteUser={handleDeleteUser}
            onDeleteScan={handleDeleteScan}
            onViewScanDetails={(scan) => setSelectedScanForDetails(scan)}
            onUpdateFeedbackStatus={handleUpdateFeedbackStatus}
            onDeleteFeedback={handleDeleteFeedback}
            onSaveSettings={(s) => setSettings(s)}
          />
        )}
      </main>

      {/* Footer */}
      <footer className="mt-auto border-t border-slate-800/80 bg-[#070b14]/90 py-8 px-4 text-xs text-slate-400 z-10">
        <div className="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
          <div className="flex items-center gap-2">
            <Shield className="w-4 h-4 text-cyan-400" />
            <span className="font-bold text-white">CYBER SHIELD</span>
            <span>&bull; BCA Major Final-Year Project</span>
          </div>

          <div className="flex items-center gap-3">
            <button
              onClick={() => setShowCodeExplorer(true)}
              className="text-cyan-400 hover:underline flex items-center gap-1"
            >
              <Code className="w-3.5 h-3.5" /> Browse XAMPP PHP Code & ZIP
            </button>
            <span className="text-slate-600">|</span>
            <button
              onClick={() => setShowVivaModal(true)}
              className="text-amber-400 hover:underline flex items-center gap-1"
            >
              <BookOpen className="w-3.5 h-3.5" /> 40+ Viva Questions & 2-Min Speech
            </button>
          </div>
        </div>
      </footer>

      {/* Modals */}
      {showAuthModal && (
        <AuthModal
          initialMode={authModalMode}
          onClose={() => setShowAuthModal(false)}
          onLoginSuccess={(user) => {
            setCurrentUser(user);
            setActiveTab(user.role === 'admin' ? 'admin-dashboard' : 'user-dashboard');
          }}
        />
      )}

      {showCodeExplorer && (
        <SourceCodeExplorer onClose={() => setShowCodeExplorer(false)} />
      )}

      {showVivaModal && (
        <VivaPreparationModal onClose={() => setShowVivaModal(false)} />
      )}

      {selectedScanForDetails && (
        <ScanDetailsModal
          scan={selectedScanForDetails}
          onClose={() => setSelectedScanForDetails(null)}
        />
      )}
    </div>
  );
}
