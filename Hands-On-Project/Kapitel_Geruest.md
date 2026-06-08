# KI PHPUnit Test Generator
## Automatische Generierung von PHPUnit-Tests unter TYPO3-Infrastruktur

**Autor:** Sinian Zhang
**CAS:** AI for Software Engineering, FHNW
**Datum:** August 2026

---

## Titelblatt

| | |
|---|---|
| **Titel** | KI PHPUnit Test Generator |
| **Untertitel** | Automatische Generierung von PHPUnit-Tests unter TYPO3-Infrastruktur |
| **Autor** | Sinian Zhang |
| **Studiengang** | CAS AI for Software Engineering, FHNW |
| **Abgabe** | August 2026 |
| **Betreuung** | [Name Dozent/in] |

---

## 1. Einleitung

### 1.1 Problembeschreibung

Die Qualitätssicherung in der Software-Entwicklung basiert auf automatisierten Tests.
In der Praxis fehlen diese jedoch häufig — nicht aus mangelndem Bewusstsein, sondern
weil das Schreiben von Tests zeitaufwändig und repetitiv ist.

Das manuelle Schreiben von PHPUnit-Tests erfordert:
- Tiefes Verständnis des zu testenden Codes
- Kenntnis des TYPO3 Testing Frameworks
- Erfahrung mit Dependency Injection, Mocking und DataProviders

Da Entwickler unter Zeitdruck oft nur die Geschäftslogik implementieren, bleibt die
Testabdeckung in vielen TYPO3-Projekten bei 0%.

**Zentrale Forschungsfrage:** Kann ein LLM (Claude API) PHP-Methoden analysieren
und daraus korrekte, ausführbare PHPUnit-Tests generieren?

**Zusätzliche Forschungsfrage (Anforderungswissen):** Welchen Mehrwert bringt es,
dem LLM neben dem Code auch Anforderungen (PHPDoc, Kommentare) als Kontext zu geben?

Die beiden Forschungsfragen werden durch zwei parallele Varianten operationalisiert:
- **Variante A – Code only:** Nur PHP-Quellcode als LLM-Input. Ergebnis: technisch korrekte, aber oberflächliche Tests; Geschäftsregeln und Edge-Cases werden oft übersehen.
- **Variante B – Code + Kontext:** PHP-Code plus PHPDoc und Inline-Kommentare als LLM-Input. Ergebnis: Tests prüfen auch Grenzwerte und Geschäftsregeln.

Beide Varianten werden für dieselben 5 Klassen durchgeführt und direkt verglichen.

### 1.2 Organisatorische Einbettung und Abgrenzungen

#### Unternehmenskontext

Die vorliegende Arbeit entsteht im Rahmen der beruflichen Tätigkeit des Autors bei der Hausformat GmbH, einer Agentur mit Fokus auf TYPO3-Webentwicklung. Im Tagesgeschäft sind Backend-Entwicklerinnen und -Entwickler für die Implementierung und Pflege von TYPO3-Extensions zuständig. Automatisierte Tests sind dabei zwar bekannt, werden jedoch aufgrund des hohen manuellen Aufwands in der Praxis selten konsequent umgesetzt. Diese Arbeit setzt genau an diesem Punkt an: Sie untersucht, ob ein Large Language Model (LLM) diesen Aufwand so weit reduzieren kann, dass Unit-Tests zum Standardbestandteil jedes Entwicklungszyklus werden.

#### Projektkontext und Demo-Extension

Als technische Basis dient das TYPO3-Demoprojekt `cas_ai_phpunit` (`typo3-projects/cas_ai_phpunit`), das lokal mit DDEV (Docker-basierte Entwicklungsumgebung) betrieben wird. Innerhalb dieses Projekts wird die Extension `hf-view-helpers` (`packages/hf-view-helpers`) als Testgegenstand verwendet. Diese Extension bündelt über 40 TYPO3 Fluid-ViewHelper-Klassen, die im Produktivbetrieb eingesetzt werden. Ein vorgängig durchgeführter Test-Audit (mittels des Claude-Code-CLI-Plugins `typo3-test-audit`) hat ergeben, dass von 47 PHP-Klassen lediglich 10 über bestehende Unit-Tests verfügen. Für 25 weitere Klassen wären Unit- oder Edge-Tests technisch möglich — genau diese Lücke adressiert der KI-Testgenerator.

