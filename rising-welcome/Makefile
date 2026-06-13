PREFIX     ?= /usr
DESTDIR    ?=
BINDIR      = $(DESTDIR)$(PREFIX)/bin
LIBDIR      = $(DESTDIR)$(PREFIX)/lib/rising-welcome
ASSETSDIR   = $(LIBDIR)/assets
APPSDIR     = $(DESTDIR)$(PREFIX)/share/applications

.PHONY: all install uninstall clean

all:
	@echo "rising-welcome — nada que compilar (Python puro)"
	@echo "Usá: make install  o  dpkg-buildpackage -us -uc -b"

install:
	install -d $(BINDIR)
	install -d $(ASSETSDIR)/icons
	install -d $(ASSETSDIR)/screenshots
	install -m 755 src/rising-welcome         $(BINDIR)/rising-welcome
	install -m 644 assets/style.css           $(ASSETSDIR)/style.css
	install -m 644 assets/icons/logo-256.png  $(ASSETSDIR)/icons/logo-256.png
	@for f in assets/screenshots/*.png; do \
		[ -f "$$f" ] && install -m 644 "$$f" $(ASSETSDIR)/screenshots/ || true; \
	done
	install -d $(APPSDIR)
	install -m 644 rising-welcome.desktop     $(APPSDIR)/rising-welcome.desktop
	@echo "rising-welcome instalado en $(PREFIX)"

uninstall:
	rm -f  $(BINDIR)/rising-welcome
	rm -rf $(LIBDIR)
	rm -f  $(APPSDIR)/rising-welcome.desktop
	@echo "rising-welcome desinstalado"

clean:
	@echo "Nada que limpiar"
