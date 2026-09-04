# TimeBox – TODO: Veröffentlichung im Nextcloud App Store

Stand: 2026-09-04 · Code: Commit `d31d802` auf `master`, Tag `v1.0.0` ✅

## 1. LICENSE ergänzen (PFlicht)
- [ ] Datei `LICENSE` (oder `COPYING`) im App-Root mit AGPL-v3-Text anlegen
- [ ] Quelle: https://www.gnu.org/licenses/agpl-3.0.txt

## 2. Git-Repository veröffentlichen (Pflicht)
- [ ] Public-Repo auf GitHub/GitLab erstellen (z. B. `github.com/<user>/timebox`)
- [ ] `git remote add origin <URL>` im App-Ordner
- [ ] Push: `git push -u origin master --tags` (Tag `v1.0.0` mitschieben)
- [ ] GitHub **Release** für Tag `v1.0.0` anlegen (Release-Notes aus Commit-Message übernehmen)

## 3. info.xml ergänzen (Pflicht/empfohlen)
- [ ] `<version>1.0.0</version>` (statt 0.2.3 – passend zum Tag)
- [ ] `<author>` mit echtem Namen (+ optional `<author><email>` )
- [ ] `<website>` → Repo-URL
- [ ] `<bugs>` → Repo-Issues-URL
- [ ] `<repository type="git">` → Repo-URL
- [ ] optional: `<screenshot>` URLs (Screenshots zuerst hochladen, siehe Punkt 5)

## 4. Build/Release-Mechanismus (Pflicht)
- [ ] `Makefile` mit Target `dist` anlegen:
      `npm ci && npx vite build` → App-Ordner als Tarball packen
- [ ] Der App Store baut das Release selbst aus dem Git-Tag (via nextcloud.com),
      alternativ Tarball beim GitHub Release als Asset hochladen
- [ ] Empfohlen: GitHub-Actions-Workflow (build + lint bei jedem Push)

## 5. Screenshots & Doku (empfohlen)
- [ ] 1–3 Screenshots der App erstellen (PNG, ideal 1350×770)
- [ ] In das Repo (`docs/img/` oder Screenshots bei einem Release/hosten)
- [ ] In `info.xml` unter `<screenshots>` eintragen

## 6. App-Signatur (Pflicht für Store)
- [ ] Account auf https://apps.nextcloud.com (mit GitHub-Login)
- [ ] Zertifikat für App-ID `timebox` beantragen (CSR mit openssl, Anleitung:
      https://nextcloud-server.netlify.app/ „Code signing“ bzw. Developer Docs)
- [ ] Signieren: `occ integrity:sign-app --path=... --certificateFile=... --privateKeyFile=...`
- [ ] Erzeugt `appinfo/certificate.pem` + `signature.json` → committen
- [ ] ⚠️ Muss bei JEDEM Release neu gemacht werden (Versionsnummer ändert sich!)

## 7. L10n ergänzen (empfohlen)
- [ ] Neue Strings in `l10n/de.json` und `en.json` aufnehmen, u. a.:
  - "Limit how many items and calendar events are shown in the TimeBox app."
  - "Maximum number of items displayed"
  - "Maximum number of calendar events displayed"

## 8. App Store Einreichung
- [ ] https://apps.nextcloud.com → „Publish your app“ → Repo-URL + Tag angeben
- [ ] Review abwarten (Moderation prüft Code, Lizenz, Security; dauert meist Tage)

## Bereits vorhanden ✅
- README.md (Features, Installation)
- l10n/de.json + en.json (Grundbestand)
- DB-Migrationen, Settings-Seite, Build (`npx vite build` läuft)
- Git-Commit + Tag v1.0.0 lokal (Push fehlt noch – kein Remote konfiguriert)
