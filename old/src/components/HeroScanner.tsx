import React, { useState } from 'react';
import { Search, Bolt, Shield, AlertTriangle, Users, Target, FlaskConical, CheckCircle } from 'lucide-react';
import { ScanResult, User } from '../types';
import { analyzeURL } from '../utils/analyzer';
import { ScanResultCard } from './ScanResultCard';

interface HeroScannerProps {
  currentUser: User | null;
  onScanCompleted: (result: ScanResult) => void;
  openAuthModal: (mode: 'login') => void;
  onViewDetails: (result: ScanResult) => void;
  stats: {
    scans: number;
    threats: number;
    users: number;
    accuracy: string;
  };
}

export const HeroScanner: React.FC<HeroScannerProps> = ({
  currentUser,
  onScanCompleted,
  openAuthModal,
  onViewDetails,
  stats
}) => {
  const [urlInput, setUrlInput] = useState('');
  const [isScanning, setIsScanning] = useState(false);
  const [latestResult, setLatestResult] = useState<ScanResult | null>(null);

  const handleScan = (e?: React.FormEvent) => {
    if (e) e.preventDefault();
    const clean = urlInput.trim();
    if (!clean) return;

    // Check auth policy if visitor tries to scan
    if (!currentUser) {
      openAuthModal('login');
      return;
    }

    setIsScanning(true);
    setTimeout(() => {
      const result = analyzeURL(clean, currentUser.id, currentUser.name);
      setLatestResult(result);
      onScanCompleted(result);
      setIsScanning(false);
    }, 600);
  };

  const handleDemoSample = (sampleUrl: string) => {
    setUrlInput(sampleUrl);
    if (!currentUser) {
      openAuthModal('login');
      return;
    }
    setIsScanning(true);
    setTimeout(() => {
      const result = analyzeURL(sampleUrl, currentUser.id, currentUser.name);
      setLatestResult(result);
      onScanCompleted(result);
      setIsScanning(false);
    }, 500);
  };

  return (
    <div className="py-12 px-4">
      <div className="max-w-4xl mx-auto text-center">
        {/* Academic Project Badge */}
        <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 text-xs font-semibold mb-6">
          <Shield className="w-3.5 h-3.5" />
          <span>BCA FINAL YEAR MAJOR PROJECT DEMONSTRATION</span>
        </div>

        {/* Hero Title */}
        <h1 className="text-4xl sm:text-5xl md:text-6xl font-extrabold text-white tracking-tight leading-tight mb-4">
          Stay One Step Ahead of <span className="bg-gradient-to-r from-cyan-400 via-sky-300 to-blue-500 bg-clip-text text-transparent">Phishing</span>
        </h1>

        <p className="text-slate-400 text-base sm:text-lg max-w-2xl mx-auto mb-8 leading-relaxed">
          Cyber Shield intelligently analyzes suspicious URLs and helps you identify potential phishing threats before they put your personal and financial credentials at risk.
        </p>

        {/* Scanner Form */}
        <div className="max-w-2xl mx-auto mb-6">
          <form
            onSubmit={handleScan}
            className="p-1.5 rounded-2xl bg-[#0b1222]/90 border border-cyan-500/30 shadow-[0_0_30px_rgba(0,243,255,0.12)] flex items-center gap-2 focus-within:border-cyan-400 focus-within:shadow-[0_0_35px_rgba(0,243,255,0.25)] transition-all"
          >
            <div className="pl-3 text-cyan-400">
              <Search className="w-5 h-5" />
            </div>
            <input
              type="text"
              value={urlInput}
              onChange={(e) => setUrlInput(e.target.value)}
              placeholder="Enter a suspicious URL (e.g., https://paypal-account-verify.click)..."
              className="w-full bg-transparent border-none text-white text-sm sm:text-base font-mono focus:outline-none placeholder-slate-500 py-2.5 px-2"
            />
            <button
              type="submit"
              disabled={isScanning}
              className="bg-gradient-to-r from-cyan-400 to-blue-500 hover:from-cyan-300 hover:to-blue-400 text-black font-bold px-5 py-2.5 rounded-xl text-sm flex items-center gap-1.5 shadow-[0_4px_14px_rgba(0,243,255,0.3)] transition-all disabled:opacity-50 flex-shrink-0"
            >
              {isScanning ? (
                <>
                  <span className="w-4 h-4 border-2 border-black border-t-transparent rounded-full animate-spin" />
                  <span>Analyzing...</span>
                </>
              ) : (
                <>
                  <Bolt className="w-4 h-4" />
                  <span>Scan URL</span>
                </>
              )}
            </button>
          </form>

          {/* Quick Demo Buttons for Viva Exam */}
          <div className="flex flex-wrap items-center justify-center gap-2 mt-3 text-xs text-slate-400">
            <span className="flex items-center gap-1 text-slate-500">
              <FlaskConical className="w-3.5 h-3.5 text-amber-400" /> Viva Test Samples:
            </span>
            <button
              onClick={() => handleDemoSample('https://github.com/torvalds/linux')}
              className="px-2.5 py-1 rounded bg-slate-800/80 border border-slate-700 hover:border-emerald-500 text-slate-300 hover:text-emerald-300 transition-colors font-mono"
            >
              Safe: GitHub
            </button>
            <button
              onClick={() => handleDemoSample('https://login.paypal.verify-auth.account.xyz/signin')}
              className="px-2.5 py-1 rounded bg-slate-800/80 border border-slate-700 hover:border-red-500 text-slate-300 hover:text-red-300 transition-colors font-mono"
            >
              Phish: PayPal Spoof
            </button>
            <button
              onClick={() => handleDemoSample('http://192.168.1.45/bank-of-america/update.html')}
              className="px-2.5 py-1 rounded bg-slate-800/80 border border-slate-700 hover:border-red-500 text-slate-300 hover:text-red-300 transition-colors font-mono"
            >
              Phish: Raw IP Banking
            </button>
            <button
              onClick={() => handleDemoSample('https://login-portal.mycompany-intranet.net')}
              className="px-2.5 py-1 rounded bg-slate-800/80 border border-slate-700 hover:border-amber-500 text-slate-300 hover:text-amber-300 transition-colors font-mono"
            >
              Suspicious: Hyphenated
            </button>
          </div>
        </div>

        {/* Display Live Scan Result */}
        {latestResult && (
          <div className="mt-8 text-left max-w-3xl mx-auto">
            <ScanResultCard result={latestResult} onViewDetails={onViewDetails} />
          </div>
        )}

        {/* Dynamic Database Statistics */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-3 max-w-3xl mx-auto mt-12">
          <div className="p-4 rounded-xl bg-slate-900/60 border border-slate-800/80 text-center">
            <div className="text-2xl sm:text-3xl font-mono font-bold text-cyan-400">
              {stats.scans.toLocaleString()}
            </div>
            <div className="text-[11px] font-semibold tracking-wider uppercase text-slate-400 mt-1 flex items-center justify-center gap-1">
              <Target className="w-3.5 h-3.5 text-cyan-400" /> URLs Scanned
            </div>
          </div>

          <div className="p-4 rounded-xl bg-slate-900/60 border border-slate-800/80 text-center">
            <div className="text-2xl sm:text-3xl font-mono font-bold text-red-400">
              {stats.threats.toLocaleString()}
            </div>
            <div className="text-[11px] font-semibold tracking-wider uppercase text-slate-400 mt-1 flex items-center justify-center gap-1">
              <AlertTriangle className="w-3.5 h-3.5 text-red-400" /> Threats Flagged
            </div>
          </div>

          <div className="p-4 rounded-xl bg-slate-900/60 border border-slate-800/80 text-center">
            <div className="text-2xl sm:text-3xl font-mono font-bold text-blue-400">
              {stats.users.toLocaleString()}
            </div>
            <div className="text-[11px] font-semibold tracking-wider uppercase text-slate-400 mt-1 flex items-center justify-center gap-1">
              <Users className="w-3.5 h-3.5 text-blue-400" /> Registered Users
            </div>
          </div>

          <div className="p-4 rounded-xl bg-slate-900/60 border border-slate-800/80 text-center">
            <div className="text-2xl sm:text-3xl font-mono font-bold text-emerald-400">
              {stats.accuracy}
            </div>
            <div className="text-[11px] font-semibold tracking-wider uppercase text-slate-400 mt-1 flex items-center justify-center gap-1">
              <CheckCircle className="w-3.5 h-3.5 text-emerald-400" /> Heuristic Accuracy
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};
