import React from 'react';
import { ShieldCheck, ShieldAlert, ShieldX, Clock, Lock, Unlock, Server, Hash, FileText } from 'lucide-react';
import { ScanResult } from '../types';

interface ScanResultCardProps {
  result: ScanResult;
  onViewDetails?: (result: ScanResult) => void;
}

export const ScanResultCard: React.FC<ScanResultCardProps> = ({ result, onViewDetails }) => {
  const isPhishing = result.result === 'PHISHING';
  const isSuspicious = result.result === 'SUSPICIOUS';
  const isSafe = result.result === 'SAFE';

  const badgeColor = isPhishing
    ? 'text-red-400 bg-red-500/10 border-red-500/30'
    : isSuspicious
    ? 'text-amber-400 bg-amber-500/10 border-amber-500/30'
    : 'text-emerald-400 bg-emerald-500/10 border-emerald-500/30';

  const circleColor = isPhishing
    ? 'text-red-500 shadow-[0_0_30px_rgba(239,68,68,0.3)]'
    : isSuspicious
    ? 'text-amber-500 shadow-[0_0_30px_rgba(245,158,11,0.3)]'
    : 'text-emerald-500 shadow-[0_0_30px_rgba(16,185,129,0.3)]';

  return (
    <div className={`rounded-xl border p-5 md:p-6 bg-[#0c1427]/80 backdrop-blur-md transition-all ${
      isPhishing ? 'border-red-500/40' : isSuspicious ? 'border-amber-500/40' : 'border-emerald-500/40'
    }`}>
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
        {/* Threat Gauge Visual */}
        <div className="lg:col-span-4 flex flex-col items-center justify-center text-center border-b lg:border-b-0 lg:border-r border-slate-700/60 pb-5 lg:pb-0 lg:pr-6">
          <div className={`w-32 h-32 rounded-full border-4 flex flex-col items-center justify-center ${circleColor}`}>
            <span className="font-mono text-4xl font-extrabold">{result.risk_score}</span>
            <span className="text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-0.5">Threat Index</span>
          </div>

          <div className="mt-4">
            <span className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border ${badgeColor}`}>
              {isPhishing && <ShieldX className="w-3.5 h-3.5" />}
              {isSuspicious && <ShieldAlert className="w-3.5 h-3.5" />}
              {isSafe && <ShieldCheck className="w-3.5 h-3.5" />}
              {result.result}
            </span>
          </div>
        </div>

        {/* Forensic Vector Breakdown */}
        <div className="lg:col-span-8">
          <div className="flex flex-wrap items-center justify-between gap-2 mb-2">
            <h3 className="text-lg font-bold text-white">
              {isPhishing
                ? 'High-Confidence Phishing Attack Vector Detected'
                : isSuspicious
                ? 'Suspicious Anomalies Require User Vigilance'
                : 'Target URL Verified Safe with Standard Profile'}
            </h3>
            <span className="text-xs text-slate-400 flex items-center gap-1">
              <Clock className="w-3 h-3" /> Just now
            </span>
          </div>

          {/* URL Box */}
          <div className="bg-slate-900/80 rounded-lg p-2.5 border border-slate-800 font-mono text-xs text-cyan-300 break-all mb-4">
            {result.url}
          </div>

          {/* Micro Metrics Grid */}
          <div className="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-4 text-center">
            <div className="p-2 rounded bg-slate-900/60 border border-slate-800">
              <div className="text-[11px] text-slate-400">Transport</div>
              <div className={`text-xs font-bold flex items-center justify-center gap-1 mt-0.5 ${result.has_https ? 'text-emerald-400' : 'text-red-400'}`}>
                {result.has_https ? <Lock className="w-3 h-3" /> : <Unlock className="w-3 h-3" />}
                {result.protocol.toUpperCase()}
              </div>
            </div>

            <div className="p-2 rounded bg-slate-900/60 border border-slate-800">
              <div className="text-[11px] text-slate-400">Resolution</div>
              <div className={`text-xs font-bold flex items-center justify-center gap-1 mt-0.5 ${result.has_ip ? 'text-red-400' : 'text-cyan-400'}`}>
                <Server className="w-3 h-3" />
                {result.has_ip ? 'Raw IP' : 'Hostname'}
              </div>
            </div>

            <div className="p-2 rounded bg-slate-900/60 border border-slate-800">
              <div className="text-[11px] text-slate-400">Length</div>
              <div className="text-xs font-bold text-white mt-0.5 font-mono">
                {result.url_length} chars
              </div>
            </div>

            <div className="p-2 rounded bg-slate-900/60 border border-slate-800">
              <div className="text-[11px] text-slate-400">Subdomains</div>
              <div className={`text-xs font-bold mt-0.5 ${result.subdomain_count >= 2 ? 'text-amber-400' : 'text-white'}`}>
                {result.subdomain_count} levels
              </div>
            </div>
          </div>

          {/* Reasons List */}
          <div className="bg-slate-900/50 p-3 rounded-lg border border-slate-800/80">
            <div className="text-xs font-semibold text-cyan-400 uppercase tracking-wider mb-2 flex items-center gap-1">
              <Hash className="w-3.5 h-3.5" /> Why was this score assigned?
            </div>
            <ul className="space-y-1.5 text-xs text-slate-300">
              {result.reasons.map((r, i) => (
                <li key={i} className="flex items-start gap-2">
                  <span className={`w-1.5 h-1.5 rounded-full mt-1.5 flex-shrink-0 ${isSafe ? 'bg-emerald-400' : isPhishing ? 'bg-red-400' : 'bg-amber-400'}`} />
                  <span>{r}</span>
                </li>
              ))}
            </ul>
          </div>

          {onViewDetails && (
            <div className="mt-4 flex justify-end">
              <button
                onClick={() => onViewDetails(result)}
                className="text-xs font-semibold text-cyan-400 hover:text-cyan-300 flex items-center gap-1 underline underline-offset-4"
              >
                <FileText className="w-3.5 h-3.5" /> View Full Forensic Audit Dossier
              </button>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};
