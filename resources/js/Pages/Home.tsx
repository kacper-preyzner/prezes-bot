import { motion, AnimatePresence } from "motion/react";
import { useState, useEffect, useCallback } from "react";

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

const navItems = [
	{ id: "hero", label: "Home", icon: "H", href: "#hero" },
	{ id: "features", label: "Moduły", icon: "M", href: "#features" },
	{ id: "terminal", label: "Terminal", icon: "T", href: "#terminal" },
	{ id: "admin", label: "Admin", icon: "A", href: "/admin" },
];

function CyberSidebar() {
	const [activeSection, setActiveSection] = useState("hero");
	const [mobileOpen, setMobileOpen] = useState(false);
	const [hovered, setHovered] = useState<string | null>(null);

	useEffect(() => {
		const handleScroll = () => {
			const sections = ["hero", "features", "terminal"];
			for (const id of [...sections].reverse()) {
				const el = document.getElementById(id);
				if (el && el.getBoundingClientRect().top <= window.innerHeight / 2) {
					setActiveSection(id);
					break;
				}
			}
		};
		window.addEventListener("scroll", handleScroll, { passive: true });
		return () => window.removeEventListener("scroll", handleScroll);
	}, []);

	const handleNav = (item: (typeof navItems)[number]) => {
		if (item.href.startsWith("/")) {
			window.location.href = item.href;
			return;
		}
		const el = document.getElementById(item.id);
		el?.scrollIntoView({ behavior: "smooth" });
		setMobileOpen(false);
	};

	return (
		<>
			{/* Desktop sidebar */}
			<nav className="hidden lg:flex fixed left-0 top-0 bottom-0 z-50 w-16 flex-col items-center justify-center gap-1">
				{/* Sidebar background */}
				<div
					className="absolute inset-0 border-r border-[#ff1744]/10"
					style={{
						background:
							"linear-gradient(180deg, rgba(8,8,8,0.95) 0%, rgba(15,5,5,0.98) 50%, rgba(8,8,8,0.95) 100%)",
					}}
				/>

				{/* Circuit trace line */}
				<div className="absolute top-0 right-0 w-[1px] h-full">
					<motion.div
						animate={{ y: ["-100%", "100%"] }}
						transition={{
							duration: 4,
							repeat: Infinity,
							ease: "linear",
						}}
						className="w-full h-16"
						style={{
							background:
								"linear-gradient(180deg, transparent, #ff1744, transparent)",
						}}
					/>
				</div>

				{/* Scan line overlay */}
				<div
					className="absolute inset-0 opacity-[0.04] pointer-events-none"
					style={{
						background:
							"repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(255,255,255,0.15) 2px, rgba(255,255,255,0.15) 4px)",
					}}
				/>

				{/* Top hex indicator */}
				<div className="absolute top-6 flex flex-col items-center gap-1">
					<motion.div
						animate={{ opacity: [0.3, 1, 0.3] }}
						transition={{ duration: 2, repeat: Infinity }}
						className="w-1.5 h-1.5 rotate-45 bg-[#ff1744]"
					/>
					<div className="w-[1px] h-6 bg-gradient-to-b from-[#ff1744]/40 to-transparent" />
				</div>

				{/* Nav items */}
				<div className="relative flex flex-col items-center gap-3">
					{navItems.map((item, i) => {
						const isActive = item.id === activeSection;
						const isHover = hovered === item.id;

						return (
							<motion.div
								key={item.id}
								initial={{ opacity: 0, x: -20 }}
								animate={{ opacity: 1, x: 0 }}
								transition={{ delay: 0.8 + i * 0.1 }}
								className="relative group"
								onMouseEnter={() => setHovered(item.id)}
								onMouseLeave={() => setHovered(null)}
							>
								{/* Connection node line */}
								{i < navItems.length - 1 && (
									<div className="absolute top-full left-1/2 -translate-x-1/2 w-[1px] h-3 bg-white/5" />
								)}

								<button
									onClick={() => handleNav(item)}
									className="relative flex items-center justify-center w-10 h-10 cursor-pointer"
								>
									{/* Active glow ring */}
									{isActive && (
										<motion.div
											layoutId="sidebar-active"
											className="absolute inset-0 border border-[#ff1744]/60"
											style={{
												boxShadow:
													"0 0 12px rgba(255,23,68,0.3), inset 0 0 12px rgba(255,23,68,0.1)",
											}}
											transition={{
												type: "spring",
												stiffness: 300,
												damping: 30,
											}}
										/>
									)}

									{/* Hover border */}
									{!isActive && isHover && (
										<motion.div
											initial={{ opacity: 0 }}
											animate={{ opacity: 1 }}
											className="absolute inset-0 border border-[#ff6b35]/30"
										/>
									)}

									{/* Background fill */}
									<div
										className={`absolute inset-[1px] transition-colors duration-300 ${
											isActive
												? "bg-[#ff1744]/10"
												: isHover
													? "bg-white/[0.03]"
													: "bg-transparent"
										}`}
									/>

									{/* Icon letter */}
									<span
										className={`relative z-10 font-orbitron text-xs font-bold tracking-wider transition-colors duration-300 ${
											isActive
												? "text-[#ff1744]"
												: isHover
													? "text-[#ff6b35]"
													: "text-white/25"
										}`}
									>
										{item.icon}
									</span>

									{/* Active left indicator bar */}
									{isActive && (
										<motion.div
											layoutId="sidebar-indicator"
											className="absolute left-0 top-1/2 -translate-y-1/2 w-[2px] h-4 bg-[#ff1744]"
											style={{
												boxShadow: "0 0 8px #ff1744",
											}}
										/>
									)}
								</button>

								{/* Tooltip label */}
								<AnimatePresence>
									{isHover && (
										<motion.div
											initial={{
												opacity: 0,
												x: -4,
												scale: 0.95,
											}}
											animate={{
												opacity: 1,
												x: 0,
												scale: 1,
											}}
											exit={{
												opacity: 0,
												x: -4,
												scale: 0.95,
											}}
											transition={{ duration: 0.15 }}
											className="absolute left-full top-1/2 -translate-y-1/2 ml-3 pointer-events-none"
										>
											<div className="relative px-3 py-1.5 bg-[#0c0c0c] border border-[#ff1744]/20">
												{/* Tooltip arrow */}
												<div className="absolute left-0 top-1/2 -translate-x-1/2 -translate-y-1/2 w-1.5 h-1.5 rotate-45 bg-[#0c0c0c] border-l border-b border-[#ff1744]/20" />
												<span className="font-space-mono text-[10px] tracking-[0.2em] text-[#ff1744]/80 uppercase whitespace-nowrap">
													{item.label}
												</span>
											</div>
										</motion.div>
									)}
								</AnimatePresence>
							</motion.div>
						);
					})}
				</div>

				{/* Bottom hex indicator */}
				<div className="absolute bottom-6 flex flex-col items-center gap-1">
					<div className="w-[1px] h-6 bg-gradient-to-t from-[#ff6b35]/40 to-transparent" />
					<motion.div
						animate={{ opacity: [0.3, 1, 0.3] }}
						transition={{
							duration: 2,
							repeat: Infinity,
							delay: 1,
						}}
						className="w-1.5 h-1.5 rotate-45 bg-[#ff6b35]"
					/>
				</div>
			</nav>

			{/* Mobile hamburger */}
			<div className="lg:hidden fixed top-4 left-4 z-50">
				<motion.button
					initial={{ opacity: 0 }}
					animate={{ opacity: 1 }}
					transition={{ delay: 0.5 }}
					onClick={() => setMobileOpen(!mobileOpen)}
					className="relative flex items-center justify-center w-11 h-11 cursor-pointer"
				>
					<div className="absolute inset-0 border border-[#ff1744]/30 bg-[#080808]/90 backdrop-blur-sm" />
					<div className="relative z-10 flex flex-col gap-[5px] items-center justify-center w-5">
						<motion.div
							animate={
								mobileOpen
									? { rotate: 45, y: 7 }
									: { rotate: 0, y: 0 }
							}
							className="w-full h-[2px] bg-[#ff1744]"
							style={{
								boxShadow: "0 0 6px rgba(255,23,68,0.5)",
							}}
						/>
						<motion.div
							animate={
								mobileOpen
									? { opacity: 0, scaleX: 0 }
									: { opacity: 1, scaleX: 1 }
							}
							className="w-full h-[2px] bg-[#ff6b35]"
						/>
						<motion.div
							animate={
								mobileOpen
									? { rotate: -45, y: -7 }
									: { rotate: 0, y: 0 }
							}
							className="w-full h-[2px] bg-[#ff1744]"
							style={{
								boxShadow: "0 0 6px rgba(255,23,68,0.5)",
							}}
						/>
					</div>
				</motion.button>
			</div>

			{/* Mobile overlay */}
			<AnimatePresence>
				{mobileOpen && (
					<motion.div
						initial={{ opacity: 0 }}
						animate={{ opacity: 1 }}
						exit={{ opacity: 0 }}
						className="lg:hidden fixed inset-0 z-40"
					>
						{/* Backdrop */}
						<div
							className="absolute inset-0 bg-[#080808]/95 backdrop-blur-sm"
							onClick={() => setMobileOpen(false)}
						/>

						{/* Mobile nav panel */}
						<motion.nav
							initial={{ x: "-100%" }}
							animate={{ x: 0 }}
							exit={{ x: "-100%" }}
							transition={{
								type: "spring",
								stiffness: 300,
								damping: 30,
							}}
							className="absolute top-0 left-0 bottom-0 w-64 border-r border-[#ff1744]/15"
							style={{
								background:
									"linear-gradient(180deg, rgba(12,4,4,0.98) 0%, rgba(8,8,8,0.99) 100%)",
							}}
						>
							{/* Scan lines */}
							<div
								className="absolute inset-0 opacity-[0.03] pointer-events-none"
								style={{
									background:
										"repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(255,255,255,0.15) 2px, rgba(255,255,255,0.15) 4px)",
								}}
							/>

							{/* Header */}
							<div className="px-6 pt-20 pb-8 border-b border-white/5">
								<span className="font-orbitron text-sm font-bold tracking-[0.2em] text-[#ff1744]">
									NAV://
								</span>
								<div className="mt-2 w-8 h-[1px] bg-gradient-to-r from-[#ff1744] to-transparent" />
							</div>

							{/* Nav items */}
							<div className="px-4 py-6 flex flex-col gap-1">
								{navItems.map((item, i) => {
									const isActive =
										item.id === activeSection;
									return (
										<motion.button
											key={item.id}
											initial={{ opacity: 0, x: -20 }}
											animate={{ opacity: 1, x: 0 }}
											transition={{ delay: 0.1 + i * 0.05 }}
											onClick={() => handleNav(item)}
											className={`group relative flex items-center gap-4 px-4 py-3 text-left transition-all duration-300 cursor-pointer ${
												isActive
													? "bg-[#ff1744]/5"
													: "hover:bg-white/[0.02]"
											}`}
										>
											{/* Active bar */}
											{isActive && (
												<div
													className="absolute left-0 top-2 bottom-2 w-[2px] bg-[#ff1744]"
													style={{
														boxShadow:
															"0 0 8px #ff1744",
													}}
												/>
											)}

											{/* Icon */}
											<div
												className={`flex items-center justify-center w-8 h-8 border transition-colors duration-300 ${
													isActive
														? "border-[#ff1744]/50 text-[#ff1744]"
														: "border-white/10 text-white/30 group-hover:border-[#ff6b35]/30 group-hover:text-[#ff6b35]"
												}`}
											>
												<span className="font-orbitron text-[10px] font-bold">
													{item.icon}
												</span>
											</div>

											{/* Label */}
											<div className="flex flex-col">
												<span
													className={`font-orbitron text-xs font-bold tracking-wider uppercase transition-colors duration-300 ${
														isActive
															? "text-[#ff1744]"
															: "text-white/50 group-hover:text-white/80"
													}`}
												>
													{item.label}
												</span>
												<span className="font-space-mono text-[9px] text-white/15 tracking-wider">
													{item.href.startsWith("/")
														? item.href
														: `sect://${item.id}`}
												</span>
											</div>

											{/* Corner accents */}
											<div
												className={`absolute top-0 right-0 w-2 h-2 border-t border-r transition-colors duration-300 ${
													isActive
														? "border-[#ff1744]/30"
														: "border-transparent group-hover:border-[#ff6b35]/20"
												}`}
											/>
										</motion.button>
									);
								})}
							</div>

							{/* Bottom decoration */}
							<div className="absolute bottom-6 left-6 right-6">
								<div className="w-full h-[1px] bg-white/5 mb-3" />
								<span className="font-space-mono text-[8px] tracking-[0.3em] text-white/10 uppercase">
									Prezes Bot // v1.0
								</span>
							</div>
						</motion.nav>
					</motion.div>
				)}
			</AnimatePresence>
		</>
	);
}

