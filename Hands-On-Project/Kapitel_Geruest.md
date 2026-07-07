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

Automatisierte Tests gelten als Grundpfeiler moderner Softwareentwicklung. Aus meiner langjährigen Erfahrung in verschiedenen Webagenturen mit TYPO3-Projekten zeigt sich jedoch: In der Praxis werden sie häufig weggelassen — nicht weil Entwicklerinnen und Entwickler ihren Nutzen nicht kennen, sondern weil der Aufwand im Projektalltag mit engem Zeitbudget schlicht zu hoch ist.

Genau diesen Engpass adressiert die vorliegende Arbeit. LLMs können PHP-Code analysieren und daraus Testcode generieren. Die zentrale Frage ist: Sind diese Tests praxistauglich — laufen sie durch, decken sie den Code sinnvoll ab, und erkennen sie echte Grenzwerte?

Zugleich ist mir die Arbeit eine Gelegenheit, sich intensiv mit einem modernen Toolset auseinanderzusetzen: Claude Code, PHPUnit mit dem TYPO3 Testing Framework, Infection für Mutationstests und PHPStan für die statische Analyse, etc.

Noch wichtig zu erwähnen: PHP-Klassen enthalten oft mehr Information als nur den ausführbaren Code — zum Beispiel in PHPDoc- oder Inline-Kommentaren, die erklären, welche Werte eine Methode erwartet, was sie zurückgibt und warum sie so implementiert wurde. Werden diese Informationen als zusätzlicher Input an das LLM übergeben, lassen sich sicherlich gezielter und qualitativ bessere Tests erzeugen. In dieser Arbeit werden Tests bewusst ohne solche Kommentare als Hinweis generiert, was zeigt, was das LLM allein aus dem Quellcode ableiten kann.

Untersucht wird dies anhand von fünf Klassen aus einer produktiven TYPO3-Extension. Die Klassen sind bewusst unterschiedlich gewählt — von einfacher Dummy-Logik über reine PHP-Logik bis hin zu komplexem TYPO3-abhängigem Code mit echtem Mocking-Bedarf. Die Ergebnisse werden quantitativ anhand von vier Kennzahlen gemessen: Erstellungszeit, Methodenabdeckung, PHPStan-Fehler und Mutation Score. Diese bilden zusammen die Grundlage für eine Einschätzung des Praxisnutzens KI-gestützter Testgenerierung.

### 1.2 Organisatorische Einbettung und Abgrenzungen

#### Unternehmenskontext

Die vorliegende Arbeit entsteht im Rahmen meiner beruflichen Tätigkeit beim ehemaligen Arbeitgeber, einer Agentur mit Fokus auf TYPO3-Webentwicklung. Im Tagesgeschäft sind Entwicklerinnen und Entwickler für die Implementierung und Pflege von TYPO3-Extensions zuständig. Automatisierte Tests sind zwar bekannt, wie oben schon erwähnt, werden jedoch aufgrund des hohen manuellen Aufwands selten konsequent umgesetzt.

Den direkten Anlass für diese Arbeit liefert eine konkrete Aufgabe: Für die Extension EXT:hf-view-helpers — eine intern entwickelte ViewHelper-Sammlung, die keine kundenspezifische Anwendung implementiert daher auch keine kundenspezifischen Daten enthält, die eher als Framework bei zahlreichen Kundenprojekten im Einsatz ist — sollte eine PHPUnit-Testumgebung aufgesetzt und nachträglich für einige zentrale Klassen Unit-Tests geschrieben werden. Die Extension hat viele Versionen, die betroffene ist eine Version mit der TYPO3-Version V14 kompatibel ist. 

Diese Arbeit setzt genau an diesem Punkt an: Sie untersucht, ob ein LLM diesen Aufwand so weit reduzieren kann, dass Unit-Tests zum Standardbestandteil jedes Entwicklungszyklus werden.

#### Projektkontext und Demo-Extension