#### Systemlandschaft

```
┌─────────────────────────────────────────────────────────┐
│  Entwickler (Hausformat GmbH)                           │
│  Claude Code CLI + Plugin typo3-test-audit              │
└───────────────────┬─────────────────────────────────────┘
                    │ generiert Prompt
                    ▼
┌───────────────────────────────┐     ┌───────────────────┐
│  Claude API (Anthropic)       │────▶│  PHPUnit-Test.php │
│  claude-sonnet-4-6            │     │  (generiert)      │
└───────────────────────────────┘     └────────┬──────────┘
                                               │ ausführen
┌─────────────────────────────────┐            ▼
│  DDEV / Docker                  │  ┌─────────────────────┐
│  ┌─────────────────────────┐    │  │  PHPUnit 12         │
│  │  TYPO3 14 / PHP 8.3     │    │  │  TYPO3 Testing-     │
│  │  hf-view-helpers        │    │  │  Framework          │
│  │  (47 Klassen)           │    │  │  Xdebug / Coverage  │
│  └─────────────────────────┘    │  └─────────────────────┘
│  MariaDB 10.11                  │
└─────────────────────────────────┘
```

#### Abgrenzungen

Diese Arbeit ist bewusst eingegrenzt, um innerhalb des CAS-Rahmens klare und messbare Ergebnisse zu liefern:

| Thema | Im Scope | Ausserhalb Scope |
|---|---|---|
| Test-Typ | PHPUnit Unit-Tests | Functional-, Integration- und E2E-Tests |
| Technologie | PHP 8.3, TYPO3 14, PHPUnit 12 | Andere Frameworks oder Sprachen |
| Testfälle | 5 ausgewählte Klassen aus `hf-view-helpers` | Gesamte Extension oder andere Projekte |
| LLM | Claude API (Anthropic) | Andere LLMs (GPT, Gemini, Llama) |
| CI/CD | Manuelles Ausführen via DDEV | Vollautomatische Pipeline-Integration |
| Deployment | Lokale Entwicklungsumgebung | Produktivsystem oder Staging |

Die Eingrenzung auf Unit-Tests und auf 5 Klassen ist methodisch bewusst gewählt: Sie ermöglicht einen fairen, kontrollierten Vergleich zwischen manuell erstellten und KI-generierten Tests mit messbaren Metriken.

### 1.3 Unternehmens- und Projektziele

| Ziel | Beschreibung | Priorität |
|---|---|---|
| **Z1** | Funktionierendes Skript: PHP-Klasse → PHPUnit-Testklasse via LLM | **Muss** ✓ |
| **Z2** | Vergleichsanalyse KI vs. manuell mit messbaren Metriken (Variante A und B) | **Muss** ✓ |
| **Z3** | Empfehlungen: Welche Klassen-Typen eignen sich für KI-Generierung? | Kann |
| **Z4** | Prompt-Framework mit TYPO3-spezifischem Kontext (DI, Mocking) | Kann |

Z1 und Z2 sind das Minimum der Arbeit. Z3 und Z4 werden nur umgesetzt, wenn nach Z1/Z2 noch Zeit bleibt.

### 1.4 Stakeholder-Analyse

| Stakeholder | Interesse | Einfluss auf Arbeit |
|---|---|---|
| Sinian Zhang (Entwickler) | Zeitersparnis, Lerngewinn PHPUnit | Hoch |
| Hausformat GmbH | Höhere Code-Qualität, schnellere CI/CD | Mittel |
| TYPO3-Community | Praxisnachweis KI-Testgenerierung unter TYPO3 | Niedrig |
| FHNW / Dozent | Wissenschaftlicher Beitrag, Messbarkeit, Hypothesen | Hoch |

