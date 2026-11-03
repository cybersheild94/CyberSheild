import React from 'react';
import { Shield, Crosshair, GraduationCap, Info, Mail, LayoutDashboard, ShieldAlert, Code2, BookOpen, LogIn, LogOut, UserCircle } from 'lucide-react';
import { User } from '../types';

interface NavbarProps {
  activeTab: string;
  setActiveTab: (tab: string) => void;
  currentUser: User | null;
  setCurrentUser: (user: User | null) => void;
  openAuthModal: (mode: 'login' | 'signup') => void;
  openVivaModal: () => void;
  openCodeExplorer: () => void;
}

export const Navbar: React.FC<NavbarProps> = ({
  activeTab,
  setActiveTab,
  currentUser,
  setCurrentUser,
  openAuthModal,
  openVivaModal,
  openCodeExplorer
}) => {
  return (
    <nav className="sticky top-0 z-40 bg-[#070b14]/90 backdrop-blur-md border-b border-cyan-500/20 px-4 py-3">
      <div className="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-3">
        {/* Brand */}
        <div 
          onClick={() => setActiveTab('home')}
          className="flex items-center gap-2.5 cursor-pointer group"
        >
          <div className="w-9 h-9 rounded-lg bg-cyan-500/10 border border-cyan-400 flex items-center justify-center text-cyan-400 group-hover:shadow-[0_0_15px_rgba(0,243,255,0.4)] transition-all">
            <Shield className="w-5 h-5" />
          </div>
          <div className="flex items-baseline">
            <span className="font-extrabold text-cyan-400 tracking-wider">CYBER</span>
            <span className="font-extrabold text-white tracking-wider ml-1">SHIELD</span>
            <span className="ml-2 px-1.5 py-0.5 rounded text-[10px] font-mono bg-cyan-500/10 text-cyan-300 border border-cyan-500/30">
              v1.0 BCA Edition
            </span>
          </div>
        </div>

        {/* Primary Navigation */}
        <div className="hidden md:flex items-center gap-1 text-sm font-medium text-slate-300">
          <button
            onClick={() => setActiveTab('home')}
            className={`px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5 ${
              activeTab === 'home' ? 'text-cyan-400 bg-cyan-500/10 font-semibold' : 'hover:text-cyan-300 hover:bg-slate-800/50'
            }`}
          >
            Home
          </button>
          <button
            onClick={() => setActiveTab('checker')}
            className={`px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5 ${
              activeTab === 'checker' ? 'text-cyan-400 bg-cyan-500/10 font-semibold' : 'hover:text-cyan-300 hover:bg-slate-800/50'
            }`}
          >
            <Crosshair className="w-4 h-4" />
            Scanner
          </button>
          <button
            onClick={() => setActiveTab('awareness')}
            className={`px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5 ${
              activeTab === 'awareness' ? 'text-cyan-400 bg-cyan-500/10 font-semibold' : 'hover:text-cyan-300 hover:bg-slate-800/50'
            }`}
          >
            <GraduationCap className="w-4 h-4" />
            Awareness
          </button>
          <button
            onClick={() => setActiveTab('about')}
            className={`px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5 ${
              activeTab === 'about' ? 'text-cyan-400 bg-cyan-500/10 font-semibold' : 'hover:text-cyan-300 hover:bg-slate-800/50'
            }`}
          >
            <Info className="w-4 h-4" />
            About
          </button>
          <button
            onClick={() => setActiveTab('contact')}
            className={`px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5 ${
              activeTab === 'contact' ? 'text-cyan-400 bg-cyan-500/10 font-semibold' : 'hover:text-cyan-300 hover:bg-slate-800/50'
            }`}
          >
            <Mail className="w-4 h-4" />
            Contact
          </button>
        </div>

        {/* Action Controls & Role Controls */}
        <div className="flex items-center gap-2">
          {/* Viva Exam Prep Button */}
          <button
            onClick={openVivaModal}
            className="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-amber-500/10 text-amber-300 border border-amber-500/30 hover:bg-amber-500/20 transition-all flex items-center gap-1.5"
            title="40+ BCA Viva Questions & Answers + 2-Minute Speech"
          >
            <BookOpen className="w-3.5 h-3.5 text-amber-400" />
            <span className="hidden sm:inline">Viva Prep (40+ Q&A)</span>
          </button>

          {/* Project Source Code & Download Button */}
          <button
            onClick={openCodeExplorer}
            className="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-cyan-500/10 text-cyan-300 border border-cyan-500/30 hover:bg-cyan-500/20 transition-all flex items-center gap-1.5"
            title="Browse all PHP, MySQL, CSS files or download zip for XAMPP"
          >
            <Code2 className="w-3.5 h-3.5 text-cyan-400" />
            <span className="hidden sm:inline">XAMPP Files & ZIP</span>
          </button>

          {/* User Session Dropdown or Login Buttons */}
          {currentUser ? (
            <div className="flex items-center gap-2 pl-2 border-l border-slate-700">
              {currentUser.role === 'admin' ? (
                <button
                  onClick={() => setActiveTab('admin-dashboard')}
                  className="px-2.5 py-1 rounded bg-red-500/20 text-red-300 border border-red-500/40 text-xs font-semibold flex items-center gap-1 hover:bg-red-500/30 transition-colors"
                >
                  <ShieldAlert className="w-3.5 h-3.5" />
                  SOC Admin
                </button>
              ) : (
                <button
                  onClick={() => setActiveTab('user-dashboard')}
                  className="px-2.5 py-1 rounded bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 text-xs font-semibold flex items-center gap-1 hover:bg-cyan-500/30 transition-colors"
                >
                  <LayoutDashboard className="w-3.5 h-3.5" />
                  Dashboard
                </button>
              )}

              <div className="flex items-center gap-1.5 text-xs text-slate-300 bg-slate-800/80 px-2 py-1 rounded border border-slate-700">
                <UserCircle className="w-4 h-4 text-cyan-400" />
                <span className="font-medium max-w-[100px] truncate">{currentUser.name}</span>
                <button
                  onClick={() => setCurrentUser(null)}
                  title="Sign Out"
                  className="ml-1 text-slate-400 hover:text-red-400 transition-colors"
                >
                  <LogOut className="w-3.5 h-3.5" />
                </button>
              </div>
            </div>
          ) : (
            <div className="flex items-center gap-1.5">
              <button
                onClick={() => openAuthModal('login')}
                className="px-3 py-1.5 rounded-lg text-xs font-semibold border border-cyan-500/40 text-cyan-400 hover:bg-cyan-500/10 transition-colors flex items-center gap-1"
              >
                <LogIn className="w-3.5 h-3.5" />
                Login
              </button>
              <button
                onClick={() => openAuthModal('signup')}
                className="px-3 py-1.5 rounded-lg text-xs font-bold bg-cyan-400 text-black hover:bg-cyan-300 transition-all shadow-[0_0_15px_rgba(0,243,255,0.3)]"
              >
                Sign Up
              </button>
            </div>
          )}
        </div>
      </div>
    </nav>
  );
};
