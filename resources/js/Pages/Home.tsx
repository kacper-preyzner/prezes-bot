import { motion } from "motion/react";

const features = [
  {
    icon: "⚡",
    title: "Gemini 2.5 Flash",
    desc: "Najnowszy model AI od Google. Błyskawiczne odpowiedzi na każde pytanie.",
  },
  {
    icon: "🎙",
    title: "Rozpoznawanie mowy",
    desc: "Mów po polsku — Prezes rozumie każde Twoje słowo w czasie rzeczywistym.",
  },
  {
    icon: "🔊",
    title: "Synteza mowy",
    desc: "ElevenLabs TTS — naturalna polska mowa, jakby rozmawiał z Tobą człowiek.",
  },
  {
    icon: "🌐",
    title: "Przeszukiwanie sieci",
    desc: "Aktualne informacje z internetu. Prezes wie, co dzieje się teraz.",
  },
  {
    icon: "📋",
    title: "Planowanie zadań",
    desc: "Ustaw przypomnienia i zadania. Prezes wykona je za Ciebie automatycznie.",
  },
  {
    icon: "🔒",
    title: "Twoje dane, Twoja sprawa",
    desc: "Autoryzacja tokenem. Nikt poza Tobą nie ma dostępu do rozmów.",
  },
];

function GridBackground() {
  return (
    <div className="fixed inset-0 overflow-hidden pointer-events-none">
      {/* Perspective grid floor */}
      <div
        className="absolute inset-0"
        style={{
          background: `
            linear-gradient(rgba(255,20,40,0.04) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,20,40,0.04) 1px, transparent 1px)
          `,
          backgroundSize: "60px 60px",
        }}
      />
      {/* Horizon glow */}
      <div
        className="absolute bottom-0 left-0 right-0 h-[40vh]"
        style={{
          background:
            "linear-gradient(to top, rgba(180,0,20,0.10), transparent)",
        }}
      />
      {/* Scan lines */}
      <div
        className="absolute inset-0 opacity-[0.03]"
        style={{
          background:
            "repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(255,255,255,0.1) 2px, rgba(255,255,255,0.1) 4px)",
        }}
      />
      {/* Floating neon lines */}
      <motion.div
        animate={{ x: ["-100%", "100%"] }}
        transition={{ duration: 8, repeat: Infinity, ease: "linear" }}
        className="absolute top-[20%] left-0 w-[60vw] h-[1px]"
        style={{
          background:
            "linear-gradient(90deg, transparent, #ff1744, #ff6b35, transparent)",
        }}
      />
      <motion.div
        animate={{ x: ["100%", "-100%"] }}
        transition={{ duration: 12, repeat: Infinity, ease: "linear" }}
        className="absolute top-[65%] left-0 w-[40vw] h-[1px]"
        style={{
          background:
            "linear-gradient(90deg, transparent, #ff6b35, #ff1744, transparent)",
        }}
      />
      {/* Ambient red wash */}
      <div
        className="absolute top-0 left-1/2 -translate-x-1/2 w-[80vw] h-[60vh] rounded-full opacity-[0.04]"
        style={{
          background:
            "radial-gradient(ellipse at center, #ff1744, transparent 70%)",
        }}
      />
    </div>
  );
}

function GlitchText({ children }: { children: string }) {
  return (
    <span className="relative inline-block">
      <span className="relative z-10">{children}</span>
      <motion.span
        aria-hidden
        className="absolute top-0 left-0 z-0 text-[#ff1744] opacity-70"
        animate={{ x: [0, -2, 2, -1, 0], y: [0, 1, -1, 0, 0] }}
        transition={{ duration: 0.3, repeat: Infinity, repeatDelay: 3 }}
      >
        {children}
      </motion.span>
      <motion.span
        aria-hidden
        className="absolute top-0 left-0 z-0 text-[#ff6b35] opacity-70"
        animate={{ x: [0, 2, -2, 1, 0], y: [0, -1, 1, 0, 0] }}
        transition={{
          duration: 0.3,
          repeat: Infinity,
          repeatDelay: 3,
          delay: 0.05,
        }}
      >
        {children}
      </motion.span>
    </span>
  );
}

