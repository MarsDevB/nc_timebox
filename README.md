# TimeBox - Nextcloud App

**Organize tasks and calendar events into timeboxes for structured productivity.**

## Features

- 📦 **Create TimeBoxes** - Group tasks and calendar events into focused time blocks
- 🔀 **Drag & Drop Sorting** - Reorder items within a timebox by dragging
- 📋 **Task Integration** - Pull in tasks from Nextcloud's task system
- 📅 **Calendar Integration** - Pull in events from Nextcloud calendars
- 🎨 **Color Coding** - Assign colors to your timeboxes
- ✏️ **Custom Items** - Add custom tasks, events, or notes to any timebox

## Installation

1. Place the `timebox` folder in your Nextcloud `custom_apps/` directory
2. Enable the app in Nextcloud's app management
3. The database migration will run automatically

## Development

```bash
cd timebox
npm install
npm run dev    # Development with hot reload
npm run build  # Production build
npm run watch  # Build on file changes
```

## Usage

1. Click **"+"** in the left sidebar to create a new timebox
2. Select a timebox to view its contents
3. Add tasks or calendar events from the right panel by clicking them
4. Drag items using the **⠿** handle to reorder them
5. Remove items with the **×** button

## Architecture

- **Backend**: PHP (Nextcloud AppFramework) with database migrations
- **Frontend**: Vue 3 + Vite + vuedraggable for drag-and-drop
- **API**: RESTful endpoints for timebox and item CRUD + calendar/task proxying

## License

AGPL-3.0-or-later