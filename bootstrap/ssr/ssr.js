import { Fragment, jsx, jsxs } from "react/jsx-runtime";
import { Head, Link, createInertiaApp } from "@inertiajs/react";
import { useEffect, useState } from "react";
import createServer from "@inertiajs/react/server";
import ReactDOMServer from "react-dom/server";
//#region \0rolldown/runtime.js
var __defProp = Object.defineProperty;
var __exportAll = (all, no_symbols) => {
	let target = {};
	for (var name in all) __defProp(target, name, {
		get: all[name],
		enumerable: true
	});
	if (!no_symbols) __defProp(target, Symbol.toStringTag, { value: "Module" });
	return target;
};
//#endregion
//#region resources/js/Components/ContentBlocks.tsx
var publicFile = (path) => `/storage/${String(path).replace(/^\/+/, "")}`;
function embedUrl(url) {
	try {
		const parsed = new URL(url);
		if (parsed.hostname.includes("youtube.com")) {
			const id = parsed.searchParams.get("v");
			return id ? `https://www.youtube.com/embed/${id}` : null;
		}
		if (parsed.hostname === "youtu.be") return `https://www.youtube.com/embed/${parsed.pathname.replace("/", "")}`;
		if (parsed.hostname.includes("aparat.com")) {
			const match = parsed.pathname.match(/\/v\/([^/]+)/);
			return match ? `https://www.aparat.com/video/video/embed/videohash/${match[1]}/vt/frame` : null;
		}
	} catch {
		return null;
	}
	return null;
}
function ContentBlocks({ blocks }) {
	const orderedBlocks = [
		...blocks.filter((block) => [
			"rich_text",
			"heading",
			"quote"
		].includes(block.type)),
		...blocks.filter((block) => [
			"video",
			"image",
			"gallery"
		].includes(block.type)),
		...blocks.filter((block) => block.type === "audio"),
		...blocks.filter((block) => ["link", "download"].includes(block.type))
	];
	return /* @__PURE__ */ jsx("div", {
		className: "article-blocks",
		children: orderedBlocks.map((block, index) => {
			const data = block.data ?? {};
			switch (block.type) {
				case "rich_text": return /* @__PURE__ */ jsx("div", {
					className: "rich-text",
					dangerouslySetInnerHTML: { __html: String(data.body ?? "") }
				}, index);
				case "heading": {
					const Heading = Number(data.level) === 3 ? "h3" : Number(data.level) === 4 ? "h4" : "h2";
					return /* @__PURE__ */ jsx(Heading, { children: String(data.text ?? "") }, index);
				}
				case "image": return /* @__PURE__ */ jsxs("figure", { children: [/* @__PURE__ */ jsx("img", {
					src: publicFile(data.path),
					alt: String(data.alt ?? ""),
					loading: "lazy"
				}), data.caption ? /* @__PURE__ */ jsx("figcaption", { children: String(data.caption) }) : null] }, index);
				case "gallery": return /* @__PURE__ */ jsx("div", {
					className: "content-gallery",
					children: (Array.isArray(data.paths) ? data.paths : []).map((path) => /* @__PURE__ */ jsx("img", {
						src: publicFile(path),
						alt: "",
						loading: "lazy"
					}, String(path)))
				}, index);
				case "audio": return /* @__PURE__ */ jsxs("figure", {
					className: "audio-block",
					children: [/* @__PURE__ */ jsx("audio", {
						controls: true,
						preload: "metadata",
						src: publicFile(data.path)
					}), data.caption ? /* @__PURE__ */ jsx("figcaption", { children: String(data.caption) }) : null]
				}, index);
				case "video": {
					const localPath = data.path ? publicFile(data.path) : null;
					const externalUrl = data.url ? String(data.url) : null;
					const embedded = externalUrl ? embedUrl(externalUrl) : null;
					return /* @__PURE__ */ jsxs("figure", {
						className: "video-block",
						children: [
							localPath && /* @__PURE__ */ jsx("video", {
								controls: true,
								preload: "metadata",
								src: localPath
							}),
							!localPath && embedded && /* @__PURE__ */ jsx("iframe", {
								src: embedded,
								title: String(data.caption ?? "ویدئو"),
								allowFullScreen: true,
								loading: "lazy"
							}),
							!localPath && externalUrl && !embedded && /* @__PURE__ */ jsx("a", {
								className: "external-media",
								href: externalUrl,
								rel: "noreferrer",
								children: "مشاهده ویدئو در وب‌سایت اصلی ←"
							}),
							data.caption ? /* @__PURE__ */ jsx("figcaption", { children: String(data.caption) }) : null
						]
					}, index);
				}
				case "link": {
					const url = String(data.url ?? "#");
					const external = /^https?:\/\//i.test(url);
					return /* @__PURE__ */ jsxs("a", {
						className: "link-card",
						href: url,
						target: external ? "_blank" : void 0,
						rel: external ? "noreferrer" : void 0,
						children: [
							/* @__PURE__ */ jsx("strong", { children: String(data.label ?? "") }),
							data.description ? /* @__PURE__ */ jsx("span", { children: String(data.description) }) : null,
							/* @__PURE__ */ jsx("b", {
								"aria-hidden": "true",
								children: "←"
							})
						]
					}, index);
				}
				case "quote": return /* @__PURE__ */ jsxs("blockquote", { children: [/* @__PURE__ */ jsx("p", { children: String(data.text ?? "") }), data.citation ? /* @__PURE__ */ jsx("cite", { children: String(data.citation) }) : null] }, index);
				case "download": return /* @__PURE__ */ jsxs("a", {
					className: "download-card",
					href: publicFile(data.path),
					download: true,
					children: ["↓ ", String(data.label ?? "دریافت فایل")]
				}, index);
				default: return null;
			}
		})
	});
}
//#endregion
//#region resources/js/Components/RelatedLinks.tsx
function RelatedLinks({ links }) {
	if (links.length === 0) return null;
	return /* @__PURE__ */ jsxs("section", {
		className: "related-links",
		"aria-labelledby": "related-links-title",
		children: [/* @__PURE__ */ jsx("h2", {
			id: "related-links-title",
			children: "پیوندهای مرتبط"
		}), links.map((link) => {
			const external = /^https?:\/\//i.test(link.url);
			return /* @__PURE__ */ jsxs("a", {
				className: "link-card",
				href: link.url,
				target: external ? "_blank" : void 0,
				rel: external ? "noreferrer" : void 0,
				children: [
					/* @__PURE__ */ jsx("strong", { children: link.label }),
					link.description && /* @__PURE__ */ jsx("span", { children: link.description }),
					/* @__PURE__ */ jsx("b", {
						"aria-hidden": "true",
						children: "←"
					})
				]
			}, link.url + link.label);
		})]
	});
}
//#endregion
//#region resources/js/locales/fa.ts
/**
* Persian UI copy for the public website.
*
* Keep navigation and section labels here so the wording can be changed
* without editing page components. This is intentionally a plain object for
* now; a locale switcher can wrap it later if the public language strategy
* becomes bilingual.
*/
var fa = {
	nav: {
		announcements: "اخبار و اطلاعیه‌ها",
		posts: "مطالب",
		postsAndSeries: "مطالب و مجموعه‌ها",
		series: "مجموعه‌ها",
		courses: "دوره‌ها و برنامه‌ها",
		books: "کتاب‌ها",
		about: "درباره ما",
		registration: "ثبت‌نام در مؤسسه",
		contact: "ارتباط با ما",
		donation: "حمایت مالی"
	},
	sections: {
		announcements: "اخبار و اطلاعیه‌ها",
		postsAndSeries: "مطالب و مجموعه‌ها",
		courses: "دوره‌ها و برنامه‌ها",
		books: "کتاب‌ها",
		about: "درباره ما",
		registration: "ثبت‌نام در مؤسسه",
		contact: "ارتباط با ما",
		donation: "حمایت مالی از اندیشکده"
	},
	contentTypes: {
		post: "مطلب",
		series: "مجموعه",
		course: "دوره",
		book: "کتاب",
		announcement: "اطلاعیه"
	},
	actions: { showMore: "نمایش بیشتر" }
};
//#endregion
//#region resources/js/Components/SiteLayout.tsx
function SiteLayout({ children, title }) {
	const [menuOpen, setMenuOpen] = useState(false);
	return /* @__PURE__ */ jsxs(Fragment, { children: [
		/* @__PURE__ */ jsx(Head, { title }),
		/* @__PURE__ */ jsx("header", {
			className: "site-header",
			children: /* @__PURE__ */ jsxs("div", {
				className: "shell header-inner",
				children: [
					/* @__PURE__ */ jsxs(Link, {
						href: "/",
						className: "brand",
						"aria-label": "صفحه اصلی اندیشکده",
						children: [/* @__PURE__ */ jsx("span", {
							className: "brand-seal",
							"aria-hidden": "true",
							children: /* @__PURE__ */ jsx("span", { children: "تبیین" })
						}), /* @__PURE__ */ jsx("strong", { children: "اندیشکده علوم و فناوری‌های نرم" })]
					}),
					/* @__PURE__ */ jsxs("button", {
						className: "menu-button",
						type: "button",
						"aria-expanded": menuOpen,
						"aria-controls": "main-navigation",
						onClick: () => setMenuOpen((open) => !open),
						children: [
							/* @__PURE__ */ jsx("span", {}),
							/* @__PURE__ */ jsx("span", {}),
							/* @__PURE__ */ jsx("span", {}),
							/* @__PURE__ */ jsx("b", { children: "منو" })
						]
					}),
					/* @__PURE__ */ jsxs("nav", {
						id: "main-navigation",
						className: menuOpen ? "is-open" : "",
						"aria-label": "ناوبری اصلی",
						children: [
							/* @__PURE__ */ jsx("a", {
								href: "/#announcements",
								children: fa.nav.announcements
							}),
							/* @__PURE__ */ jsx("a", {
								href: "/#posts-series",
								children: fa.nav.postsAndSeries ?? fa.nav.posts
							}),
							/* @__PURE__ */ jsx("a", {
								href: "/#courses",
								children: fa.nav.courses
							}),
							/* @__PURE__ */ jsx("a", {
								href: "/#books",
								children: fa.nav.books
							}),
							/* @__PURE__ */ jsx("a", {
								href: "/#about",
								children: fa.nav.about
							}),
							/* @__PURE__ */ jsx("a", {
								href: "/#registration",
								children: fa.nav.registration
							}),
							/* @__PURE__ */ jsx("a", {
								href: "/#contact",
								children: fa.nav.contact
							}),
							/* @__PURE__ */ jsx(Link, {
								href: "/donation",
								children: fa.nav.donation
							})
						]
					}),
					/* @__PURE__ */ jsxs("div", {
						className: "header-search",
						role: "search",
						children: [/* @__PURE__ */ jsx("span", { children: "جستجو" }), /* @__PURE__ */ jsx("svg", {
							viewBox: "0 0 24 24",
							"aria-hidden": "true",
							children: /* @__PURE__ */ jsx("path", { d: "m21 21-4.35-4.35m2.35-5.15a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z" })
						})]
					})
				]
			})
		}),
		/* @__PURE__ */ jsx("main", { children }),
		/* @__PURE__ */ jsx("footer", {
			className: "site-footer",
			children: /* @__PURE__ */ jsxs("div", {
				className: "shell footer-inner",
				children: [/* @__PURE__ */ jsx("p", { children: "اندیشکده علوم و فناوری‌های نرم انقلاب اسلامی" }), /* @__PURE__ */ jsx("a", {
					href: "#top",
					children: "بازگشت به بالا ↑"
				})]
			})
		})
	] });
}
//#endregion
//#region resources/js/Pages/Content/Show.tsx
var Show_exports$3 = /* @__PURE__ */ __exportAll({ default: () => Show$3 });
var labels = {
	course: fa.contentTypes.course,
	book: fa.contentTypes.book,
	announcement: fa.contentTypes.announcement
};
function Show$3({ content }) {
	return /* @__PURE__ */ jsx(SiteLayout, {
		title: content.title,
		children: /* @__PURE__ */ jsxs("article", {
			className: "article-page shell",
			children: [
				/* @__PURE__ */ jsxs("header", {
					className: "article-header",
					children: [
						/* @__PURE__ */ jsx("span", {
							className: "article-kind",
							children: labels[content.type]
						}),
						/* @__PURE__ */ jsx("h1", { children: content.title }),
						content.excerpt && /* @__PURE__ */ jsx("p", { children: content.excerpt }),
						content.coverImageUrl && /* @__PURE__ */ jsx("img", {
							src: content.coverImageUrl,
							alt: content.title
						})
					]
				}),
				/* @__PURE__ */ jsx(ContentBlocks, { blocks: content.contentBlocks }),
				/* @__PURE__ */ jsx(RelatedLinks, { links: content.links })
			]
		})
	});
}
//#endregion
//#region resources/js/Pages/Donation/Show.tsx
var Show_exports$2 = /* @__PURE__ */ __exportAll({ default: () => Show$2 });
var numberFormat = new Intl.NumberFormat("fa-IR");
function Show$2({ donation }) {
	const [amount, setAmount] = useState("");
	const submit = (event) => {
		event.preventDefault();
	};
	return /* @__PURE__ */ jsx(SiteLayout, {
		title: donation.title,
		children: /* @__PURE__ */ jsxs("section", {
			className: "standalone-section shell donation-page",
			children: [
				/* @__PURE__ */ jsxs("div", {
					className: "section-heading",
					children: [/* @__PURE__ */ jsx("span", { children: fa.nav.donation }), /* @__PURE__ */ jsx("h1", { children: donation.title })]
				}),
				/* @__PURE__ */ jsx("div", {
					className: "section-lead section-rich-text",
					dangerouslySetInnerHTML: { __html: donation.body }
				}),
				/* @__PURE__ */ jsxs("form", {
					className: "donation-form",
					onSubmit: submit,
					children: [
						/* @__PURE__ */ jsx("label", {
							htmlFor: "donation-amount",
							children: "مبلغ حمایت (تومان)"
						}),
						/* @__PURE__ */ jsx("div", {
							className: "quick-amounts",
							children: donation.quickAmounts.map((quickAmount) => /* @__PURE__ */ jsx("button", {
								type: "button",
								className: amount === String(quickAmount) ? "is-selected" : "",
								"aria-pressed": amount === String(quickAmount),
								onClick: () => setAmount(String(quickAmount)),
								children: numberFormat.format(quickAmount)
							}, quickAmount))
						}),
						/* @__PURE__ */ jsx("input", {
							id: "donation-amount",
							inputMode: "numeric",
							min: donation.minimumAmount,
							pattern: "[0-9۰-۹]+",
							required: true,
							step: "1",
							type: "number",
							value: amount,
							onChange: (event) => setAmount(event.target.value),
							placeholder: numberFormat.format(donation.minimumAmount)
						}),
						/* @__PURE__ */ jsxs("small", { children: [
							"حداقل مبلغ: ",
							numberFormat.format(donation.minimumAmount),
							" تومان"
						] }),
						/* @__PURE__ */ jsx("button", {
							className: "primary-action",
							disabled: !donation.gatewayReady || Number(amount) < donation.minimumAmount,
							type: "submit",
							children: donation.gatewayReady ? "ادامه پرداخت" : "درگاه پرداخت به‌زودی فعال می‌شود"
						})
					]
				})
			]
		})
	});
}
//#endregion
//#region resources/js/Components/AnnouncementList.tsx
function AnnouncementList({ items }) {
	if (items.length === 0) return /* @__PURE__ */ jsx("p", {
		className: "empty-copy",
		children: "اطلاعیه‌ای برای نمایش وجود ندارد."
	});
	return /* @__PURE__ */ jsx("div", {
		className: "announcement-list",
		children: items.map((item) => /* @__PURE__ */ jsxs(Link, {
			href: item.url,
			children: [/* @__PURE__ */ jsx("span", { children: item.title }), /* @__PURE__ */ jsx("b", {
				"aria-hidden": "true",
				children: "←"
			})]
		}, item.id))
	});
}
//#endregion
//#region resources/js/Components/BannerCarousel.tsx
function BannerCarousel({ banners }) {
	const [active, setActive] = useState(0);
	useEffect(() => {
		if (banners.length < 2) return;
		const timer = window.setInterval(() => {
			setActive((current) => (current + 1) % banners.length);
		}, 6500);
		return () => window.clearInterval(timer);
	}, [banners.length]);
	if (banners.length === 0) return /* @__PURE__ */ jsx("section", {
		className: "hero reference-hero",
		"aria-label": "معرفی اندیشکده",
		children: /* @__PURE__ */ jsxs("div", {
			className: "hero-slide is-active placeholder-slide",
			children: [/* @__PURE__ */ jsx("div", {
				className: "hero-art",
				"aria-hidden": "true",
				children: /* @__PURE__ */ jsx("span", { children: "اندیشه" })
			}), /* @__PURE__ */ jsxs("div", {
				className: "hero-content",
				children: [
					/* @__PURE__ */ jsx("span", {
						className: "hero-category",
						children: "اندیشکده"
					}),
					/* @__PURE__ */ jsx("h1", { children: "رسانه، اندیشه و گفت‌وگو" }),
					/* @__PURE__ */ jsx("p", { children: "مجموعه‌ای از نوشته‌ها و محتوای چندرسانه‌ای برای فهم دقیق‌تر موضوعات امروز" }),
					/* @__PURE__ */ jsx("a", {
						className: "primary-action",
						href: "#posts-series",
						children: "ادامه"
					})
				]
			})]
		})
	});
	return /* @__PURE__ */ jsxs("section", {
		className: "hero reference-hero",
		"aria-roledescription": "carousel",
		"aria-label": "بنرهای برگزیده",
		children: [banners.map((banner, index) => /* @__PURE__ */ jsxs("article", {
			className: `hero-slide ${index === active ? "is-active" : ""}`,
			"aria-hidden": index !== active,
			children: [/* @__PURE__ */ jsx("img", {
				src: banner.imageUrl,
				alt: banner.title
			}), /* @__PURE__ */ jsxs("div", {
				className: "hero-content",
				children: [
					/* @__PURE__ */ jsx("span", {
						className: "hero-category",
						children: banner.title
					}),
					/* @__PURE__ */ jsx("h1", { children: banner.title }),
					banner.subtitle && /* @__PURE__ */ jsx("p", { children: banner.subtitle }),
					banner.linkUrl && /* @__PURE__ */ jsx("a", {
						className: "primary-action",
						href: banner.linkUrl,
						target: /^https?:\/\//i.test(banner.linkUrl) ? "_blank" : void 0,
						rel: /^https?:\/\//i.test(banner.linkUrl) ? "noreferrer" : void 0,
						tabIndex: index === active ? 0 : -1,
						children: banner.linkLabel || "ادامه"
					})
				]
			})]
		}, banner.id)), banners.length > 1 && /* @__PURE__ */ jsx("div", {
			className: "carousel-controls",
			children: /* @__PURE__ */ jsx("div", {
				className: "carousel-dots",
				children: banners.map((banner, index) => /* @__PURE__ */ jsx("button", {
					type: "button",
					className: index === active ? "is-active" : "",
					onClick: () => setActive(index),
					"aria-label": `نمایش بنر ${index + 1}`,
					"aria-current": index === active
				}, banner.id))
			})
		})]
	});
}
//#endregion
//#region resources/js/Components/ContentCardGrid.tsx
var kindLabels = {
	post: fa.contentTypes.post,
	series: fa.contentTypes.series,
	course: fa.contentTypes.course,
	book: fa.contentTypes.book,
	announcement: fa.contentTypes.announcement
};
function ContentCardGrid({ items }) {
	if (items.length === 0) return /* @__PURE__ */ jsx("p", {
		className: "empty-copy",
		children: "محتوایی برای نمایش وجود ندارد."
	});
	return /* @__PURE__ */ jsx("div", {
		className: "content-card-grid",
		children: items.map((item) => /* @__PURE__ */ jsxs("article", {
			className: "content-card",
			children: [/* @__PURE__ */ jsxs(Link, {
				href: item.url,
				className: "content-card-image",
				children: [item.coverImageUrl ? /* @__PURE__ */ jsx("img", {
					src: item.coverImageUrl,
					alt: item.title,
					loading: "lazy"
				}) : /* @__PURE__ */ jsx("span", {
					className: "content-placeholder",
					"aria-hidden": "true",
					children: "اندیشه"
				}), /* @__PURE__ */ jsx("span", {
					className: "content-card-kind",
					children: kindLabels[item.type]
				})]
			}), /* @__PURE__ */ jsxs("div", {
				className: "content-card-body",
				children: [/* @__PURE__ */ jsx("h3", { children: /* @__PURE__ */ jsx(Link, {
					href: item.url,
					children: item.title
				}) }), item.excerpt && /* @__PURE__ */ jsx("p", { children: item.excerpt })]
			})]
		}, item.type + "-" + item.id))
	});
}
//#endregion
//#region resources/js/Pages/Home.tsx
var Home_exports = /* @__PURE__ */ __exportAll({ default: () => Home });
var contactHref = (method) => {
	const value = method.value.trim();
	if (!value) return null;
	if (method.type === "phone" || method.type === "mobile") return "tel:" + value;
	if (method.type === "email") return "mailto:" + value;
	if (/^https?:\/\//i.test(value)) return value;
	const handle = value.replace(/^@/, "");
	if (method.type === "telegram") return `https://t.me/${handle}`;
	if (method.type === "instagram") return `https://instagram.com/${handle}`;
	if (method.type === "eitaa") return `https://eitaa.com/${handle}`;
	if (method.type === "whatsapp") {
		const phone = value.replace(/\D/g, "");
		return phone ? `https://wa.me/${phone}` : null;
	}
	return null;
};
function SectionHeading({ eyebrow, title, moreHref }) {
	return /* @__PURE__ */ jsxs("header", {
		className: "section-heading",
		children: [/* @__PURE__ */ jsxs("div", { children: [/* @__PURE__ */ jsx("span", { children: eyebrow }), /* @__PURE__ */ jsx("h2", { children: title })] }), moreHref && /* @__PURE__ */ jsxs(Link, {
			className: "section-more",
			href: moreHref,
			children: [fa.actions.showMore, " ←"]
		})]
	});
}
function Home({ banners, postsAndSeries, announcements, courses, books, about, registration, contactMethods }) {
	return /* @__PURE__ */ jsxs(SiteLayout, { children: [
		/* @__PURE__ */ jsx("div", { id: "top" }),
		/* @__PURE__ */ jsx(BannerCarousel, { banners }),
		/* @__PURE__ */ jsxs("section", {
			className: "home-section shell announcements-section",
			id: "announcements",
			children: [/* @__PURE__ */ jsx(SectionHeading, {
				eyebrow: "تازه‌ها",
				title: fa.sections.announcements,
				moreHref: "/announcements"
			}), /* @__PURE__ */ jsx(AnnouncementList, { items: announcements })]
		}),
		/* @__PURE__ */ jsxs("section", {
			className: "home-section shell",
			id: "posts-series",
			children: [/* @__PURE__ */ jsx(SectionHeading, {
				eyebrow: "خواندنی‌ها",
				title: fa.sections.postsAndSeries,
				moreHref: "/posts"
			}), /* @__PURE__ */ jsx(ContentCardGrid, { items: postsAndSeries.slice(0, 8) })]
		}),
		/* @__PURE__ */ jsxs("section", {
			className: "home-section shell",
			id: "courses",
			children: [/* @__PURE__ */ jsx(SectionHeading, {
				eyebrow: "آموزش",
				title: fa.sections.courses,
				moreHref: "/courses"
			}), /* @__PURE__ */ jsx(ContentCardGrid, { items: courses })]
		}),
		/* @__PURE__ */ jsxs("section", {
			className: "home-section shell",
			id: "books",
			children: [/* @__PURE__ */ jsx(SectionHeading, {
				eyebrow: "مطالعه",
				title: fa.sections.books,
				moreHref: "/books"
			}), /* @__PURE__ */ jsx(ContentCardGrid, { items: books })]
		}),
		/* @__PURE__ */ jsx("section", {
			className: "info-section shell",
			id: "about",
			children: /* @__PURE__ */ jsxs("div", { children: [/* @__PURE__ */ jsx(SectionHeading, {
				eyebrow: "آشنایی",
				title: about.title
			}), /* @__PURE__ */ jsx("div", {
				className: "section-rich-text",
				dangerouslySetInnerHTML: { __html: about.body }
			})] })
		}),
		/* @__PURE__ */ jsxs("section", {
			className: "cta-section shell",
			id: "registration",
			children: [/* @__PURE__ */ jsxs("div", { children: [/* @__PURE__ */ jsx(SectionHeading, {
				eyebrow: "همراهی",
				title: registration.title
			}), /* @__PURE__ */ jsx("div", {
				className: "section-rich-text",
				dangerouslySetInnerHTML: { __html: registration.body }
			})] }), registration.url && /* @__PURE__ */ jsx("a", {
				className: "primary-action",
				href: registration.url,
				target: "_blank",
				rel: "noreferrer",
				children: "تکمیل فرم ثبت‌نام"
			})]
		}),
		/* @__PURE__ */ jsxs("section", {
			className: "contact-section shell",
			id: "contact",
			children: [/* @__PURE__ */ jsx(SectionHeading, {
				eyebrow: "در تماس باشیم",
				title: fa.sections.contact
			}), contactMethods.length > 0 ? /* @__PURE__ */ jsx("div", {
				className: "contact-grid",
				children: contactMethods.map((method) => {
					const href = contactHref(method);
					return href ? /* @__PURE__ */ jsxs("a", {
						href,
						target: href.startsWith("http") ? "_blank" : void 0,
						rel: href.startsWith("http") ? "noreferrer" : void 0,
						children: [/* @__PURE__ */ jsx("strong", { children: method.label }), /* @__PURE__ */ jsx("span", {
							dir: "auto",
							children: method.value
						})]
					}, method.label + method.value) : /* @__PURE__ */ jsxs("div", { children: [/* @__PURE__ */ jsx("strong", { children: method.label }), /* @__PURE__ */ jsx("span", {
						dir: "auto",
						children: method.value
					})] }, method.label + method.value);
				})
			}) : /* @__PURE__ */ jsx("p", {
				className: "empty-copy",
				children: "اطلاعات تماس به‌زودی تکمیل می‌شود."
			})]
		})
	] });
}
//#endregion
//#region resources/js/Pages/Listing.tsx
var Listing_exports = /* @__PURE__ */ __exportAll({ default: () => Listing });
function Listing({ title, eyebrow, kind, items }) {
	return /* @__PURE__ */ jsx(SiteLayout, {
		title,
		children: /* @__PURE__ */ jsxs("section", {
			className: "shell listing-page",
			children: [/* @__PURE__ */ jsx("header", {
				className: "section-heading listing-heading",
				children: /* @__PURE__ */ jsxs("div", { children: [/* @__PURE__ */ jsx("span", { children: eyebrow }), /* @__PURE__ */ jsx("h1", { children: title })] })
			}), kind === "announcements" ? /* @__PURE__ */ jsx(AnnouncementList, { items }) : /* @__PURE__ */ jsx(ContentCardGrid, { items })]
		})
	});
}
//#endregion
//#region resources/js/Pages/Posts/Show.tsx
var Show_exports$1 = /* @__PURE__ */ __exportAll({ default: () => Show$1 });
function Show$1({ post }) {
	return /* @__PURE__ */ jsx(SiteLayout, {
		title: post.title,
		children: /* @__PURE__ */ jsxs("article", {
			className: "article-page shell",
			children: [
				/* @__PURE__ */ jsxs("header", {
					className: "article-header",
					children: [
						post.series && /* @__PURE__ */ jsx(Link, {
							href: `/series/${post.series.slug}`,
							className: "article-series",
							children: post.series.title
						}),
						/* @__PURE__ */ jsx("h1", { children: post.title }),
						post.excerpt && /* @__PURE__ */ jsx("p", { children: post.excerpt }),
						post.coverImageUrl && /* @__PURE__ */ jsx("img", {
							src: post.coverImageUrl,
							alt: post.title
						})
					]
				}),
				/* @__PURE__ */ jsx(ContentBlocks, { blocks: post.contentBlocks }),
				/* @__PURE__ */ jsx(RelatedLinks, { links: post.links })
			]
		})
	});
}
//#endregion
//#region resources/js/Components/PostList.tsx
var persianDate = new Intl.DateTimeFormat("fa-IR", {
	day: "numeric",
	month: "long",
	year: "numeric"
});
function PostList({ posts }) {
	return /* @__PURE__ */ jsx("div", {
		className: "post-grid",
		children: posts.map((post) => /* @__PURE__ */ jsxs("article", { children: [/* @__PURE__ */ jsxs(Link, {
			href: `/posts/${post.slug}`,
			className: "post-card-image",
			children: [post.coverImageUrl ? /* @__PURE__ */ jsx("img", {
				src: post.coverImageUrl,
				alt: post.title,
				loading: "lazy"
			}) : /* @__PURE__ */ jsx("span", {
				className: "post-placeholder",
				"aria-hidden": "true",
				children: /* @__PURE__ */ jsx("b", { children: "اندیشه" })
			}), /* @__PURE__ */ jsx("span", {
				className: "post-kind",
				children: "مطلب"
			})]
		}), /* @__PURE__ */ jsxs("div", {
			className: "post-card-body",
			children: [
				post.publishedAt && /* @__PURE__ */ jsx("span", {
					className: "post-meta",
					children: /* @__PURE__ */ jsx("time", {
						dateTime: post.publishedAt,
						children: persianDate.format(new Date(post.publishedAt))
					})
				}),
				/* @__PURE__ */ jsx("h3", { children: /* @__PURE__ */ jsx(Link, {
					href: `/posts/${post.slug}`,
					children: post.title
				}) }),
				post.excerpt && /* @__PURE__ */ jsx("p", { children: post.excerpt }),
				/* @__PURE__ */ jsx(Link, {
					href: `/posts/${post.slug}`,
					className: "continue-button",
					children: "ادامه ..."
				})
			]
		})] }, post.slug))
	});
}
//#endregion
//#region resources/js/Pages/Series/Show.tsx
var Show_exports = /* @__PURE__ */ __exportAll({ default: () => Show });
function Show({ series }) {
	return /* @__PURE__ */ jsxs(SiteLayout, {
		title: series.title,
		children: [/* @__PURE__ */ jsx("section", {
			className: "page-hero",
			children: /* @__PURE__ */ jsxs("div", {
				className: "shell page-hero-grid",
				children: [/* @__PURE__ */ jsxs("div", { children: [
					/* @__PURE__ */ jsx("span", {
						className: "eyebrow",
						children: "مجموعه"
					}),
					/* @__PURE__ */ jsx("h1", { children: series.title }),
					series.description && /* @__PURE__ */ jsx("p", { children: series.description })
				] }), series.coverImageUrl && /* @__PURE__ */ jsx("img", {
					src: series.coverImageUrl,
					alt: series.title
				})]
			})
		}), /* @__PURE__ */ jsxs("section", {
			className: "shell listing-page",
			children: [/* @__PURE__ */ jsx("h2", { children: "نوشته‌های این مجموعه" }), series.posts.length > 0 ? /* @__PURE__ */ jsx(PostList, { posts: series.posts }) : /* @__PURE__ */ jsx("p", {
				className: "muted",
				children: "هنوز نوشته‌ای منتشر نشده است."
			})]
		})]
	});
}
//#endregion
//#region resources/js/ssr.tsx
createServer((page) => createInertiaApp({
	page,
	render: ReactDOMServer.renderToString,
	title: (title) => title ? `${title} | اندیشکده` : "اندیشکده",
	resolve: (name) => {
		const resolved = (/* @__PURE__ */ Object.assign({
			"./Pages/Content/Show.tsx": Show_exports$3,
			"./Pages/Donation/Show.tsx": Show_exports$2,
			"./Pages/Home.tsx": Home_exports,
			"./Pages/Listing.tsx": Listing_exports,
			"./Pages/Posts/Show.tsx": Show_exports$1,
			"./Pages/Series/Show.tsx": Show_exports
		}))[`./Pages/${name}.tsx`];
		if (!resolved) throw new Error(`Inertia page not found: ${name}`);
		return resolved.default;
	},
	setup: ({ App, props }) => /* @__PURE__ */ jsx(App, { ...props })
}));
//#endregion
export {};
