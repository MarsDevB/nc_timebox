# TimeBox – TODO: Veröffentlichung im Nextcloud App Store

Stand: 2026-09-05 · Repo: https://github.com/MarsDevB/nc_timebox (master gepusht, Tag v1.0.0, GitHub-Release als Pre-release, Workflow grün ✅)

## 1. LICENSE ergänzen (PFlicht)
- [x] Datei `LICENSE` im App-Root mit AGPL-v3-Text anlegen ✅ (erledigt)

## 2. Git-Repository veröffentlichen (Pflicht)
- [x] Public-Repo `github.com/MarsDevB/nc_timebox` erstellt ✅
- [x] `git remote add origin git@github.com:MarsDevB/nc_timebox.git` ✅
- [x] Push: `git push -u origin master --tags` ✅ (Default-Branch im Repo auf `master` setzen!)
- [x] GitHub **Release** für Tag `v1.0.0` angelegt (als **Pre-release** markiert) – Workflow `.github/workflows/build.yml` baut grün und hängt den Tarball (`dist/timebox-1.0.0.tar.gz`) als Asset an ✅

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
- [x] 1–3 Screenshots der App erstellt und im Repo unter `docs/img/Screenshot_1–5.png` abgelegt ✅
- [x] In `info.xml` unter `<screenshots>` eingetragen ✅ (Screenshot_1–3 via raw.githubusercontent.com;
      ⚠️ Info.xml-Änderung ist NACH dem Tag v1.0.0 – falls gewünscht: `git push` master, Tag ggf. mit
      `git tag -f v1.0.0 && git push origin v1.0.0 --force` auf den neuesten Stand bringen)

## 6. App-Signatur (Pflicht für Store)
- [ ] Schlüssel + CSR lokal erzeugen:
      ```bash
      mkdir -p ~/.nextcloud/certificates
      openssl req -nodes -newkey rsa:4096 \
        -keyout ~/.nextcloud/certificates/timebox.key \
        -out ~/.nextcloud/certificates/timebox.csr \
        -subj "/CN=timebox"
      ```
- [ ] Zertifikat beantragen via Pull Request auf
      **https://github.com/nextcloud/app-certificate-requests**
      (GitHub-Web-Interface: „Create new file" → Datei `timebox/timebox.csr` nennen →
      CSR-Inhalt einfügen → committen → Pull Request öffnen.
      Nice to have: Link zur App-Source, also https://github.com/MarsDevB/nc_timebox.
      Keine Person mentionen – Subscriber kommen von selbst.)
- [ ] Nach Merge: Zertifikat von apps.nextcloud.com abrufen / PR-Antwort entnehmen →
      speichern als `~/.nextcloud/certificates/timebox.crt`
- [ ] App-Registrierung auf https://apps.nextcloud.com (GitHub-Login), Formular füllen:
      - **Public certificate**: kompletter Inhalt der `timebox.crt` (mit BEGIN/END-Zeilen)
      - **Signature over your app's ID**:
        ```bash
        echo -n "timebox" | openssl dgst -sha512 -sign ~/.nextcloud/certificates/timebox.key | openssl base64
        ```
      - ⚠️ `.key` NIEMALS hochladen, `.csr` nur im Zertifikats-PR!
      - ⚠️ Zertifikat-Update im Formular löscht alle vorhandenen Releases → Key sicher backupen!
- [ ] Signieren (im Nextcloud-Container, wo `occ` läuft):
      ```bash
      occ integrity:sign-app \
        --path=/pfad/zu/custom_apps/timebox \
        --certificateFile=~/.nextcloud/certificates/timebox.crt \
        --privateKeyFile=~/.nextcloud/certificates/timebox.key
      ```
- [ ] Erzeugt `appinfo/certificate.pem` + `appinfo/signature.json` → committen
- [ ] Tag `v1.0.0` auf den Signatur-Commit verschieben und pushen:
      ```bash
      git tag -f v1.0.0 -m "Release 1.0.0"
      git push origin master v1.0.0 --force
      ```
- [ ] ⚠️ Muss bei JEDEM Release neu gemacht werden (Versionsnummer ändert sich!) –
      Reihenfolge immer: **erst signieren, dann Tarball bauen / Tag pushen**

## 7. L10n ergänzen (empfohlen)
- [x] Neue Strings in `l10n/de.json` und `en.json` aufgenommen ✅:
  - "Limit how many items and calendar events are shown in the TimeBox app."
  - "Maximum number of items displayed"
  - "Maximum number of calendar events displayed"

## 8. App Store Einreichung
- [ ] https://apps.nextcloud.com → „Publish your app“ → Repo-URL `MarsDevB/nc_timebox` + Tag `v1.0.0` angeben
      (alternativ: fertigen Tarball `dist/timebox-1.0.0.tar.gz` hochladen – Signatur steckt dann bereits
      in `appinfo/signature.json` im Tarball; gefragt nach Zertifikat → `timebox.crt`, nie `.key`/`.csr`)
- [ ] Review abwarten (Moderation prüft Code, Lizenz, Security; dauert meist Tage)
- [ ] Nach Review-Freigabe: GitHub-Release von „Pre-release" auf „Latest" umstellen

## Bereits vorhanden ✅
- README.md (Features, Installation)
- l10n/de.json + en.json (Grundbestand)
- DB-Migrationen, Settings-Seite, Build (`npx vite build` läuft)
- Repo auf GitHub (master + Tag v1.0.0), Release als Pre-release, CI grün
