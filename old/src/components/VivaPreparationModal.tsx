import React, { useState } from 'react';
import { X, BookOpen, Search, Sparkles, Mic, Copy, Check } from 'lucide-react';
import { VIVA_QUESTIONS, VIVA_SCRIPT_2_MINUTES } from '../data/vivaData';

interface VivaPreparationModalProps {
  onClose: () => void;
}

export const VivaPreparationModal: React.FC<VivaPreparationModalProps> = ({ onClose }) => {
  const [activeTab, setActiveTab] = useState<'questions' | 'script'>('questions');
  const [search, setSearch] = useState('');
  const [selectedCategory, setSelectedCategory] = useState<string>('ALL');
  const [copiedScript, setCopiedScript] = useState(false);

  const categories = ['ALL', 'PHP & Backend', 'MySQL & Database', 'Security & Heuristics', 'Frontend & JS', 'Testing & Defense'];

  const filteredQuestions = VIVA_QUESTIONS.filter(q => {
    const matchesSearch = q.question.toLowerCase().includes(search.toLowerCase()) || q.answer.toLowerCase().includes(search.toLowerCase());
    const matchesCategory = selectedCategory === 'ALL' || q.category === selectedCategory;
    return matchesSearch && matchesCategory;
  });

  const handleCopyScript = () => {
    navigator.clipboard.writeText(VIVA_SCRIPT_2_MINUTES.trim());
    setCopiedScript(true);
    setTimeout(() => setCopiedScript(false), 2000);
  };

  return (
    <div className="fixed inset-0 z-50 bg-black/85 backdrop-blur-md flex items-center justify-center p-2 sm:p-4">
      <div className="w-full max-w-4xl h-[88vh] bg-[#070b14] border border-amber-500/30 rounded-2xl shadow-2xl flex flex-col overflow-hidden">
        {/* Header */}
        <div className="p-4 border-b border-slate-800 flex items-center justify-between bg-slate-900/60">
          <div className="flex items-center gap-2">
            <div className="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400">
              <BookOpen className="w-4 h-4" />
            </div>
            <div>
              <h2 className="text-sm sm:text-base font-bold text-white flex items-center gap-2">
                BCA Viva Voce Examination Preparation Simulator
              </h2>
              <p className="text-[11px] text-slate-400">
                40+ Questions & Answers + 2-Minute Oral Defense Script
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

        {/* Tab Switcher */}
        <div className="px-4 pt-3 flex items-center gap-2 border-b border-slate-800 bg-slate-900/30">
          <button
            onClick={() => setActiveTab('questions')}
            className={`px-3 py-2 text-xs font-bold border-b-2 transition-colors flex items-center gap-1.5 ${
              activeTab === 'questions' ? 'border-amber-400 text-amber-400' : 'border-transparent text-slate-400 hover:text-white'
            }`}
          >
            <Sparkles className="w-3.5 h-3.5" /> 40+ Viva Questions ({VIVA_QUESTIONS.length})
          </button>
          <button
            onClick={() => setActiveTab('script')}
            className={`px-3 py-2 text-xs font-bold border-b-2 transition-colors flex items-center gap-1.5 ${
              activeTab === 'script' ? 'border-amber-400 text-amber-400' : 'border-transparent text-slate-400 hover:text-white'
            }`}
          >
            <Mic className="w-3.5 h-3.5" /> 2-Minute Oral Presentation Script
          </button>
        </div>

        {/* Body Content */}
        <div className="flex-1 overflow-y-auto p-4 sm:p-6 text-xs text-slate-300 space-y-4">
          {activeTab === 'questions' && (
            <>
              {/* Search & Category Filter */}
              <div className="flex flex-wrap gap-2 items-center justify-between">
                <div className="relative flex-1 min-w-[200px]">
                  <Search className="w-3.5 h-3.5 text-slate-500 absolute left-3 top-1/2 -translate-y-1/2" />
                  <input
                    type="text"
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                    placeholder="Search viva questions or keywords (e.g. PDO, Bcrypt, XSS)..."
                    className="w-full bg-slate-900 border border-slate-700 rounded-lg pl-8 pr-3 py-1.5 text-xs text-white focus:outline-none focus:border-amber-400"
                  />
                </div>

                <div className="flex flex-wrap gap-1">
                  {categories.map(cat => (
                    <button
                      key={cat}
                      onClick={() => setSelectedCategory(cat)}
                      className={`px-2 py-1 rounded text-[10px] font-semibold transition-colors ${
                        selectedCategory === cat
                          ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40'
                          : 'bg-slate-900 text-slate-400 hover:text-white'
                      }`}
                    >
                      {cat}
                    </button>
                  ))}
                </div>
              </div>

              {/* Questions List */}
              <div className="space-y-3 mt-4">
                {filteredQuestions.map((q) => (
                  <div key={q.id} className="p-4 rounded-xl bg-slate-900/60 border border-slate-800">
                    <div className="flex items-start justify-between gap-2 mb-2">
                      <span className="font-bold text-amber-400 text-xs sm:text-sm">
                        Q{q.id}. {q.question}
                      </span>
                      <span className="px-2 py-0.5 rounded text-[10px] font-mono bg-slate-800 text-slate-400 flex-shrink-0">
                        {q.category}
                      </span>
                    </div>

                    <p className="text-slate-300 leading-relaxed text-xs mb-2">
                      {q.answer}
                    </p>

                    {q.quickTip && (
                      <div className="p-2 rounded bg-amber-500/10 border border-amber-500/20 text-[11px] text-amber-300 flex items-center gap-1.5">
                        <Sparkles className="w-3.5 h-3.5 flex-shrink-0 text-amber-400" />
                        <span><strong>Viva Examiner Tip:</strong> {q.quickTip}</span>
                      </div>
                    )}
                  </div>
                ))}
              </div>
            </>
          )}

          {activeTab === 'script' && (
            <div className="p-6 rounded-xl bg-slate-900/60 border border-slate-800 space-y-4">
              <div className="flex items-center justify-between pb-3 border-b border-slate-800">
                <div>
                  <h3 className="text-sm font-bold text-white flex items-center gap-2">
                    <Mic className="w-4 h-4 text-amber-400" />
                    How to Explain This Project in 2 Minutes During Viva
                  </h3>
                  <p className="text-[11px] text-slate-400 mt-0.5">
                    Memorize these structured key points to deliver a confident, high-scoring viva opening.
                  </p>
                </div>
                <button
                  onClick={handleCopyScript}
                  className="px-3 py-1.5 rounded-lg bg-amber-500/20 text-amber-300 border border-amber-500/40 text-xs font-semibold flex items-center gap-1.5 hover:bg-amber-500/30 transition-colors"
                >
                  {copiedScript ? <Check className="w-3.5 h-3.5" /> : <Copy className="w-3.5 h-3.5" />}
                  <span>{copiedScript ? 'Copied!' : 'Copy Script'}</span>
                </button>
              </div>

              <div className="font-sans text-xs sm:text-sm text-slate-300 leading-relaxed whitespace-pre-line p-4 rounded-lg bg-black/40 border border-slate-800">
                {VIVA_SCRIPT_2_MINUTES.trim()}
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};
