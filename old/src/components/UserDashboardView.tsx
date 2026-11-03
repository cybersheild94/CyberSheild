import React, { useState } from 'react';
import { LayoutDashboard, ShieldCheck, ShieldAlert, ShieldX, Search, Filter, Plus, Clock, Key, Eye } from 'lucide-react';
import { User, ScanResult } from '../types';

interface UserDashboardViewProps {
  currentUser: User;
  userScans: ScanResult[];
  onScanNew: () => void;
  onViewDetails: (scan: ScanResult) => void;
  onUpdateProfile: (updated: Partial<User>) => void;
}

export const UserDashboardView: React.FC<UserDashboardViewProps> = ({
  currentUser,
  userScans,
  onScanNew,
  onViewDetails
}) => {
  const [activeTab, setActiveTab] = useState<'overview' | 'scans' | 'profile'>('overview');
  const [searchQuery, setSearchQuery] = useState('');
  const [filterResult, setFilterResult] = useState<'ALL' | 'SAFE' | 'SUSPICIOUS' | 'PHISHING'>('ALL');

  // Metrics
  const totalScans = userScans.length;
  const safeScans = userScans.filter(s => s.result === 'SAFE').length;
  const suspiciousScans = userScans.filter(s => s.result === 'SUSPICIOUS').length;
  const phishingScans = userScans.filter(s => s.result === 'PHISHING').length;

  const safePct = totalScans > 0 ? Math.round((safeScans / totalScans) * 100) : 0;
  const suspPct = totalScans > 0 ? Math.round((suspiciousScans / totalScans) * 100) : 0;
  const phishPct = totalScans > 0 ? Math.round((phishingScans / totalScans) * 100) : 0;

  // Filtered scans
  const filteredScans = userScans.filter(s => {
    const matchesSearch = s.url.toLowerCase().includes(searchQuery.toLowerCase()) || s.domain.toLowerCase().includes(searchQuery.toLowerCase());
    const matchesFilter = filterResult === 'ALL' || s.result === filterResult;
    return matchesSearch && matchesFilter;
  });

  return (
    <div className="max-w-7xl mx-auto py-8 px-4">
      {/* Header */}
      <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 pb-6 border-b border-slate-800">
        <div>
          <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-[11px] font-mono bg-cyan-500/10 text-cyan-300 border border-cyan-500/30 mb-2">
            USER FORENSIC DASHBOARD
          </div>
          <h1 className="text-2xl sm:text-3xl font-bold text-white flex items-center gap-2">
            Welcome, {currentUser.name}
          </h1>
          <p className="text-sm text-slate-400 mt-1">
            Review your personal URL scans, threat breakdowns, and security telemetry.
          </p>
        </div>

        <div className="flex items-center gap-2">
          <button
            onClick={onScanNew}
            className="px-4 py-2 rounded-lg bg-cyan-400 hover:bg-cyan-300 text-black font-bold text-xs flex items-center gap-1.5 shadow-[0_0_15px_rgba(0,243,255,0.3)] transition-all"
          >
            <Plus className="w-4 h-4" /> Scan New URL
          </button>
        </div>
      </div>

      {/* Tabs */}
      <div className="flex items-center gap-2 mb-6 border-b border-slate-800">
        <button
          onClick={() => setActiveTab('overview')}
          className={`px-4 py-2 text-sm font-semibold border-b-2 transition-colors flex items-center gap-1.5 ${
            activeTab === 'overview'
              ? 'border-cyan-400 text-cyan-400'
              : 'border-transparent text-slate-400 hover:text-slate-200'
          }`}
        >
          <LayoutDashboard className="w-4 h-4" /> Overview & Charts
        </button>
        <button
          onClick={() => setActiveTab('scans')}
          className={`px-4 py-2 text-sm font-semibold border-b-2 transition-colors flex items-center gap-1.5 ${
            activeTab === 'scans'
              ? 'border-cyan-400 text-cyan-400'
              : 'border-transparent text-slate-400 hover:text-slate-200'
          }`}
        >
          <Clock className="w-4 h-4" /> My Scans ({totalScans})
        </button>
      </div>

      {activeTab === 'overview' && (
        <div className="space-y-6">
          {/* 4 Metric Cards */}
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div className="p-5 rounded-xl bg-[#0c1427]/80 border border-slate-800 flex items-center gap-4">
              <div className="w-12 h-12 rounded-xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400">
                <Search className="w-6 h-6" />
              </div>
              <div>
                <div className="text-2xl font-bold font-mono text-cyan-400">{totalScans}</div>
                <div className="text-xs text-slate-400 font-semibold uppercase">Total Scans</div>
              </div>
            </div>

            <div className="p-5 rounded-xl bg-[#0c1427]/80 border border-slate-800 flex items-center gap-4">
              <div className="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                <ShieldCheck className="w-6 h-6" />
              </div>
              <div>
                <div className="text-2xl font-bold font-mono text-emerald-400">{safeScans}</div>
                <div className="text-xs text-slate-400 font-semibold uppercase">Safe URLs</div>
              </div>
            </div>

            <div className="p-5 rounded-xl bg-[#0c1427]/80 border border-slate-800 flex items-center gap-4">
              <div className="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400">
                <ShieldAlert className="w-6 h-6" />
              </div>
              <div>
                <div className="text-2xl font-bold font-mono text-amber-400">{suspiciousScans}</div>
                <div className="text-xs text-slate-400 font-semibold uppercase">Suspicious</div>
              </div>
            </div>

            <div className="p-5 rounded-xl bg-[#0c1427]/80 border border-slate-800 flex items-center gap-4">
              <div className="w-12 h-12 rounded-xl bg-red-500/10 border border-red-500/30 flex items-center justify-center text-red-400">
                <ShieldX className="w-6 h-6" />
              </div>
              <div>
                <div className="text-2xl font-bold font-mono text-red-400">{phishingScans}</div>
                <div className="text-xs text-slate-400 font-semibold uppercase">Phishing Threats</div>
              </div>
            </div>
          </div>

          {/* Visualization Row */}
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
            {/* Threat Distribution Bars */}
            <div className="lg:col-span-6 p-6 rounded-xl bg-[#0c1427]/80 border border-slate-800">
              <h3 className="text-base font-bold text-white mb-4 flex items-center justify-between">
                <span>My Threat Classification Breakdown</span>
                <span className="text-xs text-cyan-400 font-mono">Real-Time MySQL Data</span>
              </h3>

              <div className="space-y-4">
                <div>
                  <div className="flex justify-between text-xs font-semibold mb-1">
                    <span className="text-emerald-400 flex items-center gap-1"><ShieldCheck className="w-3.5 h-3.5" /> Safe URLs ({safeScans})</span>
                    <span className="text-slate-300 font-mono">{safePct}%</span>
                  </div>
                  <div className="w-full h-3 bg-slate-800 rounded-full overflow-hidden">
                    <div className="h-full bg-emerald-500 rounded-full transition-all" style={{ width: `${safePct}%` }} />
                  </div>
                </div>

                <div>
                  <div className="flex justify-between text-xs font-semibold mb-1">
                    <span className="text-amber-400 flex items-center gap-1"><ShieldAlert className="w-3.5 h-3.5" /> Suspicious ({suspiciousScans})</span>
                    <span className="text-slate-300 font-mono">{suspPct}%</span>
                  </div>
                  <div className="w-full h-3 bg-slate-800 rounded-full overflow-hidden">
                    <div className="h-full bg-amber-500 rounded-full transition-all" style={{ width: `${suspPct}%` }} />
                  </div>
                </div>

                <div>
                  <div className="flex justify-between text-xs font-semibold mb-1">
                    <span className="text-red-400 flex items-center gap-1"><ShieldX className="w-3.5 h-3.5" /> Phishing Threats ({phishingScans})</span>
                    <span className="text-slate-300 font-mono">{phishPct}%</span>
                  </div>
                  <div className="w-full h-3 bg-slate-800 rounded-full overflow-hidden">
                    <div className="h-full bg-red-500 rounded-full transition-all" style={{ width: `${phishPct}%` }} />
                  </div>
                </div>
              </div>

              <div className="mt-6 p-3 rounded-lg bg-slate-900/60 border border-slate-800 text-xs text-slate-400">
                <span className="text-white font-semibold block mb-1">Heuristic Engine Status: Active</span>
                Scans are validated across 10 deterministic lexical weights with persistent audit integrity.
              </div>
            </div>

            {/* Quick Security Tips */}
            <div className="lg:col-span-6 p-6 rounded-xl bg-[#0c1427]/80 border border-slate-800 flex flex-col justify-between">
              <div>
                <h3 className="text-base font-bold text-white mb-3 flex items-center gap-2">
                  <Key className="w-4 h-4 text-cyan-400" /> Account Security Details
                </h3>
                <div className="space-y-2 text-xs text-slate-300">
                  <div className="flex justify-between py-1.5 border-b border-slate-800">
                    <span className="text-slate-400">Registered Email:</span>
                    <span className="font-mono text-cyan-300">{currentUser.email}</span>
                  </div>
                  <div className="flex justify-between py-1.5 border-b border-slate-800">
                    <span className="text-slate-400">Access Level:</span>
                    <span className="text-emerald-400 font-semibold uppercase">{currentUser.role}</span>
                  </div>
                  <div className="flex justify-between py-1.5 border-b border-slate-800">
                    <span className="text-slate-400">Database Persistence:</span>
                    <span className="text-white">Active (User ID: {currentUser.id})</span>
                  </div>
                  <div className="flex justify-between py-1.5">
                    <span className="text-slate-400">Password Encryption:</span>
                    <span className="text-cyan-300 font-mono">Bcrypt (cost=10)</span>
                  </div>
                </div>
              </div>

              <div className="mt-6 pt-4 border-t border-slate-800 flex gap-2">
                <button
                  onClick={onScanNew}
                  className="flex-1 py-2 rounded-lg bg-cyan-500/10 border border-cyan-500/30 text-cyan-300 hover:bg-cyan-500/20 text-xs font-semibold transition-colors flex items-center justify-center gap-1"
                >
                  <Search className="w-3.5 h-3.5" /> Analyze Another URL
                </button>
              </div>
            </div>
          </div>

          {/* Recent Scans Table */}
          <div className="rounded-xl bg-[#0c1427]/80 border border-slate-800 overflow-hidden">
            <div className="p-4 border-b border-slate-800 flex justify-between items-center">
              <h3 className="text-sm font-bold text-white flex items-center gap-2">
                <Clock className="w-4 h-4 text-cyan-400" /> Recent Scans
              </h3>
              <button
                onClick={() => setActiveTab('scans')}
                className="text-xs text-cyan-400 hover:underline font-semibold"
              >
                View All &rarr;
              </button>
            </div>

            <div className="overflow-x-auto">
              <table className="w-full text-left text-xs text-slate-300">
                <thead className="bg-slate-900/60 text-[11px] uppercase tracking-wider text-slate-400 font-mono border-b border-slate-800">
                  <tr>
                    <th className="p-3">Target URL</th>
                    <th className="p-3">Score</th>
                    <th className="p-3">Classification</th>
                    <th className="p-3">Timestamp</th>
                    <th className="p-3 text-right">Action</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-800">
                  {userScans.slice(0, 5).map(scan => (
                    <tr key={scan.id} className="hover:bg-slate-800/30 transition-colors">
                      <td className="p-3 font-mono text-cyan-300 max-w-[280px] truncate" title={scan.url}>
                        {scan.url}
                      </td>
                      <td className="p-3 font-mono font-bold">
                        <span className={scan.risk_score > 59 ? 'text-red-400' : scan.risk_score > 29 ? 'text-amber-400' : 'text-emerald-400'}>
                          {scan.risk_score}/100
                        </span>
                      </td>
                      <td className="p-3">
                        <span className={`px-2 py-0.5 rounded text-[10px] font-bold border ${
                          scan.result === 'PHISHING' ? 'text-red-400 bg-red-500/10 border-red-500/30' : scan.result === 'SUSPICIOUS' ? 'text-amber-400 bg-amber-500/10 border-amber-500/30' : 'text-emerald-400 bg-emerald-500/10 border-emerald-500/30'
                        }`}>
                          {scan.result}
                        </span>
                      </td>
                      <td className="p-3 text-slate-400">{scan.created_at}</td>
                      <td className="p-3 text-right">
                        <button
                          onClick={() => onViewDetails(scan)}
                          className="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-cyan-300 font-semibold text-xs flex items-center gap-1 ml-auto"
                        >
                          <Eye className="w-3.5 h-3.5" /> Details
                        </button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      )}

      {activeTab === 'scans' && (
        <div className="space-y-4">
          {/* Toolbar */}
          <div className="p-4 rounded-xl bg-[#0c1427]/80 border border-slate-800 flex flex-wrap items-center justify-between gap-3">
            <div className="flex items-center gap-2 flex-1 min-w-[240px]">
              <div className="relative w-full">
                <Search className="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <input
                  type="text"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  placeholder="Search by URL or domain..."
                  className="w-full bg-slate-900 border border-slate-700 rounded-lg pl-9 pr-3 py-1.5 text-xs text-white focus:outline-none focus:border-cyan-400"
                />
              </div>
            </div>

            <div className="flex items-center gap-2">
              <Filter className="w-4 h-4 text-slate-400" />
              <select
                value={filterResult}
                onChange={(e) => setFilterResult(e.target.value as any)}
                className="bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-white focus:outline-none focus:border-cyan-400"
              >
                <option value="ALL">All Classifications</option>
                <option value="SAFE">SAFE Only</option>
                <option value="SUSPICIOUS">SUSPICIOUS Only</option>
                <option value="PHISHING">PHISHING Only</option>
              </select>
            </div>
          </div>

          {/* Full Scans Table */}
          <div className="rounded-xl bg-[#0c1427]/80 border border-slate-800 overflow-hidden">
            <div className="overflow-x-auto">
              <table className="w-full text-left text-xs text-slate-300">
                <thead className="bg-slate-900/60 text-[11px] uppercase tracking-wider text-slate-400 font-mono border-b border-slate-800">
                  <tr>
                    <th className="p-3">Scan ID</th>
                    <th className="p-3">Target URL</th>
                    <th className="p-3">Domain</th>
                    <th className="p-3">Transport</th>
                    <th className="p-3">Risk Score</th>
                    <th className="p-3">Verdict</th>
                    <th className="p-3">Scanned On</th>
                    <th className="p-3 text-right">Action</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-800">
                  {filteredScans.length === 0 ? (
                    <tr>
                      <td colSpan={8} className="p-8 text-center text-slate-500">
                        No scan records found matching your filters.
                      </td>
                    </tr>
                  ) : (
                    filteredScans.map(s => (
                      <tr key={s.id} className="hover:bg-slate-800/30 transition-colors">
                        <td className="p-3 font-mono text-slate-400">#SEC-{s.id}</td>
                        <td className="p-3 font-mono text-cyan-300 max-w-[220px] truncate" title={s.url}>{s.url}</td>
                        <td className="p-3 font-mono text-white">{s.domain}</td>
                        <td className="p-3">
                          <span className={`px-1.5 py-0.5 rounded text-[10px] font-mono ${s.has_https ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400'}`}>
                            {s.protocol.toUpperCase()}
                          </span>
                        </td>
                        <td className="p-3 font-mono font-bold">
                          <span className={s.risk_score > 59 ? 'text-red-400' : s.risk_score > 29 ? 'text-amber-400' : 'text-emerald-400'}>
                            {s.risk_score}/100
                          </span>
                        </td>
                        <td className="p-3">
                          <span className={`px-2 py-0.5 rounded text-[10px] font-bold border ${
                            s.result === 'PHISHING' ? 'text-red-400 bg-red-500/10 border-red-500/30' : s.result === 'SUSPICIOUS' ? 'text-amber-400 bg-amber-500/10 border-amber-500/30' : 'text-emerald-400 bg-emerald-500/10 border-emerald-500/30'
                          }`}>
                            {s.result}
                          </span>
                        </td>
                        <td className="p-3 text-slate-400">{s.created_at}</td>
                        <td className="p-3 text-right">
                          <button
                            onClick={() => onViewDetails(s)}
                            className="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-cyan-300 font-semibold text-xs flex items-center gap-1 ml-auto"
                          >
                            <Eye className="w-3.5 h-3.5" /> Inspect
                          </button>
                        </td>
                      </tr>
                    ))
                  )}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};