function ScrollbarGlitch() {
	const [glitches, setGlitches] = useState<
		{ id: number; top: number; height: number; color: string }[]
	>([]);

	const fireGlitch = useCallback(() => {
		const count = Math.floor(Math.random() * 3) + 1;
		const newGlitches = Array.from({ length: count }, (_, i) => ({
			id: Date.now() + i,
			top: Math.random() * 100,
			height: Math.random() * 6 + 2,
			color: Math.random() > 0.5 ? "#ff1744" : "#ff6b35",
		}));
		setGlitches(newGlitches);
		setTimeout(() => setGlitches([]), 150);
	}, []);

	useEffect(() => {
		const scheduleNext = () => {
			const delay = Math.random() * 4000 + 2000;
			return setTimeout(() => {
				fireGlitch();
				timerId = scheduleNext();
			}, delay);
		};
		let timerId = scheduleNext();
		return () => clearTimeout(timerId);
	}, [fireGlitch]);

	return (
		<div className="fixed top-0 right-0 bottom-0 w-[10px] z-[9999] pointer-events-none">
			{glitches.map((g) => (
				<motion.div
					key={g.id}
					initial={{ opacity: 0.9, scaleX: 1 }}
					animate={{ opacity: 0, scaleX: 1.5 }}
					transition={{ duration: 0.15 }}
					className="absolute right-0"
					style={{
						top: `${g.top}%`,
						height: `${g.height}px`,
						width: "14px",
						background: g.color,
						boxShadow: `0 0 12px ${g.color}, -4px 0 20px ${g.color}66`,
						mixBlendMode: "screen",
					}}
				/>
			))}
		</div>
	);
}