### 1.5 Kontextanalyse

**Technischer Stack:**
- TYPO3 14.3, PHP 8.3, MariaDB 10.11
- DDEV (Docker-basierte Entwicklungsumgebung)
- PHPUnit 12 + TYPO3 TestingFramework
- Claude API (Anthropic) als LLM

**Referenzen:**
- TYPO3 Unit Testing Documentation:
  https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/Testing/UnitTesting/Introduction.html
- Laravel PAO (agent-optimized PHP output): https://github.com/laravel/pao

---

## 2. Planung

### 2.1 Wertstromanalyse

**Aktueller Zustand (Ist-Prozess):**
```
PHP-Klasse wird geschrieben
    ↓
Manuelle Test-Erstellung (optional, oft weggelassen)
    ↓
CI/CD ohne Testabdeckung
    ↓
Bugs erst in Produktion entdeckt
```

**Zielzustand (Soll-Prozess mit KI-Unterstützung):**
```
PHP-Klasse wird geschrieben
    ↓
KI-Generator erzeugt PHPUnit-Tests (< 1 Min)
    ↓
PHPUnit ausführen, ggf. manuell nachbessern
    ↓
CI/CD mit Testabdeckung
    ↓
Frühere Fehlererkennung
```

### 2.2 Geplanter KI-Einsatz

**Vollständiger Ablauf — drei Abschnitte:**

```
╔══════════════════════════════════════════════════════╗
║  ABSCHNITT 1: INPUT & VORBEREITUNG                   ║
╠══════════════════════════════════════════════════════╣
║                                                      ║
║  PHP-Klasse (z.B. JsonDecodeViewHelper.php)          ║
║       ↓                                              ║
║  Code-Analyse (typo3-test-audit Plugin)              ║
║  - public Methoden identifizieren                    ║
║  - Abhängigkeiten prüfen (DI, GeneralUtility, etc.)  ║
║  - Klassifizierung: Unit / Edge / Functional         ║
║       ↓                                              ║
║  Prompt zusammenbauen                                ║
║  Variante A: nur PHP-Quellcode                       ║
║  Variante B: PHP-Code + PHPDoc + Kommentare          ║
╚══════════════════════════════════════════════════════╝
       ↓
╔══════════════════════════════════════════════════════╗
║  ABSCHNITT 2: AUSFÜHRUNG & VALIDIERUNG               ║
╠══════════════════════════════════════════════════════╣
║                                                      ║
║  LLM (Claude API)                                    ║
║       ↓                                              ║
║  Ausgabe: PHP-Testklasse als Text                    ║
║       ↓                                              ║
║  Test speichern (Tests/Unit/...Test.php)             ║
║       ↓                                              ║
║  PHPUnit ausführen (ddev exec phpunit)               ║
║       ↓                                              ║
║  ┌──────────────────────────────┐                   ║
║  │ GREEN  → sofort nutzbar      │                   ║
║  │ FAIL   → manuell nachbessern │                   ║
║  └──────────────────────────────┘                   ║
║       ↓                                             ║
║  Effekte messen:                                    ║
║  - Zeit: Generierung + Review + Korrekturen (Min.)  ║
║  - Code Coverage (Line %)                           ║
║  - Anzahl GREEN-Tests ohne Änderung                 ║
╚══════════════════════════════════════════════════════╝
       ↓
╔══════════════════════════════════════════════════════╗
║  ABSCHNITT 3: BEWERTUNG & ERKENNTNISSE               ║
╠══════════════════════════════════════════════════════╣
║                                                      ║
║  Vergleich: Manuell vs. KI (Var. A) vs. KI (Var. B)║
║  Kriterien:                                          ║
║  - Laufen Tests durch? (ja / nein / nach Korrektur) ║
║  - Code Coverage Line (%)                            ║
║  - Assertions sinnvoll? (Edge-Cases, Grenzwerte)    ║
║  - Mocking korrekt? (TYPO3-Dependencies)            ║
║  - Zeitersparnis gegenüber manuell                  ║
╚══════════════════════════════════════════════════════╝
```

