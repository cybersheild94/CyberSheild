import React from 'react';
import { ShieldCheck, Rocket, Code2, Database, Cpu, CheckCircle2 } from 'lucide-react';

export const AboutView: React.FC = () => {
  return (
    <div className="max-w-5xl mx-auto py-10 px-4">
      <div className="text-center mb-10">
        <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 text-xs font-semibold mb-3">
          <Code2 className="w-3.5 h-3.5" />
          <span>BCA MAJOR PROJECT SPECIFICATION</span>
        </div>
        <h1 className="text-3xl sm:text-4xl font-extrabold text-white mb-3">
          About Cyber Shield
        </h1>
        <p className="text-slate-400 max-w-2xl mx-auto text-sm sm:text-base">
          An academic research and engineering platform demonstrating intelligent, zero-latency phishing defense without external rate-limited dependencies.
        </p>
      </div>

      {/* Abstract Card */}
      <div className="p-6 sm:p-8 rounded-2xl bg-[#0c1427]/80 border border-slate-800 mb-8">
        <h2 className="text-lg font-bold text-white mb-3 flex items-center gap-2">
          <ShieldCheck className="w-5 h-5 text-cyan-400" /> Executive Project Abstract
        </h2>
        <p className="text-xs sm:text-sm text-slate-300 leading-relaxed mb-4">
          Phishing remains one of the most persistent attack vectors in modern cybersecurity. Conventional defenses rely on centralized static blacklists that suffer from latency windows during zero-hour attacks, where attackers deploy automated short-lived domains that disappear in under 4 hours.
        </p>
        <p className="text-xs sm:text-sm text-slate-300 leading-relaxed">
          <strong className="text-white">CYBER SHIELD</strong> solves this problem by deploying an intelligent rule-based heuristic classification pipeline. By extracting lexical, structural, and semantic attributes directly from a target URL in real time, Cyber Shield calculates a deterministic 0–100 Threat Index in under 25 milliseconds.
        </p>
      </div>

      {/* Technology Stack Grid */}
      <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div className="p-4 rounded-xl bg-slate-900/60 border border-slate-800 text-center">
          <Code2 className="w-6 h-6 text-cyan-400 mx-auto mb-2" />
          <div className="text-sm font-bold text-white">PHP 8+ Backend</div>
          <div className="text-[11px] text-slate-400 mt-1">Strict Types, PDO & OOP</div>
        </div>
        <div className="p-4 rounded-xl bg-slate-900/60 border border-slate-800 text-center">
          <Database className="w-6 h-6 text-yellow-400 mx-auto mb-2" />
          <div className="text-sm font-bold text-white">MySQL 5.7+ / 8.0</div>
          <div className="text-[11px] text-slate-400 mt-1">InnoDB, FKs, Indexes</div>
        </div>
        <div className="p-4 rounded-xl bg-slate-900/60 border border-slate-800 text-center">
          <Cpu className="w-6 h-6 text-emerald-400 mx-auto mb-2" />
          <div className="text-sm font-bold text-white">Rule Heuristics</div>
          <div className="text-[11px] text-slate-400 mt-1">10 Scoring Vectors</div>
        </div>
        <div className="p-4 rounded-xl bg-slate-900/60 border border-slate-800 text-center">
          <Rocket className="w-6 h-6 text-blue-400 mx-auto mb-2" />
          <div className="text-sm font-bold text-white">XAMPP Ready</div>
          <div className="text-[11px] text-slate-400 mt-1">Zero-Config Portability</div>
        </div>
      </div>

      {/* Implemented vs Future Scope */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div className="p-6 rounded-xl bg-[#0c1427]/80 border border-emerald-500/30">
          <h3 className="text-sm font-bold text-emerald-400 mb-3 flex items-center gap-2">
            <CheckCircle2 className="w-4 h-4" /> Currently Implemented (Phase 1)
          </h3>
          <ul className="space-y-2 text-xs text-slate-300">
            <li className="flex items-start gap-2">
              <span className="text-emerald-400">&bull;</span>
              <span>10-Vector Lexical Heuristic Detection Engine</span>
            </li>
            <li className="flex items-start gap-2">
              <span className="text-emerald-400">&bull;</span>
              <span>Role-Based Authentication (User vs Admin RBAC)</span>
            </li>
            <li className="flex items-start gap-2">
              <span className="text-emerald-400">&bull;</span>
              <span>Bcrypt Password Hashing and CSRF Protection</span>
            </li>
            <li className="flex items-start gap-2">
              <span className="text-emerald-400">&bull;</span>
              <span>Relational MySQL Persistence & Audit Logging</span>
            </li>
            <li className="flex items-start gap-2">
              <span className="text-emerald-400">&bull;</span>
              <span>Dynamic Visualizations and Forensic Inspection</span>
            </li>
          </ul>
        </div>

        <div className="p-6 rounded-xl bg-[#0c1427]/80 border border-cyan-500/30">
          <h3 className="text-sm font-bold text-cyan-400 mb-3 flex items-center gap-2">
            <Rocket className="w-4 h-4" /> Future Scope & Enhancements (Phase 2)
          </h3>
          <ul className="space-y-2 text-xs text-slate-300">
            <li className="flex items-start gap-2">
              <span className="text-cyan-400">&bull;</span>
              <span>Random Forest / XGBoost ML Model Training on Kaggle Data</span>
            </li>
            <li className="flex items-start gap-2">
              <span className="text-cyan-400">&bull;</span>
              <span>Direct WHOIS Socket Lookup for Domain Registration Age</span>
            </li>
            <li className="flex items-start gap-2">
              <span className="text-cyan-400">&bull;</span>
              <span>Google Safe Browsing & VirusTotal API Integration</span>
            </li>
            <li className="flex items-start gap-2">
              <span className="text-cyan-400">&bull;</span>
              <span>Cross-Browser Extension for Pre-Click Interception</span>
            </li>
            <li className="flex items-start gap-2">
              <span className="text-cyan-400">&bull;</span>
              <span>Raw .EML Email Header Parser for SPF / DKIM Checks</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  );
};
