---
name: test-audit-chart
description: Generates a standalone SVG donut chart from an existing test-audit-*.md report file in a TYPO3 extension directory. Use when the user asks to "generate chart", "create SVG", "visualize the audit", or "draw chart" for a previously run test audit.
argument-hint: <extension-path-or-name>
---

# TYPO3 Test Audit Chart

## Steps

**1. Resolve root** — absolute → `packages/<arg>` → `packages-dev/<arg>`. Ask if no argument.

**2. Find report** — `find "$ext_root" -maxdepth 1 -name "test-audit-*.md" | sort`. None → stop; one → use; multiple → ask.

**3. Read frontmatter** — extract: `extension`, `classes_total`→`total`, `unit`, `edge`, `functional`, `not_testable`.

**4. Generate SVG** — write `$ext_root/test-audit-{extension}.svg`.

**5. Confirm:**
```
✔ SVG  → <absolute .svg path>
  from → <absolute .md path>
```

## SVG Spec

**Constraints (hard):** No single merged segment · No `filter` attribute · No HTML wrapper · Skip any segment where count = 0.

Font shorthand (use everywhere): `font-family="system-ui,-apple-system,sans-serif"`

**Canvas:** `width="580" height="440"` · `<rect width="580" height="440" fill="#ffffff" rx="12" stroke="#e0e0e0" stroke-width="1"/>`

**Title:**
```xml
<text x="290" y="34" text-anchor="middle" [font] font-size="15" font-weight="600" fill="#1a1a2e">Test Audit &#x2014; {extension}</text>
<text x="290" y="54" text-anchor="middle" [font] font-size="11" fill="#999">{total} classes analyzed</text>
```

**Donut** — `cx=165 cy=175 r_o=118 r_i=64`, arc stroke `#ffffff` width `2`, start=−90°, clockwise.

Arc formula:
```
end = start + count/total*360
large_arc = 1 if span>180 else 0
d = "M sx,sy A r_o,r_o 0 {large_arc},1 ex,ey L ix,iy A r_i,r_i 0 {large_arc},0 jx,jy Z"
```

Segments in order:

| Label | Field | Color |
|---|---|---|
| Unit | `unit` | `#66BB6A` |
| Edge | `edge` | `#FFB74D` |
| Functional Test | `functional` | `#2196F3` |
| Not testable | `not_testable` | `#9E9E9E` |

**Center — exactly 2 lines:**
```xml
<text x="165" y="180" text-anchor="middle" [font] font-size="28" font-weight="700" fill="#1a1a2e">{total}</text>
<text x="165" y="200" text-anchor="middle" [font] font-size="10" fill="#aaa">classes</text>
```

**Legend** — `rect_y=74`, step `46px`, skip count=0. Per item:
```xml
<rect x="318" y="{rect_y}" width="13" height="13" fill="{color}" rx="3"/>
<text x="339" y="{rect_y+11}" [font] font-size="12" fill="#333">{label}</text>
<text x="339" y="{rect_y+27}" [font] font-size="11" fill="#888">{count} &#xB7; {pct}%</text>
<rect x="339" y="{rect_y+33}" width="150" height="4" fill="#eeeeee" rx="2"/>
<rect x="339" y="{rect_y+33}" width="{round(count/total*150)}" height="4" fill="{color}" rx="2"/>
```

**Footer — exactly these 5 elements:**
```xml
<line x1="20" y1="390" x2="560" y2="390" stroke="#e0e0e0" stroke-width="1"/>
<text x="20"  y="407" [font] font-size="10" font-weight="600" fill="#555">Unit</text>
<text x="44"  y="407" [font] font-size="10" fill="#888">= suitable for unit tests &#xB7; </text>
<text x="212" y="407" [font] font-size="10" font-weight="600" fill="#555">Edge</text>
<text x="236" y="407" [font] font-size="10" fill="#888">= both unit and functional viable</text>
```
