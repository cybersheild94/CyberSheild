import React from 'react';
import { ShieldAlert, Mail, MessageSquare, Globe, UserCheck, Copy, Share2, CheckCircle2, XCircle } from 'lucide-react';

export const AwarenessView: React.FC = () => {
  return (
    <div className="max-w-6xl mx-auto py-10 px-4">
      {/* Header */}
      <div className="text-center mb-10">
        <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 text-xs font-semibold mb-3">
          <ShieldAlert className="w-3.5 h-3.5" />
          <span>CYBERSECURITY DEFENSE KNOWLEDGE BASE</span>
        </div>
        <h1 className="text-3xl sm:text-4xl font-extrabold text-white mb-3">
          Anatomy of Phishing Attacks
        </h1>
        <p className="text-slate-400 max-w-2xl mx-auto text-sm sm:text-base">
          Phishing attacks manipulate human psychology rather than software exploits alone. Learn how deceptive vectors operate and how to spot them instantly.
        </p>
      </div>

      {/* 6 Phishing Attack Types */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-12">
        <div className="p-6 rounded-xl bg-[#0c1427]/80 border border-slate-800 hover:border-cyan-500/30 transition-all">
          <div className="w-10 h-10 rounded-lg bg-red-500/10 text-red-400 flex items-center justify-center mb-4">
            <Mail className="w-5 h-5" />
          </div>
          <h3 className="text-base font-bold text-white mb-2">1. Email Phishing</h3>
          <p className="text-xs text-slate-400 leading-relaxed mb-3">
            Deceptive transactional emails mimicking banking institutions, streaming services, or IT departments requesting urgent password updates or invoice verification.
          </p>
          <span className="text-[10px] font-bold text-red-400 uppercase tracking-wider">Mass Volume Vector</span>
        </div>

        <div className="p-6 rounded-xl bg-[#0c1427]/80 border border-slate-800 hover:border-cyan-500/30 transition-all">
          <div className="w-10 h-10 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center mb-4">
            <MessageSquare className="w-5 h-5" />
          </div>
          <h3 className="text-base font-bold text-white mb-2">2. SMS Phishing (Smishing)</h3>
          <p className="text-xs text-slate-400 leading-relaxed mb-3">
            SMS alerts warning of suspended courier packages or banking OTP confirmations with embedded shortlinks (e.g. <code>bit.ly/bank-otp</code>).
          </p>
          <span className="text-[10px] font-bold text-amber-400 uppercase tracking-wider">High Click-Through</span>
        </div>

        <div className="p-6 rounded-xl bg-[#0c1427]/80 border border-slate-800 hover:border-cyan-500/30 transition-all">
          <div className="w-10 h-10 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center mb-4">
            <Globe className="w-5 h-5" />
          </div>
          <h3 className="text-base font-bold text-white mb-2">3. Website Clone Phishing</h3>
          <p className="text-xs text-slate-400 leading-relaxed mb-3">
            Pixel-perfect replicas of legitimate login portals hosted on deceptive domains (e.g. <code>paypa1.com</code> or <code>login-microsoft.top</code>) designed to intercept MFA codes.
          </p>
          <span className="text-[10px] font-bold text-cyan-400 uppercase tracking-wider">Primary Target of Cyber Shield</span>
        </div>

        <div className="p-6 rounded-xl bg-[#0c1427]/80 border border-slate-800 hover:border-cyan-500/30 transition-all">
          <div className="w-10 h-10 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center mb-4">
            <UserCheck className="w-5 h-5" />
          </div>
          <h3 className="text-base font-bold text-white mb-2">4. Spear Phishing</h3>
          <p className="text-xs text-slate-400 leading-relaxed mb-3">
            Targeted attacks against high-profile individuals or specific university staff using customized OSINT intelligence gathered from LinkedIn and academic profiles.
          </p>
          <span className="text-[10px] font-bold text-blue-400 uppercase tracking-wider">High Impact Precision</span>
        </div>

        <div className="p-6 rounded-xl bg-[#0c1427]/80 border border-slate-800 hover:border-cyan-500/30 transition-all">
          <div className="w-10 h-10 rounded-lg bg-purple-500/10 text-purple-400 flex items-center justify-center mb-4">
            <Copy className="w-5 h-5" />
          </div>
          <h3 className="text-base font-bold text-white mb-2">5. Clone Phishing</h3>
          <p className="text-xs text-slate-400 leading-relaxed mb-3">
            Adversaries intercept an existing genuine email thread, clone the message body, and replace the attached file or hyperlinks with malicious weaponized redirects.
          </p>
          <span className="text-[10px] font-bold text-purple-400 uppercase tracking-wider">Evasion Technique</span>
        </div>

        <div className="p-6 rounded-xl bg-[#0c1427]/80 border border-slate-800 hover:border-cyan-500/30 transition-all">
          <div className="w-10 h-10 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center mb-4">
            <Share2 className="w-5 h-5" />
          </div>
          <h3 className="text-base font-bold text-white mb-2">6. Social Media Phishing</h3>
          <p className="text-xs text-slate-400 leading-relaxed mb-3">
            Compromised contacts or bogus customer support bots sending direct messages containing enticing links (e.g. counterfeit crypto giveaways or fake copyright strikes).
          </p>
          <span className="text-[10px] font-bold text-emerald-400 uppercase tracking-wider">Social Impersonation</span>
        </div>
      </div>

      {/* Spotting Fake URLs in 5 Seconds */}
      <div className="p-6 sm:p-8 rounded-2xl bg-[#0c1427]/90 border border-cyan-500/20">
        <h2 className="text-xl font-bold text-white mb-6 flex items-center gap-2">
          <CheckCircle2 className="w-5 h-5 text-cyan-400" />
          The 5-Second Phishing URL Inspection Checklist
        </h2>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs text-slate-300">
          <div className="space-y-4">
            <div className="p-4 rounded-xl bg-slate-900/80 border border-slate-800">
              <div className="text-red-400 font-bold mb-1 flex items-center gap-1.5">
                <XCircle className="w-4 h-4" /> Deceptive Subdomain Padding
              </div>
              <p className="text-slate-400">
                Phishers prefix trusted brand names before their true host:<br />
                <span className="font-mono text-cyan-300">https://paypal.com.account-verify.click</span><br />
                <span className="text-red-400 font-semibold mt-1 inline-block">&rarr; Real host is account-verify.click, NOT PayPal!</span>
              </p>
            </div>

            <div className="p-4 rounded-xl bg-slate-900/80 border border-slate-800">
              <div className="text-red-400 font-bold mb-1 flex items-center gap-1.5">
                <XCircle className="w-4 h-4" /> Typosquatting Substitutions
              </div>
              <p className="text-slate-400">
                Replacing letters with lookalikes, such as numeral &apos;1&apos; for &apos;l&apos; or &apos;rn&apos; for &apos;m&apos; (e.g. <span className="font-mono text-cyan-300">rnicrosoft.com</span> instead of <span className="font-mono text-emerald-300">microsoft.com</span>).
              </p>
            </div>
          </div>

          <div className="space-y-4">
            <div className="p-4 rounded-xl bg-slate-900/80 border border-slate-800">
              <div className="text-red-400 font-bold mb-1 flex items-center gap-1.5">
                <XCircle className="w-4 h-4" /> Raw Numeric IP Address
              </div>
              <p className="text-slate-400">
                Legitimate businesses always use official registered domain names. A URL such as <span className="font-mono text-cyan-300">http://192.168.1.10/login</span> avoids registrar domain KYC checks.
              </p>
            </div>

            <div className="p-4 rounded-xl bg-slate-900/80 border border-slate-800">
              <div className="text-red-400 font-bold mb-1 flex items-center gap-1.5">
                <XCircle className="w-4 h-4" /> High-Abuse Disposable TLDs
              </div>
              <p className="text-slate-400">
                Cheap domain extensions such as <span className="font-mono text-cyan-300">.xyz, .top, .click, .buzz</span> registered anonymously hours prior to launching an automated attack.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};