function GridBackground() {
	return (
		<div className="overflow-hidden fixed inset-0 pointer-events-none">
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
				className="absolute right-0 bottom-0 left-0 h-[40vh]"
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
				className="absolute top-0 left-1/2 rounded-full -translate-x-1/2 w-[80vw] h-[60vh] opacity-[0.04]"
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
		<span className="inline-block relative">
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
			<ScrollbarGlitch />
			<CyberSidebar />

			{/* Hero */}
			<section
				id="hero"
				className="flex relative flex-col justify-center items-center px-6 lg:pl-22 min-h-screen text-center"
			>
				{/* Corner decorations */}
				<div className="absolute top-8 left-8 lg:left-24 w-16 h-16 border-t border-l border-[#ff1744]/30" />
				<div className="absolute top-8 right-8 w-16 h-16 border-t border-r border-[#ff6b35]/30" />
				<div className="absolute bottom-8 left-8 lg:left-24 w-16 h-16 border-b border-l border-[#ff6b35]/30" />
				<div className="absolute bottom-8 right-8 w-16 h-16 border-b border-r border-[#ff1744]/30" />

				<motion.div
					initial={{ opacity: 0 }}
					animate={{ opacity: 1 }}
					transition={{ duration: 1 }}
					className="mb-6"
				>
					<span className="font-space-mono text-xs tracking-[0.4em] text-[#ff1744]/60 uppercase">
						Status: Online
					</span>
				</motion.div>

				<motion.h1
					initial={{ opacity: 0, y: 40 }}
					animate={{ opacity: 1, y: 0 }}
					transition={{ duration: 0.8, delay: 0.3 }}
					className="mb-6 text-6xl font-black tracking-tight leading-none md:text-8xl lg:text-9xl font-orbitron"
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
					className="mb-10 max-w-xl text-lg font-light tracking-wide md:text-xl text-white/50"
				>
					Twój prywatny asystent AI.
					<br />
					Mów, pisz, planuj — Prezes ogarnie resztę.
				</motion.p>

				<motion.div
					initial={{ opacity: 0, y: 20 }}
					animate={{ opacity: 1, y: 0 }}
					transition={{ duration: 0.8, delay: 0.9 }}
					className="flex flex-col gap-4 sm:flex-row"
				>
					<button className="overflow-hidden relative py-3 px-8 text-sm font-bold tracking-widest uppercase group font-orbitron">
						<div className="absolute inset-0 bg-[#ff1744]" />
						<div className="absolute inset-[1px] bg-[#080808] transition-colors group-hover:bg-[#ff1744]/10" />
						<span className="relative z-10 text-[#ff1744] group-hover:text-white transition-colors">
							Pobierz aplikację
						</span>
					</button>
					<a
						href="/admin"
						className="inline-block overflow-hidden relative py-3 px-8 text-sm font-bold tracking-widest uppercase group font-orbitron"
					>
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
					className="flex absolute bottom-12 flex-col gap-2 items-center"
				>
					<span className="text-[10px] tracking-[0.3em] text-[#ff1744]/40 font-space-mono uppercase">
						Scroll
					</span>
					<div className="w-[1px] h-8 bg-gradient-to-b from-[#ff1744]/40 to-transparent" />
				</motion.div>
			</section>

			{/* Features */}
			<section
				id="features"
				className="relative py-32 px-6 lg:pl-22 mx-auto max-w-7xl"
			>
				<motion.div
					initial={{ opacity: 0 }}
					whileInView={{ opacity: 1 }}
					viewport={{ once: true }}
					className="mb-20 text-center"
				>
					<span className="font-space-mono text-xs tracking-[0.4em] text-[#ff6b35]/60 uppercase block mb-4">
						// Moduły systemu
					</span>
					<h2 className="text-4xl font-bold md:text-5xl font-orbitron">
						<span className="text-[#ff1744]">Pełne</span>{" "}
						wyposażenie
					</h2>
				</motion.div>

				<div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
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

							<div className="mb-4 text-3xl">{f.icon}</div>
							<h3 className="font-orbitron text-sm font-bold tracking-wider text-[#ff1744] mb-2 uppercase">
								{f.title}
							</h3>
							<p className="text-sm font-light leading-relaxed text-white/40">
								{f.desc}
							</p>

							{/* Hover glow */}
							<div className="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none bg-gradient-to-br from-[#ff1744]/5 to-[#ff6b35]/5" />
						</motion.div>
					))}
				</div>
			</section>

			{/* CTA Terminal */}
			<section
				id="terminal"
				className="flex relative justify-center py-32 px-6 lg:pl-22"
			>
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
						<div className="p-8 text-sm font-space-mono">
							<p className="text-[#ff1744]/60 mb-2">
								$ prezes --status
							</p>
							<p className="mb-1 text-white/80">
								&gt; Model: Gemini 2.5 Flash ....{" "}
								<span className="text-[#0f0]">ONLINE</span>
							</p>
							<p className="mb-1 text-white/80">
								&gt; Voice Engine: ElevenLabs ...{" "}
								<span className="text-[#0f0]">READY</span>
							</p>
							<p className="mb-1 text-white/80">
								&gt; Web Search: Perplexity .....{" "}
								<span className="text-[#0f0]">ACTIVE</span>
							</p>
							<p className="mb-4 text-white/80">
								&gt; Task Scheduler ............{" "}
								<span className="text-[#0f0]">RUNNING</span>
							</p>
							<motion.p
								animate={{ opacity: [1, 0, 1] }}
								transition={{
									duration: 1,
									repeat: Infinity,
								}}
								className="text-[#ff1744]"
							>
								$ Zainstaluj i zacznij rozmawiać_
							</motion.p>
						</div>
					</div>
				</motion.div>
			</section>

			{/* Footer */}
			<footer className="py-8 px-6 lg:pl-22 text-center border-t border-white/5">
				<p className="uppercase font-space-mono text-[10px] tracking-[0.3em] text-white/20">
					Prezes Bot // Zbudowany w Polsce //{" "}
					{new Date().getFullYear()}
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