Als technische Basis dient das TYPO3-Demoprojekt `cas_ai_phpunit` (https://github.com/sinianzhang/cas_ai_phpunit), das lokal mit DDEV (Docker-basierte Entwicklungsumgebung) betrieben wird. Innerhalb dieses Projekts wird die Extension 'hf-view-helpers' als Testgegenstand verwendet. Sie bündelt fast 50 TYPO3 Fluid-ViewHelper-Klassen, die im Produktivbetrieb eingesetzt werden.

Um die Testbarkeit der Klassen systematisch zu erfassen, wurde im Rahmen dieser Arbeit ein eigenes Claude-Code-Plugin entwickelt: 'typo3-test-audit'. Es enthält vier Skills, darunter der Skill 'test-audit-text' analysiert alle PHP-Klassen einer Extension und erstellt einen Überblick — wie viele Klassen existieren, welche sich für PHPUnit Unit-Tests eignen und welche funktionale Tests erfordern, jeweils mit kurzer Begründung. Die Auswahl der fünf Beispielklassen für diese Arbeit basiert auf diesem Audit-Report. 

Das Plugin ist nicht projektspezifisch und kann künftig bei beliebigen TYPO3-Projekten wiederverwendet werden — ein zusätzlicher Mehrwert/Benifit dieser Arbeit. Die Funktionsweise wird in Abschnitt 3.1 ausführlicher beschrieben.

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

### 1.3 Unternehmens-, Lern-, und Projektziele

Die Ziele dieser Arbeit lassen sich auf drei Ebenen betrachten.
**Unternehmensziele (für ehemaligen auch zukünfgiten Arbeitgeber)**
Es steht im Vordergrund, dass automatisierte Tests im Alltag tatsächlich geschrieben werden — und nicht nur in der Theorie sinnvoll wären. Wie in Abschnitt 1.1 beschrieben, fehlt im Projektgeschäft schlicht die Zeit dafür. 

Das Unternehmen erhofft sich von dieser Arbeit zwei konkrete Dinge für die Test-Klassen:
Weniger Aufwand: Wenn ein grosser Teil der Testerstellung durch KI übernommen (mit oder ohne Inline-Kommentar zur Generierung bestimmtes TestCases) werden kann, sinkt die Hürde, Tests überhaupt zu schreiben. 

Weniger Fehler: Das senkt langfristig das Risiko, dass die UnitTestClass selbst fehlerhaft ist und dadurch ein falsches Sicherheitsgefühl entsteht. Hier wird in erster Linie auf Fehler beschränkt, die statisch erkennbar sind.

**persönliche Lernziele**
Neben den beiden inhaltlichen Zielen ist mir die Arbeit auch persönlich wichtig, um mich als Entwickler weiterzuentwickeln. PHPUnit-Tests waren bislang nicht Teil meines Alltags — genau das macht diese Arbeit für mich zu einer guten Gelegenheit, mich intensiv und strukturiert mit dem Thema Testing auseinanderzusetzen: Wie schreibt man sinnvolle Testfälle, wie funktioniert Mocking von TYPO3-Abhängigkeiten, wie liest man einen Coverage-Report richtig, und was sagt ein Mutation Score tatsächlich aus? Gleichzeitig lerne ich den Umgang mit einem modernen Toolset, das über PHPUnit hinausgeht — insbesondere Claude Code als KI-gestützte Entwicklungsumgebung, PHPStan für die statische Analyse und Infection für Mutationstests. Dieses Wissen bleibt auch über die Arbeit hinaus nutzbar, sowohl für mich persönlich als auch für den Einsatz beim Arbeitgeber.

**Projektziele (CAS-Arbeit)**
Für die Arbeit selbst verfolge ich drei Hauptziele, die auch die Struktur der Auswertung in Kapitel 3 und 4 bestimmen:

Erstes Hauptziel — Ein wiederverwendbares Werkzeug: Das im Rahmen dieser Arbeit entwickelte Plugin typo3-test-audit (siehe Abschnitt 1.2 und 3.1) soll nicht nur für diese Arbeit, sondern auch danach im Tagesgeschäft nutzbar sein — für beliebige TYPO3-Extensions, nicht nur für hf-view-helpers.

Zweites Hauptziel — Machbarkeit zeigen: Ich will nachweisen, dass sich aus einer bestehenden PHP-Klasse mithilfe eines LLM automatisch eine lauffähige PHPUnit-Testklasse erzeugen lässt, ohne dass ich die Tests von Grund auf selbst schreiben muss. „Lauffähig" heisst hier ganz konkret: Die Tests lassen sich mit PHPUnit ausführen, sie bestehen (grün) und sie prüfen sinnvolle Fälle statt nur oberflächlich zu bestehen.

Drittes Hauptziel — Fairer Vergleich mit Zahlen: Ich will nicht nur behaupten, dass KI-generierte Tests gut sind, sondern das mit Zahlen belegen. Dazu vergleiche ich für jede der fünf ausgewählten Klassen die von mir manuell geschriebenen Tests mit den KI-generierten Tests, anhand von vier klar messbaren Kriterien:
Erstellungszeit — wie lange dauert es, bis eine lauffähige, fehlerfreie Testklasse vorliegt?Methodenabdeckung (Coverage) — wie viele der öffentlichen Methoden werden überhaupt von einem Test aufgerufen?
PHPStan-Fehler — wie sauber und typsicher ist der generierte Testcode?
Mutation Score — prüfen die Tests wirklich die Logik, oder würden sie auch bei einer fehlerhaften Implementierung noch grün bleiben?


### 1.4 Stakeholder-Analyse

An dieser Arbeit sind mehrere Parteien beteiligt oder zumindest indirekt betroffen, auch wenn nicht alle aktiv mitarbeiten. Die folgende Übersicht beschreibt für jede Partei, welches Interesse sie an der Arbeit hat und wie stark sie mitbestimmen kann, wie die Arbeit abläuft und welche Richtung sie nimmt.

Sinian Zhang (Autor und Entwickler) — Einfluss: hoch Ich schreibe die Arbeit und bin gleichzeitig der Entwickler, der die Tests erstellt und die KI-Ergebnisse prüft. Ich habe zwei Ziele: Erstens will ich im Projektalltag Zeit sparen, indem ein grosser Teil der Testerstellung automatisch läuft. Zweitens will ich im Rahmen des CAS solides Wissen zu PHPUnit-Testing sowie einige Tools aufbauen (siehe Lernziel in Abschnitt 1.3). Weil ich sowohl die Methodik festlege als auch die Messungen selbst mache, habe ich den grössten Einfluss auf die Arbeit.

Ehemaliger oder zukünftiger Arbeitgeber — Einfluss: mittel Die Firma profitiert wirtschaftlich von besserer Code-Qualität. Mein Arbeitgeber hat den ursprünglichen Anlass für das Thema geliefert (siehe Abschnitt 1.2) und liefert mit dem Demoprojekt den fachlichen Rahmen, mischt sich aber nicht in die wissenschaftliche Methodik oder einzelne Entscheidungen der Arbeit ein. Auch für einen zukünftigen Arbeitgeber ist die Arbeit relevant: Sowohl das Plugin typo3-test-audit als auch das dabei aufgebaute Fachwissen zu KI-gestützter Testgenerierung lassen sich in künftigen Projekten wiederverwenden.

TYPO3-Community — Einfluss: niedrig für die breitere TYPO3-Community ist die Arbeit interessant. Da das Plugin typo3-test-audit wiederverwendbar ist (siehe Abschnitt 1.2), könnten die Ergebnisse auch anderen TYPO3-Entwicklerinnen und -Entwicklern nützen. Die Community ist aber nicht aktiv an der Arbeit beteiligt und hat deshalb keinen direkten Einfluss auf deren Verlauf.

FHNW / Dozent — Einfluss: hoch Aus akademischer Sicht steht der wissenschaftliche Beitrag der Arbeit im Vordergrund: eine nachvollziehbare Methodik, überprüfbare Hypothesen (siehe Abschnitt 2.3) und eine saubere, messbare Auswertung. Der Dozent bzw. die Dozentin begleitet die Arbeit fachlich, gibt Feedback zu Aufbau und Methodik und bewertet am Ende das Ergebnis. Damit ist der Einfluss auf Anforderungen und Qualitätsmassstäbe hoch, auch wenn ich die inhaltliche Umsetzung selbst mache.


---

## 2. Planung

### 2.1 Wertstromanalyse

Die Wertstromanalyse (Value Stream Mapping, VSM) ist eine aus dem Lean Management stammende Methode, die den gesamten Arbeitsfluss „von der Idee bis in Produktion" visualisiert und dabei Engpässe (Bottlenecks) sowie Wartezeiten zwischen einzelnen Prozessschritten sichtbar macht (dora.dev/guides/value-stream-management/). Im Kontext dieser Arbeit wird der Wertstrom auf den relevanten Ausschnitt eingegrenzt: von der fertig geschriebenen PHP-Klasse bis zur produktiv nutzbaren Testabdeckung.

**Ist-Zustand (aktueller Prozess):**

Sobald eine PHP-Klasse fertig geschrieben ist, müsste als nächster Schritt die passende Testklasse manuell erstellt werden. Das dauert pro Klasse je nach komplixität mehr (vgl. Messdaten Abschnitt 3.5). Weil dieser Aufwand im Projektalltag mit engem Zeitbudget kaum aufzubringen ist, wird dieser Schritt in der Praxis sehr häufig ganz übersprungen — also der Bottleneck sitzt hier also innerhalb eines einzelnen Schritts, der manuellen Testerstellung selbst (siehe H1, Abschnitt 2.3). Die Klasse landet dadurch ohne Testabdeckung in der CI/CD-Pipeline.

Dahinter steckt eine Unterscheidung, die auch das DORA-Modell zur Messung von Software-Delivery-Performance trifft: den Normalfall, bei dem eine Funktion ohne Probleme bis in die Produktion läuft, und den Problemfall, bei dem ein Fehler erst in Produktion bemerkt und dort nachträglich behoben werden muss. Fehlende Tests verlangsamen den Normalfall nicht direkt, machen aber den deutlich teureren Problemfall wahrscheinlicher.

![Wertstromanalyse Ist-Zustand](images/wertstrom_ist_zustand.png)

**Soll-Zustand (Prozess mit KI-Unterstützung):**

Sobald die PHP-Klasse fertig geschrieben ist, erzeugt der KI-Generator die passende Testklasse in sehr kurzer Zeit inklusive Korrektur (vgl. Messdaten Abschnitt 3.5). Anschliessend wird die Testklasse mit PHPUnit ausgeführt und bei Bedarf in wenigen Minuten von Hand (ggf. per KI) nachgebessert. Weil dieser Schritt kaum noch Zeit kostet, entfällt er nicht mehr aus Zeitgründen, und die Klasse gelangt mit Testabdeckung in die CI/CD-Pipeline. Fehler werden dadurch früher erkannt, und der aufwändige Recovery-Pfad — also die nachträgliche Fehlerbehebung in Produktion — wird seltener durchlaufen.

CI/CD-Integration ist in der Praxis Standardverfahren, wird in dieser Arbeit jedoch bewusst nicht umgesetzt, sondern nur als Zielpunkt des Wertstroms mitgeführt — um den Aufwand im Rahmen des CAS auf die Testgenerierung selbst zu fokussieren (vgl. Abgrenzung in Abschnitt 1.2).

Bezug zu DORA-Metriken-Theorie: Weil die Testerstellung schneller geht, wird auch die Zeit von der Codeänderung bis zur Auslieferung kürzer — das misst DORA mit der Kennzahl Lead Time for Changes. Ausserdem werden Fehler eher schon vor dem Deployment gefunden statt erst danach, wodurch weniger fehlerhafte Änderungen in Produktion landen — DORA misst das mit der Change Failure Rate. Die Wertstromanalyse zeigt also, warum die Zeitersparnis durch KI (Hypothese H1 in Abschnitt 2.3) für diese Arbeit wichtig ist.

![Wertstromanalyse Soll-Zustand](images/wertstrom_soll_zustand.png)



### 2.2 Geplanter KI-Einsatz

**Vollständiger Ablauf — drei Abschnitte:**

```
══════════════════════════════════════════════════════
ABSCHNITT 1: INPUT & VORBEREITUNG
══════════════════════════════════════════════════════
Code-Analyse aller PHP-Klasse einer Extension (KI-CLI-Skill:test-audit-text)
- public Methoden identifizieren
- Abhängigkeiten prüfen (DI, GeneralUtility, etc.)
- Klassifizierung: Unit / Edge / Functional
    ↓  
(ENTWEDER)
Generierung von Tests für alle Klasse (KI-CLI-Skill:generate-unit-tests) 
(ODER)
Generierung von Tests für eine Klasse   
- Prompt erstellen, mit AAA-Pattern  (Claude Code)
- Mit oder ohne PHPDoc/ Inline-Kommentare
══════════════════════════════════════════════════════
    ↓
	↓
══════════════════════════════════════════════════════
ABSCHNITT 2: AUSFÜHRUNG & VALIDIERUNG 
(je nach Komplxität von zu testenden Klassen)
══════════════════════════════════════════════════════
LLM (Claude Code) generiert und speichert eine oder alle PHP-Testklassen
    ↓
Kontrolle, Fix, Nachverbesserung(manuell oder per KI) einer oder alle PHP-Testklassen
- PHPUnit ausführen und analysieren
- Code Coverage 
- PHPstan (Error-Fix: manuell oder per KI)
- Mutationstest per infection (Verbesserung: manuell oder per KI-CLI-Skill:fix-unit-tests)
- Subjektive Analyse und Korrektur
══════════════════════════════════════════════════════
	↓
	↓
══════════════════════════════════════════════════════
ABSCHNITT 3: BEWERTUNG & ERKENNTNISSE
(nur Messmethodik dieser Arbeit — kein Bestandteil des wiederverwendbaren Plugin-Workflows)
Vergleich: Manuell vs. KI-generiert, Kriterien 
- Laufen Tests durch? (ja / nein / nach Korrektur)
- Code Coverage Funktions- und Methodenabdeckung (%)
- Assertions sinnvoll? (Edge-Cases, Grenzwerte)
- Mocking korrekt? (TYPO3-Dependencies)
- PHPstan (Error-Fix per KI) 
- Mutationstest per infection  
- Zeitersparnis gegenüber manuell
```

**Bemerkungen**:
- Die konkrete Anwendung auf genau diese 5 Klassen (inkl. Begründung der Auswahl) ist bereits Inhalt von 2.4 "Vorgehen zur Einführung und Validierung" und wird in 3.2/3.4 mit echten Ergebnissen durchexerziert. 

- Die 4 Plugin Skills finden Sie im Abschnitt 3.1 Tooling und Infrastruktur.

- Die Abschnitte 1 und 2 des obigen Ablaufs (Code-Analyse, Prompt, LLM-Aufruf, PHPUnit-Ausführung) bilden den generischen Prozess, den das Plugin typo3-test-audit bei jeder beliebigen Klasse einer TYPO3-Extension durchläuft — unabhängig vom konkreten Projekt.

- Abschnitt 3 dagegen — der Vergleich mit manuell geschriebenen Tests — ist kein Bestandteil dieses wiederverwendbaren Ablaufs, sondern ausschliesslich die Messmethodik, mit der in dieser CAS-Arbeit die Praxistauglichkeit der KI-generierten Tests überprüft wird (siehe Abschnitt 3.5). 

- Für die Bewertung und Erkenntnisse dieser CAS-Arbeit bekommt die KI bei der Testgenerierung bewusst nur den reinen PHP-Code zu sehen — ohne PHPDoc oder Kommentare. Grund: So zeigt sich, was die KI rein aus dem Code selbst herausfinden kann, ohne zusätzliche Hilfestellung. (s. Abschnitt 1.1).

### 2.3 Hypothesen und erwartete Benefits

**Hypothesen im Klartext:**
Ich habe einige grobe Hypothesen wie folgendes aufgelistet.
H1: KI reduziert die Erstellungszeit deutlich gegenüber der manuellen Vorgehensweise. Bei trivialen Klassen ist eine sehr kurze Generierungszeit zu erwarten, bei komplexeren Klassen ist etwas mehr Korrekturzeit einzuplanen.

H2: Die statische Fehlerrate (z.B. PHPStan-Fehler) in KI-generierten Testklassen ist niedrig bzw. mit manuell erstellten Tests vergleichbar — die KI produziert also nicht systematisch mehr syntaktische/statisch erkennbare Fehler als ein Mensch.

H3: KI-generierte Tests erreichen eine hohe Methods Coverage, auch bei komplexeren Format-ViewHelpern. (Exakte Schwellenwerte werden erst nach der Messung festgelegt.)

H4: Der Grossteil der generierten Tests läuft ohne manuelle Korrekturen durch. Einzelne Klassen, insbesondere solche mit TYPO3-Abhängigkeiten, können Nacharbeit erfordern.

H5: Bei Glue-Code mit TYPO3-Dependencies erfordert das KI-generierte Mocking manuelle Korrekturen, ist aber nicht grundsätzlich falsch. Die KI liefert einen brauchbaren Ausgangspunkt, der genaue Aufwand wird im Messprotokoll erfasst.

H6: Unabhängig davon, ob zusätzlich PHPDoc und Inline-Kommentare als Kontext mitgegeben werden, liefert die KI generell Tests mit guter Assertions-Qualität: Grenzwerte werden geprüft und Edge-Cases sinnvoll behandelt.

H7: KI-generierte Tests erzielen nach Korrektur einen hohen Mutation Score (MSI of Covered, gemessen mit Infection PHP). 

Die Messmethodik sowie die genauen Zielwerte, Qualitätsreferrenz etc. werden im Abschnitt 3.5 nach den ersten Messdurchläufen gemessen und festgelegt und die Benefits werden ausführlich erläutet.

### 2.4 Vorgehen zur Einführung und Validierung

Die fünf Klassen wurden gezielt so ausgewählt, dass sie ein breites Spektrum von Komplexität und TYPO3-Abhängigkeit abdecken:
1) JsonDecodeViewHelper (Classes/ViewHelpers/Format/JsonDecodeViewHelper.php): Reine PHP-Logik ohne TYPO3-Kern-Abhängigkeit. Enthält relevante Edge-Cases (ungültiges JSON, leere Eingaben) und erfordert einfache Stubs/Mocks.
2) CleanHtmlViewHelper (Classes/ViewHelpers/Format/CleanHtmlViewHelper.php): Ebenfalls reine PHP-Logik ohne TYPO3-Abhängigkeiten. Der Einsatz umfangreicher regulärer Ausdrücke erhöht die Komplexität und macht Edge-Case-Tests besonders relevant.
3) RoundViewHelper (Classes/ViewHelpers/Format/RoundViewHelper.php): Reine PHP-Logik mit mathematischen Berechnungen (Rundung, Genauigkeit). Edge-Cases wie Grenzwerte und Gleitkommawerte erfordern sorgfältige Testabdeckung.
4) Greeter (Classes/Dummy/Greeter.php): Eine triviale Dummy-Klasse ohne TYPO3-Abhängigkeiten, Edge-Cases oder Mocking-Bedarf. Dient als Baseline und Kontrollfall für die einfachste Testsituation.
5) DateViewHelper (Classes/ViewHelpers/Format/DateViewHelper.php): Enthält reale TYPO3-Abhängigkeiten (Glue-Code). Das notwendige Mocking ist deutlich aufwändiger als bei den übrigen Klassen und zeigt exemplarisch die Grenzen der automatisierten Testgenerierung.