**Zwei Varianten für Anforderungswissen:**

| Variante | LLM-Input | Erwartetes Ergebnis |
|---|---|---|
| **A – Code only** | PHP-Quellcode | Technisch korrekte, aber oberflächliche Tests; fehlende Edge-Cases |
| **B – Code + Kontext** | PHP-Code + PHPDoc + Kommentare | Tests prüfen auch Geschäftsregeln und Grenzwerte |

### 2.3 Hypothesen und erwartete Benefits

| Metrik | Status Quo (Manuell) | Hypothese (KI) | Erwarteter Messwert |
|---|---|---|---|
| Erstellungszeit (Generierung + Review + Korrekturen) | ~15 Min/Klasse | sehr niedrig | < 1 Min/Klasse |
| Testabdeckung (Line Coverage %) | 0% (keine Tests vorhanden) | deutlich höher | > 65% |
| Validität (sofort ausführbar, GREEN) | 100% (da manuell) | evtl. fehlerhaft | > 75% GREEN ohne Korrekturen |
| Mocking-Korrektheit (TYPO3-Dependencies) | 100% | KI unsicherer | < 50% korrekt ohne Nacharbeit (Grenze der KI) |
| Assertions-Qualität: Variante B vs. A (Skala 1–5) | 5 (manuell als Referenz) | B besser als A | Variante B ≥ +1 Punkt gegenüber A |

**Hypothesen im Klartext:**
- **H1:** KI reduziert die Erstellungszeit von ~15 Min auf < 1 Min pro Klasse.
- **H2:** KI erreicht > 65% Line Coverage bei reiner Logik (Format-ViewHelper).
- **H3:** > 75% der generierten Tests laufen ohne manuelle Korrekturen durch.
- **H4:** Bei Glue-Code mit TYPO3-Dependencies erreicht die KI weniger als 50% Mocking-Korrektheit ohne Nacharbeit — dies zeigt die Grenzen der automatischen Generierung.
- **H5:** Variante B (Code + PHPDoc) erzielt auf der Assertions-Qualitätsskala (1–5) mindestens 1 Punkt mehr als Variante A (Code only).

### 2.4 Vorgehen zur Einführung und Validierung

**Ausgewählte 5 Testklassen aus `packages/hf-view-helpers`:**

| # | Klasse | Dateipfad | Typ | Tests vorhanden |
|---|---|---|---|---|
| 1 | `JsonDecodeViewHelper` | `Classes/ViewHelpers/Format/JsonDecodeViewHelper.php` | Reine Logik | Nein ← ideal |
| 2 | `CleanHtmlViewHelper` | `Classes/ViewHelpers/Format/CleanHtmlViewHelper.php` | Reine Logik (Regex) | Nein ← ideal |
| 3 | `RoundViewHelper` | `Classes/ViewHelpers/Format/RoundViewHelper.php` | Reine Logik (Mathe) | Nein ← ideal |
| 4 | `Service` | `Classes/Dummy/Service.php` | Glue-Code leicht | Ja (als Referenz) |
| 5 | `DateViewHelper` | `Classes/ViewHelpers/Format/DateViewHelper.php` | Glue-Code mittel | Teilweise |

**Begründung der Auswahl:**
- Klassen 1–3: keine bestehenden Unit-Tests (0% Coverage), reine PHP-Logik ohne TYPO3-Dependencies → idealer Startpunkt, klares Messergebnis möglich
- Klasse 4: überschaubare Dependency Injection (`ErrorHandler`) → kontrollierter Einstieg in Mocking
- Klasse 5: realistische TYPO3-Abhängigkeit (`DateUtility`, `DateTime`) → zeigt Grenzen der KI bei Glue-Code

Die Klassen stammen aus `packages/hf-view-helpers`, da dort der Test-Audit-Workflow (Plugin `typo3-test-audit`) bereits eingerichtet ist und alle 47 Klassen klassifiziert vorliegen (4 Unit, 21 Edge, 18 Functional, 4 nicht testbar). Die gewählten 5 Klassen decken bewusst den Bogen von reiner Logik bis zu TYPO3-Abhängigkeiten ab.

