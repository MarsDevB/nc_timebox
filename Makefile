GIT_REPOSITORY ?= https://github.com/MarsDevB/nc_timebox
APP_NAME := timebox
VERSION := $(shell xmllint --xpath 'string(/info/version)' appinfo/info.xml 2>/dev/null || echo "1.0.0")
DIST_DIR := dist
TARBALL := $(DIST_DIR)/$(APP_NAME)-$(VERSION).tar.gz

# Files/dirs that must NOT end up in the release tarball
EXCLUDES := --exclude=./node_modules \
            --exclude=./.git \
            --exclude=./src \
            --exclude=./.github \
            --exclude=./Makefile \
            --exclude=./vite.config.js \
            --exclude=./package.json \
            --exclude=./package-lock.json \
            --exclude=./.gitignore \
            --exclude=./TODO_APPSTORE.md \
            --exclude=*.map \
            --exclude=./js/timebox-main.js.LICENSE.txt

.PHONY: all build dist clean

all: dist

## Build frontend assets (production)
build:
	npm ci
	npx vite build

## Build everything and create the release tarball (like the App Store does)
dist: build
	@mkdir -p $(DIST_DIR)
	tar --transform 's,^\./,$(APP_NAME)/,' -czf $(TARBALL) $(EXCLUDES) \
		--exclude=./$(DIST_DIR) \
		--exclude=./$(APP_NAME)-$(VERSION) .
	@echo "Created $(TARBALL)"

clean:
	rm -rf $(DIST_DIR) js/timebox-main.js js/timebox-main.js.map css/timebox-main.css