import React from 'react';
import { X, ShieldAlert, ShieldCheck, ShieldX, Calendar, Server, Hash, FileCheck } from 'lucide-react';
import { ScanResult } from '../types';

interface ScanDetailsModalProps {
  scan: ScanResult | null;
  onClose: () => void;
}

export const ScanDetailsModal: React.FC<ScanDetailsModalProps> = ({ scan, onClose }) => {
  if (!scan) return null;

  const isPhishing = scan.result === 'PHISHING';
  const isSuspicious = scan.result === 'SUSPICIOUS';

  return (
    <div className="fixed inset-0 z-50 bg-black/85 backdrop-blur-md flex items-center justify-center p-3 sm:p-4">
      <div className="w-full max-w-3xl bg-[#070b14] border border-cyan-500/30 rounded-2xl shadow-2xl flex flex-col max-h-[90vh] overflow-hidden">
        {/* Header */}
        <div className="p-4 border-b border-slate-800 flex items-center justify-between bg-slate-900/60">
          <div className="flex items-center gap-2">
            <div className={`w-8 h-8 rounded-lg flex items-center justify-center ${
              isPhishing ? 'bg-red-500/10 text-red-400' : isSuspicious ? 'bg-amber-500/10 text-amber-400' : 'bg-emerald-500/10 text-emerald-400'
            }`}>
              {isPhishing ? <ShieldX className="w-4 h-4" /> : isSuspicious ? <ShieldAlert className="w-4 h-4" /> : <ShieldCheck className="w-4 h-4" />}
            </div>
            <div>
              <h3 className="text-sm sm:text-base font-bold text-white">
                Deep Forensic Inspection Dossier #SEC-{scan.id}
              </h3>
              <p className="text-[11px] text-slate-400">
                Persistent MySQL record with complete multi-vector telemetry
              </p>
            </div>
          </div>
          <button
            onClick={onClose}
            className="p-1.5 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white transition-colors"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Modal Body */}
        <div className="p-5 overflow-y-auto space-y-4 text-xs text-slate-300">
          {/* Target URL */}
          <div className="p-3 rounded-lg bg-slate-900/80 border border-slate-800">
            <span className="text-[10px] uppercase font-bold text-slate-400 block mb-1">Target URL Under Analysis</span>
            <div className="font-mono text-cyan-300 text-sm break-all font-semibold">
              {scan.url}
            </div>
          </div>

          {/* Core Score Banner */}
          <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
            <div className="p-3 rounded-lg bg-slate-900/60 border border-slate-800">
              <span className="text-[11px] text-slate-400">Threat Index</span>
              <div className={`text-xl font-mono font-bold mt-1 ${isPhishing ? 'text-red-400' : isSuspicious ? 'text-amber-400' : 'text-emerald-400'}`}>
                {scan.risk_score}/100
              </div>
            </div>
            <div className="p-3 rounded-lg bg-slate-900/60 border border-slate-800">
              <span className="text-[11px] text-slate-400">Verdict</span>
              <div className="text-xs font-bold text-white mt-1.5">
                {scan.result}
              </div>
            </div>
            <div className="p-3 rounded-lg bg-slate-900/60 border border-slate-800">
              <span className="text-[11px] text-slate-400">Protocol</span>
              <div className={`text-xs font-bold font-mono mt-1 ${scan.has_https ? 'text-emerald-400' : 'text-red-400'}`}>
                {scan.protocol.toUpperCase()} ({scan.has_https ? 'TLS/SSL' : 'Insecure'})
              </div>
            </div>
            <div className="p-3 rounded-lg bg-slate-900/60 border border-slate-800">
              <span className="text-[11px] text-slate-400">Length</span>
              <div className="text-xs font-bold text-white mt-1 font-mono">
                {scan.url_length} characters
              </div>
            </div>
          </div>

          {/* Lexical Details */}
          <div className="p-4 rounded-xl bg-slate-900/40 border border-slate-800 space-y-2">
            <h4 className="font-bold text-white flex items-center gap-1.5 mb-2">
              <Server className="w-4 h-4 text-cyan-400" /> Host & Lexical Characteristics
            </h4>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
              <div className="flex justify-between py-1 border-b border-slate-800">
                <span className="text-slate-400">Domain / Authority:</span>
                <span className="font-mono text-white">{scan.domain}</span>
              </div>
              <div className="flex justify-between py-1 border-b border-slate-800">
                <span className="text-slate-400">Subdomain Hierarchy:</span>
                <span className="font-mono text-white">{scan.subdomain_count} levels</span>
              </div>
              <div className="flex justify-between py-1 border-b border-slate-800">
                <span className="text-slate-400">Numeric IP Address:</span>
                <span className={scan.has_ip ? 'text-red-400 font-bold' : 'text-slate-300'}>
                  {scan.has_ip ? 'Yes (Hostile Raw IP)' : 'No (Standard FQDN)'}
                </span>
              </div>
              <div className="flex justify-between py-1 border-b border-slate-800">
                <span className="text-slate-400">Credential Keywords:</span>
                <span className="text-red-400 font-mono">{scan.suspicious_keywords || 'None'}</span>
              </div>
            </div>
          </div>

          {/* Triggered Reasons */}
          <div className="p-4 rounded-xl bg-slate-900/40 border border-slate-800">
            <h4 className="font-bold text-white flex items-center gap-1.5 mb-3">
              <Hash className="w-4 h-4 text-cyan-400" /> Specific Heuristics Assigned
            </h4>
            <ul className="space-y-2">
              {scan.reasons.map((r, i) => (
                <li key={i} className="flex items-start gap-2 bg-slate-950/60 p-2.5 rounded-lg border border-slate-800/80">
                  <FileCheck className="w-3.5 h-3.5 text-cyan-400 mt-0.5 flex-shrink-0" />
                  <span className="text-slate-300">{r}</span>
                </li>
              ))}
            </ul>
          </div>

          {/* Metadata */}
          <div className="flex flex-wrap items-center justify-between text-slate-500 text-[11px] pt-2">
            <span className="flex items-center gap-1">
              <Calendar className="w-3 h-3" /> Scanned On: {scan.created_at}
            </span>
            <span>Client IP: {scan.ip_address}</span>
            <span>Recorded in MySQL `scans` table</span>
          </div>
        </div>
      </div>
    </div>
  );
};