**Ablauf für jede Klasse:**
1. Manuelle Tests schreiben (Referenz) — Zeit stoppen
2. KI-Generator ausführen — Zeit stoppen
3. PHPUnit ausführen, Coverage messen
4. Ergebnisse in Vergleichstabelle eintragen

### 2.5 Beitrag der Arbeit

Diese Arbeit liefert:
1. Praktischen Nachweis, dass KI-Testgenerierung unter TYPO3 14 funktioniert
2. Messbare Vergleichsdaten (KI vs. manuell) für 5 reale Klassen
3. Empfehlungen: Für welche Klassen-Typen eignet sich KI-Generierung?
4. Erkenntnis: Wann verbessert zusätzlicher Kontext (Variante B) die Qualität?

---

## 3. Umsetzung

### 3.1 Tooling und Infrastruktur

| Tool | Zweck | Version |
|---|---|---|
| DDEV | Lokale TYPO3-Entwicklungsumgebung (Docker) | 1.x |
| TYPO3 | CMS-Framework | 14.3 |
| PHP | Laufzeit | 8.3 |
| PHPUnit | Test-Framework | 12 |
| TYPO3 TestingFramework | TYPO3-spezifische Test-Utilities (erweitert PHPUnit um TYPO3-Helpers) | aktuell |
| Claude API | LLM für Testgenerierung | claude-sonnet-4-6 |
| Xdebug / php-code-coverage | Code Coverage Messung | aktuell |
| Claude Code CLI Plugin `typo3-test-audit` | Drei-Skill-Workflow: Klassifizierung (test-audit-text) → Visualisierung (test-audit-chart) → Generierung (generate-unit-tests) | lokal |

**Plugin-Workflow `typo3-test-audit` im Detail:**
1. **`/test-audit-text`** — Analysiert alle PHP-Klassen der Extension via Grep, klassifiziert in Unit / Edge / Functional / Not testable, schreibt `test-audit-hf-view-helpers.md` und `.txt`
2. **`/test-audit-chart`** — Liest das `.md`-Report und erzeugt ein SVG-Donut-Chart als visuelle Übersicht
3. **`/generate-unit-tests`** — Liest das `.txt`-Report und generiert PHPUnit-Testklassen für alle Unit+Edge-Klassen (AAA-Pattern, DataProvider, TYPO3-Mocking)

### 3.2 Beispielbasierte Demonstration

Für jede der 5 Klassen wird dokumentiert:

**Input:**
- PHP-Quellcode der Klasse (Variante A)
- PHP-Quellcode + PHPDoc + Kommentare (Variante B)

**Prompt-Template:**
```
System: Du bist ein erfahrener TYPO3-Entwickler und PHPUnit-Experte.
        Erstelle Tests für PHP 8.3, PHPUnit 12, TYPO3 TestingFramework.

User: Analysiere folgende PHP-Klasse und generiere PHPUnit Unit-Tests.
      Anforderungen:
      - declare(strict_types=1), final class, Methoden public function(): void
      - Mindestens 3 Testmethoden pro public Methode (Happy-Path, Grenzwert, Fehlerfall)
      - #[DataProvider]-Attribut für parametrisierte Szenarien (PHPUnit 12)
      - Korrekte Namespaces (Hausformat\ViewHelpers\Tests\Unit\...)
      - Extend von TYPO3\TestingFramework\Core\Unit\UnitTestCase
      - #[Test]-Attribut statt @test-Docblock (PHPUnit 12)
      - AAA-Struktur: // Arrange, // Act, // Assert
      
      [PHP-Quellcode hier]
```

**Output:** Generierte PHP-Testklasse

**Messung:** PHPUnit-Ausgabe (Green/Red/Failures), Coverage-Report

### 3.3 Einbezug der Stakeholder

- **Hausformat-Team:** Review der generierten Tests auf Praxistauglichkeit
- **Dozent FHNW:** Begleitung der Methodik, Feedback zu Hypothesen und Auswertung