Begründung der Auswahl:
Die Klassen stammen alle aus packages/hf-view-helpers, da dort der Test-Audit-Workflow (Plugin typo3-test-audit) bereits eingerichtet ist und alle 47 Klassen klassifiziert vorliegen (4 Unit, 21 Edge-Cases, 18 Functional, 4 nicht testbar). Die gewählten fünf Klassen decken bewusst den Bogen von trivialer Logik über komplexe reine PHP-Logik bis hin zu TYPO3-abhängigem Glue-Code ab.
 „Clue Codes" ist eine Verschreibung von „Glue-Code" (englisch für „Klebstoff-Code"). Der Begriff bezeichnet Code, der verschiedene Systemteile miteinander verbindet — in TYPO3 konkret: ViewHelper-Klassen, die TYPO3-interne Dienste aufrufen (z.B. makeInstance(), CacheManager, DateUtility). Solcher Code ist eng mit dem Framework verzahnt und schwer isoliert zu testen.

### 2.5

---

## 3. Umsetzung

### 3.1 Tooling und Infrastruktur

Für die Umsetzung dieser Arbeit kommen folgende Tools und Technologien zum Einsatz:
DDEV — Lokale Entwicklungsumgebung auf Docker-Basis, in der das TYPO3-Projekt betrieben wird (https://ddev.com/)
TYPO3 14.3 — Das CMS-Framework, für das die Extension entwickelt und getestet wird (https://typo3.com/de/typo3-v14)
PHP 8.3 — Laufzeitumgebung für alle PHP-Klassen und Tests
PHPUnit 12 — Test-Framework für die Ausführung der Unit-Tests
TYPO3 TestingFramework — Erweitert PHPUnit um TYPO3-spezifische Hilfsfunktionen (beinhaltet in TYPO3 14.3)
Claude API (claude-sonnet-4-6) — Das verwendete LLM zur Testgenerierung
Xdebug / php-code-coverage — Werkzeuge zur Messung der Codeabdeckung (https://xdebug.org/)
PHPStan — Statisches Analyse-Tool; prüft den PHP-Code auf Typfehler und potenzielle Bugs, ohne ihn auszuführen — wird hier eingesetzt, um die Qualität der generierten Testklassen zu bewerten (https://phpstan.org/) 
Infection — Mutations-Test-Framework für PHP; prüft die Qualität der Tests, indem es den Quellcode gezielt verändert und überprüft, ob die Tests diese Änderungen erkennen (sogenannte Mutanten "töten") (https://infection.github.io/)


### 3.2 Entwickltes Resultat: Claude Code CLI Plugin
Das Plugin wurde im Rahmen dieser Arbeit entwickelt und besteht aus vier aufeinander aufbauenden Skills, die direkt im Claude Code CLI aufgerufen werden:

/test-audit-text — Der erste Schritt: Das Plugin durchsucht alle PHP-Klassen einer Extension und klassifiziert sie automatisch in vier Kategorien: geeignet für Unit-Tests, Edge-Fälle (sowohl Unit- als auch Functional-Tests möglich), nur für Functional-Tests geeignet, oder nicht direkt testbar. Die Klassifizierung basiert auf Signalen im Quellcode, z. B. ob eine Klasse TYPO3-Infrastruktur wie CacheManager, ConnectionPool oder $GLOBALS['TSFE'] verwendet. Das Ergebnis wird als .md- und .txt-Datei gespeichert. Auf diesem Report basiert auch die Auswahl der fünf Beispielklassen in dieser Arbeit.

/test-audit-chart — Liest den generierten Report und erstellt daraus ein SVG-Donut-Diagramm als visuelle Übersicht der Klassenverteilung.

/generate-unit-tests — Liest den .txt-Report und generiert automatisch PHPUnit-Testklassen für alle Unit- und Edge-Klassen. Pro Methode werden Happy-Path, Grenzwerte, Bool-Flags und Fehlerpfade abgedeckt. Alle Tests folgen dem AAA-Pattern (Arrange, Act, Assert). Existiert bereits eine Testdatei, analysiert der Skill vorhandene Kommentare und PHPDoc-Hinweise und ergänzt gezielt die fehlenden Testfälle — bestehender Code wird dabei nicht verändert.

/fix-unit-tests — Liest einen Infection-Report (Mutationstest) und ergänzt gezielt neue Testmethoden für alle überlebten Mutanten. So können Lücken in der Testabdeckung systematisch geschlossen werden.
Das Plugin hat eigentlich keinen direkten Bezug auf der Arbeit, das Plugin ist wiederverwendbar und nicht auf dieses Projekt beschränkt — es kann bei beliebigen TYPO3-Extensions eingesetzt werden, was ich sicherlich als einen Mehrwert und Benifit halte, möchte daher expliziert in diesem Abschnitt aufnehmen und kurz erläutern.

### 3.3 Nutzung von Plugin
Load Plugin
claude --plugin-dir .claude/plugins/typo3-test-audit
(mit Screenshot)

Run 4 Skills
/typo3-test-audit:test-audit-text <extension-path-or-name>
/typo3-test-audit:test-audit-chart <extension-path-or-name>
/typo3-test-audit:generate-unit-tests <extension-path-or-name>
/typo3-test-audit:fix-unit-tests <extension-path-or-name>
(mit Screenshot)

### 3.4 Beispielbasierte Demonstration

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

### 3.5 Pilotnutzung
Als Pilotprojekt dient die Extension hf-view-helpers im TYPO3-Demoprojekt cas_ai_phpunit. Die fünf Beispielklassen wurden anhand des Audit-Reports ausgewählt und decken bewusst unterschiedliche Komplexitätsstufen ab — von einfacher Dummy-Logik bis hin zu TYPO3-abhängigem Code mit Mocking-Bedarf.

Für jede Klasse wird der vollständige Messzyklus in drei Schritten durchgeführt:

Manuelle Referenz — Die Testklasse wird vollständig von Hand geschrieben. Diese Messung dient als Baseline für den Vergleich mit der KI-generierten Variante.
KI-Variante A (Code only) — Der Quellcode der Klasse wird ohne zusätzlichen Kontext an das LLM übergeben. Die generierte Testklasse wird anschliessend mit PHPUnit ausgeführt und die Ergebnisse werden gemessen.
KI-Variante B (Code + Kontext) — Zusätzlich zum Quellcode werden PHPDoc-Kommentare und Inline-Kommentare als Hinweise mitgegeben. Ziel ist es zu prüfen, ob der zusätzliche Kontext die Testqualität verbessert.
Nach jedem Schritt wird das Messprotokoll ausgefüllt (siehe Abschnitt 3.5). Die Ergebnisse aller fünf Klassen fliessen vollständig in Kapitel 4 ein.

### 3.6 Messung und Beobachtung der Benefits und Effekte

#### Messmethodik

Für jede der fünf Klassen wird das Messprotokoll in zwei Gruppen ausgefüllt: einmal für die manuelle Referenz, einmal für die KI-gestützte Variante.
Manuell: Die Stoppuhr startet zu Beginn der Test-Erstellung. Der Entwickler liest die Zielklasse, versteht die öffentlichen Methoden, überlegt sinnvolle TestCases und schreibt die Testklasse vollständig aus. Die Messung gilt als abgeschlossen, sobald zwei Kriterien erfüllt sind: PHPUnit läuft fehlerfrei durch, und PHPStan meldet keine Fehler. Erst dann wird die Stoppuhr gestoppt.

KI: Die Zeitmessung erfolgt in zwei Phasen:
Phase 1 — Generierung: Die Stoppuhr startet mit dem Absenden des Prompts. Die KI legt die fehlende Testklasse samt Testmethoden an. Die Stoppuhr stoppt, sobald die Generierung abgeschlossen ist.
Der Prompt auf Deutsch lautet z.B.: Erstelle für die folgende PHP-Klasse JsonDecodeViewHelper.php eine PHPUnit-Testklasse mit breiter Code Coverage. Verwende das TYPO3 TestingFramework und halte dich an das AAA-Pattern (Arrange, Act, Assert).
Phase 2 — Korrektur: Die Stoppuhr startet erneut. Die generierte Testklasse wird mit PHPStan analysiert; auftretende Fehler werden per KI korrigiert. Die Stoppuhr stoppt, sobald PHPStan keine Fehler mehr meldet.
Auch für die KI-Variante gilt dasselbe Abschluss-Kriterium wie für die manuelle Messung: PHPUnit läuft fehlerfrei, PHPStan meldet keine Fehler.

Test-Befehl anhand der Beispiel-Klasse: JsonDecodeViewHelper.php
PhpUnitTest:
ddev exec php vendor/bin/phpunit \
-c packages/hf-view-helpers/Build/phpunit/UnitTests.xml \
packages/hf-view-helpers/Tests/Unit/ViewHelpers/Format/JsonDecodeViewHelperTest.php



PhpUnit-Coverage-Test: 
ddev exec XDEBUG_MODE=coverage php vendor/bin/phpunit \
-c packages/hf-view-helpers/Build/phpunit/UnitTests.xml \
--coverage-text \
packages/hf-view-helpers/Tests/Unit/ViewHelpers/Format/JsonDecodeViewHelperTest.php
    
PHPStan-Test:
ddev php vendor/bin/phpstan analyse \
-c packages/hf-view-helpers/Build/phpstan/phpstan.tests.neon \
packages/hf-view-helpers/Tests/Unit/ViewHelpers/Format/JsonDecodeViewHelperTest.php

Kriterien zur Bewertung
Kombination von 3 Testverfahren, nämlich (PhpUnit)Codeabdeckungstest, PhpStan-Test und Mutationstest.
Codeabdeckung (Code Coverage) beschreibt, wie viel Prozent des Quellcodes durch Tests ausgeführt werden. Eine hohe Codeabdeckung deutet auf eine gründlichere Testung und ein geringeres Fehlerrisiko hin. In meiner Bewertung wird in der ersten Linie Funktions- und Methodenabdeckung berücksichtigt, also wie viele Funktionen bzw. Methoden mindestens einmal durch Tests aufgerufen wurden. (https://docs.phpunit.de/en/13.2/code-coverage.html)
PHPStan ist ein leistungsstarkes Tool zur statischen Code-Analyse für PHP-Projekte. Es hilft Entwicklern, Fehler frühzeitig zu erkennen und die Codequalität zu verbessern, ohne den Code tatsächlich ausführen zu müssen. (https://phpstan.org/)
Ein PHPStan Level (https://phpstan.org/user-guide/rule-levels) bestimmt die Strenge der statischen Code-Analyse in PHPStan. . Die Level reichen von Stufe 0 (grundlegende Syntaxprüfungen) bis 10 (extrem strenge Typisierung). Bei meiner Bewertung ist der Level 6 in Einsatz. Level 6 ist der pragmatische Mittelweg: 
1) Erzwingt vollständige Typ-Annotationen — wichtig für lesbare, wartbare Tests.
2) Die PHPStan-PHPUnit-Extension greift auf Level 6 optimal: Sie erkennt falsch verwendete Mocks, fehlerhafte Assertion-Signaturen und ungültige DataProvider-Strukturen.
3) Etablierter Standard in der TYPO3-Community für Testcode-Analyse.

Mutationstest ist ein Softwaretest, wo künstliche Bugs (Mutationen) im Code produziert werden, um festzustellen, ob die vorhandenen Tests ausreichen, um diese künstlichen Fehler zu entdecken. Die Codeabdeckung reicht nicht immer aus, Mutationstests sind für Unit-Tests unerlässlich, da sie die tatsächliche Qualität und Aussagekraft Ihrer Tests überprüfen. Ich habe Infection im Einsatz, Inection ist eine der bekanntesten Mutation Testing Frameworks für PHP (https://github.com/infection/infection) Bei meiner Bewertung ist MSI-of covered (https://infection.github.io/guide/#Covered-Code-Mutation-Score-Indicator) berücksichtigt. Nämlich Mutation Score Indicator, der nur Mutanten in Code berücksichtigt, der tatsächlich von Tests ausgeführt wird.

Bewertungsskala Assertions (Stufe:1–5) 
Die Stufenzuordnung basiert grundsätzlich auf subjektiver Beobachtung sowie den folgenden Überlegungen und Kriterien.
Stufe-5: Alle öffentlichen Methoden geprüft, Assertions für Happy-Path, Grenzwerte und Fehlerfälle, keine Tautologien, Testwerte unterscheiden ähnliche Implementierungen, Mocking korrekt (richtiger Typ createMock/createStub, richtige Methode, kein unnötiges Mocking), PHPStan-Errors: 0–1, MSI > 90%
Stufe-4: Grundsätzlich korrekte Assertions ohne Tautologien, einzelne Grenzwerte oder Nebenmethoden fehlen, Mutationstests mehrheitlich bestanden, Mocking weitgehend korrekt, kleinere Typ-Ungenauigkeiten, PHPStan-Errors: 2–3, MSI 75–90%
Stufe-3: Assertions vorhanden, aber mit strukturellen Schwächen: Testwerte unterscheiden ähnliche Implementierungen nicht, und/oder ganze Methoden oder Argument-Defaults sind vollständig ungeprüft, Mocking zum Teil strukturell falsch (falscher Typ, fehlende oder überflüssige Abhängigkeiten), PHPStan-Errors: 5–10, MSI 50–75%
Stufe-2: Mehrere tautologische oder triviale Assertions (z.B. assertInstanceOf, assertNotNull), Kernlogik wird nicht wirklich verifiziert, Mocking fehlend oder sinnlos, PHPStan-Errors: > 10, MSI < 50%
Stufe-1: Tests bestehen formal, prüfen aber keine sinnvollen Eigenschaften, Implementierung ist falsch oder fehlerhaft, oder jede Implementierung würde die Tests bestehen, sehr viele PHPStan-Errors: > 10, sehr geringer MSI < 50%

#### Ergebnistabelle — Übersicht alle 5 Klassen *(wird nach Umsetzung ausgefüllt)*

| Klasse | Zeit Man. | Coverage Man. (Methods) | Mutationstest Man. (MSI of Covered) | PHPStan Man. | 
|---|---|---|---|---|
| JsonDecodeViewHelper | > 30 Min. | 50% | 41% | 0 |
| ForViewHelper | 30 Min. | 100% | 53% | 0 |
| RoundViewHelper | > 60 Min. | (?) | (?) | (?) |
| Greeter | 30 Min. | 100% | 100% | 0 |
| DateViewHelper | > 30 Min. | 50% | 48% | 0 |


| Klasse | Zeit KI (Error-Fix) | Coverage KI (Methods) | Mutationstest KI vor und nach dem Fix (MSI of Covered) | PHPStan KI (Error vor und nach dem Fix) | Assert.-Stufe KI vor und nach dem Fix (1–5) |
|---|---|---|---|---|---|
| JsonDecodeViewHelper | 1.5 Min. | 100% | 53% -> 100% | 0 | 3 -> 5 |
| ForViewHelper | 3 Min. (+2 Min.) | 100% | 61% -> 100% | 6 -> 0 | 3 -> 5 |
| RoundViewHelper | 2.5 Min. (+1 Min.) | 100% | 82% -> 100% | 1 -> 0 | 4 -> 5 |
| Greeter | < 1 Min. | 100% | 100% | 0 | 5 |
| DateViewHelper | 2.5 Min. (+1 Min.) | 100% | 57% -> 100% | 2 -> 0 | 4 -> 5 |



#### Bezug zu den Hypothesen

**H1 — Zeitersparnis:** Die manuelle Erstellung dauerte je nach Klasse zwischen 30 und über 60 Minuten — wobei RoundViewHelper mit über 60 Minuten den Höchstwert markiert. Die KI-gestützte Generierung inkl. Korrekturzeit liegt bei allen fünf Klassen unter 5 Minuten: Greeter unter 1 Minute, JsonDecodeViewHelper 1,5 Minuten, RoundViewHelper und DateViewHelper je 3,5 Minuten, ForViewHelper als aufwändigster Fall bei 5 Minuten total. Die Zeitersparnis ist damit in allen Fällen deutlich — auch bei komplexeren Klassen beträgt der Faktor mindestens 6. H1 wird durch die Messdaten klar bestätigt.

**H2 — Statische Fehlerrate (PHPStan):** bezüglich der Daten in der Ergebnistabelle waren die manuell erstellten Tests durchgehend PHPStan-fehlerfrei. Bei den KI-generierten Tests zeigte sich vor der Korrekturphase ein anderes Bild. Die KI produziert im ersten Wurf also nicht weniger, sondern bei einem Teil der Klassen sogar mehr statisch erkennbare Fehler als ein Mensch. H2 wird durch die Messung damit nicht ganz bestätigt. Bemerkenswert ist jedoch, wie schnell sich diese Fehler beheben lassen. Die KI ist also zumindest in der Lage, statische Fehler sehr rasch selbst zu korrigieren.

**H3 — Methods Coverage:** Die manuellen Tests erreichen je nach Klasse 50–100 % Methods Coverage; bei JsonDecodeViewHelper und DateViewHelper nur 50 %, weil nicht alle öffentlichen Methoden abgedeckt wurden. Die KI-generierten Tests erreichen bei allen fünf Klassen 100 % Methods Coverage — unabhängig von der Komplexität der Klasse. H3 wird vollständig bestätigt und übertrifft die ursprüngliche Erwartung.

**H4 — Validität (First-Run GREEN):** Als „First-Run GREEN" gilt eine Testklasse, wenn PHPUnit fehlerfrei läuft und PHPStan keine Fehler meldet. Nach diesem strengen Kriterium erfüllen zwei von fünf Klassen (JsonDecodeViewHelper und Greeter) diese Bedingung sofort. ForViewHelper (6 PHPStan-Fehler), RoundViewHelper (1 Fehler) und DateViewHelper (2 Fehler) benötigten eine Korrekturphase. Die Quote liegt damit bei 40 % — unterhalb der hypothetisierten Mehrheit. Der Korrekturbedarf war jedoch in allen Fällen gering und in unter 2 Minuten behoben; ein vollständiges Scheitern trat nie auf. H4 wird in der strengen Definition nicht bestätigt, relativiert sich aber durch den niedrigen Korrekturaufwand.

**H5 — Mocking-Korrektheit bei Glue-Code:** Der DateViewHelper als einzige Klasse mit echten TYPO3-Dependencies lieferte einen direkt brauchbaren Ausgangspunkt: Assertions-Stufe 4 vor dem Fix, 2 PHPStan-Fehler, MSI 57 %. Nach kurzer Korrektur: Stufe 5, PHPStan-fehlerfrei, MSI 100 %. Das Mocking war strukturell nicht grundsätzlich falsch — es fehlten lediglich korrekte Typ-Annotationen. H5 wird bestätigt: Die KI scheitert bei Glue-Code nicht, liefert aber einen Entwurf, der gezielter Nacharbeit bedarf.

**H6 — Assertions-Qualität:** Die KI liefert generell Tests mit guter Assertions-Qualität: Grenzwerte werden geprüft, Edge-Cases sinnvoll behandelt. Die Ergebnistabelle zeigt, dass die initiale Assertions-Stufe bei einfacheren Klassen (Greeter, RoundViewHelper, DateViewHelper) bereits bei 4–5 lag; bei komplexeren Klassen mit mehr Methoden (JsonDecodeViewHelper, ForViewHelper) startete die KI auf Stufe 3, erreichte aber nach gezielter Nachbesserung durchgehend Stufe 5. Das bedeutet: Die KI erzeugt keine trivialen oder tautologischen Tests — sie prüft die Kernlogik, erkennt relevante Eingabewerte und deckt Fehlerfälle ab. H6 wird bestätigt.

**H7 — Mutationstest (MSI of Covered):** Vor Korrekturen lagen die MSI-Werte zwischen 53 % (JsonDecodeViewHelper) und 100 % (Greeter). Nach Korrekturen erreichten alle fünf Klassen 100 % MSI. Selbst der schwächste Ausgangswert (53 %) konnte durch gezielte Assertions-Verbesserungen auf 100 % angehoben werden. H7 wird vollständig bestätigt: KI-generierte Tests erzielen nach Korrektur einen sehr hohen Mutation Score und sind damit nicht nur syntaktisch, sondern auch inhaltlich aussagekräftig.

---

## 4. Diskussion

### 4.1 Erreichte Ergebnisse und Beitrag der Arbeit

Die Messungen aus Abschnitt 3.6 bestätigen den Kern der Arbeit: KI-gestützte Testgenerierung ist unter TYPO3 14 praktisch einsetzbar und reduziert den Zeitaufwand drastisch, ohne die Testqualität systematisch zu verschlechtern.

Die meisten Hypothesen wurden bestätigt: u.a. Zeitersparnis, viel breitere Methods-Coverage bzw. nach der Korrektur gegenüber manuelle Referenz, Stube/Mocking bei Glue-Code überwiegend brauchbar, gute Assertion-Qualität, etc. Nicht bzw. nur teilweise bestätigt wurden einen Hypothese, nämlich: die KI produzierte im ersten Wurf mit teils mehr PHPStan-Fehler als die manuelle Referenz, die Korrekturphase kurz aber benötigt.

Der zentrale Befund liegt damit nicht in "KI ersetzt den Menschen", sondern "KI unterstützt den Menschen": Die KI übernimmt die zeitintensive Fleissarbeit der Testerstellung praktisch vollständig, während eine kurze, gezielte menschliche (oder KI-gestützte) Korrekturphase notwendig bleibt, bzw. mit Hilfe von Inline-Kommentar als Hinweis zum konkreten TestCase bringt KI theoretisch viel bessere Qualität. Diese wohl benötigte Korrektur kostet wenige Zeit — der Gesamtaufwand bleibt damit trotzdem um ein Vielfaches unter der manuellen Referenz.

Reine Logik vs. Glue-Code: Bei den drei reinen PHP-Logik-Klassen (JsonDecodeViewHelper, RoundViewHelper, etc.) lieferte die KI durchgehend hochwertige, teils sofort fehlerfreie Tests. Beim einzigen Glue-Code-Fall mit echten TYPO3-Abhängigkeiten (DateViewHelper) war das Ergebnis vor Korrektur bereits solide, verbesserte sich aber erst durch gezielte Nachbesserung.

Beitrag der Arbeit:
- Praktischer Nachweis, dass KI-gestützte PHPUnit-Testgenerierung unter TYPO3 14 funktioniert und unter Umständen produktionsnahe Ergebnisse liefert.
- Ein wiederverwendbares, projektunabhängiges Werkzeug (Plugin typo3-test-audit), das über diese Arbeit hinaus im Tagesgeschäft einsetzbar ist.
- Eine differenzierte Empfehlung: KI-Generierung eignet sich besser für reine Logik-Klassen; bei TYPO3-Glue-Code liefert sie einen brauchbaren, aber korrekturbedürftigen Ausgangspunkt.

### 4.2 Andere Erfahrungen und Arbeiten 
**Vergleich mit anderen Praxiserfahrungen**
Praxisberichte oder Referenzen zum Einsatz generativer KI (z. B. GitHub Copilot, JetBrains AI, ChatGPT) im Software-Testing findet man im Internet oder Publikationen, sie  zeigen, zusammengefasst, ein klares und ähnliches Muster aus Vorteilen und Grenzen.

Vorteile: KI spart Zeit bei repetitivem, wiederkehrendem Code und liefert schnell ein brauchbares Testgerüst. Die Code-Coverage steigt dadurch rasch an. Zudem liefert KI auf Nachfrage schnell Ideen für Edge-Cases (z. B. Null-Werte, Extremwerte), die man selbst leicht übersieht.

Nachteile: Der „Echo Chamber“-Effekt, also KI übernimmt Fehler aus dem Code, statt sie zu erkennen — rechnet eine Funktion aus Versehen falsch, bestätigt die KI dieses falsche Ergebnis trotzdem als richtig. KI neigt dazu, Mocks exzessiv zu nutzen anstatt das tatsächliche funktionale Rückgabe-Verhalten einer Komponente zu prüfen eben auch veraltete Test-Methoden (z.B. deprecated PHPUnit-Features) zu nutzen. 

Um dieses Risiko zu senken, braucht es drei Validierungsmassnahmen:

Menschliche Kontrolle: KI-generierter Testcode darf nie ungeprüft übernommen werden — er muss wie der Code eines unerfahrenen Entwicklers auf fachliche Richtigkeit und bezüglich der Geschäftsanforderungen geprüft werden.

Mutationstests (z. B. Infection PHP): Der eigentliche Härtetest. Ein Tool baut gezielt Fehler in den Produktionscode ein — schlägt der Test dabei nicht fehl, ist die Assertion nutzlos und muss überarbeitet werden.

Ergänzende Integrationstests: Da KI Abhängigkeiten (Datenbanken, APIs) meist einfach wegmockt, müssen wichtige Systempfade zusätzlich mit echten Integrationstests abgesichert werden.

**Einordnung in die akademische Forschung**
Meine Ergebnisse lassen sich in den breiteren Forschungsstand mehr oder weginer einordnen. 
Durrani et al. (2025) untersuchen mittels eines Zero-Truncated-Poisson-Modells quantitativ, wie sich KI-Einsatz auf Genauigkeit und Effizienz über sechs SE-Phasen hinweg auswirkt. Für die Testing-Phase zeigen sie, dass KI die Test-Accuracy signifikant verbessert (p < 0.0001), der Effizienzgewinn jedoch statistisch nicht signifikant ist (p = 0.1359) — mit dem Hinweis, dass KI-generierte Testsuiten weiterhin manuelle Validierung bzw. "human-in-the-loop"-Aufsicht benötigen.
 
Mein Erkenntnis deckt sich mit den eigenen Messungen: Auch hier war bei 3 von 5 Klassen eine Korrekturphase nötig (H2/H3), und die Zeitersparnis (H1) war zwar sehr deutlich, ersetzte aber keine menschliche Kontrolle. 

Vergleich mit einer Breitenstudie z.B. von Durrani et al., liefert meine Arbeit eher tool- und technologiespezifische Evidenz für einen konkreten LLM-Workflow (Claude API) im TYPO3/PHPUnit-Kontext.

### 4.3 Empfehlung, Innovation, Lernen

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