export default function Home() {
  return (
    <div className="min-h-screen bg-[#080808] text-white font-rajdhani overflow-hidden">
      <GridBackground />

      {/* Hero */}
      <section className="relative min-h-screen flex flex-col items-center justify-center px-6 text-center">
        {/* Corner decorations */}
        <div className="absolute top-8 left-8 w-16 h-16 border-t border-l border-[#ff1744]/30" />
        <div className="absolute top-8 right-8 w-16 h-16 border-t border-r border-[#ff6b35]/30" />
        <div className="absolute bottom-8 left-8 w-16 h-16 border-b border-l border-[#ff6b35]/30" />
        <div className="absolute bottom-8 right-8 w-16 h-16 border-b border-r border-[#ff1744]/30" />

        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          transition={{ duration: 1 }}
          className="mb-6"
        >
          <span className="font-space-mono text-xs tracking-[0.4em] text-[#ff1744]/60 uppercase">
            System v2.5 // Status: Online
          </span>
        </motion.div>

        <motion.h1
          initial={{ opacity: 0, y: 40 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 0.3 }}
          className="font-orbitron text-6xl md:text-8xl lg:text-9xl font-black tracking-tight leading-none mb-6"
        >
          <GlitchText>PREZES</GlitchText>
          <br />
          <span
            className="text-transparent bg-clip-text"
            style={{
              backgroundImage:
                "linear-gradient(90deg, #ff1744, #ff6b35, #ff1744)",
              backgroundSize: "200% 100%",
              animation: "gradient-shift 3s linear infinite",
            }}
          >
            BOT
          </span>
        </motion.h1>

        <motion.p
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 0.6 }}
          className="text-lg md:text-xl text-white/50 max-w-xl mb-10 font-light tracking-wide"
        >
          Twój prywatny asystent AI.
          <br />
          Mów, pisz, planuj — Prezes ogarnie resztę.
        </motion.p>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 0.9 }}
          className="flex flex-col sm:flex-row gap-4"
        >
          <button className="group relative px-8 py-3 font-orbitron text-sm font-bold tracking-widest uppercase overflow-hidden">
            <div className="absolute inset-0 bg-[#ff1744]" />
            <div className="absolute inset-[1px] bg-[#080808] transition-colors group-hover:bg-[#ff1744]/10" />
            <span className="relative z-10 text-[#ff1744] group-hover:text-white transition-colors">
              Pobierz aplikację
            </span>
          </button>
          <a href="/admin" className="group relative px-8 py-3 font-orbitron text-sm font-bold tracking-widest uppercase overflow-hidden inline-block">
            <div className="absolute inset-0 bg-[#ff6b35]" />
            <div className="absolute inset-[1px] bg-[#080808] transition-colors group-hover:bg-[#ff6b35]/10" />
            <span className="relative z-10 text-[#ff6b35] group-hover:text-white transition-colors">
              Admin panel
            </span>
          </a>
        </motion.div>

        {/* Scroll indicator */}
        <motion.div
          animate={{ y: [0, 10, 0] }}
          transition={{ duration: 2, repeat: Infinity }}
          className="absolute bottom-12 flex flex-col items-center gap-2"
        >
          <span className="text-[10px] tracking-[0.3em] text-[#ff1744]/40 font-space-mono uppercase">
            Scroll
          </span>
          <div className="w-[1px] h-8 bg-gradient-to-b from-[#ff1744]/40 to-transparent" />
        </motion.div>
      </section>

      {/* Features */}
      <section className="relative px-6 py-32 max-w-7xl mx-auto">
        <motion.div
          initial={{ opacity: 0 }}
          whileInView={{ opacity: 1 }}
          viewport={{ once: true }}
          className="text-center mb-20"
        >
          <span className="font-space-mono text-xs tracking-[0.4em] text-[#ff6b35]/60 uppercase block mb-4">
            // Moduły systemu
          </span>
          <h2 className="font-orbitron text-4xl md:text-5xl font-bold">
            <span className="text-[#ff1744]">Pełne</span> wyposażenie
          </h2>
        </motion.div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {features.map((f, i) => (
            <motion.div
              key={f.title}
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: i * 0.1 }}
              className="group relative p-6 border border-white/5 hover:border-[#ff1744]/30 transition-all duration-500 bg-white/[0.02]"
            >
              {/* Corner accents */}
              <div className="absolute top-0 left-0 w-3 h-3 border-t border-l border-[#ff1744]/50 transition-all duration-500 group-hover:w-5 group-hover:h-5 group-hover:border-[#ff1744]" />
              <div className="absolute bottom-0 right-0 w-3 h-3 border-b border-r border-[#ff6b35]/50 transition-all duration-500 group-hover:w-5 group-hover:h-5 group-hover:border-[#ff6b35]" />

              <div className="text-3xl mb-4">{f.icon}</div>
              <h3 className="font-orbitron text-sm font-bold tracking-wider text-[#ff1744] mb-2 uppercase">
                {f.title}
              </h3>
              <p className="text-white/40 text-sm leading-relaxed font-light">
                {f.desc}
              </p>

              {/* Hover glow */}
              <div className="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none bg-gradient-to-br from-[#ff1744]/5 to-[#ff6b35]/5" />
            </motion.div>
          ))}
        </div>
      </section>

      {/* CTA Terminal */}
      <section className="relative px-6 py-32 flex justify-center">
        <motion.div
          initial={{ opacity: 0, scale: 0.95 }}
          whileInView={{ opacity: 1, scale: 1 }}
          viewport={{ once: true }}
          className="w-full max-w-2xl"
        >
          <div className="border border-[#ff1744]/20 bg-black/80 backdrop-blur">
            {/* Terminal header */}
            <div className="flex items-center gap-2 px-4 py-2 border-b border-[#ff1744]/10">
              <div className="w-2.5 h-2.5 rounded-full bg-[#ff1744]/60" />
              <div className="w-2.5 h-2.5 rounded-full bg-[#ff6b35]/60" />
              <div className="w-2.5 h-2.5 rounded-full bg-[#ffab40]/60" />
              <span className="ml-2 font-space-mono text-[10px] text-white/30">
                prezes@bot:~
              </span>
            </div>
            {/* Terminal body */}
            <div className="p-8 font-space-mono text-sm">
              <p className="text-[#ff1744]/60 mb-2">$ prezes --status</p>
              <p className="text-white/80 mb-1">
                &gt; Model: Gemini 2.5 Flash ....{" "}
                <span className="text-[#0f0]">ONLINE</span>
              </p>
              <p className="text-white/80 mb-1">
                &gt; Voice Engine: ElevenLabs ...{" "}
                <span className="text-[#0f0]">READY</span>
              </p>
              <p className="text-white/80 mb-1">
                &gt; Web Search: Perplexity .....{" "}
                <span className="text-[#0f0]">ACTIVE</span>
              </p>
              <p className="text-white/80 mb-4">
                &gt; Task Scheduler ............{" "}
                <span className="text-[#0f0]">RUNNING</span>
              </p>
              <motion.p
                animate={{ opacity: [1, 0, 1] }}
                transition={{ duration: 1, repeat: Infinity }}
                className="text-[#ff1744]"
              >
                $ Zainstaluj i zacznij rozmawiać_
              </motion.p>
            </div>
          </div>
        </motion.div>
      </section>

      {/* Footer */}
      <footer className="border-t border-white/5 px-6 py-8 text-center">
        <p className="font-space-mono text-[10px] tracking-[0.3em] text-white/20 uppercase">
          Prezes Bot // Zbudowany w Polsce // {new Date().getFullYear()}
        </p>
      </footer>

      <style>{`
        @keyframes gradient-shift {
          0% { background-position: 0% 50%; }
          50% { background-position: 100% 50%; }
          100% { background-position: 0% 50%; }
        }
      `}</style>
    </div>
  );
}