### 3.4 Pilotnutzung

Pilotprojekt: `packages/hf-view-helpers` im TYPO3-Projekt `cas_ai_phpunit`

Für alle 5 ausgewählten Klassen wird der vollständige Messzyklus durchgeführt:
1. Manuelle Tests schreiben (Referenz, Ergebnis: Manuell-Baseline)
2. KI Variante A generieren (Code only), PHPUnit ausführen, Ergebnis messen
3. KI Variante B generieren (Code + Kontext), PHPUnit ausführen, Ergebnis messen
4. Messprotokoll pro Klasse ausfüllen (siehe 3.5)

Ergebnisse fliessen vollständig in Kapitel 4 (Diskussion).

### 3.5 Messung und Beobachtung der Benefits und Effekte

#### Messmethodik

Für jede der 5 Klassen wird das Messprotokoll dreimal ausgefüllt: einmal für die manuelle Referenz, einmal für Variante A (Code only) und einmal für Variante B (Code + Kontext). Die ausgefüllten Protokolle finden sich vollständig im Anhang D.

Die Messung folgt diesen Regeln:

- **Erstellungszeit:**
  - *Manuell:* Stoppuhr startet beim ersten Tastendruck (erster Buchstabe des Testcodes) und endet, wenn PHPUnit erstmals GREEN zeigt. Korrekturen sind inbegriffen.
  - *KI (Variante A und B):* Stoppuhr startet beim Eingeben des Plugin-Befehls (`/generate-unit-tests`) in Claude Code CLI. Endet wenn PHPUnit GREEN zeigt — inklusive Wartezeit auf die LLM-Generierung und allfälliger manueller Korrekturen.

- **Anzahl manueller Korrekturen:** Eine Korrektur = eine gespeicherte Dateiänderung (ein Speichervorgang), nach der PHPUnit neu ausgeführt wird. Mehrere Zeilen in einem Speichervorgang zählen als 1 Korrektur. Manuell = immer 0 (Referenz).

- **Coverage:** Gemessen mit `XDEBUG_MODE=coverage` + `--coverage-text`, gefiltert auf die Zielklasse via `grep`. Ausgewertet wird ausschliesslich die Line Coverage der Zielklasse. **Gleicher Befehl für alle drei Varianten (Manuell, KI-A, KI-B):**
  ```bash
  ddev xdebug on
  ddev exec XDEBUG_MODE=coverage php vendor/bin/phpunit \
      -c packages/hf-view-helpers/Build/phpunit/UnitTests.xml \
      --coverage-text \
      packages/hf-view-helpers/Tests/Unit/ViewHelpers/Format/[Klasse]Test.php \
      2>&1 | grep -A3 "Format\\\\[Klasse]"
  ddev xdebug off
  ```
  Für Klassen ausserhalb `Format/` den Namespace anpassen, z.B.:
  ```bash
  # Service (Dummy\Service):
  2>&1 | grep -A3 "Dummy\\\\Service"
  ```

- **PHPStan Errors:** Statische Analyse mit Level 6 und PHPUnit-Extension. Zählt Typfehler, falsche Mock-Verwendung und andere statische Probleme, die PHPUnit nicht sieht. **Gleicher Befehl für alle drei Varianten — Manuell dient als Baseline (Ziel: 0 Errors):**
  ```bash
  ddev xdebug off
  ddev php vendor/bin/phpstan analyse \
      -c packages/hf-view-helpers/Build/phpstan/phpstan.tests.neon \
      packages/hf-view-helpers/Tests/Unit/ViewHelpers/Format/[Klasse]Test.php
  ```
  PHPStan-Fehler trotz grünem PHPUnit sind ein Qualitätsmerkmal, das nur statische Analyse aufdeckt.

- **Mocking korrekt:** Beurteilung durch den Autor anhand von drei Kriterien: korrekter Typ (`createMock` vs. `createStub`), korrekte Methode gemockt, kein unnötiges Mocking. Skala: ja / teilweise / nein.

