import React, { useState } from 'react';
import { Mail, Send, CheckCircle2 } from 'lucide-react';
import { User, FeedbackMessage } from '../types';

interface ContactViewProps {
  currentUser: User | null;
  onSendMessage: (msg: FeedbackMessage) => void;
}

export const ContactView: React.FC<ContactViewProps> = ({ currentUser, onSendMessage }) => {
  const [name, setName] = useState(currentUser?.name || '');
  const [email, setEmail] = useState(currentUser?.email || '');
  const [subject, setSubject] = useState('');
  const [message, setMessage] = useState('');
  const [submitted, setSubmitted] = useState(false);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!name || !email || !subject || !message) return;

    const newFeedback: FeedbackMessage = {
      id: Math.floor(100 + Math.random() * 900),
      name,
      email,
      subject,
      message,
      status: 'unread',
      created_at: new Date().toISOString().replace('T', ' ').substring(0, 19)
    };

    onSendMessage(newFeedback);
    setSubmitted(true);
    setSubject('');
    setMessage('');
    setTimeout(() => setSubmitted(false), 5000);
  };

  return (
    <div className="max-w-3xl mx-auto py-10 px-4">
      <div className="text-center mb-8">
        <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 text-xs font-semibold mb-3">
          <Mail className="w-3.5 h-3.5" />
          <span>SECURITY INQUIRIES & VIVA SUPPORT</span>
        </div>
        <h1 className="text-3xl font-extrabold text-white mb-2">
          Contact Security Team
        </h1>
        <p className="text-slate-400 text-sm">
          Submit false positive reports, project documentation queries, or viva evaluation questions.
        </p>
      </div>

      <div className="p-6 sm:p-8 rounded-2xl bg-[#0c1427]/80 border border-slate-800">
        {submitted ? (
          <div className="p-6 text-center">
            <CheckCircle2 className="w-12 h-12 text-emerald-400 mx-auto mb-3" />
            <h3 className="text-lg font-bold text-white mb-1">Inquiry Dispatched to SOC Inbox</h3>
            <p className="text-xs text-slate-400 max-w-md mx-auto">
              Your message has been saved to the MySQL `feedback` table and is accessible in the administrator control panel.
            </p>
          </div>
        ) : (
          <form onSubmit={handleSubmit} className="space-y-4 text-xs">
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label className="block text-slate-400 font-semibold mb-1">Your Full Name</label>
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
            </div>

            <div>
              <label className="block text-slate-400 font-semibold mb-1">Subject</label>
              <input
                type="text"
                value={subject}
                onChange={(e) => setSubject(e.target.value)}
                placeholder="e.g. False Positive Inquiry / BCA Final Exam Demonstration"
                required
                className="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-cyan-400"
              />
            </div>

            <div>
              <label className="block text-slate-400 font-semibold mb-1">Message</label>
              <textarea
                rows={4}
                value={message}
                onChange={(e) => setMessage(e.target.value)}
                placeholder="Detail your inquiry or scan ID..."
                required
                className="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-cyan-400"
              />
            </div>

            <div className="flex justify-between items-center pt-2">
              <span className="text-[11px] text-slate-500">
                Submissions are stored in MySQL table: <code className="text-cyan-400">feedback</code>
              </span>
              <button
                type="submit"
                className="px-5 py-2 rounded-lg bg-gradient-to-r from-cyan-400 to-blue-500 hover:from-cyan-300 hover:to-blue-400 text-black font-bold flex items-center gap-1.5 shadow-[0_0_15px_rgba(0,243,255,0.3)] transition-all"
              >
                <Send className="w-3.5 h-3.5" /> Send Message
              </button>
            </div>
          </form>
        )}
      </div>
    </div>
  );
};
