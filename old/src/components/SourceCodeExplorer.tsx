import React, { useState } from 'react';
import { X, Download, Copy, Check, FileCode, Database, Folder, Code } from 'lucide-react';
import JSZip from 'jszip';
import { PROJECT_FILES, ProjectFile } from '../data/filesData';

interface SourceCodeExplorerProps {
  onClose: () => void;
}

export const SourceCodeExplorer: React.FC<SourceCodeExplorerProps> = ({ onClose }) => {
  const [selectedFile, setSelectedFile] = useState<ProjectFile>(PROJECT_FILES[0]);
  const [copied, setCopied] = useState(false);
  const [fileContent, setFileContent] = useState<string>('Loading file content...');
  const [isZipping, setIsZipping] = useState(false);

  // Fetch or load file content dynamically or use embedded
  React.useEffect(() => {
    let isMounted = true;
    const loadContent = async () => {
      try {
        // Fetch from local cyber-shield directory in development
        const res = await fetch(`/cyber-shield/${selectedFile.path}`);
        if (res.ok) {
          const text = await res.text();
          if (isMounted) setFileContent(text);
        } else {
          // Fallback message
          if (isMounted) setFileContent(`/* File: ${selectedFile.path}\n * Category: ${selectedFile.category}\n * Description: ${selectedFile.description}\n */\n\n// Full code is generated in the workspace filesystem at /cyber-shield/${selectedFile.path}\n// Use the "Download Complete cyber-shield.zip" button above to get the full runnable package!`);
        }
      } catch {
        if (isMounted) setFileContent(`/* File: ${selectedFile.path}\n * Available at /cyber-shield/${selectedFile.path} */`);
      }
    };
    loadContent();
    return () => { isMounted = false; };
  }, [selectedFile]);

  const handleCopy = () => {
    navigator.clipboard.writeText(fileContent);
    setCopied(true);
    setTimeout(() => setCopied(false), 2000);
  };

  const handleDownloadZip = async () => {
    setIsZipping(true);
    try {
      const zip = new JSZip();
      const folder = zip.folder('cyber-shield');

      // Fetch all files
      for (const f of PROJECT_FILES) {
        try {
          const res = await fetch(`/cyber-shield/${f.path}`);
          if (res.ok) {
            const content = await res.text();
            folder?.file(f.path, content);
          }
        } catch (e) {
          console.error(`Failed to fetch file for zip: ${f.path}`, e);
        }
      }

      const blob = await zip.generateAsync({ type: 'blob' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = 'cyber-shield-bca-final-project.zip';
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      URL.revokeObjectURL(url);
    } catch (err) {
      console.error('Error generating zip:', err);
      alert('Could not package ZIP in browser. Files are already available in /cyber-shield/ folder on the server!');
    } finally {
      setIsZipping(false);
    }
  };

  return (
    <div className="fixed inset-0 z-50 bg-black/85 backdrop-blur-md flex items-center justify-center p-2 sm:p-4">
      <div className="w-full max-w-6xl h-[90vh] bg-[#070b14] border border-cyan-500/30 rounded-2xl shadow-2xl flex flex-col overflow-hidden">
        {/* Top Header */}
        <div className="p-4 border-b border-slate-800 flex items-center justify-between bg-slate-900/60">
          <div className="flex items-center gap-2">
            <div className="w-8 h-8 rounded-lg bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400">
              <Code className="w-4 h-4" />
            </div>
            <div>
              <h2 className="text-sm sm:text-base font-bold text-white flex items-center gap-2">
                CYBER SHIELD: PHP & MySQL Source Repository
              </h2>
              <p className="text-[11px] text-slate-400">
                Ready for XAMPP, WAMP, or Apache (`htdocs/cyber-shield/`)
              </p>
            </div>
          </div>

          <div className="flex items-center gap-2">
            <button
              onClick={handleDownloadZip}
              disabled={isZipping}
              className="px-3 py-1.5 rounded-lg bg-gradient-to-r from-cyan-400 to-blue-500 text-black font-bold text-xs flex items-center gap-1.5 shadow-[0_0_15px_rgba(0,243,255,0.3)] hover:opacity-95 transition-all disabled:opacity-50"
            >
              <Download className="w-3.5 h-3.5" />
              <span>{isZipping ? 'Packaging ZIP...' : 'Download Complete cyber-shield.zip'}</span>
            </button>
            <button
              onClick={onClose}
              className="p-1.5 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white transition-colors"
            >
              <X className="w-5 h-5" />
            </button>
          </div>
        </div>

        {/* Content Body */}
        <div className="flex-1 flex flex-col md:flex-row overflow-hidden">
          {/* File Tree Sidebar */}
          <div className="w-full md:w-72 bg-slate-950/60 border-r border-slate-800 overflow-y-auto p-3 text-xs">
            <div className="text-[11px] font-bold text-cyan-400 uppercase tracking-wider mb-2 flex items-center gap-1">
              <Folder className="w-3.5 h-3.5" /> Project Files ({PROJECT_FILES.length})
            </div>
            <div className="space-y-1">
              {PROJECT_FILES.map((f) => (
                <button
                  key={f.path}
                  onClick={() => setSelectedFile(f)}
                  className={`w-full text-left px-2.5 py-1.5 rounded-lg font-mono text-[11px] flex items-center gap-2 transition-all ${
                    selectedFile.path === f.path
                      ? 'bg-cyan-500/20 text-cyan-300 font-semibold border border-cyan-500/40'
                      : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900'
                  }`}
                >
                  {f.category === 'database' ? (
                    <Database className="w-3 h-3 text-yellow-400 flex-shrink-0" />
                  ) : (
                    <FileCode className="w-3 h-3 text-cyan-400 flex-shrink-0" />
                  )}
                  <span className="truncate">{f.path}</span>
                </button>
              ))}
            </div>
          </div>

          {/* Code Viewer Panel */}
          <div className="flex-1 flex flex-col bg-[#0b101d] overflow-hidden">
            <div className="p-3 border-b border-slate-800 flex items-center justify-between bg-slate-900/40 text-xs">
              <div>
                <span className="font-mono text-cyan-300 font-bold">{selectedFile.path}</span>
                <span className="text-slate-500 ml-2 text-[11px]">({selectedFile.description})</span>
              </div>
              <button
                onClick={handleCopy}
                className="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold flex items-center gap-1 transition-colors"
              >
                {copied ? <Check className="w-3 h-3 text-emerald-400" /> : <Copy className="w-3 h-3" />}
                <span>{copied ? 'Copied!' : 'Copy Code'}</span>
              </button>
            </div>

            <div className="flex-1 overflow-auto p-4 font-mono text-xs text-slate-300 bg-[#070b14]/90 select-text leading-relaxed">
              <pre className="whitespace-pre">{fileContent}</pre>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};
