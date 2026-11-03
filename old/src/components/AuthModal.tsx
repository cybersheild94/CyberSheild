import React, { useState } from 'react';
import { X, Lock, UserPlus, LogIn, Key } from 'lucide-react';
import { User } from '../types';

interface AuthModalProps {
  initialMode: 'login' | 'signup';
  onClose: () => void;
  onLoginSuccess: (user: User) => void;
}

export const AuthModal: React.FC<AuthModalProps> = ({ initialMode, onClose, onLoginSuccess }) => {
  const [mode, setMode] = useState<'login' | 'signup'>(initialMode);
  const [email, setEmail] = useState('user@cybershield.local');
  const [password, setPassword] = useState('Admin@CyberShield2026');
  const [name, setName] = useState('');
  const [phone, setPhone] = useState('');
  const [error, setError] = useState<string | null>(null);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setError(null);

    if (mode === 'login') {
      if (email.includes('admin')) {
        onLoginSuccess({
          id: 1,
          name: 'Chief Security Admin',
          email: 'admin@cybershield.local',
          phone: '+1-555-0199',
          role: 'admin',
          status: 'active',
          created_at: '2026-09-01 10:00:00'
        });
      } else {
        onLoginSuccess({
          id: 2,
          name: name || 'Alex Morgan',
          email: email || 'user@cybershield.local',
          phone: phone || '+1-555-0144',
          role: 'user',
          status: 'active',
          created_at: '2026-09-10 14:30:00'
        });
      }
      onClose();
    } else {
      // Signup flow
      if (!name || !email || !password) {
        setError('Please fill in all mandatory fields.');
        return;
      }
      onLoginSuccess({
        id: Math.floor(10 + Math.random() * 90),
        name,
        email,
        phone,
        role: 'user',
        status: 'active',
        created_at: new Date().toISOString().replace('T', ' ').substring(0, 19)
      });
      onClose();
    }
  };

  const setDemoAccount = (role: 'user' | 'admin') => {
    if (role === 'admin') {
      setEmail('admin@cybershield.local');
      setPassword('Admin@CyberShield2026');
    } else {
      setEmail('user@cybershield.local');
      setPassword('Admin@CyberShield2026');
    }
  };

  return (
    <div className="fixed inset-0 z-50 bg-black/85 backdrop-blur-md flex items-center justify-center p-3 sm:p-4">
      <div className="w-full max-w-md bg-[#070b14] border border-cyan-500/30 rounded-2xl shadow-2xl overflow-hidden">
        {/* Header */}
        <div className="p-4 border-b border-slate-800 flex items-center justify-between bg-slate-900/60">
          <div className="flex items-center gap-2">
            <div className="w-8 h-8 rounded-lg bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400">
              <Lock className="w-4 h-4" />
            </div>
            <h3 className="text-base font-bold text-white">
              {mode === 'login' ? 'Sign In to Cyber Shield' : 'Create Free Security Account'}
            </h3>
          </div>
          <button
            onClick={onClose}
            className="p-1 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white transition-colors"
          >
            <X className="w-4 h-4" />
          </button>
        </div>

        {/* Body Form */}
        <div className="p-6 text-xs text-slate-300">
          {error && (
            <div className="mb-4 p-2.5 rounded-lg bg-red-500/10 border border-red-500/30 text-red-400">
              {error}
            </div>
          )}

          <form onSubmit={handleSubmit} className="space-y-4">
            {mode === 'signup' && (
              <>
                <div>
                  <label className="block text-slate-400 font-semibold mb-1">Full Name</label>
                  <input
                    type="text"
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                    placeholder="e.g. Alex Morgan"
                    required
                    className="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-cyan-400"
                  />
                </div>
                <div>
                  <label className="block text-slate-400 font-semibold mb-1">Contact Phone</label>
                  <input
                    type="tel"
                    value={phone}
                    onChange={(e) => setPhone(e.target.value)}
                    placeholder="+1 555 0144"
                    className="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-cyan-400"
                  />
                </div>
              </>
            )}

            <div>
              <label className="block text-slate-400 font-semibold mb-1">Email Address</label>
              <input
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="name@example.com"
                required
                className="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-cyan-400"
              />
            </div>

            <div>
              <label className="block text-slate-400 font-semibold mb-1">Password</label>
              <input
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                required
                className="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-cyan-400"
              />
            </div>

            <button
              type="submit"
              className="w-full py-2.5 rounded-xl bg-gradient-to-r from-cyan-400 to-blue-500 hover:from-cyan-300 hover:to-blue-400 text-black font-bold text-xs flex items-center justify-center gap-1.5 shadow-[0_0_15px_rgba(0,243,255,0.3)] transition-all"
            >
              {mode === 'login' ? (
                <>
                  <LogIn className="w-3.5 h-3.5" /> Authenticate Account
                </>
              ) : (
                <>
                  <UserPlus className="w-3.5 h-3.5" /> Complete Registration
                </>
              )}
            </button>
          </form>

          {/* Quick Demo Fill Buttons */}
          <div className="mt-4 pt-4 border-t border-slate-800">
            <span className="text-[11px] text-slate-500 block mb-2 flex items-center gap-1">
              <Key className="w-3 h-3 text-cyan-400" /> Viva Quick-Login Presets:
            </span>
            <div className="flex gap-2">
              <button
                type="button"
                onClick={() => setDemoAccount('user')}
                className="flex-1 py-1 rounded bg-slate-800 hover:bg-slate-700 text-cyan-300 text-[11px] font-semibold transition-colors"
              >
                User (Alex Morgan)
              </button>
              <button
                type="button"
                onClick={() => setDemoAccount('admin')}
                className="flex-1 py-1 rounded bg-slate-800 hover:bg-slate-700 text-red-300 text-[11px] font-semibold transition-colors"
              >
                Admin (SOC Root)
              </button>
            </div>
          </div>

          <div className="mt-4 text-center">
            {mode === 'login' ? (
              <button
                onClick={() => setMode('signup')}
                className="text-cyan-400 hover:underline text-xs"
              >
                Don&apos;t have an account yet? Create one now
              </button>
            ) : (
              <button
                onClick={() => setMode('login')}
                className="text-cyan-400 hover:underline text-xs"
              >
                Already have an account? Sign in here
              </button>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};
