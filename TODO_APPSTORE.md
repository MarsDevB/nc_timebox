# TimeBox – TODO: Veröffentlichung im Nextcloud App Store

Stand: 2026-09-04 · Code: Commit `d31d802` auf `master`, Tag `v1.0.0` ✅

## 1. LICENSE ergänzen (PFlicht)
- [x] Datei `LICENSE` im App-Root mit AGPL-v3-Text anlegen ✅ (erledigt)

## 2. Git-Repository veröffentlichen (Pflicht)
- [ ] Public-Repo `github.com/MarsDevB/nc_timebox` erstellen
- [ ] `git remote add origin git@github.com:MarsDevB/nc_timebox.git`
- [ ] Push: `git push -u origin master --tags` (Tag `v1.0.0` mitschieben)
- [ ] GitHub **Release** für Tag `v1.0.0` anlegen – Workflow `.github/workflows/build.yml` hängt automatisch den Tarball (`dist/timebox-1.0.0.tar.gz`) als Asset an

## 3. info.xml ergänzen (Pflicht/empfohlen)
- [x] `<version>1.0.0</version>` ✅
- [x] `<author>MarsDevB</author>` ✅
- [x] `<website>` → https://github.com/MarsDevB/nc_timebox ✅
- [x] `<bugs>` → https://github.com/MarsDevB/nc_timebox/issues ✅
- [x] `<repository type="git">` → https://github.com/MarsDevB/nc_timebox.git ✅
- [ ] optional: `<screenshot>` URLs (Screenshots zuerst hochladen, siehe Punkt 5)

## 4. Build/Release-Mechanismus (Pflicht)
- [x] `Makefile` mit Target `dist` angelegt (`npm ci && npx vite build` → Tarball `dist/timebox-<version>.tar.gz` mit Top-Level-Ordner `timebox/`) ✅ – getestet
- [x] GitHub-Actions-Workflow `.github/workflows/build.yml` (build + xmllint + php -l bei jedem Push; Release-Asset bei Tags) ✅
- [x] Tarball lokal getestet: `make dist` → `dist/timebox-1.0.0.tar.gz` (144K, sauberer Inhalt) ✅

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
- [x] Neue Strings in `l10n/de.json` und `en.json` aufgenommen ✅:
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