- **Bewertungsskala Assertions (1–5):**

| Wert | Bedeutung |
|---|---|
| 5 | Alle Assertions sinnvoll, Edge-Cases und Grenzwerte abgedeckt, keine Tautologien |
| 4 | Kleinere Lücken (z.B. ein Grenzwert fehlt), aber grundsätzlich korrekt |
| 3 | Assertions vorhanden, aber oberflächlich — Happy-Path-lastig |
| 2 | Mehrere tautologische oder triviale Assertions (z.B. `assertInstanceOf`) |
| 1 | Tests bestehen formal, prüfen aber keine sinnvollen Eigenschaften |

---

#### Unterschied Prompt Variante A vs. Variante B

| Variante | LLM-Input | Was wird weggelassen |
|---|---|---|
| **A – Code only** | PHP-Quellcode (Methodenkörper und Signaturen) | PHPDoc-Blöcke (`/** ... */`) und Inline-Kommentare (`//`) werden entfernt |
| **B – Code + Kontext** | PHP-Quellcode vollständig, inklusive PHPDoc und allen Kommentaren | — |

Der Quellcode für Variante A wird vor dem Prompt manuell um alle Kommentarblöcke bereinigt. Variante B verwendet den Originalcode unverändert.

---

#### Messprotokoll — quantitative Metriken *(pro Klasse, Vorlage)*

| Kriterium | Manuell | KI Variante A | KI Variante B |
|---|---|---|---|
| Erstellungszeit gesamt (Min.) | [messen] | [messen] | [messen] |
| Code Coverage Line (%) | [messen] | [messen] | [messen] |
| Anzahl PHPUnit-Failures beim ersten Lauf | 0 | [zählen] | [zählen] |
| Anzahl manueller Korrekturen bis GREEN | 0 | [zählen] | [zählen] |
| PHPStan Errors (Level 6) | [messen] | [zählen] | [zählen] |

#### Messprotokoll — qualitative Bewertung *(pro Klasse, Vorlage)*

| Kriterium | Manuell | KI Variante A | KI Variante B |
|---|---|---|---|
| Mocking korrekt? (TYPO3-Dependencies) | Ja | [ja/teilw./nein] | [ja/teilw./nein] |
| Edge-Cases abgedeckt? (Grenzwerte, Sonderfälle) | Ja | [ja/teilw./nein] | [ja/teilw./nein] |
| Assertions sinnvoll? (Skala 1–5) | 5 | [1–5] | [1–5] |

---

#### Aggregationstabelle — Übersicht alle 5 Klassen *(wird nach Umsetzung ausgefüllt)*

| Klasse | Typ | Zeit Man. (Min.) | Zeit KI A (Min.) | Zeit KI B (Min.) | Coverage Man. (%) | Coverage A (%) | Coverage B (%) | Korrekt. A | Korrekt. B | PHPStan Man. | PHPStan A | PHPStan B | Assert. A (1–5) | Assert. B (1–5) |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| JsonDecodeViewHelper | Reine Logik | | | | | | | | | | | | | |
| CleanHtmlViewHelper | Reine Logik (Regex) | | | | | | | | | | | | | |
| RoundViewHelper | Reine Logik (Mathe) | | | | | | | | | | | | | |
| Service | Glue-Code leicht | | | | | | | | | | | | | |
| DateViewHelper | Glue-Code mittel | | | | | | | | | | | | | |
| **Ø / Gesamt** | | | | | | | | | | | | | | |

*Korrekt. = Anzahl manueller Korrekturen bis GREEN. Assert. = Assertions-Qualitätsskala 1–5.*

#### Bezug zu den Hypothesen

