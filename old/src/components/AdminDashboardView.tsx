import React, { useState } from 'react';
import { ShieldAlert, Users, Radar, Inbox, Sliders, CheckCircle2, UserCheck, UserX, Trash2, Eye, Mail, Save, RefreshCw } from 'lucide-react';
import { User, ScanResult, FeedbackMessage, SystemSettings } from '../types';

interface AdminDashboardViewProps {
  currentAdmin: User;
  allUsers: User[];
  allScans: ScanResult[];
  allFeedback: FeedbackMessage[];
  settings: SystemSettings;
  onUpdateUserStatus: (userId: number, status: 'active' | 'suspended') => void;
  onDeleteUser: (userId: number) => void;
  onDeleteScan: (scanId: number) => void;
  onViewScanDetails: (scan: ScanResult) => void;
  onUpdateFeedbackStatus: (feedbackId: number, status: 'read' | 'unread') => void;
  onDeleteFeedback: (feedbackId: number) => void;
  onSaveSettings: (newSettings: SystemSettings) => void;
}

export const AdminDashboardView: React.FC<AdminDashboardViewProps> = ({
  currentAdmin,
  allUsers,
  allScans,
  allFeedback,
  settings,
  onUpdateUserStatus,
  onDeleteUser,
  onDeleteScan,
  onViewScanDetails,
  onUpdateFeedbackStatus,
  onDeleteFeedback,
  onSaveSettings
}) => {
  const [adminSection, setAdminSection] = useState<'overview' | 'users' | 'scans' | 'feedback' | 'settings'>('overview');
  const [userSearch, setUserSearch] = useState('');
  const [scanSearch, setScanSearch] = useState('');
  const [editableSettings, setEditableSettings] = useState<SystemSettings>(settings);
  const [settingsSaved, setSettingsSaved] = useState(false);

  // Metrics
  const totalUsers = allUsers.length;
  const totalScans = allScans.length;
  const safeScans = allScans.filter(s => s.result === 'SAFE').length;
  const suspiciousScans = allScans.filter(s => s.result === 'SUSPICIOUS').length;
  const phishingScans = allScans.filter(s => s.result === 'PHISHING').length;
  const unreadFeedback = allFeedback.filter(f => f.status === 'unread').length;

  const handleSaveSettings = (e: React.FormEvent) => {
    e.preventDefault();
    onSaveSettings(editableSettings);
    setSettingsSaved(true);
    setTimeout(() => setSettingsSaved(false), 3000);
  };

  const filteredUsers = allUsers.filter(u =>
    u.name.toLowerCase().includes(userSearch.toLowerCase()) ||
    u.email.toLowerCase().includes(userSearch.toLowerCase())
  );

  const filteredScans = allScans.filter(s =>
    s.url.toLowerCase().includes(scanSearch.toLowerCase()) ||
    s.domain.toLowerCase().includes(scanSearch.toLowerCase()) ||
    (s.user_name && s.user_name.toLowerCase().includes(scanSearch.toLowerCase()))
  );

  return (
    <div className="max-w-7xl mx-auto py-8 px-4">
      {/* Top Banner */}
      <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 pb-6 border-b border-slate-800">
        <div>
          <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-[11px] font-mono bg-red-500/10 text-red-400 border border-red-500/30 mb-2">
            <ShieldAlert className="w-3.5 h-3.5" />
            SECURITY OPERATIONS CENTER (SOC)
          </div>
          <h1 className="text-2xl sm:text-3xl font-bold text-white flex items-center gap-2">
            SOC Root Console
          </h1>
          <p className="text-sm text-slate-400 mt-1">
            Logged in as <strong className="text-white">{currentAdmin.name}</strong> ({currentAdmin.email}) with master administrative access.
          </p>
        </div>

        <div className="flex items-center gap-2">
          <span className="px-3 py-1 rounded-full text-xs font-mono bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 flex items-center gap-1">
            <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />
            Heuristic Pipeline Online
          </span>
        </div>
      </div>

      {/* Nav Tabs */}
      <div className="flex flex-wrap items-center gap-2 mb-6 border-b border-slate-800 pb-2">
        <button
          onClick={() => setAdminSection('overview')}
          className={`px-3 py-2 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 ${
            adminSection === 'overview' ? 'bg-red-500/20 text-red-300 border border-red-500/40' : 'text-slate-400 hover:text-white'
          }`}
        >
          SOC Overview
        </button>
        <button
          onClick={() => setAdminSection('users')}
          className={`px-3 py-2 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 ${
            adminSection === 'users' ? 'bg-red-500/20 text-red-300 border border-red-500/40' : 'text-slate-400 hover:text-white'
          }`}
        >
          <Users className="w-3.5 h-3.5" /> Manage Users ({totalUsers})
        </button>
        <button
          onClick={() => setAdminSection('scans')}
          className={`px-3 py-2 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 ${
            adminSection === 'scans' ? 'bg-red-500/20 text-red-300 border border-red-500/40' : 'text-slate-400 hover:text-white'
          }`}
        >
          <Radar className="w-3.5 h-3.5" /> URL Scans Telemetry ({totalScans})
        </button>
        <button
          onClick={() => setAdminSection('feedback')}
          className={`px-3 py-2 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 ${
            adminSection === 'feedback' ? 'bg-red-500/20 text-red-300 border border-red-500/40' : 'text-slate-400 hover:text-white'
          }`}
        >
          <Inbox className="w-3.5 h-3.5" /> Feedback Inbox
          {unreadFeedback > 0 && (
            <span className="px-1.5 py-0.2 rounded-full bg-red-500 text-white text-[10px] font-bold">
              {unreadFeedback}
            </span>
          )}
        </button>
        <button
          onClick={() => setAdminSection('settings')}
          className={`px-3 py-2 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 ${
            adminSection === 'settings' ? 'bg-red-500/20 text-red-300 border border-red-500/40' : 'text-slate-400 hover:text-white'
          }`}
        >
          <Sliders className="w-3.5 h-3.5" /> System Settings
        </button>
      </div>

      {/* Overview Section */}
      {adminSection === 'overview' && (
        <div className="space-y-6">
          {/* 6 Metric Cards */}
          <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <div className="p-4 rounded-xl bg-[#0c1427]/80 border border-slate-800 text-center">
              <span className="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Total Users</span>
              <div className="text-2xl font-bold font-mono text-blue-400 mt-1">{totalUsers}</div>
            </div>
            <div className="p-4 rounded-xl bg-[#0c1427]/80 border border-slate-800 text-center">
              <span className="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Total Scans</span>
              <div className="text-2xl font-bold font-mono text-cyan-400 mt-1">{totalScans}</div>
            </div>
            <div className="p-4 rounded-xl bg-[#0c1427]/80 border border-slate-800 text-center">
              <span className="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Safe URLs</span>
              <div className="text-2xl font-bold font-mono text-emerald-400 mt-1">{safeScans}</div>
            </div>
            <div className="p-4 rounded-xl bg-[#0c1427]/80 border border-slate-800 text-center">
              <span className="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Suspicious</span>
              <div className="text-2xl font-bold font-mono text-amber-400 mt-1">{suspiciousScans}</div>
            </div>
            <div className="p-4 rounded-xl bg-[#0c1427]/80 border border-slate-800 text-center">
              <span className="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Phishing</span>
              <div className="text-2xl font-bold font-mono text-red-400 mt-1">{phishingScans}</div>
            </div>
            <div className="p-4 rounded-xl bg-[#0c1427]/80 border border-slate-800 text-center">
              <span className="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Inquiries</span>
              <div className="text-2xl font-bold font-mono text-purple-400 mt-1">{allFeedback.length}</div>
            </div>
          </div>

          {/* Real-time Threat Chart Bars */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div className="p-6 rounded-xl bg-[#0c1427]/80 border border-slate-800">
              <h3 className="text-sm font-bold text-white mb-4">Global Classification Distribution</h3>
              <div className="space-y-4">
                <div>
                  <div className="flex justify-between text-xs font-semibold mb-1">
                    <span className="text-emerald-400">Safe Classifications ({safeScans})</span>
                    <span className="text-slate-400 font-mono">
                      {totalScans > 0 ? Math.round((safeScans / totalScans) * 100) : 0}%
                    </span>
                  </div>
                  <div className="w-full h-3 bg-slate-800 rounded-full overflow-hidden">
                    <div
                      className="h-full bg-emerald-500 rounded-full"
                      style={{ width: `${totalScans > 0 ? (safeScans / totalScans) * 100 : 0}%` }}
                    />
                  </div>
                </div>

                <div>
                  <div className="flex justify-between text-xs font-semibold mb-1">
                    <span className="text-amber-400">Suspicious Domains ({suspiciousScans})</span>
                    <span className="text-slate-400 font-mono">
                      {totalScans > 0 ? Math.round((suspiciousScans / totalScans) * 100) : 0}%
                    </span>
                  </div>
                  <div className="w-full h-3 bg-slate-800 rounded-full overflow-hidden">
                    <div
                      className="h-full bg-amber-500 rounded-full"
                      style={{ width: `${totalScans > 0 ? (suspiciousScans / totalScans) * 100 : 0}%` }}
                    />
                  </div>
                </div>

                <div>
                  <div className="flex justify-between text-xs font-semibold mb-1">
                    <span className="text-red-400">Confirmed Phishing Threats ({phishingScans})</span>
                    <span className="text-slate-400 font-mono">
                      {totalScans > 0 ? Math.round((phishingScans / totalScans) * 100) : 0}%
                    </span>
                  </div>
                  <div className="w-full h-3 bg-slate-800 rounded-full overflow-hidden">
                    <div
                      className="h-full bg-red-500 rounded-full"
                      style={{ width: `${totalScans > 0 ? (phishingScans / totalScans) * 100 : 0}%` }}
                    />
                  </div>
                </div>
              </div>
            </div>

            <div className="p-6 rounded-xl bg-[#0c1427]/80 border border-slate-800 flex flex-col justify-between">
              <div>
                <h3 className="text-sm font-bold text-white mb-2">SOC Telemetry Policy</h3>
                <p className="text-xs text-slate-400 mb-4">
                  Cyber Shield maintains full relational integrity in MySQL. Scans remain permanently tied to target user profiles, ensuring verifiable chain-of-custody for academic security audits.
                </p>
                <div className="space-y-2 text-xs text-slate-300">
                  <div className="flex justify-between py-1 border-b border-slate-800">
                    <span className="text-slate-400">Database Engine:</span>
                    <span className="font-mono text-cyan-300">MySQL InnoDB (utf8mb4)</span>
                  </div>
                  <div className="flex justify-between py-1 border-b border-slate-800">
                    <span className="text-slate-400">Heuristic Engine:</span>
                    <span className="text-white">{settings.detection_engine_version}</span>
                  </div>
                  <div className="flex justify-between py-1">
                    <span className="text-slate-400">Max Scans / Hour per IP:</span>
                    <span className="font-mono text-amber-400">{settings.max_scans_per_hour}</span>
                  </div>
                </div>
              </div>
              <div className="mt-4 pt-3 border-t border-slate-800 flex gap-2">
                <button
                  onClick={() => setAdminSection('users')}
                  className="flex-1 py-1.5 rounded bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200"
                >
                  Manage Users
                </button>
                <button
                  onClick={() => setAdminSection('scans')}
                  className="flex-1 py-1.5 rounded bg-red-500/20 border border-red-500/40 text-red-300 text-xs font-semibold hover:bg-red-500/30"
                >
                  Inspect Scans
                </button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Users Section */}
      {adminSection === 'users' && (
        <div className="space-y-4">
          <div className="p-4 rounded-xl bg-[#0c1427]/80 border border-slate-800 flex items-center justify-between gap-3">
            <input
              type="text"
              value={userSearch}
              onChange={(e) => setUserSearch(e.target.value)}
              placeholder="Search user by name or email..."
              className="bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-white max-w-sm w-full focus:outline-none focus:border-red-400"
            />
            <span className="text-xs text-slate-400">
              Showing {filteredUsers.length} of {allUsers.length} accounts
            </span>
          </div>

          <div className="rounded-xl bg-[#0c1427]/80 border border-slate-800 overflow-hidden">
            <div className="overflow-x-auto">
              <table className="w-full text-left text-xs text-slate-300">
                <thead className="bg-slate-900/60 text-[11px] uppercase tracking-wider text-slate-400 font-mono border-b border-slate-800">
                  <tr>
                    <th className="p-3">ID</th>
                    <th className="p-3">Full Name</th>
                    <th className="p-3">Email Address</th>
                    <th className="p-3">Role</th>
                    <th className="p-3">Status</th>
                    <th className="p-3">Registered On</th>
                    <th className="p-3 text-right">Actions</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-800">
                  {filteredUsers.map(u => (
                    <tr key={u.id} className="hover:bg-slate-800/30 transition-colors">
                      <td className="p-3 font-mono text-slate-500">#{u.id}</td>
                      <td className="p-3 font-semibold text-white">{u.name}</td>
                      <td className="p-3 font-mono text-cyan-300">{u.email}</td>
                      <td className="p-3">
                        <span className={`px-2 py-0.5 rounded text-[10px] font-bold ${
                          u.role === 'admin' ? 'bg-red-500/20 text-red-300 border border-red-500/30' : 'bg-blue-500/20 text-blue-300 border border-blue-500/30'
                        }`}>
                          {u.role.toUpperCase()}
                        </span>
                      </td>
                      <td className="p-3">
                        <span className={`px-2 py-0.5 rounded text-[10px] font-bold ${
                          u.status === 'active' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-red-500/10 text-red-400 border border-red-500/30'
                        }`}>
                          {u.status.toUpperCase()}
                        </span>
                      </td>
                      <td className="p-3 text-slate-400">{u.created_at}</td>
                      <td className="p-3 text-right">
                        {u.id === currentAdmin.id ? (
                          <span className="text-[11px] text-slate-500 italic">Current Session</span>
                        ) : (
                          <div className="flex items-center justify-end gap-1.5">
                            {u.status === 'active' ? (
                              <button
                                onClick={() => onUpdateUserStatus(u.id, 'suspended')}
                                title="Suspend Account"
                                className="p-1 rounded hover:bg-amber-500/20 text-amber-400 transition-colors"
                              >
                                <UserX className="w-4 h-4" />
                              </button>
                            ) : (
                              <button
                                onClick={() => onUpdateUserStatus(u.id, 'active')}
                                title="Activate Account"
                                className="p-1 rounded hover:bg-emerald-500/20 text-emerald-400 transition-colors"
                              >
                                <UserCheck className="w-4 h-4" />
                              </button>
                            )}
                            <button
                              onClick={() => {
                                if (window.confirm(`Permanently delete account for ${u.name}?`)) {
                                  onDeleteUser(u.id);
                                }
                              }}
                              title="Delete Account"
                              className="p-1 rounded hover:bg-red-500/20 text-red-400 transition-colors"
                            >
                              <Trash2 className="w-4 h-4" />
                            </button>
                          </div>
                        )}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      )}

      {/* Scans Telemetry Section */}
      {adminSection === 'scans' && (
        <div className="space-y-4">
          <div className="p-4 rounded-xl bg-[#0c1427]/80 border border-slate-800 flex items-center justify-between gap-3">
            <input
              type="text"
              value={scanSearch}
              onChange={(e) => setScanSearch(e.target.value)}
              placeholder="Search by URL, domain, or user name..."
              className="bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-white max-w-sm w-full focus:outline-none focus:border-red-400"
            />
            <span className="text-xs text-slate-400">
              Total Scans in MySQL: {allScans.length}
            </span>
          </div>

          <div className="rounded-xl bg-[#0c1427]/80 border border-slate-800 overflow-hidden">
            <div className="overflow-x-auto">
              <table className="w-full text-left text-xs text-slate-300">
                <thead className="bg-slate-900/60 text-[11px] uppercase tracking-wider text-slate-400 font-mono border-b border-slate-800">
                  <tr>
                    <th className="p-3">Scan ID</th>
                    <th className="p-3">User</th>
                    <th className="p-3">URL</th>
                    <th className="p-3">Host Domain</th>
                    <th className="p-3">Score</th>
                    <th className="p-3">Verdict</th>
                    <th className="p-3">Client IP</th>
                    <th className="p-3">Timestamp</th>
                    <th className="p-3 text-right">Actions</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-800">
                  {filteredScans.map(s => (
                    <tr key={s.id} className="hover:bg-slate-800/30 transition-colors">
                      <td className="p-3 font-mono text-slate-500">#{s.id}</td>
                      <td className="p-3 text-white font-medium">{s.user_name || 'Public Guest'}</td>
                      <td className="p-3 font-mono text-cyan-300 max-w-[200px] truncate" title={s.url}>{s.url}</td>
                      <td className="p-3 font-mono text-slate-300">{s.domain}</td>
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
                      <td className="p-3 font-mono text-slate-400">{s.ip_address}</td>
                      <td className="p-3 text-slate-400">{s.created_at}</td>
                      <td className="p-3 text-right">
                        <div className="flex items-center justify-end gap-1.5">
                          <button
                            onClick={() => onViewScanDetails(s)}
                            className="p-1 rounded hover:bg-cyan-500/20 text-cyan-400 transition-colors"
                            title="Inspect"
                          >
                            <Eye className="w-4 h-4" />
                          </button>
                          <button
                            onClick={() => {
                              if (window.confirm('Delete this scan record permanently?')) {
                                onDeleteScan(s.id);
                              }
                            }}
                            className="p-1 rounded hover:bg-red-500/20 text-red-400 transition-colors"
                            title="Purge Record"
                          >
                            <Trash2 className="w-4 h-4" />
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      )}

      {/* Feedback Section */}
      {adminSection === 'feedback' && (
        <div className="space-y-4">
          <div className="rounded-xl bg-[#0c1427]/80 border border-slate-800 overflow-hidden">
            <div className="overflow-x-auto">
              <table className="w-full text-left text-xs text-slate-300">
                <thead className="bg-slate-900/60 text-[11px] uppercase tracking-wider text-slate-400 font-mono border-b border-slate-800">
                  <tr>
                    <th className="p-3">ID</th>
                    <th className="p-3">Sender Name</th>
                    <th className="p-3">Email</th>
                    <th className="p-3">Subject</th>
                    <th className="p-3">Message</th>
                    <th className="p-3">Status</th>
                    <th className="p-3">Received</th>
                    <th className="p-3 text-right">Actions</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-800">
                  {allFeedback.map(f => (
                    <tr key={f.id} className="hover:bg-slate-800/30 transition-colors">
                      <td className="p-3 font-mono text-slate-500">#{f.id}</td>
                      <td className="p-3 font-semibold text-white">{f.name}</td>
                      <td className="p-3 font-mono text-cyan-300">{f.email}</td>
                      <td className="p-3 text-white font-medium">{f.subject}</td>
                      <td className="p-3 text-slate-400 max-w-[260px] truncate" title={f.message}>{f.message}</td>
                      <td className="p-3">
                        <span className={`px-2 py-0.5 rounded text-[10px] font-bold ${
                          f.status === 'unread' ? 'bg-red-500/20 text-red-300 border border-red-500/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30'
                        }`}>
                          {f.status.toUpperCase()}
                        </span>
                      </td>
                      <td className="p-3 text-slate-400">{f.created_at}</td>
                      <td className="p-3 text-right">
                        <div className="flex items-center justify-end gap-1.5">
                          <button
                            onClick={() => onUpdateFeedbackStatus(f.id, f.status === 'unread' ? 'read' : 'unread')}
                            className="p-1 rounded hover:bg-slate-700 text-slate-300 transition-colors"
                            title="Toggle Read/Unread"
                          >
                            <Mail className="w-4 h-4" />
                          </button>
                          <button
                            onClick={() => onDeleteFeedback(f.id)}
                            className="p-1 rounded hover:bg-red-500/20 text-red-400 transition-colors"
                            title="Delete Feedback"
                          >
                            <Trash2 className="w-4 h-4" />
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      )}

      {/* Settings Section */}
      {adminSection === 'settings' && (
        <div className="max-w-2xl bg-[#0c1427]/80 p-6 rounded-xl border border-slate-800">
          <h3 className="text-base font-bold text-white mb-4 flex items-center gap-2">
            <Sliders className="w-4 h-4 text-cyan-400" /> Platform Configuration (MySQL `settings`)
          </h3>

          {settingsSaved && (
            <div className="mb-4 p-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs flex items-center gap-2">
              <CheckCircle2 className="w-4 h-4" /> Settings updated successfully in database!
            </div>
          )}

          <form onSubmit={handleSaveSettings} className="space-y-4 text-xs">
            <div>
              <label className="block text-slate-400 font-semibold mb-1">Platform Brand Title</label>
              <input
                type="text"
                value={editableSettings.site_title}
                onChange={(e) => setEditableSettings({ ...editableSettings, site_title: e.target.value })}
                className="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-red-400"
              />
            </div>

            <div>
              <label className="block text-slate-400 font-semibold mb-1">Platform Slogan / Tagline</label>
              <input
                type="text"
                value={editableSettings.site_tagline}
                onChange={(e) => setEditableSettings({ ...editableSettings, site_tagline: e.target.value })}
                className="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-red-400"
              />
            </div>

            <div className="grid grid-cols-2 gap-3">
              <div>
                <label className="block text-slate-400 font-semibold mb-1">Detection Engine Tag</label>
                <input
                  type="text"
                  value={editableSettings.detection_engine_version}
                  onChange={(e) => setEditableSettings({ ...editableSettings, detection_engine_version: e.target.value })}
                  className="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white font-mono focus:outline-none focus:border-red-400"
                />
              </div>
              <div>
                <label className="block text-slate-400 font-semibold mb-1">Max Scans / Hour (Per IP)</label>
                <input
                  type="number"
                  value={editableSettings.max_scans_per_hour}
                  onChange={(e) => setEditableSettings({ ...editableSettings, max_scans_per_hour: parseInt(e.target.value) || 50 })}
                  className="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white font-mono focus:outline-none focus:border-red-400"
                />
              </div>
            </div>

            <div className="pt-2 flex justify-end">
              <button
                type="submit"
                className="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-500 text-white font-bold text-xs flex items-center gap-1.5 shadow-[0_0_15px_rgba(239,68,68,0.3)] transition-all"
              >
                <Save className="w-3.5 h-3.5" /> Save System Settings
              </button>
            </div>
          </form>
        </div>
      )}
    </div>
  );
};