| Hypothese | Messgrösse | Zielwert | Ergebnis |
|---|---|---|---|
| H1: Zeitersparnis | Erstellungszeit (Min.) | < 1 Min. (KI) vs. ~15 Min. (manuell) | [nach Umsetzung] |
| H2: Coverage | Line Coverage (%) | > 65% | [nach Umsetzung] |
| H3: Validität | Anzahl Korrekturen bis GREEN | > 75% der Tests beim ersten Lauf GREEN | [nach Umsetzung] |
| H4: Mocking-Grenzen | Mocking korrekt (Glue-Code) | < 50% korrekt ohne Nacharbeit | [nach Umsetzung] |
| H5: Kontextvorteil | Assertions-Skala Var. B vs. A | Var. B ≥ +1 Punkt auf Skala 1–5 gegenüber A | [nach Umsetzung] |

---

## 4. Diskussion

### 4.1 Erreichte Ergebnisse

_[Wird nach Umsetzung ausgefüllt]_

- Wurden die Hypothesen aus Kapitel 2.3 bestätigt oder widerlegt?
- Wie verhält sich die KI bei reiner Logik vs. Glue-Code?
- Wie gross ist der Unterschied zwischen Variante A und Variante B?

### 4.2 Beitrag der Arbeit

_[Wird nach Umsetzung ausgefüllt]_

Erwartete Erkenntnisse:
- **Für welche Klassen-Typen eignet sich KI-Generierung?**
  - Reine Logik (Format-ViewHelpers): sehr gut geeignet
  - Glue-Code mit TYPO3-Mocking: eingeschränkt geeignet, Nacharbeit nötig
- **Wann ist zusätzlicher Kontext notwendig?**
  - Bei komplexen Geschäftsregeln, die nicht im Code sichtbar sind
  - Empfehlung: PHPDoc konsequent pflegen

### 4.3 Empfohlene nächste Schritte

- Integration des KI-Generators in CI/CD-Pipeline (Pre-Commit Hook)
- Erweiterung auf Functional Tests (TYPO3 TestingFramework)
- Veröffentlichung als TYPO3 Extension oder CLI-Tool (Packagist / GitHub)
- Weiterentwicklung des Prompt-Frameworks für TYPO3-spezifische Patterns (DI, Repositories)
- **Mutationstesting zur objektiven Qualitätsmessung:** In dieser Arbeit wurde die Assertion-Qualität subjektiv mit einer Skala 1–5 bewertet. Als nächster Schritt könnte [Infection](https://infection.github.io/) (PHP Mutation Testing Framework) eingesetzt werden. Infection verändert automatisch den Produktionscode (z.B. `>` → `>=`, `+` → `-`) und prüft, ob die Tests diese Mutationen erkennen. Das Ergebnis ist der **Mutation Score Indicator (MSI)** — eine objektive, automatisch berechnete Kennzahl für Assertion-Qualität. Da PHPUnit kein eingebautes Mutationstesting bietet, ist Infection als separate Abhängigkeit (`infection/infection`) zu installieren. Der erhebliche Laufzeit-Overhead (pro Mutation ein vollständiger PHPUnit-Lauf) war der Grund, dieses Werkzeug aus dem Scope dieser Arbeit auszuschliessen.

---

## Anhang

### A: Prompt-Template (vollständig)
_[Wird in Kapitel 3.2 definiert und hier vollständig abgedruckt]_

### B: Generierte Testklassen
- B1: JsonDecodeViewHelperTest.php (KI-generiert, Variante A + B)
- B2: CleanHtmlViewHelperTest.php (KI-generiert, Variante A + B)
- B3: RoundViewHelperTest.php (KI-generiert, Variante A + B)
- B4: ServiceTest.php (KI-generiert, Variante A + B)
- B5: DateViewHelperTest.php (KI-generiert, Variante A + B)

### C: PHPUnit-Ausgaben und Coverage-Reports
_[Screenshots / Textausgaben der PHPUnit-Läufe]_

### D: Ausgefüllte Messprotokolle (pro Klasse)

Die vollständig ausgefüllten Messprotokolle (quantitativ + qualitativ) für alle 5 Klassen finden sich hier nach Abschluss der Messungen. Die Aggregation aller Werte ist in der Aggregationstabelle in Kapitel 3.5 zusammengefasst.

_[Wird nach Umsetzung ausgefüllt — je ein Protokollblock pro Klasse (Manuell / KI-A / KI-B)]_
